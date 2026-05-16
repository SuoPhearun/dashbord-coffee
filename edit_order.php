<?php
include "connection.php"; // must contain PDO $conn

// =====================
// 1. GET DATA BY ID
// =====================
if (!isset($_GET['id'])) {
    header("Location: order.php");
    exit();
}

$id = $_GET['id'];

// Fetch order
$stmt = $conn->prepare("SELECT * FROM tbl_order WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "Order not found!";
    exit();
}

// =====================
// 2. UPDATE DATA
// =====================
if (isset($_POST['update'])) {
    $customer_id = $_POST['customer_id'];
    $order_date  = $_POST['order_date'];
    $total_amount  = $_POST['total_amount'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];
    $shipping_address = $_POST['shipping_address'];
    $phone = $_POST['phone'];
    $note = $_POST['note'];
    $created_at = $_POST['created_at'];
    
    // Corrected UPDATE query for tbl_order
    $stmt = $conn->prepare("
        UPDATE tbl_order SET 
             customer_id = ?, 
             order_date = ?, 
             total_amount = ?, 
             payment_method = ?, 
             status = ?, 
             shipping_address = ?, 
             phone = ?, 
             note = ?, 
             created_at = ?
        WHERE id = ?
    ");

    $updated = $stmt->execute([
        $customer_id, $order_date, $total_amount, $payment_method, 
        $status, $shipping_address, $phone, $note, $created_at, $id
    ]);

    if ($updated) {
        echo "<script>
            alert('✔️ Order updated successfully!');
            window.location.href='order.php';
        </script>";
    } else {
        echo "<script>alert('❌ Update failed!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Order</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
<div class="card shadow">

<div class="card-header bg-primary text-white">
    <h4>Edit Order</h4>
</div>

<div class="card-body">

<form method="POST">

    <!-- CUSTOMER ID -->
    <label>Customer ID</label>
    <input type="number" name="customer_id" class="form-control mb-3"
        value="<?= htmlspecialchars($row['customer_id']) ?>" required>

    <!-- ORDER DATE -->
    <label>Order Date</label>
    <input type="datetime-local" name="order_date" class="form-control mb-3"
        value="<?= date('Y-m-d\TH:i', strtotime($row['order_date'])) ?>" required>

    <!-- TOTAL AMOUNT -->
    <label>Total Amount ($)</label>
    <input type="number" step="0.01" name="total_amount" class="form-control mb-3"
        value="<?= htmlspecialchars($row['total_amount']) ?>" required>

    <!-- PAYMENT METHOD -->
    <label>Payment Method</label>
    <select name="payment_method" class="form-select mb-3">
        <option value="Cash" <?= ($row['payment_method'] == "Cash") ? "selected" : "" ?>>Cash</option>
        <option value="Credit Card" <?= ($row['payment_method'] == "Credit Card") ? "selected" : "" ?>>Credit Card</option>
        <option value="Bank Transfer" <?= ($row['payment_method'] == "Bank Transfer") ? "selected" : "" ?>>Bank Transfer</option>
        <option value="PayPal" <?= ($row['payment_method'] == "PayPal") ? "selected" : "" ?>>PayPal</option>
    </select>

    <!-- STATUS -->
    <label>Status</label>
    <select name="status" class="form-select mb-3">
        <option value="Pending" <?= ($row['status'] == "Pending") ? "selected" : "" ?>>Pending</option>
        <option value="Processing" <?= ($row['status'] == "Processing") ? "selected" : "" ?>>Processing</option>
        <option value="Shipped" <?= ($row['status'] == "Shipped") ? "selected" : "" ?>>Shipped</option>
        <option value="Delivered" <?= ($row['status'] == "Delivered") ? "selected" : "" ?>>Delivered</option>
        <option value="Cancelled" <?= ($row['status'] == "Cancelled") ? "selected" : "" ?>>Cancelled</option>
    </select>

    <!-- SHIPPING ADDRESS -->
    <label>Shipping Address</label>
    <textarea name="shipping_address" class="form-control mb-3" rows="3" required><?= htmlspecialchars($row['shipping_address']) ?></textarea>

    <!-- PHONE -->
    <label>Phone</label>
    <input type="text" name="phone" class="form-control mb-3"
        value="<?= htmlspecialchars($row['phone']) ?>" required>

    <!-- NOTE -->
    <label>Note</label>
    <textarea name="note" class="form-control mb-3" rows="2"><?= htmlspecialchars($row['note']) ?></textarea>

    <!-- CREATED AT -->
    <label>Created Date</label>
    <input type="datetime-local" name="created_at" class="form-control mb-3"
        value="<?= date('Y-m-d\TH:i', strtotime($row['created_at'])) ?>" required>

    <!-- BUTTONS -->
    <button type="submit" name="update" class="btn btn-success">Update Order</button>
    <a href="order.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>
</div>

</body>
</html>