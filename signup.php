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
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //throw an erro if fail
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //get results as arrays
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
        $error_message = "Password need to be 8 characters long, try again.";
    } elseif ($password !== $passwordConfirm) {
        $error_message = "Passwords do not match.";
    } else {
        
//For MySQL DB connection 

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
//Checks if emails already exist so there cant be duplicates
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "An account with this email already exists.";
            } else {
                //Hashes the password for max security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (email, password, isadmin, darkmode) VALUES (?, ?, 0, 0)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$email, $hashed_password])) {
                    //stores ther user ID and email in the session so they get redirected to the indexpage and stay logged in
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['email'] = $email;
                    //the link back to the homepage (index.php)
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
}
//this is if the user clicks login
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
                <li><a href="signup.php" class="active"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="top-nav">
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

    
    <div class="auth-wrapper-card"> 
            
            <?php if (!empty($error_message)): ?>
            <p style='color:#ff4d4d; text-align:center; font-weight:bold; margin-bottom: 15px;'><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <h2>Sign Up</h2>
        <form id ="signup-form" action="signup.php" method="post">
            <label for="signupEmail">Email:</label><br>
            <input type="email" id="signupEmail" name="signupEmail" required><br>

            <label for="signupEmailConfirm">Confirm Email:</label><br>
            <input type="email" id="signupEmailConfirm" name="signupEmailConfirm" required><br>

            <label for="signupPassword"> Enter Password:</label><br>
            <input type="password" id="signupPassword" name="signupPassword" required><br>

            <label for="signupPasswordConfirm">Confirm Password:</label><br>  
            <input type="password" id="signupPasswordConfirm" name="signupPasswordConfirm" required><br>

            <input type="submit" name="signupSubmit" value="Sign Up" class="submit-btn">
        </form>

        <hr>

        <h2>Login</h2>
        <form action="signup.php" method="post">
            <label for="loginEmail">Email:</label><br>
            <input type="email" id="loginEmail" name="loginEmail" required><br><br>

            <label for="loginPassword">Password:</label><br>
            <input type="password" id="loginPassword" name="loginPassword" required><br><br>

            <input type="submit" name = "loginSubmit" value="Login" class="submit-btn">
        </form>
        
    </div>

</div>

</body>
</html>