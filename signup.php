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
            $stmt = $pdo->prepare("SELECT userid, email, password, isadmin FROM users WHERE email = ?");
            $stmt->execute([$loginEmail]);
            $userRecord = $stmt->fetch();

            // password_verify checks the typed password against the hashed one in the DB!
            if ($userRecord && password_verify($loginPassword, $userRecord['password'])) {
                // Log them in!
                $_SESSION['user_id'] = $userRecord['userid'];
                $_SESSION['email'] = $userRecord['email'];
                $_SESSION['isadmin'] = $userRecord['isadmin'];
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
    <title>Sign Up / login - Tech Forge</title>

    <link rel="stylesheet" href="Stylesheet.css">
    <link rel="stylesheet" href="Signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="javascript.js" defer></script> 
    <script src="signup.js" defer></script>  

    <link rel="shortcut icon" href="TechForge_Logo.png">
    
    <style>

        /* SPLIT LAYOUT STYLES */
			.auth-split-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 50px; 
			width: 100%;
            max-width: 1400px; 
            margin: 60px auto;
            padding: 20px;
            flex-wrap: wrap;
        }

        .auth-box {
			flex: 1 1 450px;
            width: 100%;
            min-width: 320px;
            max-width: 600px; 
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

        .auth-box form {
            display: flex;
            flex-direction: column;
        }

        .auth-box label {
            color: #b0b0b0;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .auth-box input[type="email"],
        .auth-box input[type="password"] {
            padding: 14px; 
            margin-bottom: 22px;
            background: #1d1a1e;
            border: 1px solid #3a3640;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            transition: var(--transition);
        }

        .auth-box input[type="email"]:focus,
        .auth-box input[type="password"]:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1);
        }

        .auth-box .submit-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
        }

        .auth-box .submit-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
        }
        
        .global-error-message {
            width: 100%;
			max-width: 1250px; 
            background: rgba(255, 77, 77, 0.1);
            border-left: 4px solid #ff4d4d;
            color: #ff4d4d;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* light mode */
        body.light-mode .auth-box {
            background: #ffffff;
            border-color: #dddddd;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        }
        
        body.light-mode .auth-box h2 {
            border-bottom-color: #eeeeee;
        }

        body.light-mode .auth-box label {
            color: #555555;
        }

        body.light-mode .auth-box input[type="email"],
        body.light-mode .auth-box input[type="password"] {
            background: #f9f9f9;
            border-color: #dddddd;
            color: #1a1a1a;
        }

        body.light-mode .auth-box input[type="email"]:focus,
        body.light-mode .auth-box input[type="password"]:focus {
            background: #ffffff;
            border-color: var(--primary);
        }

        /* responsive 
         * stack them on smaller screens */
        @media (max-width: 768px) {
            .auth-split-container {
                gap: 30px;
                margin: 20px auto;
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
            <li><a href="compare.php"><i class="fas fa-scale-balanced"></i> <span>Compare</span></a></li>
            <li><a href="ContactUs.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
            <li><a href="AboutUs.php"><i class="fas fa-info-circle"></i> <span>About</span></a></li>

<?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sign Out</span></a></li>
            <?php else: ?>
                <li><a href="signup.php" class="active"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="top-nav">
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

    <div class="auth-split-container"> 
            
        <?php if (!empty($error_message)): ?>
            <div class="global-error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="auth-box">
            <h2 class="aldrich-regular">Login</h2>
            <form action="signup.php" method="post">
                <label for="loginEmail">Email:</label>
                <input type="email" id="loginEmail" name="loginEmail" required>

                <label for="loginPassword">Password:</label>
                <input type="password" id="loginPassword" name="loginPassword" required>

                <input type="submit" name="loginSubmit" value="Login" class="submit-btn">
            </form>
        </div>

        <div class="auth-box">
            <h2 class="aldrich-regular">Sign Up</h2>
            <form id="signup-form" action="signup.php" method="post">
                <label for="signupEmail">Email:</label>
                <input type="email" id="signupEmail" name="signupEmail" required>

                <label for="signupEmailConfirm">Confirm Email:</label>
                <input type="email" id="signupEmailConfirm" name="signupEmailConfirm" required>

                <label for="signupPassword">Enter Password:</label>
                <input type="password" id="signupPassword" name="signupPassword" required>

                <label for="signupPasswordConfirm">Confirm Password:</label>  
                <input type="password" id="signupPasswordConfirm" name="signupPasswordConfirm" required>

                <input type="submit" name="signupSubmit" value="Sign Up" class="submit-btn">
            </form>
        </div>

    </div>

</div>

</body>
</html>