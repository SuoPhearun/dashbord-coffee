<?php
include "connection.php";

// Variable for search keyword
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// Build query with JOIN to get order items from tbl_order and order_items
$sqls = "SELECT o.*, 
         (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items,
         (SELECT GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') 
          FROM order_items oi 
          JOIN tbl_products p ON oi.product_id = p.id 
          WHERE oi.order_id = o.id) as product_list
         FROM tbl_order o";
$params = [];

if (!empty($searchKeyword)) {
    $sqls .= " WHERE o.customer_id LIKE :search 
               OR o.payment_method LIKE :search 
               OR o.status LIKE :search 
               OR o.phone LIKE :search
               OR o.id LIKE :search";
    $params[':search'] = "%$searchKeyword%";
}
$sqls .= " ORDER BY o.id DESC";

$stmt = $conn->prepare($sqls);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$res = $stmt;
$totalOrders = $res->rowCount();

// Get products for dropdown
$productStmt = $conn->query("SELECT id, name, price FROM tbl_products ORDER BY name");
$products = $productStmt->fetchAll();

// Save new order with multiple products
if (isset($_POST['save'])) {
    try {
        $conn->beginTransaction();
        
        // Insert into tbl_order
        $sql = "INSERT INTO tbl_order (customer_id, order_date, shipping_address, phone, total_amount, payment_method, status, note, created_at)
                VALUES (:customer_id, :order_date, :shipping_address, :phone, :total_amount, :payment_method, :status, :note, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':customer_id' => $_POST['customer_id'],
            ':order_date' => $_POST['order_date'],
            ':shipping_address' => $_POST['shipping_address'],
            ':phone' => $_POST['phone'],
            ':total_amount' => $_POST['total_amount'],
            ':payment_method' => $_POST['payment_method'],
            ':status' => $_POST['status'],
            ':note' => $_POST['note']
        ]);
        
        $order_id = $conn->lastInsertId();
        
        // Insert into order_items
        $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                    VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)";
        $itemStmt = $conn->prepare($itemSql);
        
        foreach ($_POST['products'] as $product) {
            if (!empty($product['product_id']) && !empty($product['quantity'])) {
                // Get product price
                $priceStmt = $conn->prepare("SELECT price, name FROM tbl_products WHERE id = :id");
                $priceStmt->execute([':id' => $product['product_id']]);
                $productData = $priceStmt->fetch();
                
                $subtotal = $productData['price'] * $product['quantity'];
                
                $itemStmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $product['product_id'],
                    ':quantity' => $product['quantity'],
                    ':unit_price' => $productData['price'],
                    ':subtotal' => $subtotal
                ]);
            }
        }
        
        $conn->commit();
        header("Location: order.php?success=Order added successfully");
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

