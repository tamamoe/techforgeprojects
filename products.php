<?php
session_start();
require_once 'config.php';

$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$sql = "SELECT p.*, c.categoryname FROM products p 
        LEFT JOIN categories c ON p.categoryid = c.categoryid 
        WHERE p.is_deleted = 0";

if ($category_filter != 'all') { $sql .= " AND c.categoryname = :category"; }

switch($sort_by) {
    case 'low': $sql .= " ORDER BY p.price ASC"; break;
    case 'high': $sql .= " ORDER BY p.price DESC"; break;
    case 'rating': $sql .= " ORDER BY p.rating DESC"; break;
    default: $sql .= " ORDER BY p.createdat DESC"; }

$stmt = $pdo->prepare($sql);
if ($category_filter != 'all') { $stmt->bindParam(':category', $category_filter); }
$stmt->execute(); $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories_sql = "SELECT * FROM categories ORDER BY categoryname";
$categories_stmt = $pdo->query($categories_sql);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">

<head>
<title> All Products - Tech Forge </title>
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
                <li><a href="index.php" ><i class="fas fa-home"></i> <span>Home</span></a></li>
                <li class="flyout-parent">
    				<a href="#" class="active" class="flyout-toggle" >
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
                <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>
    <li><a href="admin_inventory.php"><i class="fas fa-boxes"></i> <span>Manage Stock</span></a></li>
<?php endif; ?>
                    <li><a href="orders.php"><i class="fas fa-receipt"></i> <span>My Orders</span></a></li>
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
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search products...">
                </div>
            </div>
            <div class="nav-right">
                <div class="nav-icons">
                    <a href="basket.html"><i class="fa-solid fa-basket-shopping"></i></a>
                    <button class="theme-toggle-btn">
				    	<i class="fa-solid fa-moon theme-icon moon-icon"></i>
					    <i class="fa-solid fa-sun theme-icon sun-icon"></i>
					</button>
                </div>
            </div>
        </div>

        <form method="GET" action="products.php">
        <div class="products-filter">
            <div class="filter-group">
                <span class="filter-label">Category:</span>
                <select class="filter-select" name="category" onchange="this.form.submit()">
                    <option value="all" <?php echo ($category_filter == 'all') ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['categoryname']); ?>" 
                                <?php echo ($category_filter == $cat['categoryname']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['categoryname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Sort by:</span>
                <select class="filter-select" name="sort" onchange="this.form.submit()">
                    <option value="newest" <?php echo ($sort_by == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_low" <?php echo ($sort_by == 'low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo ($sort_by == 'high') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="rating" <?php echo ($sort_by == 'rating') ? 'selected' : ''; ?>>Highest Rated</option>
                </select>
            </div>
        </div>
        </form>

        <section>
            <div class="section-header">
                <h2>All Products (<?php echo count($products); ?>)</h2>
            </div>
            
            <div class="product-square-grid">
                <?php if(count($products) > 0): ?>
                    <?php foreach($products as $product): ?>
                        <div class="product-box">
                            <div class="product-image">
                            <?php if($product['imageurl']): ?>
                                <img src="<?php echo htmlspecialchars($product['imageurl']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['productname']); ?>"
                                     style="width:100%; height:100%; object-fit:cover; transition: 0.3s; <?php echo ($product['stock'] <= 0) ? 'opacity: 0.4; filter: grayscale(100%);' : ''; ?>">
                            <?php else: ?>
                                Product Image
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['productname']); ?></h3>
                            
                            <div style="margin-bottom: 8px; margin-top: 4px;">
                                <?php if ($product['stock'] <= 0): ?>
                                    <span style="color: #ff4d4d; font-size: 0.75rem; font-weight: 700; background: rgba(220,53,69,0.1); padding: 3px 8px; border-radius: 20px; border: 1px solid rgba(220,53,69,0.3);">
                                        Out of Stock
                                    </span>
                                <?php elseif ($product['stock'] <= 5): ?>
                                    <span style="color: #ffc107; font-size: 0.75rem; font-weight: 700; background: rgba(255,193,7,0.1); padding: 3px 8px; border-radius: 20px; border: 1px solid rgba(255,193,7,0.3);">
                                        Low Stock: <?php echo $product['stock']; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #28a745; font-size: 0.75rem; font-weight: 700; background: rgba(40,167,69,0.1); padding: 3px 8px; border-radius: 20px; border: 1px solid rgba(40,167,69,0.3);">
                                        Stock: <?php echo $product['stock']; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 60)); ?>...</p>
                            
                            <div class="product-price">
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="price" style="color: #ff4d4d; font-weight: 900; font-size: 1.1rem; letter-spacing: 1px;">SOLD OUT!</span>
                                <?php else: ?>
                                    <span class="price">£<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                                
                                <div style="color: #ffa500;">
                                    <?php 
                                    $rating = $product['rating'];
                                    $full_stars = floor($rating);
                                    $half_star = ($rating - $full_stars) >= 0.5;
                                    
                                    for($i = 0; $i < $full_stars; $i++) {
                                        echo '<i class="fas fa-star"></i>';
                                    }
                                    if($half_star) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    }
                                    for($i = $full_stars + ($half_star ? 1 : 0); $i < 5; $i++) {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                    echo " (" . number_format($rating, 1) . ")";
                                    ?>
                                </div>
                            </div>
                            
                            <?php if ($product['stock'] <= 0): ?>
                                <button style="background: #3a3248; color: #888; cursor: not-allowed; border: 1px solid #3a3248;" disabled>Unavailable</button>
                            <?php else: ?>
                                <button onclick="addToCart(<?php echo $product['productid']; ?>)">Add to Cart</button>
                                <?php endif; ?>
                        </div> </div> <?php endforeach; ?>
            <?php endif; ?>
        </div> </section>
</div> <script>
    //https://stackoverflow.com/questions/76004372/i-want-to-add-products-to-the-shopping-cart-in-php my goat thank you for this
    function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Product added to cart!');
        } else {
            alert('Please login to add items to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}
 </script>

</body>
</html>