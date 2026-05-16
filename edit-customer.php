<?php
include "connection.php"; // must contain PDO $conn

// =====================
// 1. GET DATA BY ID
// =====================
if (!isset($_GET['id'])) {
    header("Location: customer.php");
    exit();
}

$id = $_GET['id'];

// Fetch product
$stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "customer not found!";
    exit();
}

// =====================
// 2. UPDATE DATA
// =====================
if (isset($_POST['update'])) {
    $name       = $_POST['name'];
    $phone      = $_POST['phone'];
    $email      = $_POST['email'];
    $address    = $_POST['address'];
    $newImage   = $_POST['image'];
    $created_at = $_POST['created_at'];
    $gender     = $_POST['gender'];
    $status     = $_POST['status'];

    // Keep old image if the new image input is empty
    $image = (empty($newImage)) ? $row['image'] : $newImage;

    // Corrected Table Name and Column names
    $stmt = $conn->prepare("
        UPDATE tbl_customer SET 
            name = ?, 
            phone = ?, 
            email = ?, 
            address = ?, 
            gender = ?, 
            created_at = ?, 
            status = ?, 
            image = ? 
        WHERE id = ?
    ");

    $updated = $stmt->execute([
        $name, $phone, $email, $address, $gender, $created_at, $status, $image, $id
    ]);

    if ($updated) {
        echo "<script>
            alert('✔️ Customer updated successfully!');
            window.location.href='customer.php';
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
<title>Edit Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
<div class="card shadow">

<div class="card-header bg-primary text-white">
    <h4>Edit Product</h4>
</div>

<div class="card-body">

<form method="POST">

    <!-- PRODUCT NAME -->
    <label>Customer Name</label>
    <input type="text" name="name" class="form-control mb-3"
        value="<?= $row['name'] ?>" required>

    <!-- TYPE -->
    <label>Gender</label>
    <select name="gender" class="form-select mb-3">
        <option <?= ($row['gender']=="Male")?"selected":"" ?>>Male</option>
        <option <?= ($row['gender']=="Felmale")?"selected":"" ?>>Felmale</option>
        
    </select>

    <!-- CATEGORY -->
    <label>Phone</label>
    <input type="text" name="phone" class="form-control mb-3"
        value="<?= $row['phone'] ?>" required>

    <!-- PRICE -->
    <label>Email</label>
    <input type="text" name="email" class="form-control mb-3"
        value="<?= $row['email'] ?>" required>

        <!-- CREATE AT -->
    <label>Create Date</label>
    <input type="date" name="created_at" class="form-control mb-3"
        value="<?= $row['created_at'] ?>" required>

    <!-- CREATE BY -->
    <label>Address</label>
    <input type="text" name="address" class="form-control mb-3"
        value="<?= $row['address'] ?>" required>

    

    <!-- STATUS -->
    <label>Status</label>
    <select name="status" class="form-select mb-3">
        <option <?= ($row['status']=="Active")?"selected":"" ?>>Active</option>
        <option <?= ($row['status']=="Inactive")?"selected":"" ?>>Inactive</option>
    </select>

    <!-- IMAGE -->
    <label>Image URL</label>
    <input type="text" name="image" class="form-control mb-3"
        value="<?= $row['image'] ?>">

    <!-- SHOW CURRENT IMAGE -->
    <?php if (!empty($row['image'])): ?>
        <img src="<?= $row['image'] ?>" width="120" class="mb-3 border">
    <?php endif; ?>

    <!-- BUTTONS -->
    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="customer.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>
</div>

</body>
</html>
