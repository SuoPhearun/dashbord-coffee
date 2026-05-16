<?php
session_start();

// YOUR CODE - Check if user is logged in
if(!isset($_SESSION["txtuname"])){
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar h1 {
            font-size: 24px;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .welcome-card h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            color: #666;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .info-card h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .info-card p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION["txtuname"]); ?>!</h2>
            <p>You have successfully logged in to your account.</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Profile Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION["txtuname"]); ?></p>
                <p><strong>Status:</strong> Active</p>
                <p><strong>Login Time:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>
            </div>
            
            <div class="info-card">
                <h3>System Status</h3>
                <p>✓ Session Active</p>
                <p>✓ Authentication Verified</p>
                <p>✓ Access Granted</p>
            </div>
            
            <div class="info-card">
                <h3>Quick Actions</h3>
                <p>• Update Profile</p>
                <p>• Change Password</p>
                <p>• View Settings</p>
            </div>
        </div>
    </div>
</body>
</html>