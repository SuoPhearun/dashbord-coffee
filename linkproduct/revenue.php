<?php
    $totalRv = 0;

$sql = "SELECT total_amount FROM tbl_order";
$res = $cn->query($sql);

while ($row = $res->fetch_array()) {
    $totalRv += $row['total_amount']; // or $row[5]
}
?>