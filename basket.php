<?php
session_start();
require_once 'config.php';

// Initialize variables
$cart_items = [];
$total_price = 0;
$shipping = 4.99;

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // LOGGED IN USER - Get cart from database
    $user_id = $_SESSION['user_id'];
    
    try {
        $sql = "SELECT c.*, p.productname, p.price, p.description, p.imageurl, p.stock 
                FROM cart c 
                JOIN products p ON c.productid = p.productid 
                WHERE c.userid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $cart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($cart_data as $item) {
            $cart_items[] = [
                'product' => $item,
                'quantity' => $item['quantity']
            ];
            $total_price += $item['price'] * $item['quantity'];
        }
    } catch(PDOException $e) {
        $error_message = "Error loading cart";
    }
} else {
    // GUEST USER - Get cart from session
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        
        if(!empty($product_ids)) {
            // Create placeholders for SQL IN clause
            $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
            
            try {
                $sql = "SELECT * FROM products WHERE productid IN ($placeholders)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($product_ids);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($products as $product) {
                    $product_id = $product['productid'];
                    $quantity = $_SESSION['cart'][$product_id]['quantity'];
                    
                    $cart_items[] = [
                        'product' => $product,
                        'quantity' => $quantity
                    ];
                    $total_price += $product['price'] * $quantity;
                }
            } catch(PDOException $e) {
                $error_message = "Error loading products";
            }
        }
    }
}

$grand_total = $total_price + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket - Tech Forge</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="basket.css">
    <script src="javascript.js" defer></script>
    <link rel="shortcut icon" href="TechForge_Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Additional basket styles */
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: #2a242d;
            border-radius: 12px;
            color: white;
            margin-bottom: 30px;
        }
        
        .empty-cart p {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: #b0b0b0;
        }
        
        .continue-shopping-btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .continue-shopping-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        .basket-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .basket-container h1 {
            color: var(--secondary);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .basket-items {
            background: #2a242d;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .basket-item {
            display: flex;
            align-items: center;
            background: #1d1a1e;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: var(--transition);
        }
        
        .basket-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .basket-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
            border: 2px solid #3a3640;
        }
        
        .product-image-placeholder {
            width: 100px;
            height: 100px;
            background: #2d2a30;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #888;
            margin-right: 20px;
            border: 2px dashed #3a3640;
        }
        
        .item-details {
            flex: 2;
        }
        
        .item-details h3 {
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 1.2rem;
        }
        
        .item-details p {
            color: #b0b0b0;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        
        .item-price {
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .qty-input {
            width: 70px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #3a3640;
            background: #2d2a30;
            color: white;
            text-align: center;
            margin: 0 20px;
        }
        
        .qty-input:focus {
            outline: none;
            border-color: var(--secondary);
        }
        
        .item-total {
            color: var(--secondary);
            font-weight: bold;
            font-size: 1.2rem;
            min-width: 100px;
            text-align: right;
            margin-right: 20px;
        }
        
        .remove-btn {
            background: #dc3545;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .remove-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .basket-summary {
            background: #2a242d;
            padding: 30px;
            border-radius: 12px;
            color: white;
        }
        
        .basket-summary h2 {
            color: var(--secondary);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            color: #b0b0b0;
            font-size: 1.1rem;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: bold;
            font-size: 1.3rem;
            color: white;
        }
        
        .basket-summary hr {
            border: 1px solid #3a3640;
            margin: 15px 0;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 15px;
        }
        
        .checkout-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Error message styling */
        .error-message {
            background: #dc3545;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Login prompt for guests */
        .login-prompt {
            text-align: center;
            background: #2a242d;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .login-prompt p {
            color: #b0b0b0;
            margin-bottom: 10px;
        }
        
        .login-prompt a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-prompt a:hover {
            text-decoration: underline;
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
                <li><a href="index.html"><i class="fas fa-home"></i> <span>Home</span></a></li>
                <li class="flyout-parent">
                    <a href="#" class="flyout-toggle">
                        <i class="fas fa-box-open"></i> <span>Products</span>
                        <i class="fas fa-chevron-right flyout-arrow"></i>
                    </a>
                    <div class="flyout-menu">
                        <a href="products.php">All Products</a>
                        <a href="products.php?category=GPU">GPU</a>
                        <a href="products.php?category=CPU">CPU</a>
                        <a href="products.php?category=RAM">RAM</a>
                        <a href="products.php?category=Motherboard">Motherboard</a>
                        <a href="products.php?category=Storage">Storage</a>
                    </div>
                </li>
                <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
                <li><a href="AboutUs.html"><i class="fas fa-info-circle"></i> <span>About</span></a></li>
                <li><a href="settings.html"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="signup.html"><i class="fas fa-sign-in-alt"></i> <span><?php echo isset($_SESSION['user_id']) ? 'Account' : 'Login'; ?></span></a></li>
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
                    <a href="basket.php"><i class="fa-solid fa-basket-shopping active-icon"></i></a>
                    <button class="theme-toggle-btn">
                        <i class="fa-solid fa-moon theme-icon moon-icon"></i>
                        <i class="fa-solid fa-sun theme-icon sun-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="basket-container">
            <h1>Your Basket</h1>
            
            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(empty($cart_items)): ?>
                <div class="empty-cart">
                    <p>Your basket is empty</p>
                    <a href="products.php" class="continue-shopping-btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="basket-items" id="basketItems">
                    <?php foreach($cart_items as $item): 
                        $product = $item['product'];
                        $quantity = $item['quantity'];
                        $item_total = $product['price'] * $quantity;
                    ?>
                        <div class="basket-item" data-product-id="<?php echo $product['productid']; ?>">
                            <?php if(!empty($product['imageurl'])): ?>
                                <img src="<?php echo htmlspecialchars($product['imageurl']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['productname']); ?>">
                            <?php else: ?>
                                <div class="product-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($product['productname']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                                <div class="item-price">£<?php echo number_format($product['price'], 2); ?> each</div>
                            </div>
                            
                            <input type="number" 
                                   class="qty-input" 
                                   value="<?php echo $quantity; ?>" 
                                   min="1" 
                                   max="<?php echo $product['stock'] ?? 99; ?>"
                                   onchange="updateQuantity(<?php echo $product['productid']; ?>, this.value)">
                            
                            <div class="item-total">
                                £<?php echo number_format($item_total, 2); ?>
                            </div>
                            
                            <button class="remove-btn" onclick="removeFromCart(<?php echo $product['productid']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="basket-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-line">
                        <span>Subtotal:</span>
                        <span id="subtotal">£<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping:</span>
                        <span id="shipping">£<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <hr>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span id="total">£<?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    <a href="products.php" class="continue-shopping-btn" style="text-align: center;">Continue Shopping</a>
                    
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <div class="login-prompt">
                            <p>Want to save your cart for later? <a href="login.php">Login</a> or <a href="signup.html">Sign Up</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function updateQuantity(productId, quantity) {
        if(quantity < 1) {
            removeFromCart(productId);
            return;
        }
        
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId + '&quantity=' + quantity
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload(); // Refresh page to show updated totals
            } else {
                alert('Error updating quantity');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating quantity');
        });
    }
    
    function removeFromCart(productId) {
        if(confirm('Remove this item from your basket?')) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload(); // Refresh page
                } else {
                    alert('Error removing item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing item');
            });
        }
    }
    
    function proceedToCheckout() {
        <?php if(isset($_SESSION['user_id'])): ?>
            window.location.href = 'checkout.php';
        <?php else: ?>
            alert('Please login to proceed to checkout');
            window.location.href = 'login.php';
        <?php endif; ?>
    }
    </script>
</body>
</html>