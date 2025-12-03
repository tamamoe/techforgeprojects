 <?php
session_start();
require_once 'config.php';

$success_message = 'yyyy';
$error_message = 'nnnn';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    if(empty($name) || empty($email) || empty($message)) { $error_message = "fill in everything";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "enter a valid email (with us)"; } else {
        try {
            $sql = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message ]);
            $success_message = "enquiry sent!";
            $name = $email = $subject = $message = '';
            } catch(PDOException $e) { $error_message = "db error"; } } } ?>
 
 <!DOCTYPE html>
<head>
<link rel="stylesheet" href="style.css">
<script src="javascript.js"></script>

<link rel="shortcut icon" href="TechForge_Logo.png">

<head>

<html>
<body>

<h1>Group 61</h1>
<h3>cs2team61.cs2410-web01pvm.aston.ac.uk<h3>


<nav class = "navabar">
  <ul class = "navlist">
   <li><a href="#home">Home</a></li>
   <li><a href="#products">Products</a></li>
   <li><a href="#contact">Contact</a></li>
   <li><a href="#about">About</a></li>
  </ul>
 <div class="rightNav">
   <input type="text" name="search" id="search" placeholder="Search">
   <button class="btn btn-sm">Search</button>
 </div>
</nav>      



</body>
</html>
