<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>KRaksa Coffee Shop | Smart Dashboard with Interactive Charts</title>
    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .active{
            background: rgba(212, 163, 115, 0.3);
            /* transform: translateX(4px); */
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fef9f0;
            overflow-x: hidden;
        }

        /* Coffee-themed Sidebar */
        .bar {
            background: linear-gradient(145deg, #3e2723 0%, #2c1a12 100%);
            box-shadow: 6px 0 18px rgba(0, 0, 0, 0.1);
            z-index: 1050;
        }

        .bar .logo {
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0 0.8rem 0;
            margin-bottom: 0.5rem;
        }

        .bar img {
            width: 48px;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));
            margin-left: 10px;
        }

        .text-head {
            color: #f5e6d3;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-left: 6px;
            font-size: 1.2rem;
        }

        .text-head small {
            font-size: 0.7rem;
            display: block;
            color: #d4a373;
        }

        .menu li {
            background: transparent;
            border: none;
            margin: 6px 0;
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .menu li a {
            text-decoration: none;
            color: #e8dcca;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: 0.2s;
        }

        .menu li a i {
            width: 24px;
            font-size: 1.2rem;
        }

        .menu li:hover {
            background: rgba(212, 163, 115, 0.3);
            transform: translateX(4px);
        }

        .menu li:hover a {
            color: #ffd966;
        }

        .logout {
            position: absolute;
            bottom: 28px;
            left: 24px;
            font-weight: 500;
            color: #d4a373;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.2s;
            padding: 10px 16px;
            border-radius: 40px;
            width: calc(100% - 48px);
        }

        .logout:hover {
            background: #c0392b;
            color: white;
        }

        .main-content {
            margin-left: 16.666%;
            padding: 28px 32px;
            transition: all 0.2s;
        }

        .page-title h2 {
            font-weight: 800;
            color: #5d3a1a;
            letter-spacing: -0.3px;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .kpi-card {
            background: white;
            border-radius: 24px;
            padding: 1.2rem 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.25s;
            border-left: 5px solid #d4a373;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.15);
        }

        .kpi-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 800;
            color: #4a2c14;
        }

        .kpi-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #8b6946;
            text-transform: uppercase;
        }

        .stats-card {
            background: white;
            border-radius: 24px;
            padding: 1.2rem;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0e4d4;
            margin-bottom: 24px;
        }

        .stats-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #d4a373;
            padding-left: 12px;
            color: #5d3a1a;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 28px;
            margin: 20px 0;
        }

        .chart-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 1.2rem;
            transition: transform 0.2s;
        }

        .chart-card:hover {
            transform: translateY(-3px);
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-table th,
        .product-table td {
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #f0e4d4;
            vertical-align: middle;
        }

        .product-table th {
            background: #faf5eb;
            color: #5d3a1a;
            font-weight: 600;
        }

        .badge-coffee {
            background: #d4a373;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
        }

        .badge-lowstock {
            background: #e74c3c;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .stock-warning {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .product-img-cell {
            width: 60px;
        }
        .product-img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            background: #f3ede5;
            border: 1px solid #ede0cf;
        }

        /* Compact bar chart container */
        canvas {
            max-height: 280px;
            width: 100% !important;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .charts-grid {
                grid-template-columns: 1fr;
            }
            .product-img {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <!-- Sidebar -->
    <div class="col-2 position-fixed vh-100 p-3 bar" style="top:0; left:0;">
        <div class="logo">
            <img src="https://cdn-icons-png.flaticon.com/512/924/924514.png" alt="coffee icon">
            <h4 class="text-head">shop Coffee<br><small>Smart Analytics</small></h4>
        </div>
        <ul class="list-group menu mx-1 my-4">
            <li class="list-group-item active"><a href="#"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li class="list-group-item"><a href="order.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
            <li class="list-group-item"><a href="#"><i class="fa-solid fa-mug-hot"></i> Products</a></li>
            <li class="list-group-item"><a href="#"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li class="list-group-item"><a href="#"><i class="fa-solid fa-file-invoice"></i> Reports</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Logout
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-title mb-3">
            <h2><i class="fa-solid fa-mug-hot me-2" style="color:#d4a373;"></i>  Coffee Shop Dashboard</h2>
            <p class="text-muted">Real-time analytics from <strong>tbl_products</strong> & order insights — Dynamic Bar Chart עם animations</p>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid" id="kpiContainer">
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-mug-hot"></i></div>
                <div class="kpi-value" id="totalProducts">--</div>
                <div class="kpi-label">Total Products</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
                <div class="kpi-value" id="totalOrders">--</div>
                <div class="kpi-label">Total Orders</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="kpi-value" id="totalRevenue">--</div>
                <div class="kpi-label">Total Revenue ($)</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                <div class="kpi-value" id="avgOrderValue">--</div>
                <div class="kpi-label">Avg Order Value</div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="stats-card" id="lowStockAlert" style="display:none;">
            <div class="stats-title"><i class="fas fa-exclamation-triangle me-2" style="color:#e74c3c;"></i> ⚠️ Low Stock Alert</div>
            <div id="lowStockContent"></div>
        </div>

        <!-- Charts Row with enhanced bar chart -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2" style="color:#d4a373;"></i> Top Selling Products</h5>
                    <button id="refreshBarChartBtn" class="btn btn-sm btn-outline-secondary rounded-pill"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <canvas id="topProductsChart" width="400" height="260"></canvas>
                <p class="small text-muted mt-2 text-center"><i class="fas fa-info-circle"></i> Bar chart displays units sold per product — interactive & responsive</p>
            </div>
            <div class="chart-card">
                <h5><i class="fas fa-chart-pie me-2"></i> Sales by Category</h5>
                <canvas id="categoryChart" width="400" height="250"></canvas>
            </div>
        </div>

        <!-- Products Table (from tbl_products) with IMAGES -->
        <div class="stats-card">
            <div class="stats-title"><i class="fas fa-table-list me-2"></i> 📦 Product Inventory (tbl_products) — Image Preview</div>
            <div style="overflow-x: auto;">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price ($)</th>
                            <th>Stock (s_qty)</th>
                            <th>Sold Count</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <tr><td colspan="8" class="text-center">Loading coffee products from tbl_products... <i class="fas fa-spinner fa-pulse"></i></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="stats-card">
            <div class="stats-title"><i class="fas fa-clock me-2"></i> 🧾 Recent Orders</div>
            <div style="overflow-x: auto;">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product(s)</th>
                            <th>Quantity</th>
                            <th>Total ($)</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersBody">
                        <tr><td colspan="6" class="text-center">Loading orders... <i class="fas fa-spinner fa-pulse"></i></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper functions
    function formatDate(dateString) {
        if (!dateString) return '—';
        try {
            let date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('en-US') + ' ' + date.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
        } catch(e) { return dateString; }
    }
    
    function escapeHtml(text) {
        if(!text) return '';
        return String(text).replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) { return c; });
    }
    
    // Global chart instances
    let barChartInstance = null;
    let pieChartInstance = null;
    
    // generate full mock data (with product images and realistic top sellers)
    function generateMockDashboardData() {
        const products = [
            { id: 1, name: "Ethiopian Yirgacheffe", category: "Single Origin", price: 14.99, stock: 42, sold_count: 128, description: "Floral, jasmine aroma", image: "https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=100&h=100&fit=crop" },
            { id: 2, name: "Caramel Macchiato", category: "Signature", price: 5.49, stock: 18, sold_count: 245, description: "Creamy caramel drizzle", image: "https://images.unsplash.com/photo-1578314675249-a6910f80cc4d?w=100&h=100&fit=crop" },
            { id: 3, name: "Dark Roast French", category: "Roasted Beans", price: 12.99, stock: 5, sold_count: 89, description: "Intense, smoky finish", image: "https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=100&h=100&fit=crop" },
            { id: 4, name: "Vanilla Latte", category: "Signature", price: 5.99, stock: 32, sold_count: 312, description: "Smooth vanilla sweetness", image: "https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=100&h=100&fit=crop" },
            { id: 5, name: "Cold Brew Nitro", category: "Iced Coffee", price: 6.49, stock: 12, sold_count: 98, description: "Velvety rich", image: "https://images.unsplash.com/photo-1558126319-c9feecbf57ee?w=100&h=100&fit=crop" },
            { id: 6, name: "Colombia Supremo", category: "Single Origin", price: 15.99, stock: 28, sold_count: 76, description: "Nutty & balanced", image: "https://images.unsplash.com/photo-1611854779393-1b2da9d400fe?w=100&h=100&fit=crop" }
        ];
        
        const recentOrders = [
            { order_id: 101, customer_name: "Sopheak M.", product_name: "Caramel Macchiato", quantity: 2, total: 10.98, order_date: "2025-04-12 14:23:00" },
            { order_id: 102, customer_name: "Dara R.", product_name: "Ethiopian Yirgacheffe", quantity: 1, total: 14.99, order_date: "2025-04-12 11:15:00" },
            { order_id: 103, customer_name: "Malyka S.", product_name: "Vanilla Latte", quantity: 3, total: 17.97, order_date: "2025-04-11 19:30:00" },
            { order_id: 104, customer_name: "Rithy K.", product_name: "Cold Brew Nitro", quantity: 1, total: 6.49, order_date: "2025-04-11 09:45:00" },
            { order_id: 105, customer_name: "Visal C.", product_name: "Dark Roast French", quantity: 2, total: 25.98, order_date: "2025-04-10 17:20:00" }
        ];
        
        // top selling sorted by sold_count desc
        const topProducts = [...products].sort((a,b) => b.sold_count - a.sold_count).slice(0,5).map(p => ({ name: p.name, sold: p.sold_count }));
        
        const categoryMap = new Map();
        products.forEach(p => {
            let cat = p.category;
            let sold = p.sold_count || 0;
            categoryMap.set(cat, (categoryMap.get(cat) || 0) + sold);
        });
        const categorySales = Array.from(categoryMap.entries()).map(([category, total_sold]) => ({ category, total_sold }));
        
        const lowStock = products.filter(p => p.stock < 20).map(p => ({ name: p.name, stock: p.stock }));
        
        const totalProducts = products.length;
        const totalOrders = 412;
        const totalRevenue = 5240.75;
        const avgOrderValue = totalRevenue / totalOrders;
        
        return { totalProducts, totalOrders, totalRevenue, avgOrderValue, lowStock, products, recentOrders, topProducts, categorySales };
    }
    
    // function to render bar chart with animation and modern style
    function renderBarChart(topProductsData) {
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        if (barChartInstance) {
            barChartInstance.destroy();
        }
        // extract labels and data
        const labels = topProductsData.map(item => item.name);
        const values = topProductsData.map(item => item.sold);
        
        barChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '📊 Units Sold',
                    data: values,
                    backgroundColor: 'rgba(212, 163, 115, 0.85)',
                    borderColor: '#b97f44',
                    borderWidth: 1,
                    borderRadius: 12,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                    hoverBackgroundColor: '#c38e5c',
                    hoverBorderColor: '#6b3e1c',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart',
                    onComplete: function() {
                        // subtle animation complete marker
                        console.log("Bar chart animated");
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: '#2c1a12',
                        titleColor: '#f5e6d3',
                        bodyColor: '#f0e4d4',
                        callbacks: {
                            label: (context) => `🍫 Sold: ${context.raw} units`
                        }
                    },
                    legend: {
                        position: 'top',
                        labels: { font: { weight: 'bold', size: 12 }, usePointStyle: true, boxWidth: 10 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0e4d4', drawBorder: true },
                        title: { display: true, text: 'Units Sold', color: '#8b6946', font: { weight: 'bold' } },
                        ticks: { stepSize: 50, precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        title: { display: true, text: 'Coffee Products', color: '#8b6946' },
                        ticks: { font: { size: 11 }, rotation: 15, maxRotation: 25 }
                    }
                },
                layout: {
                    padding: { top: 12, bottom: 8, left: 8, right: 8 }
                }
            }
        });
    }
    
    function renderPieChart(categorySales) {
        const ctx2 = document.getElementById('categoryChart').getContext('2d');
        if (pieChartInstance) pieChartInstance.destroy();
        pieChartInstance = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: categorySales.map(c => c.category),
                datasets: [{
                    data: categorySales.map(c => c.total_sold),
                    backgroundColor: ['#d4a373', '#b5835a', '#8b5a2b', '#e6c3a0', '#c49a6c', '#a0714f'],
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                animation: { animateScale: true, animateRotate: true, duration: 1000 },
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 }, usePointStyle: true } },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw} units sold (${((ctx.raw / ctx.dataset.data.reduce((a,b)=>a+b,0))*100).toFixed(1)}%)` } }
                }
            }
        });
    }
    
    // Main render dashboard
    function renderDashboard(data) {
        // KPI
        $('#totalProducts').text(data.totalProducts || 0);
        $('#totalOrders').text(data.totalOrders || 0);
        $('#totalRevenue').text('$' + (data.totalRevenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#avgOrderValue').text('$' + (data.avgOrderValue || 0).toFixed(2));
        
        // low stock alert
        if(data.lowStock && data.lowStock.length > 0) {
            $('#lowStockAlert').show();
            let lowStockHtml = '<div class="row g-2">';
            data.lowStock.forEach(item => {
                lowStockHtml += `<div class="col-md-3 col-sm-4"><span class="badge-lowstock d-inline-flex align-items-center gap-1"><i class="fas fa-box"></i> ${escapeHtml(item.name)}: ${item.stock} left</span></div>`;
            });
            lowStockHtml += '</div>';
            $('#lowStockContent').html(lowStockHtml);
        } else { $('#lowStockAlert').hide(); }
        
        // Products table
        if(data.products && data.products.length) {
            let html = '';
            data.products.forEach(p => {
                let stockStatusHtml = (p.stock < 20) ? `<span class="stock-warning"><i class="fas fa-exclamation-circle"></i> Low (${p.stock})</span>` : `${p.stock}`;
                let statusBadge = (p.stock > 20) ? '<span class="text-success"><i class="fas fa-check-circle"></i> In Stock</span>' : '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Reorder Soon</span>';
                let imgUrl = p.image && p.image.trim() !== '' ? p.image : 'https://placehold.co/400x400/f7e9d7/8b5a2b?text=Coffee';
                html += `<tr>
                    <td>${p.id}</td>
                    <td class="product-img-cell"><img src="${escapeHtml(imgUrl)}" class="product-img" alt="${escapeHtml(p.name)}" onerror="this.onerror=null;this.src='https://placehold.co/400x400/d4a373/fff?text=KRaksa'"></td>
                    <td><strong>${escapeHtml(p.name)}</strong><br><small class="text-muted">${escapeHtml(p.description || '')}</small></td>
                    <td><span class="badge-coffee">${escapeHtml(p.category)}</span></td>
                    <td>$${parseFloat(p.price).toFixed(2)}</td>
                    <td>${stockStatusHtml}</td>
                    <td>${p.sold_count || 0}</td>
                    <td>${statusBadge}</td>
                </tr>`;
            });
            $('#productsTableBody').html(html);
        } else { $('#productsTableBody').html('<tr><td colspan="8" class="text-center">📭 No products found</td></tr>'); }
        
        // Recent orders
        if(data.recentOrders && data.recentOrders.length) {
            let orderHtml = '';
            data.recentOrders.forEach(order => {
                orderHtml += `<tr>
                    <td>#${order.order_id}</td>
                    <td>${escapeHtml(order.customer_name || 'Walk-in Guest')}</td>
                    <td>${escapeHtml(order.product_name || 'Coffee item')}</td>
                    <td>${order.quantity || 0}</td>
                    <td>$${parseFloat(order.total).toFixed(2)}</td>
                    <td>${formatDate(order.order_date)}</td>
                </tr>`;
            });
            $('#recentOrdersBody').html(orderHtml);
        } else { $('#recentOrdersBody').html('<tr><td colspan="6" class="text-center">No recent orders</td></tr>'); }
        
        // Render Bar Chart (ensure it works and is "ដើត" - lively/active)
        if(data.topProducts && data.topProducts.length) {
            renderBarChart(data.topProducts);
        } else {
            renderBarChart([{ name: "No data", sold: 0 }]);
        }
        // Pie chart
        if(data.categorySales && data.categorySales.length) renderPieChart(data.categorySales);
        else renderPieChart([{ category: "Coffee", total_sold: 1 }]);
    }
    
    // Fetch (with fallback)
    function fetchDashboardData() {
        $.ajax({
            url: 'get_dashboard_data.php',
            method: 'GET',
            dataType: 'json',
            timeout: 4000,
            success: function(resp) {
                if(resp && typeof resp === 'object') {
                    if(resp.products && Array.isArray(resp.products)) {
                        resp.products.forEach(p => { if(p.image && !p.image.match(/^(http|\/)/i)) p.image = 'uploads/' + p.image; });
                    }
                    renderDashboard(resp);
                } else { renderDashboard(generateMockDashboardData()); }
            },
            error: function() {
                console.log("Using mock data with dynamic bar chart");
                renderDashboard(generateMockDashboardData());
            }
        });
    }
    
    // manual refresh bar chart with bounce
    function refreshBarChartOnly() {
        const mock = generateMockDashboardData();
        if(mock.topProducts) {
            renderBarChart(mock.topProducts);
            // small toast-like effect
            const btn = $('#refreshBarChartBtn');
            btn.html('<i class="fas fa-check-circle"></i> Updated!');
            setTimeout(() => btn.html('<i class="fas fa-sync-alt"></i> Refresh'), 1500);
        }
    }
    
    $(document).ready(function() {
        fetchDashboardData();
        setInterval(fetchDashboardData, 35000);
        $('#refreshBarChartBtn').on('click', function(e) {
            e.preventDefault();
            refreshBarChartOnly();
        });
    });
</script>
</body>
</html>