<?php
      // order.php
      require_once 'connection.php';
      
      // ឆែកមើលការភ្ជាប់ Database
      if (!isset($pdo)) {
          die("Error: មិនទាន់មានការភ្ជាប់ Database ទេ។ សូមពិនិត្យ connection.php");
      }
      
      // --- ១. ការគ្រប់គ្រងទិន្នន័យ (Create / Edit / Delete) ---
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
          if ($_POST['action'] === 'create') {
              $c_name = trim($_POST['c_name']);
              $item = trim($_POST['item']);
              $total_amount = floatval($_POST['total_amount']);
              $status = $_POST['status'];
              
              $stmt = $pdo->prepare("INSERT INTO tbl_order (c_name, item, total_amount, status) VALUES (?, ?, ?, ?)");
              $stmt->execute([$c_name, $item, $total_amount, $status]);
              header("Location: order.php?msg=created");
              exit;
          }
          
          if ($_POST['action'] === 'edit') {
              $id = intval($_POST['id']);
              $c_name = trim($_POST['c_name']);
              $item = trim($_POST['item']);
              $total_amount = floatval($_POST['total_amount']);
              $status = $_POST['status'];
              
              $stmt = $pdo->prepare("UPDATE tbl_order SET c_name=?, item=?, total_amount=?, status=? WHERE id=?");
              $stmt->execute([$c_name, $item, $total_amount, $status, $id]);
              header("Location: order.php?msg=updated");
              exit;
          }
      
          if ($_POST['action'] === 'delete') {
              $id = intval($_POST['id']);
              $stmt = $pdo->prepare("DELETE FROM tbl_order WHERE id = ?");
              $stmt->execute([$id]);
              header("Location: order.php?msg=deleted");
              exit;
          }
      }
      
      // --- ២. ការទាញយកទិន្នន័យសម្រាប់បង្ហាញ និង Pagination ---
      
      // កំណត់តម្លៃ Filter និង Search
      $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
      $per_page = 10;
      $offset = ($page - 1) * $per_page;
      
      // បង្កើត SQL សម្រាប់ Filter
      $where_clauses = ["1=1"];
      $params = [];
      
      if ($status_filter !== 'all') {
          $where_clauses[] = "status = ?";
          $params[] = $status_filter;
      }
      if (!empty($search)) {
          $where_clauses[] = "(c_name LIKE ? OR item LIKE ?)";
          $params[] = "%$search%";
          $params[] = "%$search%";
      }
      
      $where_sql = implode(" AND ", $where_clauses);
      
      // រាប់ចំនួនសរុបដើម្បីធ្វើ Pagination
      $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_order WHERE $where_sql");
      $count_stmt->execute($params);
      $total_records = $count_stmt->fetchColumn();
      $total_pages = ceil($total_records / $per_page);
      
      // ទាញទិន្នន័យជាក់ស្តែង
      $data_sql = "SELECT * FROM tbl_order WHERE $where_sql ORDER BY id DESC LIMIT $per_page OFFSET $offset";
      $data_stmt = $pdo->prepare($data_sql);
      $data_stmt->execute($params);
      $paginated_orders = $data_stmt->fetchAll();
      
      // ទាញ Statistics សម្រាប់ Card ខាងលើ
      $total_orders = $pdo->query("SELECT COUNT(*) FROM tbl_order")->fetchColumn();
      $total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM tbl_order")->fetchColumn();
      $pending_count = $pdo->query("SELECT COUNT(*) FROM tbl_order WHERE status IN ('pending', 'processing')")->fetchColumn();
      $completed_count = $pdo->query("SELECT COUNT(*) FROM tbl_order WHERE status = 'completed'")->fetchColumn();
      
      // ឆែកមើលទិន្នន័យសម្រាប់ Edit Modal
      $edit_order = null;
      if (isset($_GET['edit'])) {
          $stmt = $pdo->prepare("SELECT * FROM tbl_order WHERE id = ?");
          $stmt->execute([intval($_GET['edit'])]);
          $edit_order = $stmt->fetch();
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRaksa Coffee Shop | Order Management</title>
       <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #fef9f0;
        }
        
        /* Sidebar */
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

        
        .logo small {
            color: #d4a373;
            font-size: 0.7rem;
            display: block;
        }
        
        .nav-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .nav-menu li a {
            display: block;
            padding: 12px 24px;
            color: #e8dcca;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-menu li a:hover {
            background: rgba(212,163,115,0.3);
            color: #ffd966;
        }
        
        .nav-menu li a.active {
            background: rgba(212,163,115,0.4);
            color: #ffd966;
            border-left: 3px solid #ffd966;
        }
        
        .nav-menu li a i {
            width: 28px;
            margin-right: 10px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px 40px;
        }
        
        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-left: 5px solid #d4a373;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #5d3a1a;
        }
        
        .stat-label {
            color: #8b6946;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        /* Table */
        .card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .card-header h3 {
            color: #5d3a1a;
            font-size: 1.3rem;
        }
        
        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-bar input, .filter-bar select {
            padding: 8px 15px;
            border: 1px solid #e0cfbc;
            border-radius: 40px;
            font-family: 'Inter', sans-serif;
        }
        
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #d4a373;
            color: white;
        }
        
        .btn-primary:hover {
            background: #b5835a;
            transform: scale(0.98);
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0e4d4;
        }
        
        th {
            background: #faf5eb;
            color: #5d3a1a;
            font-weight: 600;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-processing { background: #d1ecf1; color: #0c5460; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        
        .status-select {
            padding: 5px 10px;
            border-radius: 40px;
            border: 1px solid #d4a373;
            background: white;
            cursor: pointer;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 5px 12px;
            font-size: 0.75rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        
        .pagination a {
            padding: 8px 14px;
            background: #f0e4d4;
            text-decoration: none;
            color: #5d3a1a;
            border-radius: 40px;
            transition: 0.3s;
        }
        
        .pagination a.active {
            background: #d4a373;
            color: white;
        }
        
        .pagination a:hover {
            background: #d4a373;
            color: white;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 24px;
            padding: 30px;
            width: 500px;
            max-width: 90%;
        }
        
        .modal-header {
            margin-bottom: 20px;
        }
        
        .modal-body input, .modal-body select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #e0cfbc;
            border-radius: 12px;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>


<div class="col-2 position-fixed vh-100 p-3 bar" style="top:0; left:0;">
        <div class="logo">
            <img src="https://cdn-icons-png.flaticon.com/512/924/924514.png" alt="coffee icon">
            <h4 class="text-head">shop Coffee<br><small>Smart Analytics</small></h4>
        </div>
        <ul class="list-group menu mx-1 my-4">
            <li class="list-group-item​ "><a href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li class="list-group-item active"><a href=""><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
            <li class="list-group-item"><a href="#"><i class="fa-solid fa-mug-hot"></i> Products</a></li>
            <li class="list-group-item"><a href="#"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li class="list-group-item"><a href="#"><i class="fa-solid fa-file-invoice"></i> Reports</a></li>
        </ul>
        <div class="logout">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Logout
        </div>
    </div>

<div class="main-content">
    <!-- Message Alert -->
    <?php if(isset($_GET['msg'])): ?>
        <?php 
        $msg_type = 'success';
        $msg_text = '';
        switch($_GET['msg']) {
            case 'created': $msg_text = '✅ Order created successfully!'; break;
            case 'updated': $msg_text = '✅ Order updated successfully!'; break;
            case 'deleted': $msg_text = '🗑️ Order deleted successfully!'; break;
            case 'status_updated': $msg_text = '✅ Status updated!'; break;
        }
        if($msg_text): ?>
        <div class="alert alert-success"><?php echo $msg_text; ?></div>
        <script>setTimeout(() => document.querySelector('.alert')?.remove(), 3000);</script>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_orders; ?></div>
            <div class="stat-label"><i class="fas fa-receipt"></i> Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
            <div class="stat-label"><i class="fas fa-dollar-sign"></i> Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $pending_count; ?></div>
            <div class="stat-label"><i class="fas fa-clock"></i> Pending / Processing</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $completed_count; ?></div>
            <div class="stat-label"><i class="fas fa-check-circle"></i> Completed</div>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list-ul"></i> All Orders</h3>
            <button class="btn btn-primary" onclick="openModal('create')">
                <i class="fas fa-plus"></i> Add New Order
            </button>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <input type="text" name="search" placeholder="🔍 Search by customer or item..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="index.php" class="btn btn-sm" style="background:#f0e4d4;">Reset</a>
            </form>
        </div>
        
        <div style="overflow-x: auto; margin-top: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Item</th>
                        <th>Total ($)</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($paginated_orders) > 0): ?>
                        <?php foreach($paginated_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($order['c_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['item'] ?: '-'); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                            <td>
                                <form method="GET" action="" style="display: inline;">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $order['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button onclick="confirmDelete(<?php echo $order['id']; ?>)" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox"></i> No orders found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>" 
                   class="<?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Simple Statistics Table -->
    <div class="card">
        <h3 style="margin-bottom: 20px;"><i class="fas fa-chart-simple"></i> Quick Statistics</h3>
        <table>
            <thead><tr><th>Status</th><th>Count</th><th>Total Revenue</th></tr></thead>
            <tbody>
                <?php
                $statuses = ['pending', 'processing', 'completed', 'cancelled'];
                foreach($statuses as $stat):
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count, COALESCE(SUM(total_amount), 0) as revenue FROM tbl_order WHERE status = ?");
                    $stmt->execute([$stat]);
                    $data = $stmt->fetch();
                ?>
                <tr>
                    <td><span class="badge badge-<?php echo $stat; ?>"><?php echo ucfirst($stat); ?></span></td>
                    <td><?php echo $data['count']; ?> orders</td>
                    <td>$<?php echo number_format($data['revenue'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Add New Order</h3>
            <button onclick="closeModal('createModal')" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <input type="text" name="c_name" placeholder="Customer Name *" required>
                <input type="text" name="item" placeholder="Item / Product">
                <input type="number" step="0.01" name="total_amount" placeholder="Total Amount ($)">
                <select name="status">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<?php if($edit_order): ?>
<div id="editModal" class="modal show">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Order #<?php echo $edit_order['id']; ?></h3>
            <a href="index.php" style="background:none; border:none; font-size:24px; text-decoration:none; color:black;">&times;</a>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $edit_order['id']; ?>">
            <div class="modal-body">
                <input type="text" name="c_name" value="<?php echo htmlspecialchars($edit_order['c_name']); ?>" placeholder="Customer Name" required>
                <input type="text" name="item" value="<?php echo htmlspecialchars($edit_order['item']); ?>" placeholder="Item">
                <input type="number" step="0.01" name="total_amount" value="<?php echo $edit_order['total_amount']; ?>" placeholder="Total Amount">
                <select name="status">
                    <option value="pending" <?php echo $edit_order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $edit_order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $edit_order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $edit_order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="modal-footer">
                <a href="index.php" class="btn">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
// Simple JavaScript only for modal and delete confirmation
function openModal(type) {
    if(type === 'create') {
        document.getElementById('createModal').style.display = 'flex';
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmDelete(id) {
    if(confirm('⚠️ Are you sure you want to delete this order? This action cannot be undone!')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if(event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Auto hide alert after 3 seconds
setTimeout(function() {
    var alert = document.querySelector('.alert');
    if(alert) alert.style.display = 'none';
}, 3000);
</script>

</body>
</html>