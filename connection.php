<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "shop1";
$port = "3308"; // Port របស់អ្នកគឺ 3308

try {
    // បង្កើតការភ្ជាប់តាមបែប PDO
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // កំណត់ Error Mode ឱ្យបង្ហាញបញ្ហាច្បាស់ៗ
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // បង្កើត Variable $conn ទុកការពារក្រែងលោមានកូដផ្សេងហៅប្រើ
    $conn = $pdo; 

} catch (PDOException $e) {
    die("ការភ្ជាប់ Database បរាជ័យ: " . $e->getMessage());
}
?>