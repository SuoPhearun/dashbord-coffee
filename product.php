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
    $type = $_POST['type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $create_by = $_POST['create_by'];
    $create_at = $_POST['create_at'];
    $status = $_POST['status'];

    // IMAGE UPLOAD
    $image = $_POST['image'];

    $sql = "INSERT INTO tbl_products 
    (name, type, category, price, create_by, create_at, status, image)
    VALUES 
    ('$productName','$type','$category','$price','$create_by','$create_at','$status','$image')";

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
    }
    .menu li:hover a {
    transform: translateX(15px);
}
    .menu li:hover {
        background-color:rgba(0,0,0,0.2);
        width: 100%;
        transform: translateX(11px);
    }
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
    .hader-dashoard{
        display:flex;
        justify-content: space-between;
    }
    .img{
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
    .form{
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        top:30px;
    }
    .img-product{
        width:60px;
    height:60px;
    object-fit:cover;
    border-radius:5px;
    }
</style>
</head>
<body>
    <div class="contaner">
        <div class="row">
            <div class="col-2 position-fixed vh-100  bar">
                <div class="logo py-5">
                    <img class="img" src="https://i.pinimg.com/736x/e5/2a/e3/e52ae301a1162863df9a68c532dd3e2e.jpg" alt="">
                    <h1 class=" text-head"> Admine Dashboard </h1>
                </div>
                <ul class="menu list-group">
                    <li class=""><a href="index.php"><i class="fa-solid fa-house"></i>Main Dashboard</a></a></li>
                    <li class=""><a class=" " href="product.php"><i class="fa-solid fa-box"></i> Menage Menu</a></li>
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
            <div class="col-10 position-relative offset-2 p-3 px-5">
                <div class="hader-dashoard col-11 p-3 mx-auto my-4 ">
                    <h2 class="text-dashoard">Product List</h2>
                    <button type="button" class="btn btn-outline-success add">+ Add New Item</button>
                </div>
                <div class="card col-9 form mx-auto my-5 shadow p-4"​ style="display: <?= $showForm ? 'none' : 'block' ?>;">               
                    <form method="POST">

                        <input type="text" name="p-name" class="form-control mb-2" placeholder="Product Name">

                        <select name="type" class="form-select mb-2">
                            <option>Hot</option>
                            <option>Cold</option>
                        </select>

                        <select name="category" class="form-select mb-2">
                            <option>Tea</option>
                            <option>Coffee</option>
                            <option>Matcha</option>
                        </select>

                        <input type="number" name="price" class="form-control mb-2" placeholder="Price" step="0.01">

                        <select name="create_by" class="form-select mb-2">
                            <option>Admine</option>
                            <option>Staff</option>
                        </select>

                        <input type="date" name="create_at" class="form-control mb-2">

                        <select name="status" class="form-select mb-2">
                            <option>Publish</option>
                            <option>Draft</option>
                        </select>

                        <input type="text" name="image" class="form-control mb-2" placeholder="Image URL (https://...)">

                        <button class="btn btn-primary" name="save">Save</button>
                        <button type="button" class="btn btn-secondary cancel">Cancel</button>

                    </form>
                </div>
                

    <div class="col-11 mx-auto my-5 shadow p-4 table-container"> 
    <table class="table table-bordered table-hover ">
<thead>
<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Name</th>
    <th>Type</th>
    <th>Category</th>
    <th>Price</th>
    <th>Create By</th>
    <th>Create At</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>

    <td>
        <?php if (!empty($row['image'])): ?>
            <img src="<?= $row['image'] ?>" class="img-product">
        <?php else: ?>
            No Image
        <?php endif; ?>
    </td>

    <td><?= $row['name'] ?></td>
    <td><?= $row['type'] ?></td>
    <td><?= $row['category'] ?></td>
    <td>$<?= $row['price'] ?></td>
    <td><?= $row['create_by'] ?></td>
    <td><?= $row['create_at'] ?></td>

    <td>
        <?php if($row['status'] == "Publish"): ?>
            <span class="badge bg-success">Publish</span>
        <?php else: ?>
            <span class="badge bg-danger">Draft</span>
        <?php endif; ?>
    </td>

    <td>
        <a href="edit-product.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
            <i class="fa fa-edit"></i>
        </a>

         <a href="delete-product.php?id=<?= $row['id'] ?>" 
       class="btn btn-danger btn-sm"
       onclick="return confirm('Are you sure to delete this product?')">
        <i class="fa fa-trash"></i>
    </a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

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