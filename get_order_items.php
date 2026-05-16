<?php
include "connection.php";

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    // Get order total from tbl_order
    $orderStmt = $conn->prepare("SELECT total_amount FROM tbl_order WHERE id = ?");
    $orderStmt->execute([$orderId]);
    $order = $orderStmt->fetch();
    
    // Get order items with product names from order_items and tbl_products
    $itemStmt = $conn->prepare("
        SELECT oi.*, p.name as product_name 
        FROM order_items oi
        JOIN tbl_products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
        ORDER BY oi.id
    ");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll();
    
    echo json_encode([
        'total_amount' => $order ? $order['total_amount'] : 0,
        'items' => $items
    ]);
}
?>