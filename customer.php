<?php 
include "connection.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === ១. កូដសម្រាប់ INSERT ទិន្នន័យ (បន្ថែមអតិថិជនថ្មី) ===
if (isset($_POST['btn_save'])) {
    $name    = $_POST['customer_name'];
    $phone   = $_POST['phone'];
    $email   = $_POST['email'];
    $address = $_POST['address'];
    $image = $_POST['image'];
    $created_at = $_POST['created_at'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];

    $sql_insert = "INSERT INTO tbl_customer (name, phone, email, address,image,created_at,gender,status) 
                   VALUES (:name, :phone, :email, :address,:image ,:created_at, :gender, :status)";
    $stmt_ins = $conn->prepare($sql_insert);
    $stmt_ins->execute([
        ':name'    => $name,
        ':phone'   => $phone,
        ':email'   => $email,
        ':address' => $address,
        ':image'   => $image,
        ':created_at'   => $created_at,
        ':gender'   => $gender,
        ':status'   => $status
    ]);
    
    // បន្ទាប់ពី Save រួច វានឹង Refresh ទំព័រដើម្បីបង្ហាញទិន្នន័យថ្មី
    header("Location: customer.php"); 
    exit;
}

// === ២. កូដសម្រាប់ SELECT ទិន្នន័យ (ស្វែងរក និង បង្ហាញ) ===
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
}

$sqls = "SELECT * FROM tbl_customer";
$params = [];

if (!empty($searchKeyword)) {
    $sqls .= " WHERE name LIKE :search 
               OR email LIKE :search 
               OR phone LIKE :search";
    $params[':search'] = "%$searchKeyword%";
}

$sqls .= " ORDER BY id DESC";
$stmt = $conn->prepare($sqls);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$totalCustomers = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Page | Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    /* រក្សាស្ទាយដើមរបស់អ្នក (Sidebar) */
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
            font-size: 1.1rem;
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
        .menu li.active {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
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
        .logout a{
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
            text-decoration: none;
        }
        .logout a:hover {
            color: white;
            transform: translateX(5px);
        }
    /* UI ថ្មីតាមរូបភាព Screenshot 2026-05-14 175619.jpg */
    .main-content { padding: 2rem; }
    .content-card { background: white; border-radius: 20px; border: 1px solid #eee; overflow: hidden; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .table-header { padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; }
    /* រចនាប្រអប់ស្វែងរកឱ្យដូចរូបភាព */
    .search-container {
        background: #fff;
        border: 1px solid #d1d5db; /* ពណ៌ខ្សែកោងខាងក្រៅ */
        border-radius: 50px; /* ធ្វើឱ្យរាងមូលទ្រវែង */
        display: flex;
        align-items: center;
        padding: 5px 5px 5px 20px;
        width: 100%;
        max-width: 500px; /* អ្នកអាចកំណត់ទំហំតាមចិត្ត */
        transition: box-shadow 0.3s ease;
    }

    .search-container:focus-within {
        box-shadow: 0 0 0 3px rgba(0, 162, 150, 0.1);
        border-color: #00A296;
    }

    .search-container i.fa-magnifying-glass {
        color: #ffffff;
        font-size: 1.1rem;
    }

    .search-container input {
        border: none;
        outline: none;
        padding: 10px 15px;
        flex-grow: 1;
        font-size: 1rem;
        color: #4b5563;
        background: transparent;
    }

    .search-container .btn-search-style {
        background-color: #00897B; /* ពណ៌បៃតងដិតដូចក្នុងរូប */
        color: white;
        border: none;
        border-radius: 40px;
        padding: 8px 25px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
        cursor: pointer;
    }

    .search-container .btn-search-style:hover {
        background-color: #00695C;
    }


    .btn-clear-search {
        padding: 0 8px;
        color: #9ca3af;
        margin-right: 10px;
        text-decoration: none;
    }
    
    .btn-clear-search:hover {
        color: #ef4444;
    }
    .table th { background-color: #fcfcfc; color: #777; font-size: 12px; padding: 15px 20px; }
    .table td { vertical-align: middle; padding: 15px 20px; border-bottom: 1px solid #f8f8f8; }
    .btn-new { background-color: #00A296; color: white; border-radius: 8px; border: none; padding: 8px 18px; font-weight: 500; }
     .img-product {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 14px;
            background: #faf3e0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border: 1px solid #ede7dc;
        }
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

    <!-- SIDEBAR (មិនផ្លាស់ប្ដូរ) -->
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
                <li><a href="analytics.php"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                <li><a href="reprot.php"><i class="fa-solid fa-list"></i> Report</a></li>
                <li><a href="setting.php"><i class="fa-solid fa-gear"></i> Setting</a></li>
            </ul>
            <div class="logout text-white">
                <a href="login.php">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> logout
                </a>
                
            </div>
        </div>

    <!-- MAIN CONTENT -->
    <div class="col-10 offset-2 main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Customers List</h3>
            <!-- ប៊ូតុងបើក Modal សម្រាប់ INSERT -->
            <button class="btn-new" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus me-2"></i>New Customer
            </button>
        </div>

        <div class="content-card">
            <div class="table-header">
                <div>
                    <strong>Total Customers: <?= $totalCustomers ?></strong>
                </div>
                <form method="GET" action="customer.php" class="search-container">
                    <!-- Icon កែវពង្រីកខាងឆ្វេង -->
                    <i class="fa-solid fa-magnifying-glass"></i>
                    
                    <!-- ប្រអប់បញ្ចូលអត្ថបទ -->
                    <input type="text" name="search" placeholder="Search by name, Email, Phone..." 
                        value="<?= htmlspecialchars($searchKeyword) ?>">

                    <!-- ប៊ូតុង Search ពណ៌បៃតងខាងស្តាំ -->
                    <button type="submit" class="btn-search-style">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                    <!-- ប៊ូតុង Clear (បង្ហាញពេលមានពាក្យ Search) -->
                    <?php if (!empty($searchKeyword)): ?>
                        <a href="customer.php" class="btn-clear-search" title="Clear search">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>


            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Customer Name</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($row['image']) ?>" class="img-product" alt="product" onerror="this.src='https://cdn-icons-png.flaticon.com/512/1046/1046784.png'">
                            </td>
                            <td>
                                <div class="fw-bold"><?= $row['name'] ?></div>
                            </td>
                            <td><?= $row['gender'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td><?= $row['address'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td class="text-end">
                                <!-- ប៊ូតុង Edit -->
                                <a href="edit-customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <!-- ប៊ូតុង Delete -->
                                <a href="delete-customer.php?id=<?= $row['id'] ?>" 
                                class="btn btn-sm btn-outline-danger" 
                                title="Delete"
                                onclick="return confirm('តើអ្នកប្រាកដថាចង់លុបអតិថិជននេះមែនទេ?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>


<!-- === MODAL សម្រាប់បញ្ចូលអតិថិជនថ្មី (INSERT) === -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Customer Name</label>
          <input type="text" name="customer_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Gender</label>
            <select name="gender" class="form-select mb-2">
                    <option>Male</option>
                    <option>Felmale</option>
            </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Creat At</label>
          <input type="date" name="created_at" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Profile</label>
          <input type="text" name="image" class="form-control mb-2" placeholder="Image URL (https://...)" value="https://cdn-icons-png.flaticon.com/512/1046/1046784.png">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Status</label>
            <select name="status" class="form-select mb-2">
                        <option>Active</option>
                        <option>Inactive</option>
            </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="btn_save" class="btn btn-primary">Save Customer</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
