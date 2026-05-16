
<?php
// ========== CONNECTION ==========
include "connection.php"; // $conn គឺជា PDO object

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========== FETCH REPORTS ==========
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

$dateFrom = '';
$dateTo = '';
if (isset($_GET['date_from'])) {
    $dateFrom = trim($_GET['date_from']);
}
if (isset($_GET['date_to'])) {
    $dateTo = trim($_GET['date_to']);
}

$statusFilter = '';
if (isset($_GET['status'])) {
    $statusFilter = trim($_GET['status']);
}

$sql = "SELECT r.*, 
        o.order_date as order_date, 
        c.name as customer_name,
        p.name as product_name,
        p.price as product_price
        FROM tbl_report r
        LEFT JOIN tbl_order o ON r.Order_id = o.id
        LEFT JOIN tbl_customer c ON r.Customer_id = c.id
        LEFT JOIN tbl_products p ON r.Product_id = p.id
        WHERE 1=1";

$params = [];

if (!empty($searchKeyword)) {
    $sql .= " AND (c.name LIKE :search OR p.name LIKE :search OR r.Order_id LIKE :search)";
    $params[':search'] = "%$searchKeyword%";
}

if (!empty($dateFrom)) {
    $sql .= " AND r.Date >= :date_from";
    $params[':date_from'] = $dateFrom;
}

if (!empty($dateTo)) {
    $sql .= " AND r.Date <= :date_to";
    $params[':date_to'] = $dateTo . ' 23:59:59';
}

if (!empty($statusFilter)) {
    $sql .= " AND r.Status = :status";
    $params[':status'] = $statusFilter;
}

$sql .= " ORDER BY r.Date DESC, r.id DESC";

$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalReports = count($reports);

// Calculate totals
$totalQty = 0;
$totalAmount = 0;
foreach ($reports as $report) {
    $totalQty += $report['Qty'];
    $totalAmount += $report['Total'];
}

// ========== SAVE REPORT ==========
if (isset($_POST['save'])) {
    $order_id = $_POST['order_id'];
    $customer_id = $_POST['customer_id'];
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];
    $total = $_POST['total'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    
    $sql = "INSERT INTO tbl_report (Order_id, Customer_id, Product_id, Qty, Total, Date, Status)
            VALUES (:order_id, :customer_id, :product_id, :qty, :total, :date, :status)";
    
    $insertStmt = $conn->prepare($sql);
    $insertStmt->bindParam(':order_id', $order_id);
    $insertStmt->bindParam(':customer_id', $customer_id);
    $insertStmt->bindParam(':product_id', $product_id);
    $insertStmt->bindParam(':qty', $qty);
    $insertStmt->bindParam(':total', $total);
    $insertStmt->bindParam(':date', $date);
    $insertStmt->bindParam(':status', $status);
    
    if ($insertStmt->execute()) {
        header("Location: report.php");
        exit();
    } else {
        echo "Error: " . implode(", ", $insertStmt->errorInfo());
    }
}

// ========== DELETE REPORT ==========
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM tbl_report WHERE id = :id");
    $deleteStmt->bindParam(':id', $id);
    if ($deleteStmt->execute()) {
        header("Location: report.php");
        exit();
    }
}

