<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit; 
}

$host = 'localhost';
$db   = 'cs2team61_db';
$user = 'cs2team61';
$pass = 'y6eEEvWY0VrTj9krI807dMUVy'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);


    $stmt = $pdo->prepare("SELECT isadmin FROM users WHERE userid = :userid");
    $stmt->execute(['userid' => $_SESSION['user_id']]);
    $currentUser = $stmt->fetch();

    if (!$currentUser || $currentUser['isadmin'] != 1) {
        header("Location: index.php");
        exit;
    }


    $feedback_message = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_stock'])) {
        $update_id = $_POST['product_id'];
        $new_stock = $_POST['new_stock'];
        
        $update_stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE productid = :id");
        $update_stmt->execute(['stock' => $new_stock, 'id' => $update_id]);
        $feedback_message = "<div class='success-msg'><i class='fas fa-check-circle'></i> Stock updated successfully!</div>";
    }

    $search_query = "";
    if (isset($_GET['search']) && trim($_GET['search']) !== '') {
        $search_query = trim($_GET['search']);
        

        $fetch_stmt = $pdo->prepare("SELECT * FROM products WHERE productname LIKE ? OR description LIKE ? ORDER BY stock ASC");
        
        $search_term = "%" . $search_query . "%";
        $fetch_stmt->execute([$search_term, $search_term]);
        
    } else {
        $fetch_stmt = $pdo->query("SELECT * FROM products ORDER BY stock ASC");
    }
    $products = $fetch_stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Tech Forge</title>
    
    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="Signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="javascript.js" defer></script> 
    <script src="signup.js" defer></script>  

    <link rel="shortcut icon" href="TechForge_Logo.png">
    
    <style>

        /* SPLIT LAYOUT STYLES */
            .auth-split-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 50px; 
            width: 100%;
            max-width: 1400px; 
            margin: 60px auto;
            padding: 20px;
            flex-wrap: wrap;
        }

        .auth-box {
            flex: 1 1 450px;
            width: 100%;
            min-width: 320px;
            max-width: 600px; 
            background: #2a242d; 
            padding: 40px 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            border: 1px solid #3a3248;
        }

        .auth-box h2 {
            color: var(--secondary);
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.8rem;
            border-bottom: 1px solid #3a3248;
            padding-bottom: 15px;
        }

        .auth-box form {
            display: flex;
            flex-direction: column;
        }

        .auth-box label {
            color: #b0b0b0;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .auth-box input[type="email"],
        .auth-box input[type="password"] {
            padding: 14px; 
            margin-bottom: 22px;
            background: #1d1a1e;
            border: 1px solid #3a3640;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            transition: var(--transition);
        }

        .auth-box input[type="email"]:focus,
        .auth-box input[type="password"]:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1);
        }

        .auth-box .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        .auth-box .submit-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
        }
        
        .global-error-message {
            width: 100%;
            max-width: 1250px; 
            background: rgba(255, 77, 77, 0.1);
            border-left: 4px solid #ff4d4d;
            color: #ff4d4d;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* light mode */
        body.light-mode .auth-box {
            background: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
        
        body.light-mode .auth-box h2 {
            border-bottom-color: #eeeeee;
        }

        body.light-mode .auth-box label {
            color: #555555;
        }

        body.light-mode .auth-box input[type="email"],
        body.light-mode .auth-box input[type="password"] {
            background: #f9f9f9;
            border-color: #dddddd;
            color: #1a1a1a;
        }

        body.light-mode .auth-box input[type="email"]:focus,
        body.light-mode .auth-box input[type="password"]:focus {
            background: #ffffff;
            border-color: var(--primary);
        }

        /* responsive 
         * stack them on smaller screens */
        @media (max-width: 768px) {
            .auth-split-container {
                gap: 30px;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body class="dark-mode">

<div class="sidebar">
    <div class="sidebar-header">
        <img src="techforgecog.png" alt="logo" class="sidebar-logo">
    </div>
    <div class="sidebar-menu">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="products.php"><i class="fas fa-box-open"></i> <span>Products</span></a></li>
            <li><a href="compare.php"><i class="fas fa-scale-balanced"></i> <span>Compare</span></a></li>
            <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
            <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>
                    <li><a href="admin_inventory.php" class="active"><i class="fas fa-boxes"></i> <span>Manage Stock</span></a></li>
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

    <div class="auth-split-container">
        <div class="auth-box" style="max-width: 1000px; flex: 1 1 100%;">
            <h2 class="aldrich-regular"><i class="fas fa-boxes"></i> Inventory Manager</h2>
            
            <form method="GET" action="admin_inventory.php" style="flex-direction: row; gap: 10px; margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>" style="flex-grow: 1; margin-bottom: 0;">
                <button type="submit" class="submit-btn" style="margin-top: 0;"><i class="fas fa-search"></i> Search</button>
            </form>

            <?php if (!empty($feedback_message)) echo $feedback_message; ?>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                    <thead>
                        <tr>
                            <th style="padding: 15px; border-bottom: 1px solid #3a3248; color: var(--secondary);">Image</th>
                            <th style="padding: 15px; border-bottom: 1px solid #3a3248; color: var(--secondary);">Product Name</th>
                            <th style="padding: 15px; border-bottom: 1px solid #3a3248; color: var(--secondary);">Price</th>
                            <th style="padding: 15px; border-bottom: 1px solid #3a3248; color: var(--secondary);">Status</th>
                            <th style="padding: 15px; border-bottom: 1px solid #3a3248; color: var(--secondary);">Update Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #3a3248;">
                                    <?php if(!empty($product['imageurl'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['imageurl']); ?>" width="50" style="border-radius:5px;">
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #3a3248;"><?php echo htmlspecialchars($product['productname']); ?></td>
                                <td style="padding: 15px; border-bottom: 1px solid #3a3248;">£<?php echo number_format($product['price'], 2); ?></td>
                                <td style="padding: 15px; border-bottom: 1px solid #3a3248;">
                                    <?php 
                                        if ($product['stock'] <= 0) {
                                            echo "<span style='color: #ff4d4d; font-weight: bold;'>Out of Stock</span>";
                                        } elseif ($product['stock'] < 10) {
                                            echo "<span style='color: #ffc107; font-weight: bold;'>Low Stock (" . $product['stock'] . ")</span>";
                                        } else {
                                            echo "<span style='color: #28a745; font-weight: bold;'>In Stock (" . $product['stock'] . ")</span>";
                                        }
                                    ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #3a3248;">
                                    <form method="POST" action="admin_inventory.php" style="flex-direction: row; gap: 10px; align-items: center;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['productid']; ?>">
                                        <input type="number" name="new_stock" value="<?php echo $product['stock']; ?>" min="0" style="width: 70px; margin-bottom: 0; padding: 10px;">
                                        <button type="submit" name="update_stock" class="submit-btn" style="margin-top: 0; padding: 10px 15px;">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>