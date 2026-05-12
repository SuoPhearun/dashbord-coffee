
<?php
$sql = "SELECT category, SUM(s_qty) AS total_qty 
        FROM tbl_products 
        GROUP BY category";

$res = $cn->query($sql);

$totalTea = 0;
$totalCoffee = 0;
$totalMilkTea = 0;
$totalFruitDrink = 0;
$grandTotal = 0;
while($row = $res->fetch_assoc()){
    $grandTotal += $row['total_qty'];
    if($row['category'] == 'Tea'){
        $totalTea = $row['total_qty'];
    }

    if($row['category'] == 'Coffee'){
        $totalCoffee = $row['total_qty'];
    }

    if($row['category'] == 'Milk Tea'){
        $totalMilkTea = $row['total_qty'];
    }

    if($row['category'] == 'Fruit Drink'){
        $totalFruitDrink = $row['total_qty'];
    }
}
?>