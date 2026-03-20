<?php
session_start();

// kick out not logged in users
if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit; 
}

// 2. DATABASE CONNECTION
$host = 'localhost';
$db   = 'cs2team61_db';
$user = 'cs2team61';
$pass = 'y6eEEvWY0VrTj9krI807dMUVy';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$feedback_message = "";
$pw_feedback = "";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Fetch current user record (needed for password verification)
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE userid = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $current_user = $user_stmt->fetch();

    // --- General settings ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) {
        $updates = [];
        $params  = [];

        if (!empty($_POST['email'])) {
            $updates[] = "email = :email";
            $params['email'] = $_POST['email'];
        }

        if (!empty($_POST['fullname'])) {
            $nameParts = explode(' ', trim($_POST['fullname']), 2);
            $updates[] = "firstname = :firstname";
            $updates[] = "lastname = :lastname";
            $params['firstname'] = $nameParts[0];
            $params['lastname']  = isset($nameParts[1]) ? $nameParts[1] : '';
        }

        $updates[] = "communicationpreference = :comm_pref";
        $params['comm_pref'] = isset($_POST['notifications']) ? 1 : 0;

        if (!empty($updates)) {
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE userid = :userid";
            $params['userid'] = $_SESSION['user_id'];
            $pdo->prepare($sql)->execute($params);
            $feedback_message = "<div style='color:#4CAF50;padding:10px;margin-bottom:15px;border:1px solid #4CAF50;background-color:#e8f5e9;border-radius:5px;text-align:center;font-weight:bold;'>Settings updated successfully!</div>";
        }
    }

    // --- Password change ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
        $current_pw  = $_POST['current_password'] ?? '';
        $new_pw      = $_POST['new_password'] ?? '';
        $confirm_pw  = $_POST['confirm_password'] ?? '';

        if (empty($current_pw) || empty($new_pw) || empty($confirm_pw)) {
            $pw_feedback = "error:All three password fields are required.";
        } elseif (!password_verify($current_pw, $current_user['password'])) {
            $pw_feedback = "error:Current password is incorrect.";
        } elseif ($new_pw !== $confirm_pw) {
            $pw_feedback = "error:New passwords do not match.";
        } elseif (strlen($new_pw) < 8) {
            $pw_feedback = "error:New password must be at least 8 characters.";
        } else {
            $pdo->prepare("UPDATE users SET password = ? WHERE userid = ?")
                ->execute([password_hash($new_pw, PASSWORD_DEFAULT), $_SESSION['user_id']]);
            $pw_feedback = "success:Password changed successfully!";
        }
    }

} catch (PDOException $e) {
    $feedback_message = "<div style='color:#f44336;padding:10px;margin-bottom:15px;border:1px solid #f44336;background-color:#ffebee;border-radius:5px;text-align:center;font-weight:bold;'>Error saving settings. Please try again.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Tech Forge</title>
	<link rel="stylesheet" href="Stylesheet.css">
    <link rel="shortcut icon" href="TechForge_Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .settings-card { 
            background: #fff; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
            transition: all 0.3s ease;
        }

        .input-group label, 
        .pref-group h4 {
            color: #333; 
            transition: color 0.3s ease;
        }
        .pref-group p {
            color: #777; 
            transition: color 0.3s ease;
        }
        .input-wrapper input,
        .preferences-section select {
            color: #333;
            background: #f9f9f9;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        .input-wrapper input:focus {
            background: #fff;
            border-color: var(--primary);
        }
        .input-wrapper i {
            color: #aaa;
            transition: color 0.3s ease;
        }
        .input-wrapper input:focus + i {
            color: var(--primary);
        }

        .page-header h2 { color: white; transition: color 0.3s ease; }
        .back-link { color: white; transition: color 0.3s ease; }
        
        .btn-cancel {
            background-color: white; 
            border: 1px solid #ddd; 
            color: #555; 
            transition: all 0.3s ease;
        }
        .btn-cancel:hover { background-color: #f5f5f5; }


        body.dark-mode .main-content {
            background: #1b171d;
        }
        body.dark-mode .page-header h2,
        body.dark-mode .back-link {
            color: white;
        }
        
        body.dark-mode .settings-card { 
            background: #2a242d;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5); 
        }

        body.dark-mode .input-group label, 
        body.dark-mode .pref-group h4 {
            color: white;
        }
        body.dark-mode .pref-group p {
            color: #ccc;
        }

        body.dark-mode .input-wrapper input,
        body.dark-mode .preferences-section select {
            color: white;
            background: #3a3640;
            border: 1px solid #555;
        }
        
        body.dark-mode .input-wrapper input::placeholder {
            color: #999;
        }
        body.dark-mode .input-wrapper input:focus {
            background: #3a3640;
            border-color: var(--primary);
        }

        body.dark-mode .input-wrapper i {
            color: #999;
        }
        
        body.dark-mode .btn-cancel {
            background-color: #3a3640;
            border: 1px solid #555;
            color: white;
        }
        body.dark-mode .btn-cancel:hover { background-color: #4c4652; }
        
        .btn-save { background-color: var(--primary); color: white; }
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
    			<li><a href="compare.php"><i class="fas fa-scale-balanced"></i> <span>Compare</span></a></li>
                <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
                <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>
    <li><a href="admin_inventory.php"><i class="fas fa-boxes"></i> <span>Manage Stock</span></a></li>
                <li><a href="admin_reports.php" class="active"><i class="fas fa-chart-line"></i> <span>Reports</span></a></li>
<?php endif; ?>
                    <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
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
                    <a href="basket.php"><i class="fa-solid fa-basket-shopping"></i></a>
                    <button class="theme-toggle-btn">
    					<i class="fa-solid fa-moon theme-icon moon-icon"></i>
    					<i class="fa-solid fa-sun theme-icon sun-icon"></i>
					</button>

                </div>
            </div>
        </div>

        <div class="settings-wrapper">
            <div class="page-header">
                <h2 class="aldrich-regular">
                    <a href="index.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                    </a> 
                    General Settings
                </h2>
            </div>

            <div class="settings-card">
                <?php if (!empty($feedback_message)) echo $feedback_message; ?>
                
                <form action="#" method="POST">
                    
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Full Name</label>
                            <div class="input-wrapper">
                                <input type="text" placeholder="Please enter your full name" name="fullname">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Email Address</label>
                            <div class="input-wrapper">
                                <input type="email" placeholder="Please enter your email address" name="email">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Phone Number</label>
                            <div class="input-wrapper">
                                <input type="tel" placeholder="Please enter your phone number" name="phone">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                    </div>

                    <div class="preferences-section">
                        <div class="pref-group">
                            <h4>Language preferences</h4>
                            <p>Select the language you want the interface to use.</p>
                            <select name="language">
                                <option>English (United Kingdom)</option>
                                <option>English (United States)</option>
                                <option>Spanish</option>
                                <option>French</option>
                            </select>
                        </div>
                        <div class="pref-group">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <h4>Email notifications</h4>
                                    <p style="margin-bottom: 0;">Stay informed with timely updates.</p>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="notifications" checked>
                                    <span class="slider"></span>
                                </slabel>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.reload();">Cancel</button>
                        <button type="submit" name="save_settings" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="settings-card" style="margin-top: 25px; padding: 30px;">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem;">
                    <i class="fas fa-lock" style="margin-right: 8px; color: var(--primary);"></i>Change Password
                </h3>

                <?php if (!empty($pw_feedback)):
                    [$pw_type, $pw_msg] = explode(':', $pw_feedback, 2);
                    $pw_color = $pw_type === 'success' ? '#4CAF50' : '#f44336';
                    $pw_bg    = $pw_type === 'success' ? '#e8f5e9' : '#ffebee';
                ?>
                    <div style="color:<?php echo $pw_color; ?>;padding:10px;margin-bottom:15px;border:1px solid <?php echo $pw_color; ?>;background:<?php echo $pw_bg; ?>;border-radius:5px;text-align:center;font-weight:bold;">
                        <?php echo htmlspecialchars($pw_msg); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="settings.php">
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Current Password</label>
                            <div class="input-wrapper">
                                <input type="password" name="current_password" placeholder="Enter your current password">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>New Password</label>
                            <div class="input-wrapper">
                                <input type="password" name="new_password" placeholder="At least 8 characters">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Confirm New Password</label>
                            <div class="input-wrapper">
                                <input type="password" name="confirm_password" placeholder="Repeat new password">
                                <i class="fas fa-pen"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions" style="margin-top: 15px;">
                        <button type="submit" name="change_password" class="btn-save">Update Password</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.querySelector('.theme-toggle-btn');
            const body = document.body;
            const moonIcon = document.querySelector('.moon-icon');
            const sunIcon = document.querySelector('.sun-icon');

            let savedTheme = localStorage.getItem('theme') || 'light';
            
            function applyTheme(theme) {
                body.classList.remove('light-mode', 'dark-mode');
                if (theme === 'dark') {
                    body.classList.add('dark-mode');
                    moonIcon.style.display = 'none';
                    sunIcon.style.display = 'inline-block';
                } else {
                    body.classList.add('light-mode');
                    moonIcon.style.display = 'inline-block';
                    sunIcon.style.display = 'none';
                }
            }

            if (!localStorage.getItem('theme')) {
                 if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    savedTheme = 'dark';
                }
            }
            
            applyTheme(savedTheme);


            toggleButton.addEventListener('click', () => {
                const currentTheme = body.classList.contains('light-mode') ? 'light' : 'dark';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                applyTheme(newTheme);
                localStorage.setItem('theme', newTheme);
            });
        });
    </script>
</body>
</html>