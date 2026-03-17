<?php
session_start();
require_once 'config.php';

// Check if user is logged in (optional for dummy page)
$is_logged_in = isset($_SESSION['user_id']);

// Get cart items for summary (optional)
$cart_items = [];
$total_price = 0;
$shipping = 4.99;

if(isset($_SESSION['user_id'])) {
    // Get from database if logged in
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT c.*, p.productname, p.price, p.imageurl 
            FROM cart c 
            JOIN products p ON c.productid = p.productid 
            WHERE c.userid = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $cart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($cart_data as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
} elseif(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Get from session if guest
    $product_ids = array_keys($_SESSION['cart']);
    if(!empty($product_ids)) {
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        $sql = "SELECT * FROM products WHERE productid IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($products as $product) {
            $product_id = $product['productid'];
            $total_price += $product['price'] * $_SESSION['cart'][$product_id]['quantity'];
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
    <title>Checkout - Tech Forge</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="TechForge_Logo.png">
    <style>
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
        }

        .checkout-header {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 20px;
        }

        .checkout-header h1 {
            color: var(--secondary);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .checkout-header p {
            color: #b0b0b0;
        }

        /* Checkout Form Styles */
        .checkout-form {
            background: #2a242d;
            padding: 30px;
            border-radius: 12px;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #3a3640;
        }

        .form-section h2 {
            color: var(--secondary);
            font-size: 1.3rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section h2 i {
            color: var(--primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #b0b0b0;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .form-group input, 
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: #1d1a1e;
            border: 1px solid #3a3640;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .form-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            color: #b0b0b0;
        }

        /* Payment Method Styles */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method {
            background: #1d1a1e;
            border: 1px solid #3a3640;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-method:hover {
            border-color: var(--secondary);
            background: #2a242d;
        }

        .payment-method.selected {
            border-color: var(--secondary);
            background: rgba(107, 70, 193, 0.2);
        }

        .payment-method i {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .payment-method span {
            display: block;
            color: white;
            font-size: 0.9rem;
        }

        /* Order Summary Styles */
        .order-summary {
            background: #2a242d;
            padding: 25px;
            border-radius: 12px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .order-summary h2 {
            color: var(--secondary);
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #3a3640;
        }

        .summary-items {
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #3a3640;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 2;
        }

        .item-info h4 {
            color: white;
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .item-info p {
            color: #b0b0b0;
            font-size: 0.85rem;
        }

        .item-quantity {
            color: #b0b0b0;
            font-size: 0.9rem;
            margin: 0 15px;
        }

        .item-price {
            color: var(--secondary);
            font-weight: bold;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            color: #b0b0b0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            font-weight: bold;
            font-size: 1.2rem;
            color: white;
            border-top: 2px solid #3a3640;
            margin-top: 10px;
        }

        .place-order-btn {
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
            margin-top: 20px;
        }

        .place-order-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .secure-badge {
            text-align: center;
            margin-top: 15px;
            color: #b0b0b0;
            font-size: 0.9rem;
        }

        .secure-badge i {
            color: var(--secondary);
            margin-right: 5px;
        }

        /* Login prompt */
        .login-prompt-checkout {
            background: #2a242d;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .login-prompt-checkout p {
            color: #b0b0b0;
            margin-bottom: 10px;
        }

        .login-prompt-checkout a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .login-prompt-checkout a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
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
                    <a href="basket.php"><i class="fa-solid fa-basket-shopping"></i></a>
                    <button class="theme-toggle-btn">
                        <i class="fa-solid fa-moon theme-icon moon-icon"></i>
                        <i class="fa-solid fa-sun theme-icon sun-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <?php if(!$is_logged_in): ?>
        <div class="login-prompt-checkout">
            <p>You're checking out as a guest. <a href="login.php">Login</a> to save your details for next time.</p>
        </div>
        <?php endif; ?>

        <div class="checkout-container">
            <div class="checkout-header">
                <h1>Checkout</h1>
                <p>Complete your purchase by filling in your details below</p>
            </div>

            <!-- Checkout Form -->
            <form action="order_confirmation.php" method="POST" class="checkout-form">
                <!-- Contact Information -->
                <div class="form-section">
                    <h2><i class="fas fa-user"></i> Contact Information</h2>
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="checkbox-label">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <label for="newsletter">Keep me updated on new products and offers</label>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="form-section">
                    <h2><i class="fas fa-truck"></i> Shipping Address</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <input type="text" id="address" name="address" placeholder="Street address" required>
                    </div>

                    <div class="form-group">
                        <label for="address2">Apartment, suite, etc. (optional)</label>
                        <input type="text" id="address2" name="address2">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="postcode">Postcode *</label>
                            <input type="text" id="postcode" name="postcode" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="country">Country *</label>
                            <select id="country" name="country" required>
                                <option value="">Select country</option>
                                <option value="UK">United Kingdom</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="AU">Australia</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" placeholder="07700 123456" required>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h2><i class="fas fa-credit-card"></i> Payment Method</h2>
                    
                    <div class="payment-methods">
                        <div class="payment-method selected" onclick="selectPaymentMethod(this, 'card')">
                            <i class="fas fa-credit-card"></i>
                            <span>Credit Card</span>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod(this, 'paypal')">
                            <i class="fab fa-paypal"></i>
                            <span>PayPal</span>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod(this, 'apple')">
                            <i class="fab fa-apple-pay"></i>
                            <span>Apple Pay</span>
                        </div>
                    </div>
                    
                    <input type="hidden" id="payment_method" name="payment_method" value="card">

                    <!-- Credit Card Details (shown by default) -->
                    <div id="card-details">
                        <div class="form-group">
                            <label for="card_number">Card Number *</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date *</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV *</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="card_name">Name on Card *</label>
                            <input type="text" id="card_name" name="card_name" placeholder="John Smith">
                        </div>
                    </div>

                    <!-- PayPal Message (hidden initially) -->
                    <div id="paypal-message" style="display: none; background: #1d1a1e; padding: 20px; border-radius: 8px; text-align: center;">
                        <p>You'll be redirected to PayPal to complete your payment.</p>
                    </div>

                    <!-- Apple Pay Message (hidden initially) -->
                    <div id="apple-message" style="display: none; background: #1d1a1e; padding: 20px; border-radius: 8px; text-align: center;">
                        <p>Pay with Apple Pay on your compatible device.</p>
                    </div>
                </div>

                <!-- Billing Address (same as shipping) -->
                <div class="form-section">
                    <div class="checkbox-label">
                        <input type="checkbox" id="same_address" name="same_address" checked onclick="toggleBillingAddress()">
                        <label for="same_address">Billing address same as shipping</label>
                    </div>

                    <div id="billing-address" style="display: none; margin-top: 20px;">
                        <h3 style="color: var(--secondary); margin-bottom: 15px;">Billing Address</h3>
                        <!-- Repeat address fields for billing -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="billing_first_name">First Name *</label>
                                <input type="text" id="billing_first_name" name="billing_first_name">
                            </div>
                            <div class="form-group">
                                <label for="billing_last_name">Last Name *</label>
                                <input type="text" id="billing_last_name" name="billing_last_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="billing_address">Address *</label>
                            <input type="text" id="billing_address" name="billing_address">
                        </div>
                    </div>
                </div>
            </form>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <div class="summary-items">
                    <?php if(empty($cart_items)): ?>
                        <p style="color: #b0b0b0; text-align: center;">No items in cart</p>
                    <?php else: ?>
                        <!-- This is dummy data for display - in real implementation, you'd loop through cart items -->
                        <div class="summary-item">
                            <div class="item-info">
                                <h4>MSI B650 GAMING PLUS</h4>
                                <p>Motherboard, Socket AM5</p>
                            </div>
                            <span class="item-quantity">x1</span>
                            <span class="item-price">£149.99</span>
                        </div>
                        <div class="summary-item">
                            <div class="item-info">
                                <h4>AMD Ryzen 7 7800X3D</h4>
                                <p>CPU, 8-Core, 4.2GHz</p>
                            </div>
                            <span class="item-quantity">x1</span>
                            <span class="item-price">£369.99</span>
                        </div>
                        <div class="summary-item">
                            <div class="item-info">
                                <h4>Corsair Vengeance 32GB</h4>
                                <p>DDR5 RAM, 6000MHz</p>
                            </div>
                            <span class="item-quantity">x2</span>
                            <span class="item-price">£189.98</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="summary-line">
                    <span>Subtotal:</span>
                    <span>£<?php echo number_format($total_price ?: 709.96, 2); ?></span>
                </div>
                <div class="summary-line">
                    <span>Shipping:</span>
                    <span>£<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span>£<?php echo number_format(($total_price ?: 709.96) + $shipping, 2); ?></span>
                </div>

                <button class="place-order-btn" onclick="placeOrder()">
                    <i class="fas fa-lock" style="margin-right: 8px;"></i> Place Order
                </button>

                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i> Secure Checkout - SSL Encrypted
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectPaymentMethod(element, method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            element.classList.add('selected');
            
            // Update hidden input
            document.getElementById('payment_method').value = method;
            
            // Show/hide relevant payment details
            document.getElementById('card-details').style.display = method === 'card' ? 'block' : 'none';
            document.getElementById('paypal-message').style.display = method === 'paypal' ? 'block' : 'none';
            document.getElementById('apple-message').style.display = method === 'apple' ? 'block' : 'none';
        }
        
        function toggleBillingAddress() {
            const checkbox = document.getElementById('same_address');
            const billingAddress = document.getElementById('billing-address');
            billingAddress.style.display = checkbox.checked ? 'none' : 'block';
        }
        
        function placeOrder() {
            // Simple dummy function - shows a success message
            alert('Thank you for your order! This is a dummy checkout page.');
            
            // You could redirect to a success page
            // window.location.href = 'order_confirmation.php';
        }
    </script>
</body>
</html>