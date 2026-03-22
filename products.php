<?php
session_start();
require_once 'config.php';

$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT p.*, c.categoryname FROM products p 
        LEFT JOIN categories c ON p.categoryid = c.categoryid 
        WHERE p.is_deleted = 0";

if ($category_filter != 'all') $sql .= " AND c.categoryname = :category";
if (!empty($search_query)) $sql .= " AND (p.productname LIKE :search OR p.description LIKE :search)";

switch($sort_by) {
    case 'low':    $sql .= " ORDER BY p.price ASC"; break;
    case 'high':   $sql .= " ORDER BY p.price DESC"; break;
    case 'rating': $sql .= " ORDER BY p.rating DESC"; break;
    default:       $sql .= " ORDER BY p.createdat DESC";
}

$stmt = $pdo->prepare($sql);
if ($category_filter != 'all') $stmt->bindParam(':category', $category_filter);
if (!empty($search_query)) { $search_param = "%{$search_query}%"; $stmt->bindParam(':search', $search_param); }
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories_sql = "SELECT * FROM categories ORDER BY categoryname";
$categories_stmt = $pdo->query($categories_sql);
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Tech Forge</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Aldrich&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="TechForge_Logo.png">

    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" media="screen and (max-width: 768px)" href="phone.css">
    
    <script src="javascript.js" defer></script>
    <script src="chatbot.js" defer></script>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="techforgecog.png" alt="logo" class="sidebar-logo">
    </div>
    <div class="sidebar-menu">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="products.php" class="active"><i class="fas fa-box-open"></i> <span>Products</span></a></li>
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
    <div class="top-nav mobile-top-nav"">
        <div class="nav-left">
            <button class="nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                <i class="fas fa-bars"></i>
            </button>
            <?php $current_search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>
            <div class="search-bar" style="position: relative;">
                <i class="fas fa-search"></i>
                <form action="products.php" method="GET" style="display:flex; width:100%; margin:0; align-items:center;">
                    <input type="text" name="search" id="live-search-input" placeholder="Search products..."
                           autocomplete="off" value="<?php echo $current_search; ?>" style="flex-grow:1;">
                    <?php if (!empty($current_search)): ?>
                        <a href="products.php" style="color:#718096; text-decoration:none; padding:0 10px;" title="Clear Search">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
                <div id="search-results-dropdown" class="search-dropdown" style="display:none;"></div>
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
                    <option value="newest"    <?php echo ($sort_by == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_low" <?php echo ($sort_by == 'low')    ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high"<?php echo ($sort_by == 'high')   ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="rating"    <?php echo ($sort_by == 'rating') ? 'selected' : ''; ?>>Highest Rated</option>
                </select>
            </div>
        </div>
    </form>

    <section>
        <div class="section-header">
            <h2 class="aldrich-regular">All Products (<?php echo count($products); ?>)</h2>
        </div>

        <?php if (!empty($search_query)): ?>
            <div style="margin-bottom:20px; font-size:1.1em; color:#aaa;">
                Showing results for: <strong style="color:#9f7aea;">"<?php echo htmlspecialchars($search_query); ?>"</strong>
            </div>
        <?php endif; ?>

        <div class="product-square-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <div class="product-box">
                <div class="product-image">
                            <?php if ($product['imageurl']): ?>
                                <img src="<?php echo htmlspecialchars($product['imageurl']); ?>"
                                     alt="<?php echo htmlspecialchars($product['productname']); ?>"
                                     style="width:100%; height:100%; object-fit:cover; transition: 0.3s; <?php echo ($product['stock'] <= 0) ? 'opacity: 0.4; filter: grayscale(100%);' : ''; ?>">
                            <?php else: ?>
                                Product Image
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['productname']); ?></h3>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; margin-top: 4px;">
                                <span class="category-badge"><?php echo htmlspecialchars($product['categoryname']); ?></span>
                                
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
                                
                                <div style="color:#ffa500;">
                                    <?php
                                    $rating     = $product['rating'];
                                    $full_stars = floor($rating);
                                    $half_star  = ($rating - $full_stars) >= 0.5;
                                    for ($i = 0; $i < $full_stars; $i++) echo '<i class="fas fa-star"></i>';
                                    if ($half_star) echo '<i class="fas fa-star-half-alt"></i>';
                                    for ($i = $full_stars + ($half_star ? 1 : 0); $i < 5; $i++) echo '<i class="far fa-star"></i>';
                                    echo " (" . number_format($rating, 1) . ")";
                                    ?>
                                </div>
                            </div>
                            
                            <div class="product-btn-group">
                                <?php if ($product['stock'] <= 0): ?>
                                    <button class="btn-cart" style="background: #3a3248; color: #888; cursor: not-allowed; border: 1px solid #3a3248;" disabled>
                                        <i class="fas fa-ban"></i> Unavailable
                                    </button>
                                <?php else: ?>
                                    <button class="btn-cart" onclick="addToCart(<?php echo $product['productid']; ?>)">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn-compare"
                                    onclick="handleCompare(this)"
                                    data-id="<?php echo $product['productid']; ?>"
                                    data-name="<?php echo htmlspecialchars($product['productname']); ?>"
                                    data-price="<?php echo number_format($product['price'], 2); ?>"
                                    data-img="<?php echo htmlspecialchars($product['imageurl']); ?>"
                                    data-category="<?php echo htmlspecialchars($product['categoryname']); ?>"
                                    data-rating="<?php echo number_format($product['rating'], 1); ?>">
                                    <i class="fas fa-balance-scale"></i> Compare
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column:1/-1; text-align:center; padding:60px 20px;">
                    <i class="fas fa-search" style="font-size:3rem; color:#3a3248; margin-bottom:15px; display:block;"></i>
                    <p style="font-size:1.2em; color:#aaa;">No products found for "<strong style="color:#9f7aea;"><?php echo htmlspecialchars($search_query); ?></strong>"</p>
                    <p style="color:#666; margin-top:8px;">Try checking your spelling or using less specific keywords.</p>
                    <a href="products.php" style="display:inline-block; margin-top:20px; background:var(--primary); color:#fff; padding:10px 24px; border-radius:8px; text-decoration:none; font-weight:600;">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<div id="compare-bar">
    <div class="compare-bar-inner">
        <div class="compare-bar-left">
            <i class="fas fa-balance-scale" style="color:#9f7aea;"></i>
            <span class="compare-bar-label">Compare</span>
            <span class="compare-count-badge" id="compare-count">0 / 2</span>
        </div>
        <div id="compare-slots"></div>
        <div class="compare-bar-right">
            <button id="compare-clear-bar" onclick="clearCompare()">
                <i class="fas fa-trash-alt"></i> Clear all
            </button>
            <button id="compare-now-btn" onclick="goToCompare()" disabled>
                <i class="fas fa-columns"></i> Compare now
            </button>
        </div>
    </div>
