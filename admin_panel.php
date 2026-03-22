<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header("Location: index.php");
    exit;
}

$feedback = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name        = trim($_POST['productname']);
    $categoryid  = (int)$_POST['categoryid'];
    $price       = (float)$_POST['price'];
    $stock       = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    $imageurl    = '';

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);

        if (in_array($mime, $allowed_types)) {
            $ext      = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $safe     = preg_replace('/[^a-z0-9]+/', '_', strtolower($name));
            $filename = 'products/' . $safe . '_' . time() . '.' . $ext;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $filename)) {
                $feedback      = 'Image upload failed. Check that the products/ folder is writable.';
                $feedback_type = 'error';
            } else {
                $imageurl = $filename;
            }
        } else {
            $feedback      = 'Invalid image type. Use JPG, PNG, or WebP.';
            $feedback_type = 'error';
        }
    }

    if (empty($feedback)) {
        if (empty($name) || $price <= 0 || $categoryid <= 0) {
            $feedback      = 'Product name, category, and a valid price are required.';
            $feedback_type = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (productname, categoryid, price, stock, description, rating, imageurl) VALUES (?, ?, ?, ?, ?, 0.0, ?)");
                $stmt->execute([$name, $categoryid, $price, $stock, $description, $imageurl]);
                $feedback      = 'Product "' . htmlspecialchars($name) . '" added successfully! It will now appear on the website.';
                $feedback_type = 'success';
            } catch (PDOException $e) {
                $feedback      = 'Database error: ' . $e->getMessage();
                $feedback_type = 'error';
            }
        }
    }
}

