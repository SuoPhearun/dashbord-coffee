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

        /* overview card special */
        .overview-card .kpi-number {
            font-size: 2.8rem;
        }

        /* responsive adjustments */
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
            <!-- Sidebar - col-2 fixed -->
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
                    <li><a href="#"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                    <li><a href="#"><i class="fa-solid fa-list"></i> Report</a></li>
                    <li><a href="#"><i class="fa-solid fa-gear"></i> Setting</a></li>
                </ul>
                <div class="logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> logout
                </div>
            </div>

            <!-- Main Content - offset-2 -->
            <div class="col-10 offset-2 p-4">
                <div class="hader-dashoard">
                    <h2 class="text-dashoard"><i class="fa-solid fa-chart-simple me-2"></i> Main Dashboard</h2>
                </div>

                <!-- KPI Statistics Cards -->
                <div class="container mt-2">
                    <div class="row g-4">
                        <!-- Total Products Card - Overview -->
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
                    </div>

                    <!-- Additional quick insight row (optional snack category hint but keeping clean) -->
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
            </div>
        </div>
    </div>

    <!-- JavaScript: Fetch dynamic product stats from database using AJAX (JSON) -->
    <script>
        // Function to fetch product statistics via fetch API (without page reload)
        function fetchDashboardStats() {
            // Use a separate endpoint: we will create a simple JSON response from same page via GET parameter ?fetch_stats=1
            fetch(window.location.pathname + '?fetch_stats=1')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update total products
                        document.getElementById('totalProductsCount').innerText = data.total_products;
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
            document.getElementById('coffeeCount').innerText = '0';
            document.getElementById('hotCoffeeCount').innerText = '0';
            document.getElementById('coldCoffeeCount').innerText = '0';
            document.getElementById('matchaCount').innerText = '0';
            document.getElementById('hotMatchaCount').innerText = '0';
            document.getElementById('coldMatchaCount').innerText = '0';
            document.getElementById('teaCount').innerText = '0';
            document.getElementById('hotTeaCount').innerText = '0';
            document.getElementById('coldTeaCount').innerText = '0';
        }

        // Auto load stats on page ready
        document.addEventListener('DOMContentLoaded', function() {
            fetchDashboardStats();
            // Optional: refresh every 30 seconds
            setInterval(fetchDashboardStats, 30000);
        });
    </script>
<?php
// ========== DYNAMIC BACKEND STATS FOR JSON RESPONSE ==========
if (isset($_GET['fetch_stats']) && $_GET['fetch_stats'] == '1') {
    header('Content-Type: application/json');
    
    include "connection.php";
    
    $stats = [
        'success' => true,
        'total_products' => 0,
        'coffee_total' => 0,
        'hot_coffee' => 0,
        'cold_coffee' => 0,
        'matcha_total' => 0,
        'hot_matcha' => 0,
        'cold_matcha' => 0,
        'tea_total' => 0,
        'hot_tea' => 0,
        'cold_tea' => 0
    ];
    
    try {
        // Total products count - កែត្រូវហើយ
        $totalQuery = $conn->query("SELECT COUNT(*) as total FROM tbl_products");
        if ($totalQuery) {
            $totalRow = $totalQuery->fetch_assoc();
            $stats['total_products'] = (int)$totalRow['total'];
        }
        
        // Coffee total & breakdown
        $coffeeTotalQuery = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Coffee'");
        if ($coffeeTotalQuery) {
            $coffeeRow = $coffeeTotalQuery->fetch_assoc();
            $stats['coffee_total'] = (int)$coffeeRow['total'];
        }
        
        $hotCoffeeQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Coffee' AND type = 'Hot'");
        if ($hotCoffeeQ) {
            $hotCoffeeRow = $hotCoffeeQ->fetch_assoc();
            $stats['hot_coffee'] = (int)$hotCoffeeRow['total'];
        }
        
        $coldCoffeeQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Coffee' AND type = 'Cold'");
        if ($coldCoffeeQ) {
            $coldCoffeeRow = $coldCoffeeQ->fetch_assoc();
            $stats['cold_coffee'] = (int)$coldCoffeeRow['total'];
        }
        
        // Matcha stats
        $matchaTotalQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Matcha'");
        if ($matchaTotalQ) {
            $matchaRow = $matchaTotalQ->fetch_assoc();
            $stats['matcha_total'] = (int)$matchaRow['total'];
        }
        
        $hotMatchaQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Matcha' AND type = 'Hot'");
        if ($hotMatchaQ) {
            $hotMatchaRow = $hotMatchaQ->fetch_assoc();
            $stats['hot_matcha'] = (int)$hotMatchaRow['total'];
        }
        
        $coldMatchaQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Matcha' AND type = 'Cold'");
        if ($coldMatchaQ) {
            $coldMatchaRow = $coldMatchaQ->fetch_assoc();
            $stats['cold_matcha'] = (int)$coldMatchaRow['total'];
        }
        
        // Tea stats
        $teaTotalQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Tea'");
        if ($teaTotalQ) {
            $teaRow = $teaTotalQ->fetch_assoc();
            $stats['tea_total'] = (int)$teaRow['total'];
        }
        
        $hotTeaQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Tea' AND type = 'Hot'");
        if ($hotTeaQ) {
            $hotTeaRow = $hotTeaQ->fetch_assoc();
            $stats['hot_tea'] = (int)$hotTeaRow['total'];
        }
        
        $coldTeaQ = $conn->query("SELECT COUNT(*) as total FROM tbl_products WHERE category = 'Tea' AND type = 'Cold'");
        if ($coldTeaQ) {
            $coldTeaRow = $coldTeaQ->fetch_assoc();
            $stats['cold_tea'] = (int)$coldTeaRow['total'];
        }
        
    } catch (Exception $e) {
        $stats['success'] = false;
        $stats['error'] = $e->getMessage();
    }
    
    echo json_encode($stats);
    exit;
}
?>
</body>
</html>