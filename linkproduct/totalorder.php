<?php
$sql = "SELECT COUNT(*) AS total_order FROM tbl_order";
$res = $cn->query($sql);
$row = $res->fetch_assoc();

$totalOrders = $row['total_order'];
?>