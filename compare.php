<?php
session_start();
require_once 'config.php';

$cat_filter = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : '';
$id_a = isset($_GET['a']) ? intval($_GET['a']) : 0;
$id_b = isset($_GET['b']) ? intval($_GET['b']) : 0;
$p1 = null; $p2 = null;

if ($id_a) { $s = $pdo->prepare("SELECT p.*, c.categoryname FROM products p LEFT JOIN categories c ON p.categoryid = c.categoryid WHERE p.productid = :id"); $s->execute([':id' => $id_a]); $p1 = $s->fetch(PDO::FETCH_ASSOC); }
if ($id_b) { $s = $pdo->prepare("SELECT p.*, c.categoryname FROM products p LEFT JOIN categories c ON p.categoryid = c.categoryid WHERE p.productid = :id"); $s->execute([':id' => $id_b]); $p2 = $s->fetch(PDO::FETCH_ASSOC); }
if ($p1 && !$cat_filter) $cat_filter = strtolower($p1['categoryname']);

// pull all products optionally filtered by category for the dropdowns
$lsql = "SELECT p.productid, p.productname, c.categoryname FROM products p LEFT JOIN categories c ON p.categoryid = c.categoryid";
if ($cat_filter) $lsql .= " WHERE LOWER(c.categoryname) = " . $pdo->quote($cat_filter);
$lsql .= " ORDER BY p.productname";
$list_products = $pdo->query($lsql)->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT * FROM categories ORDER BY categoryname")->fetchAll(PDO::FETCH_ASSOC);

// same star rendering as products.php lol
// https://stackoverflow.com/questions/44491290/display-star-in-front-end-based-on-rating
function renderStars($rating) {
    $full = floor($rating); $half = ($rating - $full) >= 0.5; $out = '';
    for ($i=0; $i<$full; $i++) $out .= '<i class="fas fa-star"></i>';
    if ($half) $out .= '<i class="fas fa-star-half-alt"></i>';
    for ($i=$full+($half?1:0); $i<5; $i++) $out .= '<i class="far fa-star"></i>';
    return $out . ' (' . number_format($rating,1) . ')';
}

