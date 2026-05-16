<?php

        // ========== CONNECTION ==========
        include "connection.php"; // $conn គឺជា PDO object
               // ការពារ user ប្តូរ link
        if(!isset($_SESSION['id'])){
            header("Location: login.php");
            exit();
        }
        
        
        // ========== JSON RESPONSE FOR STATS (MUST BE BEFORE ANY HTML OUTPUT) ==========
        if (isset($_GET['fetch_stats']) && $_GET['fetch_stats'] == '1') {
            header('Content-Type: application/json');
            
            try {
                $stats = [
                    'success' => true,
                    'total_products' => 0,
                    'total_orders' => 0,
                    'paid_orders' => 0,
                    'unpaid_orders' => 0,
                    'coffee_total' => 0,
                    'hot_coffee' => 0,
                    'cold_coffee' => 0,
                    'matcha_total' => 0,
                    'hot_matcha' => 0,
                    'cold_matcha' => 0,
                    'tea_total' => 0,
                    'hot_tea' => 0,
                    'cold_tea' => 0,
                    'Snack_total' => 0,
                    'hot_Snack' => 0,
                    'cold_Snack' => 0
                ];
                
                // Total products (skip null rows)
                $totalQuery = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE name IS NOT NULL AND name != ''");
                $totalRow = $totalQuery->fetch(PDO::FETCH_ASSOC);
                $stats['total_products'] = (int)$totalRow['total'];
                
                // Total orders
                $totalOrderQuery = $conn->query("SELECT COUNT(*) as total FROM tbl_order WHERE customer_id IS NOT NULL");
                $totalOrderRow = $totalOrderQuery->fetch(PDO::FETCH_ASSOC);
                $stats['total_orders'] = (int)$totalOrderRow['total'];
        
                // Paid orders - FIXED: removed incorrect 'name' condition
                $paidOrders = $conn->query("SELECT COUNT(*) as total FROM tbl_order WHERE LOWER(status) = 'paid'");
                $stats['paid_orders'] = (int)$paidOrders->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Unpaid orders - FIXED: corrected spelling 'unpaid'
                $unpaidOrders = $conn->query("SELECT COUNT(*) as total FROM tbl_order WHERE LOWER(status) = 'unpaid'");
                $stats['unpaid_orders'] = (int)$unpaidOrders->fetch(PDO::FETCH_ASSOC)['total'];
                
                // === COFFEE ===
                $coffeeTotal = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Coffee' AND name IS NOT NULL");
                $stats['coffee_total'] = (int)$coffeeTotal->fetch(PDO::FETCH_ASSOC)['total'];
                
                $hotCoffee = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Coffee' AND type = 'Hot' AND name IS NOT NULL");
                $stats['hot_coffee'] = (int)$hotCoffee->fetch(PDO::FETCH_ASSOC)['total'];
                
                $coldCoffee = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Coffee' AND (type = 'cold' OR type = 'Cold') AND name IS NOT NULL");
                $stats['cold_coffee'] = (int)$coldCoffee->fetch(PDO::FETCH_ASSOC)['total'];
                
                // === MATCHA ===
                $matchaTotal = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Matcha' AND name IS NOT NULL");
                $stats['matcha_total'] = (int)$matchaTotal->fetch(PDO::FETCH_ASSOC)['total'];
                
                $hotMatcha = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Matcha' AND type = 'Hot' AND name IS NOT NULL");
                $stats['hot_matcha'] = (int)$hotMatcha->fetch(PDO::FETCH_ASSOC)['total'];
                
                $coldMatcha = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Matcha' AND (type = 'cold' OR type = 'Cold') AND name IS NOT NULL");
                $stats['cold_matcha'] = (int)$coldMatcha->fetch(PDO::FETCH_ASSOC)['total'];
                
                // === TEA ===
                $teaTotal = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Tea' AND name IS NOT NULL");
                $stats['tea_total'] = (int)$teaTotal->fetch(PDO::FETCH_ASSOC)['total'];
                
                $hotTea = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Tea' AND type = 'Hot' AND name IS NOT NULL");
                $stats['hot_tea'] = (int)$hotTea->fetch(PDO::FETCH_ASSOC)['total'];
                
                $coldTea = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Tea' AND (type = 'cold' OR type = 'Cold') AND name IS NOT NULL");
                $stats['cold_tea'] = (int)$coldTea->fetch(PDO::FETCH_ASSOC)['total'];
                
                // === SNACK ===
                $snackTotal = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Snack' AND name IS NOT NULL");
                $stats['Snack_total'] = (int)$snackTotal->fetch(PDO::FETCH_ASSOC)['total'];
                
                $hotSnack = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Snack' AND type = 'Hot' AND name IS NOT NULL");
                $stats['hot_Snack'] = (int)$hotSnack->fetch(PDO::FETCH_ASSOC)['total'];
                
                $coldSnack = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Snack' AND (type = 'cold' OR type = 'Cold') AND name IS NOT NULL");
                $stats['cold_Snack'] = (int)$coldSnack->fetch(PDO::FETCH_ASSOC)['total'];
                
                echo json_encode($stats);
                
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
        
        // ========== SAVE PRODUCT ==========
        if (isset($_POST['save'])) {
            $productName = $_POST['p-name'];
            $type = $_POST['type'];
            $category = $_POST['category'];
            $price = $_POST['price'];
            $create_by = $_POST['create_by'];
            $create_at = $_POST['create_at'];
            $status = $_POST['status'];
            $image = $_POST['image'];
            
            $sql = "INSERT INTO tbl_products (name, type, category, price, created_by, create_at, status, image)
                    VALUES (:name, :type, :category, :price, :create_by, :create_at, :status, :image)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $productName);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':create_by', $create_by);
            $stmt->bindParam(':create_at', $create_at);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':image', $image);
            
            if ($stmt->execute()) {
                header("Location: product.php");
                exit();
            } else {
                echo "Error: " . implode(", ", $stmt->errorInfo());
            }
        }
        
        // ========== FETCH PRODUCTS ==========
        $searchKeyword = '';
        if (isset($_GET['search'])) {
            $searchKeyword = trim($_GET['search']);
        }
        
        $sqls = "SELECT * FROM tbl_products WHERE name IS NOT NULL AND name != ''";
        if (!empty($searchKeyword)) {
            $searchKeyword = "%$searchKeyword%";
            $sqls .= " AND (name LIKE :search OR category LIKE :search OR type LIKE :search)";
        }
        $sqls .= " ORDER BY id DESC";
        
        $stmt = $conn->prepare($sqls);
        if (!empty($searchKeyword)) {
            $stmt->bindParam(':search', $searchKeyword);
        }
        $stmt->execute();
        $res = $stmt;
        $totalProducts = $res->rowCount();
        
        // ========== FETCH ORDERS ==========
        $orderSearchKeyword = '';
        if (isset($_GET['order_search'])) {
            $orderSearchKeyword = trim($_GET['order_search']);
        }
        
        $orderSql = "SELECT * FROM tbl_order WHERE 1=1";
        if (!empty($orderSearchKeyword)) {
            $orderSearchKeyword = "%$orderSearchKeyword%";
            $orderSql .= " AND (customer_id LIKE :search OR payment_method LIKE :search OR status LIKE :search OR phone LIKE :search)";
        }
        $orderSql .= " ORDER BY id DESC";
        
        $orderStmt = $conn->prepare($orderSql);
        if (!empty($orderSearchKeyword)) {
            $orderStmt->bindParam(':search', $orderSearchKeyword);
        }
        $orderStmt->execute();
        $orderRes = $orderStmt;
        $totalOrders = $orderRes->rowCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Main Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ========== RESET & BASE ========== */
        a{
             text-decoration: none;
             
        }
        .logout  i {
            /* color:#eee; */
            color: #e8e8e4;
        }
        .logout  a {
            /* color:#eee; */
            color: #e8e8e4;
        }
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

        /* ========== SIDEBAR STYLES ========== */
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

        /* ========== MAIN HEADER ========== */
        .hader-dashoard {
            background: white;
            padding: 1rem 1.8rem;
            border-radius: 28px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.02);
            margin-bottom: 30px;
        }

        .text-dashoard {
            color: #1E3A3A;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.3px;
            margin: 0;
        }

        /* ========== KPI CARDS STYLES ========== */
        .kpi-card {
            background: white;
            border-radius: 28px;
            padding: 1.5rem 1.2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
            border: 1px solid rgba(0, 130, 120, 0.08);
            margin-bottom: 1.5rem;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.06);
        }

        .kpi-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #6c8b8b;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kpi-number {
            font-size: 2.4rem;
            font-weight: 800;
            color: #00897B;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        .divider-light {
            height: 2px;
            background: linear-gradient(90deg, #e0eceb, transparent);
            margin: 1rem 0;
        }

        .category-stats {
            margin-top: 0.8rem;
        }

        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #edf2f2;
            font-size: 0.9rem;
        }

        .category-item:last-child {
            border-bottom: none;
        }

        .category-name {
            color: #2c5a55;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-count {
            background: #eef5f4;
            padding: 4px 12px;
            border-radius: 40px;
            font-weight: 700;
            color: #00897B;
            font-size: 0.85rem;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background: #e0f2f0;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #00897B;
            font-size: 1.6rem;
            margin-bottom: 1rem;
        }

        .overview-card .kpi-number {
            font-size: 2.8rem;
        }
        
        /* TABLE STYLES */
        .table-container {
            background: white;
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #e9ecef;
            margin-top: 2rem;
        }
        
        .custom-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .custom-table thead th {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #2c3e46;
            padding: 1rem 0.8rem;
            border-bottom: 2px solid #e2e8f0;
            background-color: #fefefe;
        }
        
        .custom-table tbody tr:hover {
            background: #f9fefc;
        }
        
        .custom-table td {
            padding: 1rem 0.8rem;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #1f2e38;
        }
        
        .badge-hot {
            background: #FFECE5;
            color: #E65C2E;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .badge-cold {
            background: #E6F4FF;
            color: #2C7DA0;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-publish {
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
        
        .status-draft {
            background: #F1F3F5;
            color: #6C757D;
            padding: 5px 12px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .img-product {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 14px;
            background: #faf3e0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border: 1px solid #ede7dc;
        }
        
        .price-tag {
            font-weight: 700;
            color: #E67E22;
            font-size: 1rem;
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
            margin-right: 8px;
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
        
        .checkbox-custom {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #00897B;
        }
        
        .table-responsive-custom {
            overflow-x: auto;
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
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1E3A3A;
            margin: 1.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #00897B;
            display: inline-block;
        }
        
        .search-section {
            background: white;
            border-radius: 60px;
            padding: 0.3rem 0.3rem 0.3rem 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .search-section input {
            border: none;
            outline: none;
            padding: 8px 12px;
            width: 240px;
            font-size: 0.9rem;
            background: transparent;
        }
        
        .search-section button {
            background: #00897B;
            border: none;
            border-radius: 50px;
            padding: 6px 20px;
            color: white;
            font-weight: 500;
            transition: 0.2s;
        }
        
        .search-section button:hover {
            background: #00695C;
        }
    </style>
</head>
<body>
    <script>

      if(sessionStorage.getItem('login') == null){
          window.location='login.php';
      }

</script>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-2 bar vh-100 position-fixed">
                <div class="logo">
                    <img class="img" src="https://i.pinimg.com/736x/e5/2a/e3/e52ae301a1162863df9a68c532dd3e2e.jpg" alt="Admin">
                    <h1 class="text-head">Admin Dashboard</h1>
                </div>
                <ul class="menu list-unstyled">
                    <li><a href="#"><i class="fa-solid fa-house"></i> Main Dashboard</a></li>
                    <li><a href="product.php"><i class="fa-solid fa-box"></i> Manage Menu</a></li>
                    <li><a href="order.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
                    <li><a href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                    <li><a href="analytics.php"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                    <li><a href="reprot.php"><i class="fa-solid fa-list"></i> Report</a></li>
                    <li><a href="settings.php"><i class="fa-solid fa-gear"></i> Setting</a></li>
                </ul>
                <div class="logout">
                    <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> logout
                </a></div>
            </div>

            <!-- Main Content -->
            <div class="col-10 offset-2 p-4">
                <div class="hader-dashoard">
                    <h2 class="text-dashoard"><i class="fa-solid fa-chart-simple me-2"></i> Main Dashboard</h2>
                </div>

                <!-- KPI Statistics Cards -->
                <div class="container-fluid mt-2">
                    <div class="row g-4">
                        <!-- Total Products Card -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="kpi-card overview-card">
                                <div class="card-icon">
                                    <i class="fa-solid fa-cubes"></i>
                                </div>
                                <div class="kpi-title">
                                    <i class="fa-regular fa-chart-bar"></i> TOTAL PRODUCTS
                                </div>
                                <div class="kpi-number" id="totalProductsCount">--</div>
                                <div class="divider-light"></div>
                                <div class="small text-muted">
                                    <i class="fa-regular fa-clock me-1"></i> All active items
                                </div>
                            </div>
                        </div>

                        <!-- Total Orders Card -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="kpi-card">
                                <div class="card-icon" style="background:#e3f3e8; color:#2F6B47;">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                                <div class="kpi-title">
                                    <i class="fa-solid fa-chart-line"></i> TOTAL ORDERS
                                </div>
                                <div class="kpi-number" id="totalOrdersCount">0</div>
                                <div class="category-stats">
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-circle-check"></i> Paid</span>
                                        <span class="category-count" id="paidOrdersCount">0</span>
                                    </div>
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-clock"></i> Unpaid</span>
                                        <span class="category-count" id="unpaidOrdersCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coffee Card -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="kpi-card">
                                <div class="card-icon" style="background:#fef2e0; color:#B95C1E;">
                                    <i class="fa-solid fa-mug-hot"></i>
                                </div>
                                <div class="kpi-title">
                                    <i class="fa-solid fa-fire"></i> COFFEE
                                </div>
                                <div class="kpi-number" id="coffeeCount">0</div>
                                <div class="category-stats">
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-circle-check"></i> Hot Coffee</span>
                                        <span class="category-count" id="hotCoffeeCount">0</span>
                                    </div>
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-snowflake"></i> Cold Coffee</span>
                                        <span class="category-count" id="coldCoffeeCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Matcha Card -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="kpi-card">
                                <div class="card-icon" style="background:#e3f3e8; color:#2F6B47;">
                                    <i class="fa-solid fa-leaf"></i>
                                </div>
                                <div class="kpi-title">
                                    <i class="fa-solid fa-seedling"></i> MATCHA
                                </div>
                                <div class="kpi-number" id="matchaCount">0</div>
                                <div class="category-stats">
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-circle-check"></i> Hot Matcha</span>
                                        <span class="category-count" id="hotMatchaCount">0</span>
                                    </div>
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-snowflake"></i> Cold Matcha</span>
                                        <span class="category-count" id="coldMatchaCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tea Card -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="kpi-card">
                                <div class="card-icon" style="background:#e8f0e7; color:#528265;">
                                    <i class="fa-solid fa-mug-saucer"></i>
                                </div>
                                <div class="kpi-title">
                                    <i class="fa-solid fa-tree"></i> TEA
                                </div>
                                <div class="kpi-number" id="teaCount">0</div>
                                <div class="category-stats">
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-circle-check"></i> Hot Tea</span>
                                        <span class="category-count" id="hotTeaCount">0</span>
                                    </div>
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-snowflake"></i> Cold Tea</span>
                                        <span class="category-count" id="coldTeaCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Snack Card -->
                        <div class="col-sm-6 col-xl-3">
                            <div class="kpi-card">
                                <div class="card-icon" style="background:#fef2e0; color:#B95C1E;">
                                    <i class="fa-solid fa-cookie-bite"></i>
                                </div>
                                <div class="kpi-title">
                                    <i class="fa-solid fa-fire"></i> SNACK
                                </div>
                                <div class="kpi-number" id="snackCount">0</div>
                                <div class="category-stats">
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-circle-check"></i> Hot Snack</span>
                                        <span class="category-count" id="hotsnackCount">0</span>
                                    </div>
                                    <div class="category-item">
                                        <span class="category-name"><i class="fa-regular fa-snowflake"></i> Cold Snack</span>
                                        <span class="category-count" id="coldsnackCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Insights -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="bg-white p-3 rounded-4 shadow-sm mt-2">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div><i class="fa-solid fa-chart-line text-success"></i> <strong>Dashboard insights</strong></div>
                                    <div class="text-muted">Real-time product statistics from database</div>
                                    <div class="ms-auto"><span class="badge bg-light text-dark px-3 py-2 rounded-pill"><i class="fa-regular fa-clock"></i> Live data</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Table -->
                <div class="container-fluid mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <h3 class="section-title"><i class="fa-solid fa-box me-2"></i>Products List</h3>
                        <form method="GET" action="" class="search-section">
                            <i class="fa-solid fa-magnifying-glass text-secondary"></i>
                            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($searchKeyword) ?>">
                            <button type="submit"><i class="fa-solid fa-search"></i> Search</button>
                            <?php if (!empty($searchKeyword)): ?>
                                <a href="index.php" class="text-danger" style="text-decoration: none;" title="Clear search"><i class="fa-solid fa-times-circle"></i></a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-responsive-custom">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" class="checkbox-custom" id="selectAllCheck"></th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Created By</th>
                                        <th>Create At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($totalProducts > 0): ?>
                                        <?php while ($row = $res->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr class="align-middle">
                                            <td><input type="checkbox" class="product-checkbox checkbox-custom" value="<?= $row['id'] ?>"></td>
                                            <td>
                                                <?php if (!empty($row['image'])): ?>
                                                    <img src="<?= htmlspecialchars($row['image']) ?>" class="img-product" alt="product" onerror="this.src='https://cdn-icons-png.flaticon.com/512/1046/1046784.png'">
                                                <?php else: ?>
                                                    <img src="https://cdn-icons-png.flaticon.com/512/1046/1046784.png" class="img-product" alt="product">
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                            <td>
                                                <?php if ($row['type'] == "Hot"): ?>
                                                    <span class="badge-hot"><i class="fa-solid fa-fire"></i> Hot</span>
                                                <?php else: ?>
                                                    <span class="badge-cold"><i class="fa-regular fa-snowflake"></i> Cold</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><i class="fa-solid fa-tag me-1"></i> <?= htmlspecialchars($row['category']) ?></td>
                                            <td class="price-tag">$<?= number_format($row['price'], 2) ?></td>
                                            <td><i class="fa-regular fa-user"></i> <?= htmlspecialchars($row['created_by']) ?></td>
                                            <td><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($row['create_at']) ?></td>
                                            <td>
                                                <?php if ($row['status'] == "Publish"): ?>
                                                    <span class="status-publish"><i class="fa-solid fa-circle-check"></i> Publish</span>
                                                <?php else: ?>
                                                    <span class="status-draft"><i class="fa-regular fa-clock"></i> Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="action-icons">
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this product?')"><i class="fa-regular fa-trash-can"></i> Delete</a>
                                            </td>
                                        </tr>                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i> No products found. 
                                                <?php if (!empty($searchKeyword)): ?> Try a different keyword or <a href="index.php">clear search</a>.<?php endif; ?>
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
    </div>

    <!-- JavaScript -->
    <script>
        function fetchDashboardStats() {
            fetch(window.location.pathname + '?fetch_stats=1')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update total products
                        document.getElementById('totalProductsCount').innerText = data.total_products;
                        
                        // Update orders
                        document.getElementById('totalOrdersCount').innerText = data.total_orders;
                        document.getElementById('paidOrdersCount').innerText = data.paid_orders;
                        document.getElementById('unpaidOrdersCount').innerText = data.unpaid_orders;
                        
                        // Coffee counts
                        document.getElementById('coffeeCount').innerText = data.coffee_total;
                        document.getElementById('hotCoffeeCount').innerText = data.hot_coffee;
                        document.getElementById('coldCoffeeCount').innerText = data.cold_coffee;
                        
                        // Matcha counts
                        document.getElementById('matchaCount').innerText = data.matcha_total;
                        document.getElementById('hotMatchaCount').innerText = data.hot_matcha;
                        document.getElementById('coldMatchaCount').innerText = data.cold_matcha;
                        
                        // Tea counts
                        document.getElementById('teaCount').innerText = data.tea_total;
                        document.getElementById('hotTeaCount').innerText = data.hot_tea;
                        document.getElementById('coldTeaCount').innerText = data.cold_tea;
                        
                        // Snack counts
                        document.getElementById('snackCount').innerText = data.Snack_total;
                        document.getElementById('hotsnackCount').innerText = data.hot_Snack;
                        document.getElementById('coldsnackCount').innerText = data.cold_Snack;
                    } else {
                        console.warn("Stats data error");
                        setFallbackValues();
                    }
                })
                .catch(error => {
                    console.error("Error fetching stats:", error);
                    setFallbackValues();
                });
        }

        function setFallbackValues() {
            document.getElementById('totalProductsCount').innerText = '0';
            document.getElementById('totalOrdersCount').innerText = '0';
            document.getElementById('paidOrdersCount').innerText = '0';
            document.getElementById('unpaidOrdersCount').innerText = '0';
            document.getElementById('coffeeCount').innerText = '0';
            document.getElementById('hotCoffeeCount').innerText = '0';
            document.getElementById('coldCoffeeCount').innerText = '0';
            document.getElementById('matchaCount').innerText = '0';
            document.getElementById('hotMatchaCount').innerText = '0';
            document.getElementById('coldMatchaCount').innerText = '0';
            document.getElementById('teaCount').innerText = '0';
            document.getElementById('hotTeaCount').innerText = '0';
            document.getElementById('coldTeaCount').innerText = '0';
            document.getElementById('snackCount').innerText = '0';
            document.getElementById('hotsnackCount').innerText = '0';
            document.getElementById('coldsnackCount').innerText = '0';
        }

        // Select all checkboxes
        const selectAllCheck = document.getElementById("selectAllCheck");
        if (selectAllCheck) {
            selectAllCheck.addEventListener("change", function() {
                const checkboxes = document.querySelectorAll(".product-checkbox");
                checkboxes.forEach(cb => cb.checked = selectAllCheck.checked);
            });
        }

        // Auto load stats on page ready
        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardStats();
            setInterval(fetchDashboardStats, 30000);
        });
    </script>
</body>
</html>