</div>

<div id="compare-toast"></div>

<style>
/* ── Product button group ── */
.product-btn-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-top: 10px;
}
.btn-cart, .btn-compare {
    width: 100%; padding: 9px 12px; border: none; border-radius: 6px;
    cursor: pointer; font-weight: 600; font-size: 0.88rem;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: all 0.25s ease;
}
.btn-cart { background: var(--primary); color: #fff; }
.btn-cart:hover { background: var(--secondary); transform: translateY(-1px); }
.btn-compare { background: transparent; color: var(--secondary); border: 1.5px solid var(--secondary); }
.btn-compare:hover { background: rgba(159,122,234,0.12); color: #fff; border-color: #fff; }
.btn-compare.added { background: rgba(107,70,193,0.25); color: #fff; border-color: var(--primary); }

.category-badge {
    display: inline-block; background: rgba(107,70,193,0.2); color: #a78bfa;
    font-size: 0.72rem; font-weight: 700; padding: 2px 8px;
    border-radius: 20px; letter-spacing: 0.04em; text-transform: uppercase;
}

/* ── Compare bar ── */
#compare-bar {
    position: fixed; bottom: 0; left: 70px; right: 0;
    background: #2a242d; border-top: 2px solid var(--primary);
    z-index: 5000; transform: translateY(100%); transition: transform 0.3s ease;
    padding: 10px 20px;
}
#compare-bar.visible { transform: translateY(0); }
.compare-bar-inner {
    display: flex; align-items: center; gap: 14px; max-width: 1400px; margin: 0 auto;
}
.compare-bar-left { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.compare-bar-label { color: #fff; font-weight: 600; font-size: 0.9rem; }
.compare-count-badge {
    background: rgba(107,70,193,0.35); color: #9f7aea;
    font-size: 0.75rem; padding: 2px 9px; border-radius: 20px; font-weight: 600;
}
#compare-slots { display: flex; gap: 10px; flex: 1; }
.compare-bar-slot {
    background: #1d1a1e; border: 1.5px dashed #3a3248; border-radius: 8px;
    width: 140px; height: 54px; display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; position: relative; gap: 8px; padding: 6px 8px; transition: border-color 0.2s;
}
.compare-bar-slot.filled { border-style: solid; border-color: var(--primary); }
.compare-bar-slot img { width: 36px; height: 36px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
.slot-name { font-size: 0.7rem; color: #c4b5d4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 68px; }
.slot-empty { font-size: 0.78rem; color: #5a4e6a; }
.compare-slot-remove {
    position: absolute; top: -7px; right: -7px; width: 18px; height: 18px;
    border-radius: 50%; background: var(--primary); border: none; color: #fff;
    font-size: 0.6rem; cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: background 0.2s;
}
.compare-slot-remove:hover { background: #e53e3e; }
.compare-bar-right { display: flex; gap: 10px; flex-shrink: 0; margin-left: auto; }
#compare-clear-bar {
    background: transparent; border: 1px solid #3a3248; color: #a08cbf;
    padding: 7px 14px; border-radius: 7px; font-size: 0.82rem; cursor: pointer;
    display: flex; align-items: center; gap: 6px; transition: all 0.2s;
}
#compare-clear-bar:hover { border-color: #e53e3e; color: #e53e3e; }
#compare-now-btn {
    background: var(--primary); border: none; color: #fff; padding: 7px 18px;
    border-radius: 7px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 6px; transition: all 0.2s;
}
#compare-now-btn:hover:not(:disabled) { background: var(--secondary); }
#compare-now-btn:disabled { opacity: 0.35; cursor: not-allowed; }

/* ── Toast ── */
#compare-toast {
    position: fixed; bottom: 90px; left: 50%; transform: translateX(-50%) translateY(10px);
    background: #2a242d; border: 1px solid var(--primary); color: #c4b5d4;
    padding: 10px 20px; border-radius: 8px; font-size: 0.85rem; font-weight: 500;
    z-index: 9999; opacity: 0; transition: opacity 0.3s, transform 0.3s; pointer-events: none;
    white-space: nowrap;
}
#compare-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

/* ── Light mode overrides ── */
body.light-mode #compare-bar       { background: #fff; border-top-color: var(--primary); }
body.light-mode .compare-bar-slot  { background: #f5f0ff; border-color: #ddd6fe; }
body.light-mode .compare-bar-slot.filled { border-color: var(--primary); }
body.light-mode .slot-name         { color: #2d3748; }
body.light-mode .slot-empty        { color: #b0a0cc; }
body.light-mode .compare-bar-label { color: var(--primary); }
body.light-mode #compare-clear-bar { border-color: #ddd6fe; color: #553c9a; }
body.light-mode #compare-clear-bar:hover { border-color: #e53e3e; color: #e53e3e; }
body.light-mode #compare-toast     { background: #fff; border-color: var(--primary); color: #2d3748; }
</style>

<script>
/* ── Add to cart ── */
function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) showToast('Added to cart!');
        else showToast('Please log in to add items to your cart.');
    })
    .catch(() => showToast('Something went wrong. Please try again.'));
}

/* ── Toast ── */
function showToast(msg) {
    const t = document.getElementById('compare-toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

/* ── Compare state ── */
const compareItems = [];
const MAX_COMPARE  = 2;

function handleCompare(btn) {
    const id = btn.dataset.id;

    if (compareItems.find(i => i.id === id)) {
        removeFromCompare(id);
        showToast('Removed from comparison.');
        return;
    }

    if (compareItems.length >= MAX_COMPARE) {
        showToast('You can only compare 2 products at a time.');
        return;
    }

    compareItems.push({
        id:       id,
        name:     btn.dataset.name,
        price:    btn.dataset.price,
        img:      btn.dataset.img,
        category: btn.dataset.category,
        rating:   btn.dataset.rating
    });

    btn.innerHTML = '<i class="fas fa-check"></i> Added';
    btn.classList.add('added');
    showToast(btn.dataset.name + ' added to compare.');
    renderCompareBar();
}

function removeFromCompare(id) {
    const idx = compareItems.findIndex(i => i.id === id);
    if (idx === -1) return;
    compareItems.splice(idx, 1);

    document.querySelectorAll('.btn-compare').forEach(btn => {
        if (btn.dataset.id === id) {
            btn.innerHTML = '<i class="fas fa-balance-scale"></i> Compare';
            btn.classList.remove('added');
        }
    });

    renderCompareBar();
}

function clearCompare() {
    compareItems.length = 0;
    document.querySelectorAll('.btn-compare').forEach(btn => {
        btn.innerHTML = '<i class="fas fa-balance-scale"></i> Compare';
        btn.classList.remove('added');
    });
    renderCompareBar();
    showToast('Comparison cleared.');
}

/* ── Redirect to compare.php with both product IDs ── */
function goToCompare() {
    if (compareItems.length < 2) return;
    const cat = encodeURIComponent(compareItems[0].category.toLowerCase());
    window.location.href = `compare.php?a=${compareItems[0].id}&b=${compareItems[1].id}&category=${cat}`;
}

/* ── Render bar ── */
function renderCompareBar() {
    const bar     = document.getElementById('compare-bar');
    const slotsEl = document.getElementById('compare-slots');
    const countEl = document.getElementById('compare-count');
    const nowBtn  = document.getElementById('compare-now-btn');

    countEl.textContent = compareItems.length + ' / 2';
    nowBtn.disabled     = compareItems.length < 2;
    slotsEl.innerHTML   = '';

    for (let i = 0; i < MAX_COMPARE; i++) {
        const slot = document.createElement('div');
        slot.className = 'compare-bar-slot' + (compareItems[i] ? ' filled' : '');

        if (compareItems[i]) {
            const item = compareItems[i];
            slot.innerHTML = `
                <img src="${item.img}" alt="${item.name}">
                <span class="slot-name" title="${item.name}">${item.name}</span>
                <button class="compare-slot-remove" title="Remove ${item.name}">✕</button>
            `;
            slot.querySelector('.compare-slot-remove').onclick = () => {
                removeFromCompare(item.id);
                showToast('Removed from comparison.');
            };
        } else {
            slot.innerHTML = `<span class="slot-empty"><i class="fas fa-plus" style="font-size:0.7rem;margin-right:4px;"></i>Add</span>`;
        }

        slotsEl.appendChild(slot);
    }

    bar.classList.toggle('visible', compareItems.length > 0);
}
</script>

</body>
</html>