<?php 
include "connection.php"; // make sure $conn is defined (PDO connection)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variable for search keyword
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

// Build query with optional search filter using PDO
$sqls = "SELECT * FROM tbl_products";
$params = [];

if (!empty($searchKeyword)) {
    $sqls .= " WHERE name LIKE :search OR category LIKE :search OR type LIKE :search";
    $params[':search'] = "%$searchKeyword%";
}
$sqls .= " ORDER BY id DESC";

// Execute query with PDO
$stmt = $conn->prepare($sqls);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$res = $stmt;

// Get total products count for PDO
$totalProducts = $res->rowCount();

// Save new product
if (isset($_POST['save'])) {
    $productName = $_POST['p-name'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $create_by = $_POST['create_by'];
    $create_at = $_POST['create_at'];
    $status = $_POST['status'];
    $image = $_POST['image'];

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO tbl_products (name, type, category, price, create_by, create_at, status, image)
            VALUES (:name, :type, :category, :price, :create_by, :create_at, :status, :image)";
    
    $insertStmt = $conn->prepare($sql);
    $insertStmt->bindParam(':name', $productName);
    $insertStmt->bindParam(':type', $type);
    $insertStmt->bindParam(':category', $category);
    $insertStmt->bindParam(':price', $price);
    $insertStmt->bindParam(':create_by', $create_by);
    $insertStmt->bindParam(':create_at', $create_at);
    $insertStmt->bindParam(':status', $status);
    $insertStmt->bindParam(':image', $image);
    
    if ($insertStmt->execute()) {
        header("Location: product.php");
        exit();
    } else {
        echo "Error: " . implode(", ", $insertStmt->errorInfo());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .menu li {
            background-color: transparent;
            border: none;
            width: 100%;
            transition: 0.3s linear;
            font-size: 1.2rem;
            list-style-type: none;
            padding: 5px 10px;
        }
        .menu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 15px;
            color: #e8e8e4;
            text-decoration: none;
            transition: 0.2s ease;
        }
        .menu li:hover a {
            transform: translateX(15px);
        }
        .menu li:hover {
            background-color: rgba(0,0,0,0.2);
            width: 100%;
            transform: translateX(11px);
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
            box-shadow: 0 5px 12px rgba(0,0,0,0.2);
            margin-bottom: 0.5rem;
        }
        .text-head {
            color: #FFF3E0;
            font-size: 1.3rem;
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
            box-shadow: 0 6px 14px rgba(0,0,0,0.02);
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
        .btn-outline-success.add {
            background: #00897B;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 40px;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-outline-success.add:hover {
            background: #00695C;
            transform: translateY(-2px);
        }
        /* SEARCH BAR + COUNTER */
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
        .product-counter {
            background: #eef2f5;
            padding: 8px 18px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1f5e56;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .product-counter i {
            font-size: 1rem;
            color: #00897B;
        }
        .stats-wrapper {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        /* TABLE STYLES */
        .table-container {
            background: white;
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #e9ecef;
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
            position: sticky;
            top: 0;
            z-index: 2;
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
        /* badge styles */
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
        /* FORM CARD */
        .form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 460px;
            background: white;
            border-radius: 32px;
            box-shadow: 0 25px 45px rgba(0,0,0,0.25);
            z-index: 1050;
            padding: 1.8rem;
            display: none;
            border: 1px solid rgba(0,128,117,0.2);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 1040;
            display: none;
        }
        .form-control, .form-select {
            border-radius: 16px;
            border: 1px solid #e0dfd5;
            padding: 0.6rem 1rem;
        }
        .btn-primary {
            background: #00897B;
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
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
            .form { width: 90%; }
            .search-section input { width: 160px; }
        }
        .clear-search {
            background: transparent;
            border: none;
            color: #6c757d;
            transition: 0.2s;
            text-decoration: none;
            padding: 0 8px;
        }
        .clear-search:hover {
            color: #dc3545;
        }
        .action-icons a {
            text-decoration: none;
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
                <h1 class="text-head"> Admin Dashboard </h1>
            </div>
            <ul class="menu list-group">
                <li><a href="index.php"><i class="fa-solid fa-house"></i>Main Dashboard</a></li>
                <li><a href="product.php"><i class="fa-solid fa-box"></i> Manage Menu</a></li>
                <li><a href="order.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
                <li><a href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                <li><a href="#"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                <li><a href="#"><i class="fa-solid fa-list"></i> Report</a></li>
                <li><a href="#"><i class="fa-solid fa-gear"></i> Setting</a></li>
            </ul>
            <div class="logout text-white">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> logout
            </div>
        </div>

        <!-- Main content -->
        <div class="col-10 offset-2 p-3 px-5">
            <div class="hader-dashoard">
                <h2 class="text-dashoard"><i class="fa-solid fa-cup-togo me-2"></i>Product List</h2>
                <div class="stats-wrapper">
                    <!-- Product Counter Card -->
                    <div class="product-counter">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Total Products: <strong><?= $totalProducts ?></strong></span>
                    </div>
                    <!-- Search Form -->
                    <form method="GET" action="product.php" class="search-section">
                        <i class="fa-solid fa-magnifying-glass text-secondary"></i>
                        <input type="text" name="search" placeholder="Search by name, category, type..." value="<?= htmlspecialchars($searchKeyword) ?>">
                        <button type="submit"><i class="fa-solid fa-search"></i> Search</button>
                        <?php if (!empty($searchKeyword)): ?>
                            <a href="product.php" class="clear-search" title="Clear search"><i class="fa-solid fa-times-circle"></i></a>
                        <?php endif; ?>
                    </form>
                    <button type="button" class="btn btn-outline-success add"><i class="fa-solid fa-plus me-1"></i> Add New Item</button>
                </div>
            </div>

            <!-- Add Product Form (Popup) -->
            <div class="card form mx-auto shadow p-4" id="productFormCard" style="display: none;">               
                <form method="POST">
                    <h4 class="mb-3"><i class="fa-solid fa-pizza-slice"></i> New Product</h4>
                    <input type="text" name="p-name" class="form-control mb-2" placeholder="Product Name" required>
                    <select name="type" class="form-select mb-2">
                        <option>Hot</option>
                        <option>Cold</option>
                    </select>
                    <select name="category" class="form-select mb-2">
                        <option>Tea</option>
                        <option>Coffee</option>
                        <option>Matcha</option>
                    </select>
                    <input type="number" name="price" class="form-control mb-2" placeholder="Price" step="0.01" required>
                    <select name="create_by" class="form-select mb-2">
                        <option>Admin</option>
                        <option>Staff</option>
                    </select>
                    <input type="date" name="create_at" class="form-control mb-2" value="<?= date('Y-m-d') ?>">
                    <select name="status" class="form-select mb-2">
                        <option>Publish</option>
                        <option>Draft</option>
                    </select>
                    <input type="text" name="image" class="form-control mb-2" placeholder="Image URL (https://...)" value="https://cdn-icons-png.flaticon.com/512/1046/1046784.png">
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary cancel">Cancel</button>
                        <button class="btn btn-primary" name="save">Save Product</button>
                    </div>
                </form>
            </div>
            <div class="overlay" id="overlayBg"></div>

            <!-- Product Table -->
            <div class="table-container mt-4">
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
                                    <td><img src="<?= htmlspecialchars($row['image']) ?>" class="img-product" alt="product" onerror="this.src='https://cdn-icons-png.flaticon.com/512/1046/1046784.png'"></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                    <td>
                                        <?php if ($row['type'] == "Hot"): ?>
                                            <span class="badge-hot"><i class="fa-solid fa-fire-flame-curved"></i> Hot</span>
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
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-mug-empty fa-2x mb-2 d-block"></i> No products found. 
                                        <?php if (!empty($searchKeyword)): ?> Try a different keyword or <a href="product.php">clear search</a>.<?php endif; ?>
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

<!-- JS for popup and select all -->
<script>
    const btnAdd = document.querySelector(".add");
    const formCard = document.getElementById("productFormCard");
    const overlayDiv = document.getElementById("overlayBg");
    const cancelBtn = document.querySelector(".cancel");

    function showForm() {
        formCard.style.display = "block";
        overlayDiv.style.display = "block";
        document.body.style.overflow = "hidden";
    }
    function hideForm() {
        formCard.style.display = "none";
        overlayDiv.style.display = "none";
        document.body.style.overflow = "auto";
    }
    
    if (btnAdd) btnAdd.addEventListener("click", showForm);
    if (cancelBtn) cancelBtn.addEventListener("click", hideForm);
    if (overlayDiv) overlayDiv.addEventListener("click", hideForm);

    // Select all checkboxes functionality
    const selectAllCheck = document.getElementById("selectAllCheck");
    if (selectAllCheck) {
        selectAllCheck.addEventListener("change", function() {
            const checkboxes = document.querySelectorAll(".product-checkbox");
            checkboxes.forEach(cb => cb.checked = selectAllCheck.checked);
        });
    }
    
    // Additional cancel inside form
    const cancelInside = document.querySelector(".form .cancel");
    if(cancelInside) cancelInside.addEventListener("click", hideForm);
</script>
</body>
</html>