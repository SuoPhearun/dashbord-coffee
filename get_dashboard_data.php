<?php
// បិទ Error Warning កុំឱ្យខូចទម្រង់ JSON
error_reporting(0);
include 'connection.php'; 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ពិនិត្យការភ្ជាប់ (ប្រើ $conn ពី connection.php របស់អ្នក)
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

try {
    $response = [];
    
    // ១. សរុបចំនួនផលិតផល
    $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_products");
    $response['totalProducts'] = mysqli_fetch_assoc($res)['total'];
    
    // ២. សរុបការលក់ និងចំណូលពី tbl_order
    $res = mysqli_query($conn, "SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as revenue FROM tbl_order");
    $orderStats = mysqli_fetch_assoc($res);
    $response['totalOrders'] = (int)$orderStats['total_orders'];
    $response['totalRevenue'] = (float)$orderStats['revenue'];
    $response['avgOrderValue'] = ($response['totalOrders'] > 0) ? round($response['totalRevenue'] / $response['totalOrders'], 2) : 0;
    
    // ៣. បញ្ជីផលិតផល (ទាញទាំងរូបភាព និងគណនាចំនួនលក់)
    $res = mysqli_query($conn, "
        SELECT p.id, p.name, p.category, p.price, p.s_qty as stock, p.image, p.description,
               COALESCE((SELECT COUNT(*) FROM tbl_order o WHERE o.item = p.name), 0) as sold_count 
        FROM tbl_products p
        ORDER BY p.id DESC
    ");
    $response['products'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // ៤. ការទិញថ្មីៗពី tbl_order (បង្ហាញព័ត៌មានលម្អិត)
    $res = mysqli_query($conn, "
        SELECT 
            id as order_id, 
            order_date, 
            total_amount as total, 
            c_name as customer_name, 
            item as product_name,
            quantity,
            status
        FROM tbl_order 
        ORDER BY order_date DESC 
        LIMIT 10
    ");
    $recentOrders = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // កែប្រែ quantity ប្រសិនបើមិនមានក្នុងតារាង
    foreach ($recentOrders as &$order) {
        if (!isset($order['quantity']) || $order['quantity'] <= 0) {
            $order['quantity'] = 1; // default quantity
        }
    }
    $response['recentOrders'] = $recentOrders;
    
    // ៥. ផលិតផលលក់ដាច់បំផុត (សម្រាប់ Bar Chart)
    $res = mysqli_query($conn, "
        SELECT 
            item as name, 
            COUNT(*) as sold,
            SUM(total_amount) as revenue
        FROM tbl_order 
        GROUP BY item 
        ORDER BY sold DESC 
        LIMIT 5
    ");
    $response['topProducts'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // ៦. លក់តាមចំណាត់ថ្នាក់ (Category)
    $res = mysqli_query($conn, "
        SELECT 
            p.category, 
            COUNT(o.id) as total_sold,
            COALESCE(SUM(o.total_amount), 0) as total_revenue
        FROM tbl_products p
        LEFT JOIN tbl_order o ON p.name = o.item
        GROUP BY p.category
        ORDER BY total_sold DESC
    ");
    $response['categorySales'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // ៧. ទំនិញជិតអស់ស្តុក (stock < 20)
    $res = mysqli_query($conn, "
        SELECT id, name, s_qty as stock, category, price 
        FROM tbl_products 
        WHERE s_qty < 20 
        ORDER BY s_qty ASC 
        LIMIT 8
    ");
    $response['lowStock'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // ៨. **បន្ថែមថ្មី:** Status Statistics សម្រាប់ Orders
    $res = mysqli_query($conn, "
        SELECT 
            status,
            COUNT(*) as count,
            COALESCE(SUM(total_amount), 0) as total_amount
        FROM tbl_order 
        GROUP BY status
    ");
    $response['orderStatusStats'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    // ៩. **បន្ថែមថ្មី:** Orders Trend (7 days)
    $res = mysqli_query($conn, "
        SELECT 
            DATE(order_date) as order_day,
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as daily_revenue
        FROM tbl_order 
        WHERE order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(order_date)
        ORDER BY order_day ASC
    ");
    $response['ordersTrend'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    
    echo json_encode($response, JSON_NUMERIC_CHECK);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

mysqli_close($conn);
?>