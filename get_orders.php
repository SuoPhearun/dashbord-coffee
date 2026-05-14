<?php
// get_orders.php - ទាញទិន្នន័យពី tbl_order សម្រាប់ Orders Page
error_reporting(0);
include 'connection.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // ទាញទិន្នន័យ Order ទាំងអស់
    $query = "SELECT 
                id, 
                c_name, 
                item, 
                order_date, 
                total_amount, 
                
                status 
              FROM tbl_order 
              ORDER BY order_date DESC";
    
    $res = mysqli_query($conn, $query);
    
    if (!$res) {
        throw new Exception(mysqli_error($conn));
    }
    
    $orders = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // កែទម្រង់កាលបរិច្ឆេទ
    foreach ($orders as &$order) {
        $order['total_amount'] = (float)$order['total_amount'];
        // $order['quantity'] = (int)$order['quantity'];
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders)
    ]);
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>