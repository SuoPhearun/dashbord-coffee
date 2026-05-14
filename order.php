<?php include "connection.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    body{
        overflow-x: hidden;
    }
    .bar{
        /* background-image: linear-gradient(95deg, #4f772d,#90a955); */
        background-color: #00A296;
    }
    .men{
        padding: 0;
        margin: 0;
        width: 100%; 
    }
    .menu li{
        background-color:transparent;
        border:none;
        width: 100%;  
        transition: 0.3s linear;
        font-size:1.2rem;
        list-style-type: none;
        padding: 5px 10px;
    }
    .menu li a{
    display: flex;              
    align-items: center;
    gap: 10px;                  
    width: 100%;
    padding: 10px 15px;
    color: #e8e8e4;
    text-decoration: none;
    transition: 0.2s ease;
    border-radius: 6px;
    }
    .menu li:hover a {
    transform: translateX(15px);
}
    .menu li:hover {
        background-color:rgba(0,0,0,0.2);
        width: 100%;
        transform: translateX(11px);
    }
    img{
        width: 100px;
        position: relative;
        left: 50%;
        bottom: 45px;
        transform: translateX(-50%);
        border-radius: 50%;
    }
    .logo{
        display:flex;
        width: 100%;
        position: relative;
        top:20px
    }
    .text-head{
        position: absolute;
        bottom:45px;
        left: 40px;   
        color:#e8e8e4;
        font-size:18px;
    }
    .text-dashoard{
        color:#3E5C14;
    }
    .logout{
        position: absolute;
        left:50px;
        font-size:1.2rem;
        bottom: 30px;
        cursor: pointer;
    }
    .revenue-top,.revenue-center{
    position: relative;
    color:#fff;
    }
    .revenue-top i,.revenue-center i{
        position: absolute;
        top: 5px;
        left: 0;
    }
    .revenue-top p{
        position: relative;
        left:20px;
    }
    .revenue-center h5{
        position: relative;
        position: relative;
        left:20px;
    }
    .tea,.coffee{
        display:flex;

    }
</style>
</head>
<body>
    <div class="contaner">
        <div class="row">
            <div class="col-2 position-fixed vh-100  bar">
                <div class="logo py-5">
                    <img src="https://i.pinimg.com/736x/e5/2a/e3/e52ae301a1162863df9a68c532dd3e2e.jpg" alt="">
                    <h1 class=" text-head"> Admine Dashboard </h1>
                </div>
                <ul class="menu list-group">
                    <li class="li-list"><a class=" " href="index.php"><i class="fa-solid fa-house"></i>Main Dashboard</a></li>
                    <li class="li-list"><a class=" " href="product.php"><i class="fa-solid fa-box"></i> Menage Menu</a></li>
                    <li class="li-list"><a class=" " href="order.php"> <i class="fa-solid fa-cart-shopping"></i> Oders</a></li>
                    <li class="li-list"><a class=" " href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                    <li class="li-list"><a class=" " href="customer.php"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                    <li class="li-list"><a class=" " href="index.php"><i class="fa-solid fa-list"></i> Report</a></li>
                    <li class="li-list"><a class=" " href="index.php"><i class="fa-solid fa-gear"></i> setting</a></li>
                </ul>
                <div class="logout text-white">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    logout
                </div>
            </div>
            <div class="col-10 position-relative offset-2 p-3">
                <div class="hader-dashoard">
                    <h4 class="text-dashoard">Order List</h4>
                </div>
                
                
        
            <?php
                $sqls = "SELECT * FROM tbl_order";
                $res = $cn->query($sqls);
            ?>

            <div class="container mt-4">

                <table class="table table-hover">
                    <thead class="">
                        <tr>
                            <th>id</th>
                            <th>Customer Name</th>
                            <th>Item</th>
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>Statur</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $totalRv = 0; 
                        while($row = $res->fetch_array()){ 
                            $totalRv += $row[4]; // Correct index                             
                            echo "
                            <tr>
                                <td>{$row[0]}</td>
                                <td>{$row[1]}</td>
                                <td>{$row[2]}</td>
                                <td>{$row[3]}</td>
                                <td>\${$row[4]}</td>
                                <td>{$row[5]}</td>
                            </tr>
                            ";
                        }                                            
                        ?>

                        <?php include "linkproduct/revenue.php"; ?>
                    </tbody>
                </table>
            </div>
            </div>
    </div>
</body>
</html>