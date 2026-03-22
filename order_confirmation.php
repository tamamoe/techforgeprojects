<?php
session_start();
require_once 'config.php';

function generateUniqueOrderNumber($pdo) {
    do {
        $number = 'TF-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
        $check = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE ordernumber = ?");
        $check->execute([$number]);
    } while ($check->fetchColumn() > 0);
    return $number;
}

$order_number = null;

if(isset($_SESSION['user_id'])) {
    try {
        $user_id = $_SESSION['user_id'];

        $cart_stmt = $pdo->prepare("SELECT c.productid, c.quantity, p.price FROM cart c JOIN products p ON c.productid = p.productid WHERE c.userid = ?");
        $cart_stmt->execute([$user_id]);
        $items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($items) {
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $order_number = generateUniqueOrderNumber($pdo);

            $order_stmt = $pdo->prepare("INSERT INTO orders (userid, ordernumber, totalamount, status) VALUES (?, ?, ?, 'pending')");
            $order_stmt->execute([$user_id, $order_number, $total]);

            $update_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE productid = ?");
            foreach ($items as $item) {
                $update_stmt->execute([$item['quantity'], $item['productid']]);
            }

            $pdo->prepare("DELETE FROM cart WHERE userid = ?")->execute([$user_id]);
        }

    } catch(PDOException $e) {
        error_log("Order processing failed: " . $e->getMessage());
    }
} elseif (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    try {
        // Guests: deduct stock but can't store order (no userid)
        $order_number = 'TF-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
        $update_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE productid = ?");
        foreach ($_SESSION['cart'] as $product_id => $details) {
            $update_stmt->execute([$details['quantity'], $product_id]);
        }
    } catch(PDOException $e) {
        error_log("Guest Order processing failed: " . $e->getMessage());
    }
}

if (!$order_number) {
    $order_number = 'TF-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
}

if(isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - Tech Forge</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <script src="javascript.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="TechForge_Logo.png">
    
    <style>
        .confirmation-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            padding: 20px;
        }

        .confirmation-card {
            background: #2a242d;
            border-radius: 12px;
            padding: 50px 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid #3a3640;
        }

        .success-icon-wrapper {
            width: 90px;
            height: 90px;
            background: rgba(74, 222, 128, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .success-icon-wrapper i {
            font-size: 3rem;
            color: #4ade80;
        }

        .confirmation-card h1 {
            color: var(--secondary);
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .confirmation-card p {
            color: #b0b0b0;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .order-details-box {
            background: #1d1a1e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 35px;
            border: 1px dashed #3a3640;
        }

        .order-details-box h3 {
            color: white;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .order-details-box p {
            margin-bottom: 0;
            font-size: 1rem;
        }

        .order-number {
            color: var(--secondary);
            font-weight: bold;
            font-size: 1.3rem;
            letter-spacing: 2px;
        }

        .btn-continue {
            display: inline-block;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(107, 70, 193, 0.3);
        }

        .btn-continue:hover {
            background: var(--secondary);
            transform: translateY(-3px);
        }

        .btn-continue i {
            margin-right: 8px;
        }

        /* ==========================================================
           LIGHT MODE OVERRIDES
           ========================================================== */
        body.light-mode .confirmation-card {
            background: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        body.light-mode .confirmation-card p {
            color: #555555;
        }

        body.light-mode .order-details-box {
            background: #f9f9f9;
            border-color: #cccccc;
        }

        body.light-mode .order-details-box h3 {
            color: #1a1a1a;
        }
    </style>
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
                <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
                <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="signup.php"><i class="fas fa-sign-in-alt"></i> <span><?php echo isset($_SESSION['user_id']) ? 'Account' : 'Login'; ?></span></a></li>
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

        <div class="confirmation-wrapper">
            <div class="confirmation-card">
                <div class="success-icon-wrapper">
                    <i class="fas fa-check"></i>
                </div>
                
                <h1 class="aldrich-regular">Order Confirmed!</h1>
                <p>Thank you for shopping with Tech Forge. We've received your order and are getting it ready to be shipped. We'll send you an email confirmation shortly.</p>
                
                <div class="order-details-box">
                    <h3>Order Number</h3>
                    <p class="order-number"><?php echo htmlspecialchars($order_number); ?></p>
                </div>

                <a href="products.php" class="btn-continue">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>

    </div>

</body>
</html>