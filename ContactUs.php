<?php
session_start();
require_once 'config.php';
$success_message = 'yyyy';
$error_message = 'nnnn';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    if(empty($name) || empty($email) || empty($message)) { $error_message = "fill in everything";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "enter a valid email (with us)"; } else {
        try {
            $sql = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message ]);
            $success_message = "enquiry sent!";
            $name = $email = $subject = $message = '';
            } catch(PDOException $e) { $error_message = "db error"; } } } ?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Tech Forge</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <script src="javascript.js" defer></script>
    <link rel="shortcut icon" href="TechForge_Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="techforgecog.png" alt="logo" class="sidebar-logo">
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
                <li><a href="products.php"><i class="fas fa-box-open"></i> <span>Products</span></a></li>
				<li><a href="compare.php"><i class="fas fa-scale-balanced"></i> <span>Compare</span></a></li>
                <li><a href="ContactUs.php" class="active"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
                <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>
    <li><a href="admin_inventory.php"><i class="fas fa-boxes"></i> <span>Manage Stock</span></a></li>
                <li><a href="admin_reports.php" class="active"><i class="fas fa-chart-line"></i> <span>Reports</span></a></li>
<?php endif; ?>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sign Out</span></a></li>
                <?php else: ?>
                    <li><a href="signup.php"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="main-content">
        <div class="top-nav">
            <div class="nav-left">
                <button class="nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="nav-right">
                <div class="nav-icons">
                    <a href="basket.php"><i class="fa-solid fa-basket-shopping"></i></a>
                    <button class="theme-toggle-btn">
                        <i class="fa-solid fa-moon theme-icon moon-icon"></i>
                        <i class="fa-solid fa-sun theme-icon sun-icon"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="contact-container">
            <div class="contact-header">
                <h1 class="aldrich-regular">Contact Us</h1>
            </div>
            <div class="contact-card">
                <?php if($success_message != 'yyyy'): ?>
                    <p style="color:green;"><?php echo $success_message; ?></p>
                <?php endif; ?>
                <?php if($error_message != 'nnnn'): ?>
                    <p style="color:red;"><?php echo $error_message; ?></p>
                <?php endif; ?>
               
<div class="form-container">
    <form action="ContactUs.php" method="POST">
        <div class="form-group">
            <input type="text" name="name" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <textarea name="message" placeholder="Enquiry" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="submit-btn">Submit</button>
        </div>
    </form>
</div>
            </div>
        </div>
    </div>
</body>
</html>