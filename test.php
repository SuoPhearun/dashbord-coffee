<?php include "connection.php"; ?>

<?php
if (isset($_POST['save'])) {

    $customer = $_POST['customer'];
    $product = $_POST['product'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];

    $total = $qty * $price;

    $sql = "INSERT INTO tbl_orders (customer, product, qty, price, total)
            VALUES ('$customer', '$product', '$qty', '$price', '$total')";

    $cn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h3 class="text-success mb-4">Add New Order</h3>

    <!-- ORDER FORM -->
    <div class="card p-4 shadow-sm">
        <form method="post">

            <div class="mb-3">
                <label>Customer Name</label>
                <input type="text" name="customer" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="product" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" name="qty" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Price</label>
                <input type="number" name="price" class="form-control" required>
            </div>

            <button class="btn btn-success" name="save">Save Order</button>

        </form>
    </div>

    <hr>

    <!-- ORDER LIST -->
    <h4 class="mt-4">Order List</h4>

    <table class="table table-bordered table-hover text-center">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $sql = "SELECT * FROM tbl_orders";
            $res = $cn->query($sql);

            while ($row = $res->fetch_array()) {
                echo "
                <tr>
                    <td>$row[0]</td>
                    <td>$row[1]</td>
                    <td>$row[2]</td>
                    <td>$row[3]</td>
                    <td>$row[4]</td>
                    <td>$row[5]</td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>

</div>

</body>
</html>