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
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$error_message = '';
$success_message = '';
//Using post to make sure data has been sent 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = filter_input(INPUT_POST, 'signupEmail', FILTER_SANITIZE_EMAIL);
    $emailConfirm = filter_input(INPUT_POST, 'signupEmailConfirm', FILTER_SANITIZE_EMAIL);
    $password = $_POST['signupPassword'] ?? '';
    $passwordConfirm = $_POST['signupPasswordConfirm'] ?? '';
//Validation and security checks
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email try again";
    } elseif ($email !== $emailConfirm) {
        $error_message = "Emails do not match, try again";
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
                    
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['email'] = $email;
                    
                    header('Location: index.html'); 
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

if (!empty($error_message)) {
    echo "<p style='color:red; text-align:center;'>Error: $error_message</p>";
}
?>