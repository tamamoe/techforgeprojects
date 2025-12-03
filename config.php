<?php
$host = 'localhost'; $dbname = 'cs2team61_db'; $username = 'cs2team61'; $password = 'y6eEEvWY0VrTj9krI807dMUVy'; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { die("Connection failed: " . $e->getMessage()); } ?>