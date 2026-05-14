<?php
include "connection.php";

// =====================
// GET DATA BY ID
// =====================
if (!isset($_GET['id'])) {
    header("Location: product.php");
    exit();
}

$id = $_GET['id'];

$stmt = $cn->prepare("SELECT * FROM tbl_products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// =====================
// UPDATE DATA
// =====================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {

    $productName = $_POST['p_name'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $create_by = $_POST['create_by'];
    $create_at = $_POST['create_at'];
    $status = $_POST['status'];
    $newImage = $_POST['image'];

    // 🔥 GET OLD IMAGE FROM DB (FIXED)
    $stmt2 = $cn->prepare("SELECT image FROM tbl_products WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $oldRow = $res2->fetch_assoc();

    $image = (!empty($newImage)) ? $newImage : $oldRow['image'];

    // UPDATE QUERY
    $stmt = $cn->prepare("
        UPDATE tbl_products SET 
            name=?,
            type=?,
            category=?,
            price=?,
            create_by=?,
            create_at=?,
            status=?,
            image=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sssdssssi",
        $productName,
        $type,
        $category,
        $price,
        $create_by,
        $create_at,
        $status,
        $image,
        $id
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Update Success');
            window.location.href='product.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
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

    <!-- ID -->
    <input type="hidden" name="id" value="<?= $row['id'] ?>">

    <!-- NAME -->
    <input type="text" name="p_name" class="form-control mb-2"
        value="<?= $row['name'] ?>" placeholder="Product Name">

    <!-- TYPE -->
    <select name="type" class="form-select mb-2">
        <option <?= ($row['type']=="Hot")?"selected":"" ?>>Hot</option>
        <option <?= ($row['type']=="Cold")?"selected":"" ?>>Cold</option>
        <option <?= ($row['type']=="Ice")?"selected":"" ?>>Ice</option>
    </select>

    <!-- CATEGORY -->
    <select name="category" class="form-select mb-2">
        <option <?= ($row['category']=="Tea")?"selected":"" ?>>Tea</option>
        <option <?= ($row['category']=="Coffee")?"selected":"" ?>>Coffee</option>
        <option <?= ($row['category']=="Matcha")?"selected":"" ?>>Matcha</option>
    </select>

    <!-- PRICE -->
    <input type="number" name="price" class="form-control mb-2"
        value="<?= $row['price'] ?>" placeholder="Price" step="0.01">

    <!-- CREATE BY -->
    <select name="create_by" class="form-select mb-2">
        <option <?= ($row['create_by']=="Admine")?"selected":"" ?>>Admine</option>
        <option <?= ($row['create_by']=="Staff")?"selected":"" ?>>Staff</option>
    </select>

    <!-- CREATE AT -->
    <input type="date" name="create_at" class="form-control mb-2"
        value="<?= $row['create_at'] ?>">

    <!-- STATUS -->
    <select name="status" class="form-select mb-2">
        <option <?= ($row['status']=="Publish")?"selected":"" ?>>Publish</option>
        <option <?= ($row['status']=="Draft")?"selected":"" ?>>Draft</option>
    </select>

    <!-- IMAGE URL -->
    <input type="text" name="image" class="form-control mb-2"
        value="<?= $row['image'] ?>" placeholder="Image URL">

    <!-- SHOW CURRENT IMAGE -->
    <?php if (!empty($row['image'])): ?>
        <img src="<?= $row['image'] ?>" width="80" class="mb-3">
    <?php endif; ?>

    <!-- BUTTON -->
    <button type="submit" name="update" class="btn btn-success">
        Update Product
    </button>

    <a href="product.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

</div>

</body>
</html>