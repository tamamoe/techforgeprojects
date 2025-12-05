<?php
session_start();

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    if(!isset($_SESSION['cart'])) { $_SESSION['cart'] = array(); }
    
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity']++; } else {
        $_SESSION['cart'][$product_id] = array( 'product_id' => $product_id, 'quantity' => 1 ); }
    
    echo json_encode(['success' => true, 'message' => 'Added']); } else { echo json_encode(['success' => false, 'message' => 'AAAH ']); }
?>