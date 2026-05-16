<?php
session_start();

// Check if form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Get form data
    $username = trim($_POST["txtuname"]);
    $password = trim($_POST["txtpwd"]);
    
    // Validate inputs
    if(empty($username) || empty($password)){
        header("location: index.php?error=Please enter username and password");
        exit;
    }
    
    // Database connection (example using MySQL)
    // Replace with your actual database credentials
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "login_system";
    
    try {
        $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
        
        // Check connection
        if($conn->connect_error){
            throw new Exception("Connection failed");
        }
        
        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 1){
            $user = $result->fetch_assoc();
            
            // Verify password (assuming passwords are hashed)
            if(password_verify($password, $user['password'])){
                // Set session variable - THIS IS YOUR CODE
                $_SESSION["txtuname"] = $username;
                $_SESSION["user_id"] = $user['id'];
                
                // Redirect to dashboard
                header("location: dashboard.php");
                exit;
            } else {
                header("location: index.php?error=Invalid username or password");
                exit;
            }
        } else {
            header("location: index.php?error=Invalid username or password");
            exit;
        }
        
        $stmt->close();
        $conn->close();
        
    } catch(Exception $e){
        // For demo purposes - simple validation
        // Remove this and use database in production
        if($username === "admin" && $password === "password123"){
            $_SESSION["txtuname"] = $username;
            header("location: dashboard.php");
            exit;
        } else {
            header("location: index.php?error=Invalid username or password");
            exit;
        }
    }
} else {
    // If someone tries to access this file directly
    header("location: index.php");
    exit;
}
?>