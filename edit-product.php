<?php
include "connection.php";

// =====================
// 1. GET DATA BY ID
// =====================
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $cn->prepare("SELECT * FROM tbl_products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

// =====================
// 2. UPDATE DATA
// =====================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {

    $id = $_POST['id']; 
    $productName = $_POST['p_name'];
    $category = $_POST['category'];
    $dstion = $_POST['pdescription'];
    $price = $_POST['price'];
    $sqty = $_POST['s_qty'];

    $stmt = $cn->prepare("
        UPDATE tbl_products SET 
            name=?, 
            category=?, 
            description=?, 
            price=?, 
            s_qty=?
        WHERE id=?
    ");

    $stmt->bind_param("sssdis",
        $productName, 
        $category, 
        $dstion, 
        $price, 
        $sqty, 
        $id
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Update success!'); window.location.href='product.php';</script>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Edit Product</h4>
        </div>

        <div class="card-body">
            <form method="POST">

                <!-- Hidden ID -->
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="p_name" class="form-control" 
                    value="<?php echo $row['name']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category" required>
                        <option value="Tea"        <?php if ($row['category']=="Tea") echo "selected"; ?>>Tea</option>
                        <option value="Milk Tea"   <?php if ($row['category']=="Milk Tea") echo "selected"; ?>>Milk Tea</option>
                        <option value="Coffee"     <?php if ($row['category']=="Coffee") echo "selected"; ?>>Coffee</option>
                        <option value="Fruit Drink"<?php if ($row['category']=="Fruit Drink") echo "selected"; ?>>Fruit Drink</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="pdescription" class="form-control" required><?php echo $row['description']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control" 
                    value="<?php echo $row['price']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Stock Qty</label>
                    <input type="number" name="s_qty" class="form-control" 
                    value="<?php echo $row['s_qty']; ?>" required>
                </div>

                <div class="mt-4">
                    <button type="submit" name="update" class="btn btn-success">💾 Save Changes</button>
                    <a href="product.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>