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
        background-image: linear-gradient(95deg, #4f772d,#90a955);
    }
    .menu li{
        background-color:transparent;
        border:none;
        transition: 0.2s linear;
        font-size:1.2rem;
        padding-left: 15px;
    }
    .menu li a{
        text-decoration: none;
        color:#e8e8e4;
    }
    .menu li:hover{
        background-color:#6a994e;
        border-radius: 5px;
        color:#3E5C14;
    }
    img{
        width: 80px;
        position: relative;
        bottom: 5px;
        left: 15px;
    }
    .logo{
        display:flex;
        border-bottom:1px solid #fff;
        align-items: center;
        position: relative;
        top:0;
    }
    .text-head{
        position: absolute;
        bottom:10px;
        left: 85px;
        color:#3E5C14;
    }
    .text-dashoard{
        color:#3E5C14;
    }
    .logout{
        position: absolute;
        left:50px;
        font-size:1.2rem;
        bottom: 60px;
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
            <div class="col-2 position-fixed bg-light vh-100 p-3 bar">
                <div class="logo">
                <img src="teas.png" alt="">
                <h4 class="text-center text-head">Tea SHOP</h4>
                </div>
                <ul class="list-group menu  mx-3 my-3">
                    <li class="list-group-item"><a class="text-center " href="index.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                    <li class="list-group-item"><a class="text-center " href="order.php"> <i class="fa-solid fa-cart-shopping"></i> Oders</a></li>
                    <li class="list-group-item"><a class="text-center " href="product.php"><i class="fa-solid fa-box"></i> Products</a></li>
                    <li class="list-group-item"><a class="text-center " href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                    <li class="list-group-item"><a class="text-center " href="index.php"><i class="fa-solid fa-list"></i> Report</a></li>
                </ul>
                <div class="logout text-white">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    logout
                </div>
            </div>
            <div class="col-10 position-relative offset-2 p-3 right-bar">
                <div class="hader-dashoard">
                    <h4 class="text-dashoard">Dashboard</h4>
                </div>
                <div class="row p-3 ">
                    <div class="col-4">
                        <div class="bar rounded p-3">
                            <div class="revenue-top ">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <p>Total Revenue</p>
                                
                            </div>
                            <div class="revenue-center">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <h5>
                                <?php 
                                include "linkproduct/revenue.php"; 
                                echo $totalRv; 
                                ?>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="bar rounded p-3">
                            <div class="revenue-top ">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <p>Total Orders</p>                              
                            </div>
                            <div class="revenue-center">
                                <h5>
                                <?php 
                                include "linkproduct/totalorder.php"; 
                                echo $totalOrders; 
                                ?>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="bar rounded p-3"><div class="revenue-top ">
                                <i class="fa-solid fa-box-open"></i>
                                <p>Total Products</p>
                                
                            </div>
                            <div class="revenue-center">
                                <div class="tea">
                                    <h5 class="px-2">Tea: </h5>
                                    <h5>
                                        <?php 
                                        include "linkproduct/totalproduct.php"; 
                                        echo $totalTea; 
                                        ?>
                                    </h5>
                                </div>
                                <div class="coffee">
                                    <h5 class="px-2">coffee: </h5>
                                    <h5>
                                        <?php
                                            echo $totalCoffee;
                                        ?>
                                    </h5>
                                </div>
                                <div class="coffee">
                                    <h5 class="px-2">Milk Tea: </h5>
                                    <h5>
                                        <?php
                                            echo $totalMilkTea;
                                        ?>
                                    </h5>
                                </div>
                                <div class="coffee">
                                    <h5 class="px-2">Fruit Drink: </h5>
                                    <h5>
                                        <?php
                                            echo $totalFruitDrink;
                                        ?>
                                    </h5>
                                </div>
                                <div class="coffee">
                                    <h5 class="px-2">Total Products: </h5>
                                    <h5>
                                        <?php
                                            echo $grandTotal;
                                        ?>
                                    </h5>
                                </div>
                            </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>