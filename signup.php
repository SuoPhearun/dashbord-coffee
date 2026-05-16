<?php
include "connection.php";

if(isset($_POST['signup'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // ពិនិត្យមើលថាពាក្យសម្ងាត់ផ្គូផ្គងគ្នាឬទេ
    if($password !== $confirm_password){
        echo "<script>alert('ពាក្យសម្ងាត់មិនត្រូវគ្នា!');</script>";
    } else {
        // ពិនិត្យមើលថាអ៊ីមែលមានរួចហើយឬនៅ
        $check_email = $conn->prepare("SELECT email FROM tbl_login WHERE email = :email");
        $check_email->execute([':email' => $email]);
        
        if($check_email->rowCount() > 0){
            echo "<script>alert('អ៊ីមែលនេះមានរួចហើយ! សូមប្រើអ៊ីមែលផ្សេង។');</script>";
        } else {
            // Hash ពាក្យសម្ងាត់មុនរក្សាទុក
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // បញ្ចូលទិន្នន័យថ្មី
            $sql = $conn->prepare("INSERT INTO tbl_login (name, email, password) VALUES (:name, :email, :password)");
            $result = $sql->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed_password
            ]);
            
            if($result){
                echo "<script>
                    alert('បង្កើតគណនីដោយជោគជ័យ! សូមចូលប្រើប្រាស់។');
                    window.location='login.php';
                </script>";
            } else {
                echo "<script>alert('មានបញ្ហាក្នុងការបង្កើតគណនី។ សូមព្យាយាមម្តងទៀត។');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Coffee Shop Admin</title>
    <style>
        :root {
            --primary-color: #00A296;
            --hover-color: #00847b;
        }
        *{
            box-sizing: border-box;
        }
        body {
            background: radial-gradient(circle at center, #ffffff 0%, #e0f2f1 100%);
            font-family: 'Khmer OS Battambang', 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100vw;
            height: 100vh;
            margin: 0;       
            position: relative;
            overflow: hidden;
        }

        .boll {
            position: absolute;
            border-radius: 50%;
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, rgba(0, 162, 150, 0.2), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(15px);
            box-shadow: 0 0 50px rgba(0, 162, 150, 0.2), 
                        inset 0 0 30px rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: myAnimation 8s infinite alternate ease-in-out;
            z-index: 1; 
        }

        .b1 { top: -100px; left: -100px; animation-delay: 0s; }
        .b2 { bottom: -150px; right: -150px; animation-delay: -2s; width: 500px; height: 500px; }
        .b3 { bottom: 10%; left: 10%; animation-delay: -4s; width: 300px; height: 300px; }
        .b4 { top: 5%; right: 15%; animation-delay: -1s; width: 250px; height: 250px; }

        @keyframes myAnimation {
            from {
                transform: translate(0, 0) scale(1);
                background-color: rgba(6, 173, 161, 0.15);
            }
            to {
                transform: translate(30px, -30px) scale(1.1);
                background-color: rgba(0, 132, 123, 0.25);
                box-shadow: 0 0 80px rgba(0, 162, 150, 0.4);
            }
        }

        .signup-container {
            position: relative; 
            z-index: 10;
            padding: 2.5rem;
            border-radius: 24px; 
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            width: 90%;
            max-width: 450px;
        }

        .signup-header h2, .signup-header p {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 5px;
            text-align:center;
        }
        
        .signup-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .signup-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
            margin-left: 5px;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 162, 150, 0.3);
            border-radius: 12px;
            outline: none;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        input:focus {
            border-color: var(--primary-color);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 162, 150, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            transition: all 0.3s;
            box-shadow: 0 8px 15px rgba(0, 162, 150, 0.2);
        }

        button:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 162, 150, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .signup-container {
                padding: 1.5rem;
                margin: 20px;
            }
            .signup-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
<div class="boll b1"></div>
<div class="boll b2"></div>
<div class="boll b3"></div>
<div class="boll b4"></div>

<div class="signup-container">
    <div class="signup-header">
        <h2>☕ បង្កើតគណនី</h2>
        <p>ចុះឈ្មោះដើម្បីគ្រប់គ្រងហាងកាហ្វេ</p>
    </div>

    <form action="" method="POST">
        <div class="form-group">
            <label>👤 ឈ្មោះពេញ</label>
            <input type="text" name="name" required placeholder="បញ្ចូលឈ្មោះរបស់អ្នក">
        </div>
        <div class="form-group">
            <label>📧 អ៊ីមែល</label>
            <input type="email" name="email" required placeholder="បញ្ចូលអ៊ីមែលរបស់អ្នក">
        </div>
        <div class="form-group">
            <label>🔒 ពាក្យសម្ងាត់</label>
            <input type="password" name="password" required placeholder="បញ្ចូលពាក្យសម្ងាត់">
        </div>
        <div class="form-group">
            <label>🔒 បញ្ជាក់ពាក្យសម្ងាត់</label>
            <input type="password" name="confirm_password" required placeholder="បញ្ចូលពាក្យសម្ងាត់ម្តងទៀត">
        </div>
        <button type="submit" name="signup">ចុះឈ្មោះ</button>
    </form>
    
    <div class="login-link">
        <p>មានគណនីរួចហើយ? <a href="login.php">ចូលប្រើប្រាស់</a></p>
    </div>
</div>

</body>
</html>