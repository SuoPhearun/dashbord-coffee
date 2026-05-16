<?php
include "connection.php";

if (!isset($_GET['id'])) {
    header("Location: order.php");
    exit();
}

$orderId = $_GET['id'];

// Get order details
$orderStmt = $conn->prepare("SELECT * FROM tbl_order WHERE id = ?");
$orderStmt->execute([$orderId]);
$order = $orderStmt->fetch();

if (!$order) {
    header("Location: order.php");
    exit();
}

// Get order items
$itemStmt = $conn->prepare("
    SELECT oi.*, p.name as product_name 
    FROM order_items oi
    JOIN tbl_products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

// Get all products
$products = $conn->query("SELECT id, name, price FROM tbl_products ORDER BY name")->fetchAll();

// Update order
if (isset($_POST['update'])) {
    try {
        $conn->beginTransaction();
        
        // Update tbl_order
        $updateOrder = $conn->prepare("UPDATE tbl_order SET 
            customer_id = :customer_id,
            order_date = :order_date,
            shipping_address = :shipping_address,
            phone = :phone,
            total_amount = :total_amount,
            payment_method = :payment_method,
            status = :status,
            note = :note
            WHERE id = :id");
        
        $updateOrder->execute([
            ':customer_id' => $_POST['customer_id'],
            ':order_date' => $_POST['order_date'],
            ':shipping_address' => $_POST['shipping_address'],
            ':phone' => $_POST['phone'],
            ':total_amount' => $_POST['total_amount'],
            ':payment_method' => $_POST['payment_method'],
            ':status' => $_POST['status'],
            ':note' => $_POST['note'],
            ':id' => $orderId
        ]);
        
        // Delete old order items
        $deleteItems = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $deleteItems->execute([$orderId]);
        
        // Insert new order items
        $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                    VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)";
        $itemStmt = $conn->prepare($itemSql);
        
        foreach ($_POST['products'] as $product) {
            if (!empty($product['product_id']) && !empty($product['quantity'])) {
                $priceStmt = $conn->prepare("SELECT price FROM tbl_products WHERE id = ?");
                $priceStmt->execute([$product['product_id']]);
                $price = $priceStmt->fetchColumn();
                
                $subtotal = $price * $product['quantity'];
                
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $product['product_id'],
                    ':quantity' => $product['quantity'],
                    ':unit_price' => $price,
                    ':subtotal' => $subtotal
                ]);
            }
        }
        
        $conn->commit();
        header("Location: order.php?success=Order updated successfully");
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .product-row { background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 15px; position: relative; }
        .remove-product { position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; }
        .btn-add-product { background: #28a745; color: white; border: none; padding: 8px 20px; border-radius: 40px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fa-solid fa-edit"></i> Edit Order #<?= $orderId ?></h4>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Customer ID</label>
                                <input type="number" name="customer_id" class="form-control" value="<?= $order['customer_id'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label>Order Date</label>
                                <input type="date" name="order_date" class="form-control" value="<?= $order['order_date'] ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label>Shipping Address</label>
                                <input type="text" name="shipping_address" class="form-control" value="<?= htmlspecialchars($order['shipping_address']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= $order['phone'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="Cash" <?= $order['payment_method'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                    <option value="Credit Card" <?= $order['payment_method'] == 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
                                    <option value="Bank Transfer" <?= $order['payment_method'] == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                    <option value="ABA Pay" <?= $order['payment_method'] == 'ABA Pay' ? 'selected' : '' ?>>ABA Pay</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5><i class="fa-solid fa-box"></i> Products</h5>
                        <div id="products-container">
                            <?php foreach($items as $index => $item): ?>
                            <div class="product-row">
                                <button type="button" class="remove-product btn btn-sm">×</button>
                                <select name="products[<?= $index ?>][product_id]" class="form-select mb-2 product-select" required>
                                    <option value="">Select Product</option>
                                    <?php foreach($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>" <?= $item['product_id'] == $product['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($product['name']) ?> - $<?= number_format($product['price'], 2) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" name="products[<?= $index ?>][quantity]" class="form-control quantity" placeholder="Quantity" min="1" value="<?= $item['quantity'] ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control subtotal" placeholder="Subtotal" value="$<?= number_format($item['subtotal'], 2) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" id="add-more-product" class="btn-add-product">
                            <i class="fa-solid fa-plus"></i> Add Another Product
                        </button>

                        <div class="alert alert-info mt-2">
                            <strong>Total Amount: $<span id="total-amount-display"><?= number_format($order['total_amount'], 2) ?></span></strong>
                            <input type="hidden" name="total_amount" id="total-amount-input" value="<?= $order['total_amount'] ?>">
                        </div>

                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="paid" <?= $order['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="unpaid" <?= $order['status'] == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                <option value="cancel" <?= $order['status'] == 'cancel' ? 'selected' : '' ?>>Cancel</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Note</label>
                            <textarea name="note" class="form-control" rows="2"><?= htmlspecialchars($order['note']) ?></textarea>
                        </div>

                        <button type="submit" name="update" class="btn btn-primary">Update Order</button>
                        <a href="order.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let productCounter = <?= count($items) ?>;

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
        subtotalInput.value = `$${subtotal.toFixed(2)}`;
        calculateTotal();
    }
    
    productSelect.addEventListener('change', updateSubtotal);
    quantityInput.addEventListener('input', updateSubtotal);
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(function(input) {
        let value = input.value.replace('$', '');
        total += parseFloat(value) || 0;
    });
    document.getElementById('total-amount-display').innerText = total.toFixed(2);
    document.getElementById('total-amount-input').value = total.toFixed(2);
}

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

// Attach events to existing rows
document.querySelectorAll('.product-row').forEach(row => attachProductEvents(row));
</script>
</body>
</html>