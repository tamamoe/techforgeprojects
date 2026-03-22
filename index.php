<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Tech Forge</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Aldrich&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="TechForge_Logo.png">

    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="carousel.css">
    
    <link rel="stylesheet" media="screen and (max-width: 768px)" href="phone.css">
    
    <script src="javascript.js" defer></script>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <img src="techforgecog.png" alt="logo" class="sidebar-logo">
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> <span>Home</span></a></li>
                <li><a href="products.php"><i class="fas fa-box-open"></i> <span>Product</span></a></li>
				<li><a href="compare.php"><i class="fas fa-scale-balanced"></i> <span>Compare</span></a></li>
                <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
                <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>


<?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>
                    <li><a href="admin_panel.php"><i class="fas fa-shield-halved"></i> <span>Admin Panel</span></a></li>
                    <li><a href="orders.php"><i class="fas fa-receipt"></i> <span>All Orders</span></a></li>
                <?php else: ?>
                    <li><a href="orders.php"><i class="fas fa-receipt"></i> <span>My Orders</span></a></li>
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
        <div class="top-nav mobile-top-nav">
            <div class="nav-left">
                <button class="nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-bar" style="position: relative;">
   					<i class="fas fa-search"></i>
				    <form action="products.php" method="GET" style="display: flex; width: 100%; margin: 0;">
        				<input type="text" name="search" id="live-search-input" placeholder="Search products..." autocomplete="off">
    				</form>
   					<div id="search-results-dropdown" class="search-dropdown" style="display: none;"></div>
				</div>
            </div>
            <div class="nav-right mobile-nav-right">
                <div class="nav-icons">
                    <a href="basket.php"><i class="fa-solid fa-basket-shopping"></i></a>
                    <button class="theme-toggle-btn">
                        <i class="fa-solid fa-moon theme-icon moon-icon"></i>
                        <i class="fa-solid fa-sun theme-icon sun-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="home-page-container">
            <div class="tagline-section">
                <h1>Ignite the Future of Tech.</h1>
            </div>

            <div class="carousel-wrapper">
                <a href="#item1" class="nav-btn prev"><i class="fas fa-chevron-left"></i></a> 
                <a href="#item5" class="nav-btn next"><i class="fas fa-chevron-right"></i></a>

                <div class="carousel">
                    <div class="card" id="item1">
                        <img src="GPU1.jpg" alt="RTX 30 Series">
                    </div>
                    
                    <div class="card" id="item2">
                        <img src="mobo1.jpg" alt="Gigabyte Eagle">
                    </div>
                    
                    <div class="card" id="item3">
                        <img src="ram1.jpg" alt="Crucial DDR5">
                    </div>
                    
                    <div class="card" id="item4">
                        <img src="GPU2.jpg" alt="Gigabyte Windforce">
                    </div>
                    
                    <div class="card" id="item5">
                        <img src="ram2.jpg" alt="Vengeance RGB">
                    </div>
                </div>
            </div>

            <div class="product-row-container">
                <div class="product-grid">
                    <!-- homepage showcase cards -- these are category-level, not specific products -->                    <!-- clicking add to compare just opens the compare page pre-filtered by category -->
                    <div class="product-card">
                        <div class="card-image"><img src="GPU1.jpg" alt="GPU"></div>
                        <h3>GPU</h3>
                        <button class="add-btn" onclick="window.location.href='compare.php?category=gpu'">
                            <i class="fas fa-scale-balanced"></i> Add to Compare
                        </button>
                    </div>
                    <div class="product-card">
                        <div class="card-image"><img src="mobo1.jpg" alt="CPU"></div>
                        <h3>CPU</h3>
                        <button class="add-btn" onclick="window.location.href='compare.php?category=cpu'">
                            <i class="fas fa-scale-balanced"></i> Add to Compare
                        </button>
                    </div>
                    <div class="product-card">
                        <div class="card-image"><img src="ram1.jpg" alt="RAM"></div>
                        <h3>RAM</h3>
                        <button class="add-btn" onclick="window.location.href='compare.php?category=ram'">
                            <i class="fas fa-scale-balanced"></i> Add to Compare
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
