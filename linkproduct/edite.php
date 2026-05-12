<?php 

include "../connection.php";

$id = $_GET['id'];

$sql = "SELECT * FROM tbl_products WHERE id = $id";
$res = $cn->query($sql);
$row = $res->fetch_assoc();

if(isset($_POST['update'])){
    $name = $_POST['p-name'];
    $cat = $_POST['category'];
    $des = $_POST['dstion'];
    $price = $_POST['price'];
    $qty = $_POST['sqty'];

    $update = "UPDATE tbl_products 
                SET name='$name', category='$cat', description='$des', price='$price', s_qty='$qty'
                WHERE id=$id";

    $cn->query($update);
    exit();
}
include "../product.php";
?>

