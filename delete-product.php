<?php
include "connection.php";

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    // OPTIONAL: get image before delete (if you want remove file later)
    $stmt = $cn->prepare("SELECT image FROM tbl_products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    // DELETE PRODUCT
    $stmt = $cn->prepare("DELETE FROM tbl_products WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        // OPTIONAL: delete image file if local upload
        if (!empty($row['image']) && file_exists($row['image'])) {
            unlink($row['image']);
        }

        echo "<script>
            alert('✅ Product deleted successfully');
            window.location.href='product.php';
        </script>";

    } else {
        echo "Error deleting: " . $stmt->error;
    }
}
?>