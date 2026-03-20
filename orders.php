<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit;
}

$host    = 'localhost';
$db      = 'cs2team61_db';
$user    = 'cs2team61';
$pass    = 'y6eEEvWY0VrTj9krI807dMUVy';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $is_admin = isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1;

    if ($is_admin) {
        $stmt = $pdo->query("SELECT o.orderid, o.ordernumber, o.totalamount, o.status, o.orderdate, u.email
                             FROM orders o
                             JOIN users u ON o.userid = u.userid
                             ORDER BY o.orderdate DESC");
    } else {
        $stmt = $pdo->prepare("SELECT orderid, ordernumber, totalamount, status, orderdate
                               FROM orders WHERE userid = ? ORDER BY orderdate DESC");
        $stmt->execute([$_SESSION['user_id']]);
    }

    $orders = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$status_colors = [
    'pending'          => '#ffc107',
    'processing'       => '#17a2b8',
    'shipped'          => '#6f42c1',
    'delivered'        => '#28a745',
    'return_requested' => '#fd7e14',
    'returned'         => '#6c757d',
    'cancelled'        => '#dc3545',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Tech Forge</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="javascript.js" defer></script>
    <link rel="shortcut icon" href="TechForge_Logo.png">

    <style>
        .orders-container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }

        .orders-header {
            margin-bottom: 25px;
        }

        .orders-header h1 {
            color: var(--secondary);
            font-size: 2rem;
        }

        .orders-header p {
            color: #b0b0b0;
            margin-top: 5px;
        }

        .orders-box {
            background: #2a242d;
            border: 1px solid #3a3248;
            border-radius: 12px;
            padding: 30px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #3a3248;
            color: #b0b0b0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .orders-table td {
            padding: 14px 15px;
            border-bottom: 1px solid #3a3248;
            color: #fff;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .status-pill {
            font-size: 0.78rem;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(255,255,255,0.05);
        }

        .btn-return {
            background: transparent;
            border: 1px solid #fd7e14;
            color: #fd7e14;
            padding: 6px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: background 0.2s, color 0.2s;
        }

        .btn-return:hover {
            background: #fd7e14;
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #b0b0b0;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
            color: #555;
        }

        /* Light mode */
        body.light-mode .orders-box {
            background: #fff;
            border-color: #ddd;
        }

        body.light-mode .orders-table th {
            border-bottom-color: #eee;
        }

        body.light-mode .orders-table td {
            color: #1a1a1a;
            border-bottom-color: #eee;
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>
                    <li><a href="admin_panel.php"><i class="fas fa-shield-halved"></i> <span>Admin Panel</span></a></li>
                    <li><a href="admin_inventory.php"><i class="fas fa-boxes"></i> <span>Manage Stock</span></a></li>
                    <li><a href="admin_reports.php"><i class="fas fa-chart-line"></i> <span>Reports</span></a></li>
                <?php endif; ?>
                <li><a href="orders.php" class="active"><i class="fas fa-receipt"></i> <span>My Orders</span></a></li>
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
                <button class="theme-toggle-btn">
                    <i class="fa-solid fa-moon theme-icon moon-icon"></i>
                    <i class="fa-solid fa-sun theme-icon sun-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="orders-container">
        <div class="orders-header">
            <h1 class="aldrich-regular">
                <i class="fas fa-receipt" style="margin-right: 10px;"></i>
                <?php echo $is_admin ? 'All Orders' : 'My Orders'; ?>
            </h1>
            <p><?php echo $is_admin ? 'Viewing all orders across all customers.' : 'Your order history.'; ?></p>
        </div>

        <div class="orders-box">
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p><?php echo $is_admin ? 'No orders have been placed yet.' : 'You haven\'t placed any orders yet.'; ?></p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <?php if ($is_admin): ?><th>Customer</th><?php endif; ?>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order):
                                $status = strtolower($order['status']);
                                $color  = $status_colors[$status] ?? '#b0b0b0';
                                $can_return = !in_array($status, ['return_requested', 'returned', 'cancelled']);
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['ordernumber']); ?></strong></td>
                                <?php if ($is_admin): ?>
                                    <td style="color: #b0b0b0;"><?php echo htmlspecialchars($order['email']); ?></td>
                                <?php endif; ?>
                                <td style="color: #b0b0b0;"><?php echo date('d M Y', strtotime($order['orderdate'])); ?></td>
                                <td>£<?php echo number_format($order['totalamount'], 2); ?></td>
                                <td>
                                    <span class="status-pill" style="color: <?php echo $color; ?>;">
                                        <?php echo strtoupper(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if (!$is_admin && $can_return): ?>
                                        <button class="btn-return" onclick="alert('Return functionality coming soon.')">
                                            Request Return
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