$same_cat = ($p1 && $p2) ? strtolower($p1['categoryname']) === strtolower($p2['categoryname']) : true;
$showing_compare = $p1 && $p2 && $same_cat;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Compare Products - Tech Forge</title>
<link rel="stylesheet" href="Stylesheet.css">
<script src="javascript.js" defer></script>
<link rel="shortcut icon" href="TechForge_Logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ── Two-column card grid ── */
.cmp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    position: relative;
    margin-bottom: 32px;
}
.cmp-vs-divider {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: var(--primary);
    color: #fff;
    font-weight: 800;
    font-size: 1rem;
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    z-index: 2;
    box-shadow: 0 0 0 4px var(--background, #1a1a1a);
}

/* ── Remove button on card ── */
.compare-remove-btn {
    position: absolute; top: 10px; right: 10px;
    background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.4);
    color: #ff6b6b; border-radius: 20px;
    padding: 3px 10px; font-size: 0.75rem; font-weight: 600;
    text-decoration: none; transition: 0.2s; z-index: 3;
}
.compare-remove-btn:hover { background: rgba(220,53,69,0.4); color: #fff; }

/* ── Category badge (matches products.php) ── */
.category-badge {
    display: inline-block; background: rgba(107,70,193,0.2); color: #a78bfa;
    font-size: 0.72rem; font-weight: 700; padding: 2px 8px;
    border-radius: 20px; letter-spacing: 0.04em; text-transform: uppercase;
    margin-bottom: 6px;
}

/* ── Spec table section ── */
.compare-specs-section { margin-top: 8px; }
.compare-specs-title {
    font-size: 1.1rem; font-weight: 700; color: #fff;
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.spec-table-scroll { overflow-x: auto; border-radius: 10px; border: 1px solid #3a3248; }
.compare-spec-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
.compare-spec-table thead tr { background: var(--primary); }
.compare-spec-table th {
    padding: 12px 16px; text-align: left; color: #fff;
    font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; white-space: nowrap;
}
.compare-spec-table td {
    padding: 11px 16px; border-bottom: 1px solid #2e2933;
    color: #c4b5d4; vertical-align: middle;
}
.compare-spec-table tr:last-child td { border-bottom: none; }
.compare-spec-table tr:nth-child(even) td { background: rgba(255,255,255,0.02); }
.spec-label-cell {
    color: #a08cbf; font-weight: 600; font-size: 0.8rem;
    background: #231f27 !important; white-space: nowrap; width: 140px;
}
.spec-row-diff td:not(.spec-label-cell) { color: #fff; }
.spec-winner { background: rgba(107,70,193,0.18) !important; color: #fff !important; font-weight: 600; }
.spec-trophy { color: #ffd700; margin-left: 5px; font-size: 0.8rem; }
.compare-verdict {
    margin-top: 18px; padding: 14px 18px;
    background: rgba(107,70,193,0.12); border: 1px solid rgba(107,70,193,0.3);
    border-left: 4px solid var(--primary); border-radius: 8px;
    color: #c4b5d4; font-size: 0.9rem;
}
.compare-verdict i { color: #ffd700; margin-right: 6px; }

/* ── Picker form ── */
.compare-pick-form { margin-top: 8px; }
.pick-notice {
    display: flex; align-items: center; gap: 10px;
    background: rgba(107,70,193,0.1); border: 1px solid rgba(107,70,193,0.3);
    border-left: 4px solid var(--primary); border-radius: 8px;
    padding: 12px 16px; color: #c4b5d4; font-size: 0.9rem; margin-bottom: 24px;
}
.pick-notice--success { background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.3); border-left-color: #10b981; color: #6ee7b7; }
.pick-notice i { font-size: 1rem; flex-shrink: 0; }
.pick-slots { display: grid; grid-template-columns: 1fr auto 1fr; gap: 16px; align-items: center; margin-bottom: 24px; }
.pick-slot-card {
    background: #1d1a1e; border: 1.5px dashed #3a3248; border-radius: 12px;
    padding: 24px 20px; text-align: center; transition: border-color 0.2s;
}
.pick-slot-card:focus-within { border-color: var(--primary); border-style: solid; }
.pick-slot-icon {
    width: 48px; height: 48px; background: rgba(107,70,193,0.15); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px; color: var(--secondary); font-size: 1.2rem;
}
.pick-slot-label { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.06em; color: #a08cbf; margin-bottom: 14px; }
.pick-select {
    width: 100%; background: #2a242d; border: 1px solid #3a3248; color: #fff;
    border-radius: 8px; padding: 10px 32px 10px 14px; font-size: 0.88rem; cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239f7aea' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center; transition: border-color 0.2s;
}
.pick-select:focus { outline: none; border-color: var(--primary); }
.pick-select:disabled { opacity: 0.4; cursor: not-allowed; }
.pick-vs {
    width: 44px; height: 44px; background: var(--primary); color: #fff;
    font-weight: 800; font-size: 0.9rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    box-shadow: 0 0 0 4px rgba(107,70,193,0.15);
}
.pick-actions { display: flex; align-items: center; justify-content: center; gap: 14px; }
.pick-btn {
    background: var(--primary); color: #fff; border: none;
    padding: 12px 28px; border-radius: 8px; font-size: 0.92rem; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.2s, transform 0.15s;
}
.pick-btn:hover:not(:disabled) { background: var(--secondary); transform: translateY(-1px); }
.pick-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pick-reset { color: #6b5a7e; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 5px; transition: color 0.2s; }
.pick-reset:hover { color: #a08cbf; }

/* ── Light mode ── */
body.light-mode .cmp-vs-divider      { box-shadow: 0 0 0 4px #f5f0ff; }
body.light-mode .spec-table-scroll   { border-color: #ddd6fe; }
body.light-mode .compare-spec-table td { color: #2d3748; border-bottom-color: #ede9f6; }
body.light-mode .spec-label-cell     { background: #ede9f6 !important; color: #553c9a; }
body.light-mode .spec-row-diff td:not(.spec-label-cell) { color: #1a1a1a; }
body.light-mode .spec-winner         { background: rgba(107,70,193,0.1) !important; color: #1a1a1a !important; }
body.light-mode .compare-verdict     { background: #f5f0ff; border-color: #ddd6fe; color: #2d3748; }
body.light-mode .pick-slot-card      { background: #f5f0ff; border-color: #ddd6fe; }
body.light-mode .pick-slot-label     { color: #553c9a; }
body.light-mode .pick-select         { background: #fff; border-color: #ddd6fe; color: #2d3748; }
body.light-mode .pick-notice         { background: #f5f0ff; border-color: #ddd6fe; color: #553c9a; }
body.light-mode .pick-reset          { color: #a78bfa; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><img src="techforgecog.png" alt="logo" class="sidebar-logo"></div>
    <div class="sidebar-menu">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="products.php"><i class="fas fa-box-open"></i> <span>Products</span></a></li>
            <li><a href="compare.php" class="active"><i class="fas fa-scale-balanced"></i> <span>Compare</span></a></li>
            <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
            <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
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
            <div class="search-bar"><i class="fas fa-search"></i><input type="text" placeholder="Search products..."></div>
        </div>
        <div class="nav-right">
            <div class="nav-icons">
                <a href="basket.php"><i class="fa-solid fa-basket-shopping"></i></a>
                <button class="theme-toggle-btn"><i class="fa-solid fa-moon theme-icon moon-icon"></i><i class="fa-solid fa-sun theme-icon sun-icon"></i></button>
            </div>
        </div>
    </div>

    <div class="compare-wrapper">
        <div class="section-header"><h2><i class="fas fa-scale-balanced" style="margin-right:8px"></i>Compare Products</h2></div>

        <!-- me when i filter -->
        <form method="GET" action="compare.php" class="products-filter compare-filter-bar">
            <div class="filter-group">
                <span class="filter-label"><i class="fas fa-filter" style="margin-right:5px"></i>Category:</span>
                <select class="filter-select" name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): $cn = strtolower($cat['categoryname']); ?>
                        <option value="<?= htmlspecialchars($cn) ?>" <?= $cat_filter===$cn ? 'selected' : '' ?>><?= htmlspecialchars($cat['categoryname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <span class="filter-label" style="font-size:0.85rem; opacity:0.7">Comparisons are limited to one category at a time — max 2 products</span>
            <?php if ($id_a) echo '<input type="hidden" name="a" value="'.$id_a.'">'; ?>
            <?php if ($id_b) echo '<input type="hidden" name="b" value="'.$id_b.'">'; ?>
        </form>

        <?php if ($p1 && $p2 && !$same_cat): ?>
        <!-- cross-category error -->
        <div class="compare-error-banner">
            <i class="fas fa-triangle-exclamation"></i>
            Cannot compare a <strong><?= htmlspecialchars($p1['categoryname']) ?></strong> with a <strong><?= htmlspecialchars($p2['categoryname']) ?></strong>.
            Please select two products from the same category.
            <a href="compare.php" style="color:var(--secondary); margin-left:10px">Start over</a>
        </div>

        <?php elseif ($showing_compare): ?>

        <!-- side-by-side product cards, styled to match products.php -->
        <div class="cmp-grid">
            <?php
            $pairs = [[$p1, $id_a, $id_b], [$p2, $id_b, $id_a]];
            foreach($pairs as [$p, $self_id, $other_id]):
            ?>
            <div class="product-box" style="position:relative;">
                <a href="compare.php?a=<?= $other_id ?>&category=<?= urlencode($cat_filter) ?>" class="compare-remove-btn" title="Remove">✕ Remove</a>
                <div class="product-image">
                    <?php if ($p['imageurl']): ?>
                        <img src="<?= htmlspecialchars($p['imageurl']) ?>" alt="<?= htmlspecialchars($p['productname']) ?>">
                    <?php else: ?>
                        <span style="color:#888">No Image</span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <span class="category-badge"><?= htmlspecialchars($p['categoryname']) ?></span>
                    <h3><?= htmlspecialchars($p['productname']) ?></h3>
                    <p style="font-size:0.82rem;color:#9a8aaa;margin-bottom:8px;line-height:1.5"><?= htmlspecialchars(substr($p['description'], 0, 90)) ?>...</p>
                    <div class="product-price">
                        <span class="price">£<?= number_format($p['price'], 2) ?></span>
                        <span style="color:#ffa500;font-size:0.85rem"><?= renderStars($p['rating']) ?></span>
                    </div>
                    <button onclick="addToCart(<?= $p['productid'] ?>)"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="cmp-vs-divider">VS</div>
        </div>

        <!-- compare specs section -->
        <!-- JS populates this from the PRODUCTS table below, matched by productid -->
        <div class="compare-specs-section">
            <h2 class="compare-specs-title"><i class="fas fa-microchip"></i> Full Specifications</h2>
            <div id="compare-specs-table"
                 data-id-a="<?= $id_a ?>"
                 data-id-b="<?= $id_b ?>"
                 data-name-a="<?= htmlspecialchars($p1['productname']) ?>"
                 data-name-b="<?= htmlspecialchars($p2['productname']) ?>">
                <p style="color:#888; padding:20px; text-align:center"><i class="fas fa-spinner fa-spin"></i> Loading specifications...</p>
            </div>
        </div>

        <?php else: ?>

        <!-- picker UI -->
        <form method="GET" action="compare.php" class="compare-pick-form">
            <?php if ($cat_filter) echo '<input type="hidden" name="category" value="'.htmlspecialchars($cat_filter).'">'; ?>

            <?php if (!$cat_filter): ?>
            <div class="pick-notice">
                <i class="fas fa-circle-info"></i>
                Select a category above first to filter the product list.
            </div>
            <?php elseif ($p1 && !$p2): ?>
            <div class="pick-notice pick-notice--success">
                <i class="fas fa-check-circle"></i>
                <strong><?= htmlspecialchars($p1['productname']) ?></strong> locked in — now pick a second <?= htmlspecialchars($p1['categoryname']) ?>.
            </div>
            <?php endif; ?>

            <div class="pick-slots">
                <!-- Slot A -->
                <div class="pick-slot-card">
                    <div class="pick-slot-icon"><i class="fas fa-cube"></i></div>
                    <div class="pick-slot-label">Product 1</div>
                    <select name="a" class="pick-select" <?= !$list_products ? 'disabled' : '' ?>>
                        <option value="">— Select a product —</option>
                        <?php foreach($list_products as $lp): ?>
                        <option value="<?= $lp['productid'] ?>" <?= ($id_a == $lp['productid']) ? 'selected' : '' ?>><?= htmlspecialchars($lp['productname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- VS pill -->
                <div class="pick-vs">VS</div>

                <!-- Slot B -->
                <div class="pick-slot-card">
                    <div class="pick-slot-icon"><i class="fas fa-cube"></i></div>
                    <div class="pick-slot-label">Product 2</div>
                    <select name="b" class="pick-select" <?= !$list_products ? 'disabled' : '' ?>>
                        <option value="">— Select a product —</option>
                        <?php foreach($list_products as $lp): ?>
                        <option value="<?= $lp['productid'] ?>" <?= ($id_b == $lp['productid']) ? 'selected' : '' ?>><?= htmlspecialchars($lp['productname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pick-actions">
                <button type="submit" class="pick-btn" <?= !$cat_filter ? 'disabled' : '' ?>>
                    <i class="fas fa-scale-balanced"></i> Compare Now
                </button>
                <?php if ($cat_filter): ?>
                <a href="compare.php" class="pick-reset"><i class="fas fa-rotate-left"></i> Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <?php endif; ?>
    </div>
</div>

<script>
// this hurts to hardcode but from chatbot
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/find
const PRODUCTS = [
  { id:1,  name:"MSI B650 GAMING PLUS WIFI",       category:"motherboard", price:134.49, stock:15, rating:4.5, socket:"AM5",     chipset:"B650",  ddr:"DDR5", formfactor:"ATX",    wifi:"Yes",  specs:"ATX, Socket AM5, DDR5, WiFi, Bluetooth" },
  { id:2,  name:"Gigabyte B850 A ELITE WF7",        category:"motherboard", price:219.99, stock:12, rating:4.6, socket:"AM5",     chipset:"B850",  ddr:"DDR5", formfactor:"ATX",    wifi:"Yes",  specs:"Socket AM5, DDR5, 256GB max memory, HDMI" },
  { id:3,  name:"ASUS B650E MAX GAMING WIFI W",     category:"motherboard", price:173.99, stock:18, rating:4.4, socket:"AM5",     chipset:"B650E", ddr:"DDR5", formfactor:"ATX",    wifi:"Yes",  specs:"White, Socket AM5, DDR5, 256GB max" },
  { id:4,  name:"MSI Z790 GAMING PLUS WIFI",        category:"motherboard", price:189.99, stock:10, rating:4.5, socket:"LGA1700", chipset:"Z790",  ddr:"DDR5", formfactor:"ATX",    wifi:"Yes",  specs:"ATX, Intel LGA 1700, DDR5, WiFi" },
  { id:5,  name:"Gigabyte H610M H V3 DDR4",         category:"motherboard", price:52.97,  stock:25, rating:4.2, socket:"LGA1700", chipset:"H610",  ddr:"DDR4", formfactor:"mATX",   wifi:"No",   specs:"Supports Intel Core 14th Gen, LGA 1700, DDR4" },
  { id:6,  name:"Gigabyte B760M GAMING X AX",       category:"motherboard", price:143.84, stock:14, rating:4.3, socket:"LGA1700", chipset:"B760",  ddr:"DDR5", formfactor:"mATX",   wifi:"Yes",  specs:"Micro ATX, LGA 1700, DDR5, WiFi" },
  { id:7,  name:"AMD Ryzen 7 7800X3D",              category:"cpu", price:325.97, stock:8,  rating:4.8, socket:"AM5",     brand:"AMD",   cores:8,  boost:"5.0 GHz",  tdp:"120W" },
  { id:8,  name:"AMD Ryzen 5 7600X",                category:"cpu", price:150.80, stock:12, rating:4.6, socket:"AM5",     brand:"AMD",   cores:6,  boost:"5.3 GHz",  tdp:"105W" },
  { id:9,  name:"Intel Core i9-14900K",             category:"cpu", price:404.00, stock:6,  rating:4.7, socket:"LGA1700", brand:"Intel", cores:24, boost:"6.0 GHz",  tdp:"125W" },
  { id:10, name:"Intel Core i5-14600K",             category:"cpu", price:217.62, stock:10, rating:4.5, socket:"LGA1700", brand:"Intel", cores:14, boost:"5.3 GHz",  tdp:"125W" },
  { id:11, name:"Intel Core i7-14700KF",            category:"cpu", price:282.62, stock:7,  rating:4.6, socket:"LGA1700", brand:"Intel", cores:20, boost:"5.6 GHz",  tdp:"125W" },
  { id:12, name:"XFX Radeon RX 9060XT",             category:"gpu", price:361.92, stock:10, rating:4.5, brand:"AMD",    vram:"16 GB", memtype:"GDDR6", arch:"RDNA 4",    tdp:"150W" },
  { id:13, name:"PowerColor RX 7900 XTX 24GB",      category:"gpu", price:719.99, stock:5,  rating:4.7, brand:"AMD",    vram:"24 GB", memtype:"GDDR6", arch:"RDNA 3",    tdp:"355W" },
  { id:14, name:"PowerColor RX 6800 XT",            category:"gpu", price:940.99, stock:8,  rating:4.6, brand:"AMD",    vram:"16 GB", memtype:"GDDR6", arch:"RDNA 2",    tdp:"300W" },
  { id:15, name:"ASUS Dual GeForce RTX 5060",       category:"gpu", price:274.99, stock:12, rating:4.4, brand:"NVIDIA", vram:"8 GB",  memtype:"GDDR7", arch:"Blackwell", tdp:"150W" },
  { id:16, name:"Gigabyte GeForce RTX 5070",        category:"gpu", price:509.99, stock:6,  rating:4.8, brand:"NVIDIA", vram:"12 GB", memtype:"GDDR7", arch:"Blackwell", tdp:"250W" },
  { id:17, name:"Gigabyte GeForce RTX 3050",        category:"gpu", price:176.77, stock:15, rating:4.2, brand:"NVIDIA", vram:"6 GB",  memtype:"GDDR6", arch:"Ampere",    tdp:"130W" },
  { id:18, name:"Crucial DDR5 16GB 4800MHz",        category:"ram", price:97.99,  stock:20, rating:4.5, capacity:"16 GB", speed:"4800 MHz", ddr:"DDR5", formfactor:"SODIMM", cl:"CL40" },
  { id:19, name:"Crucial DDR5 8GB 4800MHz",         category:"ram", price:34.50,  stock:25, rating:4.4, capacity:"8 GB",  speed:"4800 MHz", ddr:"DDR5", formfactor:"SODIMM", cl:"CL40" },
  { id:20, name:"Acer Predator Hera 32GB",          category:"ram", price:256.99, stock:10, rating:4.7, capacity:"32 GB", speed:"6800 MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL32" },
  { id:21, name:"TEAMGROUP T-Force Delta RGB 32GB", category:"ram", price:311.99, stock:12, rating:4.6, capacity:"32 GB", speed:"6000 MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL40" },
  { id:22, name:"Kingston FURY Renegade 64GB",      category:"ram", price:316.99, stock:8,  rating:4.8, capacity:"64 GB", speed:"6400 MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL32" },
  { id:23, name:"Corsair VENGEANCE RGB 64GB",       category:"ram", price:349.99, stock:7,  rating:4.7, capacity:"64 GB", speed:"7000 MHz", ddr:"DDR5", formfactor:"DIMM",   cl:"CL40" },
  { id:24, name:"fanxiang M.2 SSD 256GB",           category:"storage", price:30.55,  stock:30, rating:4.3, capacity:"256 GB", gen:"Gen 3", read:"3200 MB/s",  write:"1800 MB/s" },
  { id:25, name:"BIWIN Black Opal NV3500 512GB",    category:"storage", price:42.99,  stock:25, rating:4.4, capacity:"512 GB", gen:"Gen 3", read:"3500 MB/s",  write:"2800 MB/s" },
  { id:26, name:"Acer Predator GM9 1TB",            category:"storage", price:119.99, stock:15, rating:4.8, capacity:"1 TB",   gen:"Gen 5", read:"14000 MB/s", write:"12000 MB/s" },
  { id:27, name:"Crucial P310 1TB",                 category:"storage", price:74.99,  stock:20, rating:4.6, capacity:"1 TB",   gen:"Gen 4", read:"7100 MB/s",  write:"6000 MB/s" },
  { id:28, name:"Lexar EQ790 2TB",                  category:"storage", price:146.99, stock:12, rating:4.5, capacity:"2 TB",   gen:"Gen 4", read:"7000 MB/s",  write:"6500 MB/s" },
  { id:29, name:"WD Black SN850X 4TB",              category:"storage", price:301.23, stock:8,  rating:4.9, capacity:"4 TB",   gen:"Gen 4", read:"7300 MB/s",  write:"6600 MB/s" },
];

// fields based on category
// labels mapped to accessor functions
const SPEC_FIELDS = {
    motherboard: [["Socket",      p => p.socket],["Chipset",      p => p.chipset],["Memory Type", p => p.ddr],["Form Factor",  p => p.formfactor],["WiFi",         p => p.wifi]],
    cpu:         [["Socket",      p => p.socket],["Brand",        p => p.brand],  ["Cores",        p => p.cores],["Boost Clock",  p => p.boost],["TDP",           p => p.tdp]],
    gpu:         [["Brand",       p => p.brand], ["VRAM",         p => p.vram],   ["Memory Type",  p => p.memtype],["Architecture", p => p.arch],["TDP",           p => p.tdp]],
    ram:         [["Capacity",    p => p.capacity],["Speed",      p => p.speed],  ["Type",         p => p.ddr],["Form Factor",   p => p.formfactor],["CAS Latency",  p => p.cl]],
    storage:     [["Capacity",    p => p.capacity],["PCIe Gen",   p => p.gen],    ["Read Speed",   p => p.read],["Write Speed",  p => p.write]],
};

const tableEl = document.getElementById('compare-specs-table');
if (tableEl) {
    const idA = parseInt(tableEl.dataset.idA), idB = parseInt(tableEl.dataset.idB);
    const nameA = tableEl.dataset.nameA, nameB = tableEl.dataset.nameB;
    const pa = PRODUCTS.find(p => p.id === idA), pb = PRODUCTS.find(p => p.id === idB);

    if (pa && pb) {
        const cat = pa.category.toLowerCase();
        const fields = SPEC_FIELDS[cat] || [];
        // base rows always shown
        const baseRows = [
            ["Price",        `£${pa.price.toFixed(2)}`,  `£${pb.price.toFixed(2)}`,  "lower"],
            ["Rating",       `${pa.rating}/5 ⭐`,         `${pb.rating}/5 ⭐`,          "higher"],
            ["Stock",        `${pa.stock} units`,          `${pb.stock} units`,          "higher"],
        ];
        // category-specific rows
        const specRows = fields.map(([label, fn]) => [label, String(fn(pa) ?? '—'), String(fn(pb) ?? '—'), null]);
        const allRows = [...baseRows, ...specRows];

        // winner logic for numbers
        function numericWinner(a, b, prefer) {
            const na = parseFloat(a.replace(/[^\d.]/g,'')), nb = parseFloat(b.replace(/[^\d.]/g,''));
            if (isNaN(na) || isNaN(nb) || na === nb) return '';
            if (prefer === 'higher') return na > nb ? 'a' : 'b';
            if (prefer === 'lower')  return na < nb ? 'a' : 'b';
            return '';
        }

        const verdict = pa.rating > pb.rating ? `<em>${pa.name}</em> edges ahead on rating.`
            : pb.rating > pa.rating ? `<em>${pb.name}</em> edges ahead on rating.`
            : 'Both are equally rated — choose by specs or price preference.';

        // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/map
        tableEl.innerHTML = `
        <div class="spec-table-scroll">
        <table class="compare-spec-table">
            <thead>
                <tr>
                    <th class="spec-col-label">Specification</th>
                    <th class="spec-col-a">${nameA}</th>
                    <th class="spec-col-b">${nameB}</th>
                </tr>
            </thead>
            <tbody>
                ${allRows.map(([f, a, b, prefer]) => {
                    const w = prefer ? numericWinner(a, b, prefer) : '';
                    const isDiff = a !== b ? ' spec-row-diff' : '';
                    return `<tr class="spec-row${isDiff}">
                        <td class="spec-label-cell">${f}</td>
                        <td class="${w==='a' ? 'spec-winner' : ''}">${a}${w==='a' ? ' <i class="fas fa-trophy spec-trophy"></i>':''}</td>
                        <td class="${w==='b' ? 'spec-winner' : ''}">${b}${w==='b' ? ' <i class="fas fa-trophy spec-trophy"></i>':''}</td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table>
        </div>
        <div class="compare-verdict"><i class="fas fa-trophy"></i> <strong>Verdict:</strong> ${verdict}</div>`;
    } else {
        tableEl.innerHTML = '<p style="color:#888; padding:20px; text-align:center">Detailed specifications not found in catalog for one or both products.</p>';
    }
}

// add to cart stolen
// https://stackoverflow.com/questions/76004372/i-want-to-add-products-to-the-shopping-cart-in-php
function addToCart(productId) {
    fetch('add_to_cart.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'product_id='+productId })
    .then(r => r.json()).then(d => { alert(d.success ? 'Product added to cart!' : 'Please login to add items to cart'); })
    .catch(() => alert('Error adding to cart'));
}
</script>

</body>
</html>