// Delete order
if (isset($_GET['delete'])) {
    try {
        // First delete from order_items (foreign key constraint)
        $deleteItems = $conn->prepare("DELETE FROM order_items WHERE order_id = :id");
        $deleteItems->bindParam(':id', $_GET['delete']);
        $deleteItems->execute();
        
        // Then delete from tbl_order
        $deleteOrder = $conn->prepare("DELETE FROM tbl_order WHERE id = :id");
        $deleteOrder->bindParam(':id', $_GET['delete']);
        $deleteOrder->execute();
        
        header("Location: order.php?success=Order deleted successfully");
        exit();
    } catch (Exception $e) {
        $error = "Error deleting order";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f4f7fc; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        .bar { background-color: #00A296; }
        .menu li {
            background-color: transparent;
            border: none;
            width: 100%;
            transition: 0.3s linear;
            font-size: 1.2rem;
            list-style-type: none;
            padding: 5px 10px;
        }
        .menu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 15px;
            color: #e8e8e4;
            text-decoration: none;
            transition: 0.2s ease;
        }
        .menu li:hover a { transform: translateX(15px); }
        .menu li:hover { background-color: rgba(0,0,0,0.2); transform: translateX(11px); }
        .logo { display: flex; flex-direction: column; align-items: center; padding-top: 1.5rem; }
        .img { width: 90px; height: 90px; border-radius: 50%; border: 3px solid #f1f8e9; margin-bottom: 0.5rem; }
        .text-head { color: #FFF3E0; font-size: 1.3rem; font-weight: 600; text-align: center; }
        .logout { position: absolute; bottom: 30px; left: 25px; color: #FFE0B5; display: flex; align-items: center; gap: 12px; cursor: pointer; }
        .logout:hover { color: white; transform: translateX(5px); }
        .hader-dashoard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1rem 1.8rem;
            border-radius: 28px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .text-dashoard { color: #1E3A3A; font-weight: 700; font-size: 1.8rem; }
        .btn-outline-success.add { background: #00897B; color: white; border: none; padding: 8px 24px; border-radius: 40px; font-weight: 600; }
        .btn-outline-success.add:hover { background: #00695C; transform: translateY(-2px); }
        .search-section {
            background: white;
            border-radius: 60px;
            padding: 0.3rem 0.3rem 0.3rem 1.2rem;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .search-section input { border: none; outline: none; padding: 8px 12px; width: 240px; background: transparent; }
        .search-section button { background: #00897B; border: none; border-radius: 50px; padding: 6px 20px; color: white; font-weight: 500; }
        .product-counter { background: #eef2f5; padding: 8px 18px; border-radius: 40px; font-weight: 600; color: #1f5e56; display: inline-flex; align-items: center; gap: 8px; }
        .table-container { background: white; border-radius: 28px; overflow: hidden; border: 1px solid #e9ecef; }
        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .custom-table thead th {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #2c3e46;
            padding: 1rem 0.8rem;
            border-bottom: 2px solid #e2e8f0;
            background-color: #fefefe;
        }
        .custom-table tbody tr:hover { background: #f9fefc; }
        .custom-table td { padding: 1rem 0.8rem; vertical-align: middle; font-size: 0.9rem; }
        .status-paid { background: #E0F2E9; color: #2C6E49; padding: 5px 12px; border-radius: 40px; font-weight: 500; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px; }
        .status-unpaid { background: #FFF3E0; color: #E67E22; padding: 5px 12px; border-radius: 40px; font-weight: 500; font-size: 0.75rem; }
        .status-cancel { background: #FEE2E2; color: #DC2626; padding: 5px 12px; border-radius: 40px; font-weight: 500; font-size: 0.75rem; }
        .price-tag { font-weight: 700; color: #E67E22; font-size: 1rem; }
        .product-list { max-width: 300px; font-size: 0.85rem; }
        .badge-items { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 20px; font-size: 0.7rem; }
        .btn-edit { background: #F4A261; color: white; padding: 5px 14px; border-radius: 30px; font-size: 0.75rem; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-delete { background: #FFF2F0; color: #D9534F; border: 1px solid #FFD9D4; padding: 5px 14px; border-radius: 30px; font-size: 0.75rem; text-decoration: none; }
        .btn-view { background: #5BC0BE; color: white; padding: 5px 14px; border-radius: 30px; font-size: 0.75rem; border: none; margin-right: 5px; }
        .form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            background: white;
            border-radius: 32px;
            z-index: 1050;
            padding: 1.8rem;
            display: none;
        }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); z-index: 1040; display: none; }
        .product-row { background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 15px; position: relative; }
        .remove-product { position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; }
        .btn-add-product { background: #28a745; color: white; border: none; padding: 8px 20px; border-radius: 40px; margin-bottom: 15px; }
        .alert-success { position: fixed; top: 20px; right: 20px; z-index: 1100; animation: slideIn 0.5s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @media (max-width: 768px) { .form { width: 95%; } }
    </style>
</head>
<body>

<?php if(isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-2 bar vh-100 position-fixed">
            <div class="logo">
                <img class="img" src="https://i.pinimg.com/736x/e5/2a/e3/e52ae301a1162863df9a68c532dd3e2e.jpg" alt="admin">
                <h1 class="text-head"> Admin Dashboard </h1>
            </div>
            <ul class="menu list-group">
                <li><a href="index.php"><i class="fa-solid fa-house"></i>Main Dashboard</a></li>
                <li><a href="product.php"><i class="fa-solid fa-box"></i> Manage Menu</a></li>
                <li><a href="order.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
                <li><a href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                <li><a href="analytics.php"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                <li><a href="reprot.php"><i class="fa-solid fa-list"></i> Report</a></li>
                <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Setting</a></li>
            </ul>
            <div class="logout text-white">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> logout
            </div>
        </div>

        <!-- Main content -->
        <div class="col-10 offset-2 p-3 px-5">
            <div class="hader-dashoard">
                <h2 class="text-dashoard"><i class="fa-solid fa-cart-shopping me-2"></i>Order List</h2>
                <div class="stats-wrapper">
                    <div class="product-counter">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Total Orders: <strong><?= $totalOrders ?></strong></span>
                    </div>
                    <form method="GET" action="order.php" class="search-section">
                        <i class="fa-solid fa-magnifying-glass text-secondary"></i>
                        <input type="text" name="search" placeholder="Search orders..." value="<?= htmlspecialchars($searchKeyword) ?>">
                        <button type="submit"><i class="fa-solid fa-search"></i> Search</button>
                        <?php if (!empty($searchKeyword)): ?>
                            <a href="order.php" class="clear-search" style="color:#dc3545;"><i class="fa-solid fa-times-circle"></i></a>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="btn btn-outline-success add"><i class="fa-solid fa-plus me-1"></i> Add New Order</button>
                </div>
            </div>

            <!-- Add Order Form Popup -->
            <div class="form" id="productFormCard">
                <h4 class="mb-3"><i class="fa-solid fa-cart-plus"></i> New Order</h4>
                <form id="orderForm" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="number" name="customer_id" class="form-control mb-2" placeholder="Customer ID" required>
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="order_date" class="form-control mb-2" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <input type="text" name="shipping_address" class="form-control mb-2" placeholder="Shipping Address" required>
                        </div>
                        <div class="col-md-6">
                            <input type="tel" name="phone" class="form-control mb-2" placeholder="Phone Number" required>
                        </div>
                        <div class="col-md-6">
                            <select name="payment_method" class="form-select mb-2" required>
                                <option value="">Select Payment Method</option>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="ABA Pay">ABA Pay</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Container -->
                    <div id="products-container">
                        <div class="product-row">
                            <button type="button" class="remove-product btn btn-sm">×</button>
                            <select name="products[0][product_id]" class="form-select mb-2 product-select" required>
                                <option value="">Select Product</option>
                                <?php foreach($products as $product): ?>
                                <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                                    <?= htmlspecialchars($product['name']) ?> - $<?= number_format($product['price'], 2) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="products[0][quantity]" class="form-control quantity" placeholder="Quantity" min="1" required>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-more-product" class="btn-add-product">
                        <i class="fa-solid fa-plus"></i> Add Another Product
                    </button>

                    <div class="alert alert-info mt-2">
                        <strong>Total Amount: $<span id="total-amount-display">0.00</span></strong>
                        <input type="hidden" name="total_amount" id="total-amount-input">
                    </div>

                    <select name="status" class="form-select mb-2" required>
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="cancel">Cancel</option>
                    </select>

                    <textarea name="note" class="form-control mb-2" placeholder="Note (optional)" rows="2"></textarea>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary cancel">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="save">Save Order</button>
                    </div>
                </form>
            </div>
            <div class="overlay" id="overlayBg"></div>

            <!-- Orders Table -->
            <div class="table-container mt-4">
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer ID</th>
                                <th>Order Date</th>
                                <th>Products (Items)</th>
                                <th>Total Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalOrders > 0): ?>
                                <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="fw-semibold"><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['customer_id']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['order_date'])) ?></td>
                                    <td class="product-list">
                                        <span class="badge-items mb-1 d-inline-block"><?= $row['total_items'] ?> items</span>
                                        <small class="d-block text-muted"><?= htmlspecialchars(substr($row['product_list'] ?? '', 0, 60)) ?>...</small>
                                    </td>
                                    <td class="price-tag">$<?= number_format($row['total_amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'paid'): ?>
                                            <span class="status-paid"><i class="fa-solid fa-circle-check"></i> Paid</span>
                                        <?php elseif ($row['status'] == 'unpaid'): ?>
                                            <span class="status-unpaid"><i class="fa-regular fa-clock"></i> Unpaid</span>
                                        <?php else: ?>
                                            <span class="status-cancel"><i class="fa-solid fa-ban"></i> Cancel</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td>
                                        <button class="btn-view view-details" data-id="<?= $row['id'] ?>">
                                            <i class="fa-solid fa-eye"></i> View
                                        </button>
                                        <a href="edit_order.php?id=<?= $row['id'] ?>" class="btn-edit">
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </a>
                                        <a href="order.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this order?')">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fa-solid fa-cart-shopping fa-2x mb-2 d-block"></i> No orders found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Order Details #<span id="modal-order-id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="order-items-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
let productCounter = 1;

// Show/Hide Form
const btnAdd = document.querySelector(".add");
const formCard = document.getElementById("productFormCard");
const overlayDiv = document.getElementById("overlayBg");

function showForm() {
    formCard.style.display = "block";
    overlayDiv.style.display = "block";
    document.body.style.overflow = "hidden";
}

function hideForm() {
    formCard.style.display = "none";
    overlayDiv.style.display = "none";
    document.body.style.overflow = "auto";
}

if (btnAdd) btnAdd.addEventListener("click", showForm);
document.querySelectorAll(".cancel").forEach(btn => {
    btn.addEventListener("click", hideForm);
});
if (overlayDiv) overlayDiv.addEventListener("click", hideForm);

// Add more product row
document.getElementById('add-more-product').addEventListener('click', function() {
    const container = document.getElementById('products-container');
    const newRow = document.createElement('div');
    newRow.className = 'product-row';
    newRow.innerHTML = `
        <button type="button" class="remove-product btn btn-sm">×</button>
        <select name="products[${productCounter}][product_id]" class="form-select mb-2 product-select" required>
            <option value="">Select Product</option>
            <?php foreach($products as $product): ?>
            <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                <?= htmlspecialchars($product['name']) ?> - $<?= number_format($product['price'], 2) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div class="row">
            <div class="col-6">
                <input type="number" name="products[${productCounter}][quantity]" class="form-control quantity" placeholder="Quantity" min="1" required>
            </div>
            <div class="col-6">
                <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
            </div>
        </div>
    `;
    container.appendChild(newRow);
    attachProductEvents(newRow);
    productCounter++;
});

// Remove product row
function attachProductEvents(row) {
    const removeBtn = row.querySelector('.remove-product');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            row.remove();
            calculateTotal();
        });
    }
    
    const productSelect = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity');
    
    function updateSubtotal() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price) || 0 : 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const subtotal = price * quantity;
        const subtotalInput = row.querySelector('.subtotal');
        subtotalInput.value = subtotal.toFixed(2);
        calculateTotal();
    }
    
    productSelect.addEventListener('change', updateSubtotal);
    quantityInput.addEventListener('input', updateSubtotal);
}

// Calculate total amount
function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(function(input) {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('total-amount-display').innerText = total.toFixed(2);
    document.getElementById('total-amount-input').value = total.toFixed(2);
}

// Attach events to existing rows
document.querySelectorAll('.product-row').forEach(row => attachProductEvents(row));

// View order details via AJAX
$(document).ready(function() {
    $('.view-details').click(function() {
        const orderId = $(this).data('id');
        
        $.ajax({
            url: 'get_order_items.php',
            method: 'GET',
            data: { id: orderId },
            dataType: 'json',
            success: function(data) {
                let html = '<table class="table table-bordered table-hover">';
                html += '<thead class="table-dark"><tr>';
                html += '<th>Product Name</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th>';
                html += '<tr></thead><tbody>';
                
                if(data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        html += `<tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>$${parseFloat(item.subtotal).toFixed(2)}</td>
                        </tr>`;
                    });
                } else {
                    html += '57<tr><td colspan="4" class="text-center">No products found</td></tr>';
                }
                
                html += `</tbody><tfoot>
                    <tr class="table-info">
                        <td colspan="3"><strong>Total Amount:</strong></td>
                        <td><strong>$${parseFloat(data.total_amount).toFixed(2)}</strong></td>
                    </tr>
                </tfoot>`;
                html += '</table>';
                
                $('#order-items-list').html(html);
                $('#modal-order-id').text(orderId);
                $('#orderDetailModal').modal('show');
            },
            error: function() {
                alert('Error loading order details');
            }
        });
    });
    
    // Auto hide alert
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
});
</script>
</body>
</html>