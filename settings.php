<?php
// ========== CONNECTION ==========
include "connection.php"; // $conn is your PDO object

// ========== PROCESS SETTINGS UPDATES ==========
$success_message = '';
$error_message = '';

// Update Shop Settings
if (isset($_POST['update_shop_settings'])) {
    $shop_name = $_POST['shop_name'];
    $shop_email = $_POST['shop_email'];
    $shop_phone = $_POST['shop_phone'];
    $shop_address = $_POST['shop_address'];
    $shop_tax_id = $_POST['shop_tax_id'];
    $currency = $_POST['currency'];
    $timezone = $_POST['timezone'];
    
    try {
        // Check if settings exist
        $checkQuery = $conn->prepare("SELECT id FROM tbl_settings WHERE setting_key = 'shop_settings'");
        $checkQuery->execute();
        
        $settingsData = json_encode([
            'shop_name' => $shop_name,
            'shop_email' => $shop_email,
            'shop_phone' => $shop_phone,
            'shop_address' => $shop_address,
            'shop_tax_id' => $shop_tax_id,
            'currency' => $currency,
            'timezone' => $timezone
        ]);
        
        if ($checkQuery->rowCount() > 0) {
            $updateQuery = $conn->prepare("UPDATE tbl_settings SET setting_value = :value WHERE setting_key = 'shop_settings'");
        } else {
            $updateQuery = $conn->prepare("INSERT INTO tbl_settings (setting_key, setting_value) VALUES ('shop_settings', :value)");
        }
        
        $updateQuery->bindParam(':value', $settingsData);
        if ($updateQuery->execute()) {
            $success_message = "Shop settings updated successfully!";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating settings: " . $e->getMessage();
    }
}

// Update Notification Settings
if (isset($_POST['update_notification_settings'])) {
    $order_notifications = isset($_POST['order_notifications']) ? 1 : 0;
    $low_stock_alerts = isset($_POST['low_stock_alerts']) ? 1 : 0;
    $daily_summary = isset($_POST['daily_summary']) ? 1 : 0;
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $low_stock_threshold = $_POST['low_stock_threshold'];
    
    try {
        $notificationData = json_encode([
            'order_notifications' => $order_notifications,
            'low_stock_alerts' => $low_stock_alerts,
            'daily_summary' => $daily_summary,
            'email_notifications' => $email_notifications,
            'low_stock_threshold' => $low_stock_threshold
        ]);
        
        $checkQuery = $conn->prepare("SELECT id FROM tbl_settings WHERE setting_key = 'notification_settings'");
        $checkQuery->execute();
        
        if ($checkQuery->rowCount() > 0) {
            $updateQuery = $conn->prepare("UPDATE tbl_settings SET setting_value = :value WHERE setting_key = 'notification_settings'");
        } else {
            $updateQuery = $conn->prepare("INSERT INTO tbl_settings (setting_key, setting_value) VALUES ('notification_settings', :value)");
        }
        
        $updateQuery->bindParam(':value', $notificationData);
        if ($updateQuery->execute()) {
            $success_message = "Notification settings updated successfully!";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating notifications: " . $e->getMessage();
    }
}

// Update Appearance Settings
if (isset($_POST['update_appearance'])) {
    $theme_color = $_POST['theme_color'];
    $sidebar_color = $_POST['sidebar_color'];
    $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
    
    try {
        $appearanceData = json_encode([
            'theme_color' => $theme_color,
            'sidebar_color' => $sidebar_color,
            'dark_mode' => $dark_mode
        ]);
        
        $checkQuery = $conn->prepare("SELECT id FROM tbl_settings WHERE setting_key = 'appearance_settings'");
        $checkQuery->execute();
        
        if ($checkQuery->rowCount() > 0) {
            $updateQuery = $conn->prepare("UPDATE tbl_settings SET setting_value = :value WHERE setting_key = 'appearance_settings'");
        } else {
            $updateQuery = $conn->prepare("INSERT INTO tbl_settings (setting_key, setting_value) VALUES ('appearance_settings', :value)");
        }
        
        $updateQuery->bindParam(':value', $appearanceData);
        if ($updateQuery->execute()) {
            $success_message = "Appearance settings updated successfully!";
        }
    } catch (PDOException $e) {
        $error_message = "Error updating appearance: " . $e->getMessage();
    }
}

// Create settings table if not exists
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS tbl_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Table might already exist
}

// Load current settings
$shop_settings = [];
$notification_settings = [];
$appearance_settings = [];

try {
    $settingsQuery = $conn->query("SELECT setting_key, setting_value FROM tbl_settings");
    while ($row = $settingsQuery->fetch(PDO::FETCH_ASSOC)) {
        if ($row['setting_key'] == 'shop_settings') {
            $shop_settings = json_decode($row['setting_value'], true);
        } elseif ($row['setting_key'] == 'notification_settings') {
            $notification_settings = json_decode($row['setting_value'], true);
        } elseif ($row['setting_key'] == 'appearance_settings') {
            $appearance_settings = json_decode($row['setting_value'], true);
        }
    }
} catch (PDOException $e) {
    // Handle error
}

// Set default values if not set
$shop_settings = array_merge([
    'shop_name' => 'Coffee Shop',
    'shop_email' => 'info@coffeeshop.com',
    'shop_phone' => '+1 234 567 8900',
    'shop_address' => '123 Coffee Street, Downtown',
    'shop_tax_id' => 'TAX-123456',
    'currency' => 'USD',
    'timezone' => 'America/New_York'
], $shop_settings);

$notification_settings = array_merge([
    'order_notifications' => 1,
    'low_stock_alerts' => 1,
    'daily_summary' => 0,
    'email_notifications' => 1,
    'low_stock_threshold' => 10
], $notification_settings);

$appearance_settings = array_merge([
    'theme_color' => '#d97706',
    'sidebar_color' => '#00A296',
    'dark_mode' => 0
], $appearance_settings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Coffee Shop Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f7fb;
            font-family: 'Inter', sans-serif;
        }

        /* Sidebar Styles */
        .bar {
            background-color: <?php echo $appearance_settings['sidebar_color']; ?>;
            min-height: 100vh;
            position: fixed;
            width: 16.666%;
            transition: all 0.3s ease;
        }

        .menu {
            padding-left: 0;
            margin-top: 1rem;
        }

        .menu li {
            list-style-type: none;
            padding: 5px 10px;
            transition: 0.3s linear;
        }

        .menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            color: #e8e8e4;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .menu li:hover a {
            transform: translateX(12px);
        }

        .menu li:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .menu li.active {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 1.5rem;
        }

        .img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f1f8e9;
            margin-bottom: 0.5rem;
        }

        .text-head {
            color: #FFF3E0;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .logout {
            position: absolute;
            bottom: 30px;
            left: 25px;
            cursor: pointer;
            color: #FFE0B5;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logout:hover {
            color: white;
        }

        /* Main Content Styles */
        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .settings-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .settings-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .settings-card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .settings-card-header h3 {
            margin: 0;
            font-weight: 600;
            color: #1a1a2e;
        }

        .settings-card-header i {
            color: <?php echo $appearance_settings['theme_color']; ?>;
            margin-right: 10px;
        }

        .settings-card-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: <?php echo $appearance_settings['theme_color']; ?>;
            box-shadow: 0 0 0 0.2rem rgba(217, 119, 6, 0.1);
        }

        .btn-primary-custom {
            background: <?php echo $appearance_settings['theme_color']; ?>;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background: <?php echo $appearance_settings['theme_color']; ?>;
            opacity: 0.85;
            transform: translateY(-2px);
        }

        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-switch .form-check-input:checked {
            background-color: <?php echo $appearance_settings['theme_color']; ?>;
            border-color: <?php echo $appearance_settings['theme_color']; ?>;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: inline-block;
            margin-left: 10px;
            border: 2px solid #dee2e6;
            vertical-align: middle;
        }

        .theme-color-option {
            cursor: pointer;
            transition: all 0.2s;
        }

        .theme-color-option:hover {
            transform: scale(1.05);
        }

        hr {
            margin: 1rem 0;
            border-color: #e9ecef;
        }

        @media (max-width: 1200px) {
            .bar {
                position: relative;
                width: 100%;
                min-height: auto;
            }
            .offset-2 {
                margin-left: 0 !important;
            }
            .logout {
                position: relative;
                margin-top: 20px;
                bottom: 0;
                left: 0;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-2 bar">
                <div class="logo">
                    <img class="img" src="https://i.pinimg.com/736x/e5/2a/e3/e52ae301a1162863df9a68c532dd3e2e.jpg" alt="Admin">
                    <h1 class="text-head">Admin Dashboard</h1>
                </div>
                <ul class="menu list-unstyled">
                    <li><a href="index.php"><i class="fa-solid fa-house"></i> Main Dashboard</a></li>
                    <li><a href="product.php"><i class="fa-solid fa-box"></i> Manage Menu</a></li>
                    <li><a href="order.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
                    <li><a href="customer.php"><i class="fa-solid fa-users"></i> Customer</a></li>
                    <li><a href="analytics.php"><i class="fa-solid fa-chart-area"></i> Analytics</a></li>
                    <li><a href="reprot.php"><i class="fa-solid fa-list"></i> Report</a></li>
                    <li class="active"><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                </ul>
                <div class="logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> logout
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-10 offset-2 p-4">
                <div class="settings-container">
                    <!-- Header -->
                    <div class="settings-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-1" style="color: #1a1a2e;">
                                    <i class="fa-solid fa-gear" style="color: <?php echo $appearance_settings['theme_color']; ?>"></i> 
                                    System Settings
                                </h2>
                                <p class="text-muted mb-0">Manage your coffee shop configuration and preferences</p>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-check-circle me-2"></i> <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Shop Information Settings -->
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3><i class="fa-solid fa-store"></i> Shop Information</h3>
                        </div>
                        <div class="settings-card-body">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Shop Name *</label>
                                        <input type="text" class="form-control" name="shop_name" 
                                               value="<?php echo htmlspecialchars($shop_settings['shop_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" name="shop_email" 
                                               value="<?php echo htmlspecialchars($shop_settings['shop_email']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number *</label>
                                        <input type="text" class="form-control" name="shop_phone" 
                                               value="<?php echo htmlspecialchars($shop_settings['shop_phone']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tax ID / VAT Number</label>
                                        <input type="text" class="form-control" name="shop_tax_id" 
                                               value="<?php echo htmlspecialchars($shop_settings['shop_tax_id']); ?>">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Shop Address *</label>
                                        <textarea class="form-control" name="shop_address" rows="2" required><?php echo htmlspecialchars($shop_settings['shop_address']); ?></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Currency</label>
                                        <select class="form-select" name="currency">
                                            <option value="USD" <?php echo $shop_settings['currency'] == 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                            <option value="EUR" <?php echo $shop_settings['currency'] == 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                            <option value="GBP" <?php echo $shop_settings['currency'] == 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                            <option value="KHR" <?php echo $shop_settings['currency'] == 'KHR' ? 'selected' : ''; ?>>KHR (៛)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Timezone</label>
                                        <select class="form-select" name="timezone">
                                            <option value="America/New_York" <?php echo $shop_settings['timezone'] == 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                                            <option value="America/Chicago" <?php echo $shop_settings['timezone'] == 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                                            <option value="America/Denver" <?php echo $shop_settings['timezone'] == 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                                            <option value="America/Los_Angeles" <?php echo $shop_settings['timezone'] == 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                                            <option value="Asia/Phnom_Penh" <?php echo $shop_settings['timezone'] == 'Asia/Phnom_Penh' ? 'selected' : ''; ?>>Phnom Penh</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-end">
                                    <button type="submit" name="update_shop_settings" class="btn btn-primary-custom">
                                        <i class="fa-solid fa-save me-2"></i> Save Shop Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3><i class="fa-solid fa-bell"></i> Notification Preferences</h3>
                        </div>
                        <div class="settings-card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="order_notifications" 
                                               <?php echo $notification_settings['order_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-semibold">New Order Notifications</label>
                                        <p class="text-muted small mt-1">Receive alerts when new orders are placed</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="low_stock_alerts" 
                                               <?php echo $notification_settings['low_stock_alerts'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-semibold">Low Stock Alerts</label>
                                        <p class="text-muted small mt-1">Get notified when product inventory is low</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="daily_summary" 
                                               <?php echo $notification_settings['daily_summary'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-semibold">Daily Sales Summary</label>
                                        <p class="text-muted small mt-1">Receive daily email report of sales performance</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_notifications" 
                                               <?php echo $notification_settings['email_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-semibold">Email Notifications</label>
                                        <p class="text-muted small mt-1">Enable email notifications for all alerts</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Low Stock Threshold</label>
                                    <input type="number" class="form-control" name="low_stock_threshold" 
                                           value="<?php echo $notification_settings['low_stock_threshold']; ?>" 
                                           style="width: 150px;" min="1" max="100">
                                    <small class="text-muted">Alert when product quantity falls below this number</small>
                                </div>
                                <hr>
                                <div class="text-end">
                                    <button type="submit" name="update_notification_settings" class="btn btn-primary-custom">
                                        <i class="fa-solid fa-save me-2"></i> Save Notification Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Appearance Settings -->
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3><i class="fa-solid fa-palette"></i> Appearance & Theme</h3>
                        </div>
                        <div class="settings-card-body">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label class="form-label">Primary Theme Color</label>
                                    <div class="row g-2">
                                        <div class="col-auto">
                                            <input type="color" class="form-control form-control-color" name="theme_color" 
                                                   value="<?php echo $appearance_settings['theme_color']; ?>" 
                                                   style="width: 80px; height: 50px;">
                                        </div>
                                        <div class="col-auto">
                                            <div class="d-flex gap-2">
                                                <div class="theme-color-option" onclick="document.querySelector('[name=theme_color]').value='#d97706'">
                                                    <div class="color-preview" style="background: #d97706;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=theme_color]').value='#00A296'">
                                                    <div class="color-preview" style="background: #00A296;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=theme_color]').value='#6366f1'">
                                                    <div class="color-preview" style="background: #6366f1;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=theme_color]').value='#ec489a'">
                                                    <div class="color-preview" style="background: #ec489a;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=theme_color]').value='#14b8a6'">
                                                    <div class="color-preview" style="background: #14b8a6;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Sidebar Color</label>
                                    <div class="row g-2">
                                        <div class="col-auto">
                                            <input type="color" class="form-control form-control-color" name="sidebar_color" 
                                                   value="<?php echo $appearance_settings['sidebar_color']; ?>" 
                                                   style="width: 80px; height: 50px;">
                                        </div>
                                        <div class="col-auto">
                                            <div class="d-flex gap-2">
                                                <div class="theme-color-option" onclick="document.querySelector('[name=sidebar_color]').value='#1a1a2e'">
                                                    <div class="color-preview" style="background: #1a1a2e;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=sidebar_color]').value='#00A296'">
                                                    <div class="color-preview" style="background: #00A296;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=sidebar_color]').value='#2c3e50'">
                                                    <div class="color-preview" style="background: #2c3e50;"></div>
                                                </div>
                                                <div class="theme-color-option" onclick="document.querySelector('[name=sidebar_color]').value='#6c5ce7'">
                                                    <div class="color-preview" style="background: #6c5ce7;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="dark_mode" 
                                               <?php echo $appearance_settings['dark_mode'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-semibold">Dark Mode</label>
                                        <p class="text-muted small mt-1">Enable dark theme for the admin dashboard</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-end">
                                    <button type="submit" name="update_appearance" class="btn btn-primary-custom">
                                        <i class="fa-solid fa-save me-2"></i> Save Appearance Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h3><i class="fa-solid fa-circle-info"></i> System Information</h3>
                        </div>
                        <div class="settings-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-semibold">PHP Version:</td>
                                            <td><?php echo phpversion(); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Database:</td>
                                            <td>MySQL / MariaDB</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Server Time:</td>
                                            <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-semibold">System Version:</td>
                                            <td>2.0.0</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Last Backup:</td>
                                            <td><?php echo date('Y-m-d'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Color preview update
        const colorInputs = document.querySelectorAll('input[type="color"]');
        colorInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Optional: Add live preview functionality
                if (this.name === 'theme_color') {
                    document.querySelectorAll('.btn-primary-custom').forEach(btn => {
                        btn.style.backgroundColor = this.value;
                    });
                }
            });
        });
    </script>
</body>
</html>