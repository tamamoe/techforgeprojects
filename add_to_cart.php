<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    // Check if user is logged in
    if(isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // Check if product already in cart
        $check_sql = "SELECT * FROM cart WHERE userid = ? AND productid = ?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$user_id, $product_id]);
        
        if($check_stmt->rowCount() > 0) {
            // Update quantity
            $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE userid = ? AND productid = ?";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([$user_id, $product_id]);
        } else {
            // Insert new cart item
            $insert_sql = "INSERT INTO cart (userid, productid, quantity) VALUES (?, ?, 1)";
            $insert_stmt = $pdo->prepare($insert_sql);
            $insert_stmt->execute([$user_id, $product_id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Added to database cart']);
    } else {
        // Use session cart for guests
        if(!isset($_SESSION['cart'])) { 
            $_SESSION['cart'] = array(); 
        }
        
        if(isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = array( 
                'product_id' => $product_id, 
                'quantity' => 1 
            );
        }
        
        echo json_encode(['success' => true, 'message' => 'Added to session cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>