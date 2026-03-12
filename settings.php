<?php
session_start();


$host = 'localhost';
$db   = 'cs2team61_db';
$user = 'cs2team61';
$pass = 'y6eEEvWY0VrTj9krI807dMUVy';
$charset = 'utf8mb4'; //debating between this charset or uft8 lmk

//set up DSN and PDO options
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //throw a erro if fail
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // get the results as arrays
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$error_message = '';
$success_message = '';
//Using post to make sure data has been sent 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signupSubmit'])) {
        $email = filter_input(INPUT_POST, 'signupEmail', FILTER_SANITIZE_EMAIL);
        $emailConfirm = filter_input(INPUT_POST, 'signupEmailConfirm', FILTER_SANITIZE_EMAIL);
        $password = $_POST['signupPassword'] ?? '';
        $passwordConfirm = $_POST['signupPasswordConfirm'] ?? '';
//Validation and security checks
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email try again";
        } elseif ($email !== $emailConfirm) {
            $error_message = "Emails do not match try again";
        } elseif (strlen($password) < 8) {
            $error_message = "Password needs to be 8 characters long, try again";
        } elseif ($password !== $passwordConfirm) {
            $error_message = "Passwords do not match.";
        } else {
            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $stmt->execute([$email]);
                //Checks if emails already exist so there cant be duplicates
                if ($stmt->fetchColumn() > 0) {
                    $error_message = "This email already exists, try logging in.";
                } else {
                //Hashes the password for max security
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (email, password, isadmin, darkmode) VALUES (?, ?, 0, 0)";
                    $stmt = $pdo->prepare($sql);
                    
                    if ($stmt->execute([$email, $hashed_password])) {
                        $_SESSION['user_id'] = $pdo->lastInsertId();
                        $_SESSION['email'] = $email;
                        header('Location: index.php'); 
                        exit;
                    } else {
                        $error_message = "Registration failed.";
                    }
                }
            } catch (PDOException $e) {
                $error_message = "Database connection error: " . $e->getMessage();
            }
        }
    }

    // --- 2. IF THEY CLICKED 'LOGIN' ---
    elseif (isset($_POST['loginSubmit'])) {
        $loginEmail = filter_input(INPUT_POST, 'loginEmail', FILTER_SANITIZE_EMAIL);
        $loginPassword = $_POST['loginPassword'] ?? '';

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            // Fetch the user's details from the database
            $stmt = $pdo->prepare("SELECT userid, email, password FROM users WHERE email = ?");
            $stmt->execute([$loginEmail]);
            $userRecord = $stmt->fetch();

            // password_verify checks the typed password against the hashed one in the DB!
            if ($userRecord && password_verify($loginPassword, $userRecord['password'])) {
                // Log them in!
                $_SESSION['user_id'] = $userRecord['userid'];
                $_SESSION['email'] = $userRecord['email'];
                header('Location: index.php');
                exit;
            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
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
                <button class="nav-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                    <i class="fas fa-bars"></i>
                </button>
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

        <div class="settings-wrapper">
            <div class="page-header">
                <h2>
                    <a href="index.html" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                    </a> 
                    General Settings
                </h2>
            </div>

            <div class="settings-card">
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
                        <div class="input-group">
                            <label>Password</label>
                            <div class="input-wrapper">
                                <input type="password" placeholder="Enter new password to change" name="password">
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
                        <button type="submit" class="btn-save">Save Changes</button>
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