try {
    $categories = $pdo->query("SELECT categoryid, categoryname FROM categories ORDER BY categoryname")->fetchAll();
} catch (PDOException $e) {
    die("Failed to load categories: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Tech Forge</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="Signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="javascript.js" defer></script>
    <script src="signup.js" defer></script>
    <link rel="shortcut icon" href="TechForge_Logo.png">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" media="screen and (max-width: 768px)" href="phone.css">
    <style>
        .admin-container {
            max-width: 1300px;
            margin: 30px auto;
            padding: 20px;
        }

        .admin-header {
            margin-bottom: 30px;
        }

        .admin-header h1 {
            color: var(--secondary);
            font-size: 2rem;
        }

        .admin-header p {
            color: #b0b0b0;
            margin-top: 5px;
        }

        /* Dashboard cards */
        .panel-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .panel-card {
            background: #2a242d;
            border: 1px solid #3a3248;
            border-radius: 12px;
            padding: 30px 25px;
            text-align: center;
            text-decoration: none;
            transition: transform 0.2s, border-color 0.2s;
        }

        .panel-card:hover {
            transform: translateY(-4px);
            border-color: var(--secondary);
        }

        .panel-card i {
            font-size: 2.2rem;
            color: var(--secondary);
            margin-bottom: 12px;
            display: block;
        }

        .panel-card h3 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 6px;
        }

        .panel-card p {
            color: #b0b0b0;
            font-size: 0.85rem;
        }

        /* Add product form box */
        .form-box {
            background: #2a242d;
            border: 1px solid #3a3248;
            border-radius: 12px;
            padding: 35px 40px;
        }

        .form-box h2 {
            color: var(--secondary);
            font-size: 1.6rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #3a3248;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            color: #b0b0b0;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            background: #1d1a1e;
            border: 1px solid #3a3640;
            border-radius: 6px;
            color: #fff;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .form-group select option {
            background: #1d1a1e;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-group input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        .image-preview {
            margin-top: 10px;
            display: none;
        }

        .image-preview img {
            max-width: 120px;
            max-height: 120px;
            border-radius: 6px;
            border: 1px solid #3a3640;
            object-fit: contain;
        }

        .submit-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .feedback-msg {
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feedback-success {
            background: rgba(74, 222, 128, 0.1);
            border-left: 4px solid #4ade80;
            color: #4ade80;
        }

        .feedback-error {
            background: rgba(255, 77, 77, 0.1);
            border-left: 4px solid #ff4d4d;
            color: #ff4d4d;
        }

        /* Light mode */
        body.light-mode .panel-card,
        body.light-mode .form-box {
            background: #fff;
            border-color: #ddd;
        }

        body.light-mode .panel-card h3 { color: #1a1a1a; }
        body.light-mode .form-box h2 { border-bottom-color: #eee; }
        body.light-mode .form-group label { color: #555; }

        body.light-mode .form-group input,
        body.light-mode .form-group select,
        body.light-mode .form-group textarea {
            background: #f9f9f9;
            border-color: #ddd;
            color: #1a1a1a;
        }

        body.light-mode .form-group select option { background: #f9f9f9; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full-width { grid-column: 1; }
            .form-box { padding: 25px 20px; }
        }
                                                  
	@media (max-width: 768px) {
    .admin-container,
    .auth-split-container {
        width: 100% !important;
        padding: 10px !important;
        margin: 10px auto !important;
        box-sizing: border-box !important;
    }
    .auth-box,
    .form-box {
        min-width: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        padding: 20px 15px !important;
        box-sizing: border-box !important;
        flex: 1 1 100% !important;
    }
    .panel-cards {
        grid-template-columns: 1fr !important;
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
                    <li><a href="admin_panel.php" class="active"><i class="fas fa-shield-halved"></i> <span>Admin Panel</span></a></li>
                   
                    <li><a href="orders.php"><i class="fas fa-receipt"></i> <span>All Orders</span></a></li>
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
        <div class="nav-left mobile-nav-left">
            <button class="nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="nav-right mobile-nav-right">
            <div class="nav-icons">
                <button class="theme-toggle-btn">
                    <i class="fa-solid fa-moon theme-icon moon-icon"></i>
                    <i class="fa-solid fa-sun theme-icon sun-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="admin-container">

        <div class="admin-header">
            <h1 class="aldrich-regular"><i class="fas fa-shield-halved" style="margin-right: 10px;"></i>Admin Panel</h1>
            <p>Manage your store from one place.</p>
        </div>

        <!-- Subpage nav cards -->
        <div class="panel-cards">
            <a href="admin_inventory.php" class="panel-card">
                <i class="fas fa-boxes"></i>
                <h3>Manage Stock</h3>
                <p>Update stock levels for existing products</p>
            </a>
            <a href="admin_reports.php" class="panel-card">
                <i class="fas fa-chart-line"></i>
                <h3>Reports</h3>
                <p>View orders, revenue, and restock alerts</p>
            </a>
        </div>

        <!-- Add product form -->
        <div class="form-box">
            <h2><i class="fas fa-plus-circle" style="margin-right: 10px;"></i>Add New Product</h2>

            <?php if (!empty($feedback)): ?>
                <div class="feedback-msg <?php echo $feedback_type === 'success' ? 'feedback-success' : 'feedback-error'; ?>">
                    <i class="fas <?php echo $feedback_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo $feedback; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin_panel.php" enctype="multipart/form-data">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="productname">Product Name *</label>
                        <input type="text" id="productname" name="productname" placeholder="e.g. MSI B650 GAMING PLUS" required>
                    </div>

                    <div class="form-group">
                        <label for="categoryid">Category *</label>
                        <select id="categoryid" name="categoryid" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['categoryid']; ?>">
                                    <?php echo htmlspecialchars($cat['categoryname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (£) *</label>
                        <input type="number" id="price" name="price" placeholder="0.00" step="0.01" min="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" placeholder="0" min="0" value="0">
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Enter product description..."></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="image">Product Image (JPG, PNG, WebP)</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                               onchange="previewImage(this)">
                        <div class="image-preview" id="imagePreview">
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                    </div>

                </div>

                <button type="submit" name="add_product" class="submit-btn">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i>Add Product
                </button>
            </form>
        </div>

    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

</body>
</html>
