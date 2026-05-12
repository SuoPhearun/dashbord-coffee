<?php include "connection.php"; ?>

<?php
session_start();
$productName=" ";
$dstion=" ";
$category=" ";
$price=" ";
$sqty=" ";
$showdata = "";

$showForm = false;

if (isset($_POST['save'])) {
    $productName = $_POST['p-name'];
    $category = $_POST['category'];
    $dstion = $_POST['dstion'];
    $price = $_POST['price'];
    $sqty = $_POST['sqty'];         
    $sql = "INSERT INTO tbl_products (name, category, description, price, s_qty) 
            VALUES ('$productName','$category','$dstion','$price','$sqty')";
    $cn->query($sql);

    header("Location: product.php");
    exit();
    }
    $sqls = "SELECT * FROM tbl_products";
    $res = $cn->query($sqls);  
    $showForm = true;

?>

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
    .hader-dashoard{
        display:flex;
        justify-content: space-between;
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
    .form{
        position: absolute;
        z-index: 100;
        left: 50%;
        transform: translateX(-50%);
        top:40px;
        
    }
          /* ----table---- */
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .table-container thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
            border-bottom: 2px solid #dee2e6;
            z-index: 2;
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
                <ul class="list-group menu mx-3 my-3">
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
            <div class="col-10 position-relative offset-2 p-3 px-5">
                <div class="hader-dashoard col-11 p-3 mx-auto my-4 ">
                    <h2 class="text-dashoard">Product List</h2>
                    <button type="button" class="btn btn-outline-success add">+ Add New Item</button>
                </div>
                <div class="card col-9 form mx-auto my-5 shadow p-4"​ style="display: <?= $showForm ? 'none' : 'block' ?>;">               
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" placeholder="Enter product name" name="p-name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option>Tea</option>
                                <option>Milk Tea</option>
                                <option>Coffee</option>
                                <option>Fruit Drink</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" placeholder="Write product details..." name="dstion"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="number" class="form-control" name="price" step="0.01">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" name="sqty">
                        </div>

                        <button class="btn btn-primary" name="save">Save Product</button>
                        <button class="btn btn-info text-white cancel" type="button">Cancel</button>

                    </form>
                </div>
                

    <div class="col-11 mx-auto my-5 shadow p-4 table-container"> 
    <table class="table table-hover table-bordered table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>Name</th>
                <th>Category</th>                
                <th>Price</th>
                <th>Qty</th>
                <th>active</th>
            </tr>
        </thead>
            
        <?php
            while($row = $res->fetch_array()){   
                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['category']}</td>
                    <td>\${$row['price']}</td>
                    <td>{$row['s_qty']}</td>
                    <td>
                                            <a href='edit-product.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm' title='Edit'>
                                                <i class='fas fa-edit'></i>
                                            </a>
                                            <a href='delete_employee.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure you want to delete this employee?\")'>
                                                <i class='fas fa-trash'></i>
                                            </a>
                                            </a>
                                        </i>
                                        </a>


                    
                </tr>
                ";
            }
        ?>
            
    </table>
</div>
    
</div>
            </div>
        </div>
    </div>
</body>

<script>
    const btnAddEl = document.querySelector(".add");
    const formEl = document.querySelector(".form");
    const cancelEl = document.querySelector(".cancel");
    const d = document.querySelectorAll('body');
    btnAddEl.addEventListener("click",()=>{
    formEl.style.display = "block";
    d.style.opacity='0.6';
})
cancelEl.addEventListener("click",()=>{
    formEl.style.display = "none";
})
</script>
</html>