<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header("Location: index.php");
    exit;
}

try {
    $metrics_query = $pdo->query("SELECT COUNT(productid) as total_products, SUM(stock) as total_items FROM products");
    $metrics = $metrics_query->fetch(PDO::FETCH_ASSOC);

    $low_stock_query = $pdo->query("SELECT productname, categoryname, price, stock FROM products LEFT JOIN categories ON products.categoryid = categories.categoryid WHERE stock <= 5 ORDER BY stock ASC");
    $low_stock_items = $low_stock_query->fetchAll(PDO::FETCH_ASSOC);

    $orders_query = $pdo->query("SELECT o.ordernumber, o.totalamount, o.status, o.orderdate, u.email FROM orders o JOIN users u ON o.userid = u.userid ORDER BY o.orderdate DESC LIMIT 50");
    $orders = $orders_query->fetchAll(PDO::FETCH_ASSOC);

    $order_metrics_query = $pdo->query("SELECT COUNT(*) as total_orders, SUM(totalamount) as total_revenue FROM orders");
    $order_metrics = $order_metrics_query->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Report Generation Failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reports - Tech Forge</title>

    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="Signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="javascript.js" defer></script> 
    <script src="signup.js" defer></script>  

    <link rel="shortcut icon" href="TechForge_Logo.png">
    
    <style>
        .auth-split-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 50px; 
            width: 100%;
            max-width: 1400px; 
            margin: 20px auto;
            padding: 20px;
            flex-wrap: wrap;
        }

        .auth-box {
            flex: 1 1 450px;
            width: 100%;
            min-width: 320px;
            max-width: 100%; 
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

        .dashboard-header {
            text-align: center;
            width: 100%;
            margin-bottom: 10px;
        }

        .dashboard-header h1 {
            color: var(--secondary);
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #b0b0b0;
        }

        .metric-content {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-top: 20px;
        }

        .metric-item i {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .metric-item h3 {
            color: #b0b0b0;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .metric-item p {
            color: #fff;
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }

        .report-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        
        .report-table th { 
            text-align: left; 
            padding: 12px 15px; 
            color: #b0b0b0; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            border-bottom: 2px solid #3a3248; 
        }
        
        .report-table td { 
            padding: 15px; 
            color: #fff; 
            border-bottom: 1px solid #3a3248; 
        }
        
        .report-table tr:hover td { 
            background: rgba(255,255,255,0.02); 
        }
        
        .status-badge { 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: bold; 
        }
        
        .status-danger { 
            background: rgba(220,53,69,0.15); 
            color: #ff4d4d; 
            border: 1px solid rgba(220,53,69,0.3); 
        }
        
        .status-warning { 
            background: rgba(255,193,7,0.15); 
            color: #ffc107; 
            border: 1px solid rgba(255,193,7,0.3); 
        }

        body.light-mode .auth-box {
            background: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
        
        body.light-mode .auth-box h2 {
            border-bottom-color: #eeeeee;
        }

        body.light-mode .metric-item h3 {
            color: #555555;
        }

        body.light-mode .metric-item p {
            color: #1a1a1a;
        }

        body.light-mode .report-table th {
            border-bottom-color: #eeeeee;
            color: #555555;
        }

        body.light-mode .report-table td {
            color: #1a1a1a;
            border-bottom-color: #eeeeee;
        }

        body.light-mode .report-table tr:hover td {
            background: #f9f9f9;
        }

        @media (max-width: 768px) {
            .auth-split-container {
                gap: 30px;
                margin: 20px auto;
            }
            .metric-content {
                flex-direction: column;
                gap: 30px;
            }
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
                <button class="nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')"><i class="fas fa-bars"></i></button>
            </div>
            <div class="nav-right">
                <div class="nav-icons"><button class="theme-toggle-btn"><i class="fa-solid fa-moon"></i></button></div>
            </div>
        </div>

        <div class="auth-split-container">
            <div class="dashboard-header">
                <h1 class="aldrich-regular">Stock Report</h1>
                <p>Overview of the stock we have.</p>
            </div>

            <div class="auth-box">
                <h2><i class="fas fa-chart-pie" style="margin-right: 10px;"></i> Stock Overview</h2>
                <div class="metric-content">
                    <div class="metric-item">
                        <i class="fas fa-tags"></i>
                        <h3>Products</h3>
                        <p><?php echo number_format($metrics['total_products'] ?? 0); ?></p>
                    </div>
                    <div class="metric-item">
                        <i class="fas fa-boxes"></i>
                        <h3>Total Items in Stock</h3>
                        <p><?php echo number_format($metrics['total_items'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="auth-box">
                <h2><i class="fas fa-shopping-bag" style="margin-right: 10px;"></i> Orders Overview</h2>
                <div class="metric-content">
                    <div class="metric-item">
                        <i class="fas fa-receipt"></i>
                        <h3>Total Orders</h3>
                        <p><?php echo number_format($order_metrics['total_orders'] ?? 0); ?></p>
                    </div>
                    <div class="metric-item">
                        <i class="fas fa-pound-sign"></i>
                        <h3>Total Revenue</h3>
                        <p>£<?php echo number_format($order_metrics['total_revenue'] ?? 0, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="auth-box" style="flex: 1 1 100%;">
                <h2><i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> Restock Alerts</h2>
                
                <?php if (count($low_stock_items) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Unit Price</th>
                                    <th>Current Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($low_stock_items as $item): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($item['productname']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($item['categoryname'] ?? 'Uncategorized'); ?></td>
                                        <td>£<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['stock']; ?></td>
                                        <td>
                                            <?php if ($item['stock'] == 0): ?>
                                                <span class="status-badge status-danger">OUT OF STOCK</span>
                                            <?php else: ?>
                                                <span class="status-badge status-warning">LOW STOCK</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #b0b0b0;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: #48bb78; margin-bottom: 15px;"></i>
                        <p>All products are sufficiently stocked. No immediate restocks required.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="auth-box" style="flex: 1 1 100%;">
                <h2><i class="fas fa-history" style="margin-right: 10px;"></i> Past Orders</h2>

                <?php if (count($orders) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer Email</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($order['ordernumber']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                                        <td>£<?php echo number_format($order['totalamount'], 2); ?></td>
                                        <td>
                                            <?php
                                                $status = strtolower($order['status']);
                                                $badge_class = $status === 'pending' ? 'status-warning' : ($status === 'cancelled' ? 'status-danger' : '');
                                            ?>
                                            <span class="status-badge <?php echo $badge_class; ?>" style="<?php echo $badge_class ? '' : 'background:rgba(74,222,128,0.15);color:#4ade80;border:1px solid rgba(74,222,128,0.3);'; ?>">
                                                <?php echo strtoupper($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y, H:i', strtotime($order['orderdate'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #b0b0b0;">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #6b7280; margin-bottom: 15px;"></i>
                        <p>No orders have been placed yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>