// Fetch customers for dropdown
$customers = $conn->query("SELECT id, name FROM tbl_customer ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// Fetch products for dropdown
$products = $conn->query("SELECT id, name, price FROM tbl_products WHERE status = 'Publish' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// Fetch orders for dropdown
$orders = $conn->query("SELECT id, customer_id FROM tbl_order ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Management | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f4f7fc;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            overflow-x: hidden;
        }
        
        /* SIDEBAR STYLES */
        .bar {
            background-color: #00A296;
        }
        
        .menu {
            padding-left: 0;
            margin-top: 1rem;
        }
        
        .menu li {
            background-color: transparent;
            border: none;
            width: 100%;
            transition: 0.3s linear;
            font-size: 1.1rem;
            list-style-type: none;
            padding: 5px 10px;
        }
        
        .menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 15px;
            color: #e8e8e4;
            text-decoration: none;
            transition: 0.2s ease;
        }
        
        .menu li:hover a {
            transform: translateX(12px);
        }
        
        .menu li:hover {
            background-color: rgba(0, 0, 0, 0.2);
            transform: translateX(8px);
        }
        
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            padding-top: 1.5rem;
        }
        
        .img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f1f8e9;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2);
            margin-bottom: 0.5rem;
        }
        
        .text-head {
            color: #FFF3E0;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .logout {
            position: absolute;
            bottom: 30px;
            left: 25px;
            font-weight: 500;
            cursor: pointer;
            color: #FFE0B5;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logout:hover {
            color: white;
            transform: translateX(5px);
        }
        
        /* MAIN CONTENT */
        .hader-dashoard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1rem 1.8rem;
            border-radius: 28px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.02);
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .text-dashoard {
            color: #1E3A3A;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.3px;
        }
        
        .btn-add {
            background: #00897B;
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 40px;
            font-weight: 600;
            transition: 0.2s;
        }
        
        .btn-add:hover {
            background: #00695C;
            transform: translateY(-2px);
            color: white;
        }
        
        /* STATS CARDS */
        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 1.2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border-left: 4px solid #00897B;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            background: #e0f2f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #00897B;
        }
        
        .stats-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1E3A3A;
            margin-bottom: 0;
        }
        
        .stats-label {
            color: #6c8b8b;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* TABLE STYLES */
        .table-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #e9ecef;
             width: 100%;
            padding: 20px;
        }
               
        .custom-table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .custom-table thead th {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2c3e46;
            padding: 1rem;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .custom-table tbody tr:hover {
            background: #f9fefc;
        }
        
        .custom-table td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #1f2e38;
        }
        
        /* FILTER SECTION */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        
        .filter-input {
            border-radius: 12px;
            border: 1px solid #e0dfd5;
            padding: 1rem 1rem;
           
        }
        
        .status-completed {
            background: #E0F2E9;
            color: #2C6E49;
            padding: 5px 12px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-pending {
            background: #FFF3E0;
            color: #E67E22;
            padding: 5px 12px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-cancelled {
            background: #FEE2E2;
            color: #DC2626;
            padding: 5px 12px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-edit {
            background: #F4A261;
            border: none;
            color: white;
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: 0.2s;
            margin-right: 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit:hover {
            background: #E76F51;
            color: white;
        }
        
        .btn-delete {
            background: #FFF2F0;
            color: #D9534F;
            border: 1px solid #FFD9D4;
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
            display: inline-block;
        }
        
        .btn-delete:hover {
            background: #ffe3e0;
            color: #c9302c;
        }
        
        /* MODAL STYLES */
        .modal-content {
            border-radius: 24px;
            border: none;
        }
        
        .modal-header {
            background: #00897B;
            color: white;
            border-radius: 24px 24px 0 0;
            padding: 1rem 1.5rem;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #e0dfd5;
            padding: 0.6rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #00897B;
            box-shadow: 0 0 0 0.2rem rgba(0,137,123,0.25);
        }
        
        .btn-primary {
            background: #00897B;
            border: none;
            border-radius: 40px;
            padding: 8px 24px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #00695C;
        }
        
        .btn-secondary {
            border-radius: 40px;
            background: #f1f3f4;
            color: #2c3e46;
            border: none;
        }
        
        .export-btn {
            background: #2C7DA0;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
            transition: 0.2s;
        }
        
        .export-btn:hover {
            background: #1F5E7A;
            color: white;
        }
        
        @media (max-width: 992px) {
            .col-2 {
                position: relative !important;
                width: 100% !important;
                height: auto !important;
            }
            .offset-2 {
                margin-left: 0 !important;
            }
            .logout {
                position: relative;
                margin-top: 20px;
                bottom: 0;
                left: 0;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-2 bar vh-100 position-fixed">
            <div class="logo">
                <img class="img" src="https://i.pinimg.com/736x/e5/2a/e3/e52ae301a1162863df9a68c532dd3e2e.jpg" alt="admin">
                <h1 class="text-head">Admin Dashboard</h1>
            </div>
            <ul class="menu list-unstyled">
                <li><a href="index.php"><i class="fa-solid fa-house"></i>Main Dashboard</a></li>
                <li><a href="product.php"><i class="fa-solid fa-box"></i> Manage Menu</a></li>
                <li><a href="order.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
                <li><a href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                <li><a href="report.php"><i class="fa-solid fa-chart-area"></i> Report</a></li>
                <li><a href="analytics.php"><i class="fa-solid fa-list"></i> Analytics</a></li>
                <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Setting</a></li>
            </ul>
            <div class="logout text-white">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> logout
            </div>
        </div>

        <!-- Main content -->
        <div class="col-10 offset-2 p-3 px-5">
            <div class="hader-dashoard">
                <h2 class="text-dashoard"><i class="fa-solid fa-chart-line me-2"></i>Report Management</h2>
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addReportModal">
                    <i class="fa-solid fa-plus me-2"></i>Add New Report
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stats-card d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number"><?= $totalReports ?></div>
                            <div class="stats-label">Total Reports</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fa-solid fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number"><?= $totalQty ?></div>
                            <div class="stats-label">Total Quantity</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fa-solid fa-boxes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">$<?= number_format($totalAmount, 2) ?></div>
                            <div class="stats-label">Total Revenue</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number"><?= date('d/m/Y') ?></div>
                            <div class="stats-label">Today's Date</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="fa-solid fa-search"></i> Search</label>
                        <input type="text" name="search" class="form-control filter-input" placeholder="Order ID, Customer, Product..." value="<?= htmlspecialchars($searchKeyword) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fa-regular fa-calendar"></i> Date From</label>
                        <input type="date" name="date_from" class="form-control filter-input" value="<?= htmlspecialchars($dateFrom) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fa-regular fa-calendar"></i> Date To</label>
                        <input type="date" name="date_to" class="form-control filter-input" value="<?= htmlspecialchars($dateTo) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fa-solid fa-filter"></i> Status</label>
                        <select name="status" class="form-select filter-input">
                            <option value="">All Status</option>
                            <option value="Completed" <?= $statusFilter == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Pending" <?= $statusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Cancelled" <?= $statusFilter == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter"></i> Apply Filter
                        </button>
                        <a href="report.php" class="btn btn-secondary w-100">
                            <i class="fa-solid fa-rotate-right"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Report Table -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="custom-table" id="reportTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalReports > 0): ?>
                                <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['id']) ?></td>
                                    <td>#<?= htmlspecialchars($report['Order_id']) ?></td>
                                    <td><?= htmlspecialchars($report['customer_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($report['product_name'] ?? 'N/A') ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($report['Qty']) ?></td>
                                    <td>$<?= number_format($report['product_price'] ?? 0, 2) ?></td>
                                    <td class="price-tag">$<?= number_format($report['Total'], 2) ?></td>
                                    <td><?= date('d/m/Y', strtotime($report['Date'])) ?></td>
                                    <td>
                                        <?php if ($report['Status'] == 'Completed'): ?>
                                            <span class="status-completed"><i class="fa-solid fa-circle-check"></i> Completed</span>
                                        <?php elseif ($report['Status'] == 'Pending'): ?>
                                            <span class="status-pending"><i class="fa-regular fa-clock"></i> Pending</span>
                                        <?php else: ?>
                                            <span class="status-cancelled"><i class="fa-solid fa-ban"></i> Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_report.php?id=<?= $report['id'] ?>" class="btn-edit">
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </a>
                                        <a href="?delete=<?= $report['id'] ?>" class="btn-delete" onclick="return confirm('Delete this report?')">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-chart-line fa-2x mb-2 d-block"></i> No reports found.
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

<!-- Add Report Modal -->
<div class="modal fade" id="addReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Add New Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Order ID <span class="text-danger">*</span></label>
                            <select name="order_id" class="form-select" required>
                                <option value="">Select Order</option>
                                <?php foreach ($orders as $order): ?>
                                <option value="<?= $order['id'] ?>">#<?= $order['id'] ?> - Customer ID: <?= $order['customer_id'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select" id="productSelect" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                                    <?= htmlspecialchars($product['name']) ?> - $<?= number_format($product['price'], 2) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty" id="qty" class="form-control" placeholder="Enter quantity" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Amount</label>
                            <input type="text" id="total" class="form-control" readonly style="background: #f5f5f5; font-weight: bold;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <input type="hidden" name="total" id="totalHidden">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save" class="btn btn-primary">Save Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-calculate total when product or quantity changes
    const productSelect = document.getElementById('productSelect');
    const qtyInput = document.getElementById('qty');
    const totalField = document.getElementById('total');
    const totalHidden = document.getElementById('totalHidden');

    function calculateTotal() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
        const qty = parseInt(qtyInput.value) || 0;
        const total = price * qty;
        
        if (!isNaN(total) && total > 0) {
            totalField.value = '$' + total.toFixed(2);
            totalHidden.value = total.toFixed(2);
        } else {
            totalField.value = '';
            totalHidden.value = '';
        }
    }

    if (productSelect) productSelect.addEventListener('change', calculateTotal);
    if (qtyInput) qtyInput.addEventListener('keyup', calculateTotal);
    
    // Initialize DataTable
    $(document).ready(function() {
        $('#reportTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "previous": "<",
                    "next": ">"
                }
            },
            "order": [[0, 'desc']]
        });
    });
</script>
</body>
</html>