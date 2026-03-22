<?php
session_start();
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname   = "cs2team61_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Return empty response if session cart is not set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['empty' => true, 'subtotal' => 0, 'total' => 0]);
    exit;
}

$ids = array_keys($_SESSION['cart']);

if (empty($ids)) {
    echo json_encode(['empty' => true, 'subtotal' => 0, 'total' => 0]);
    exit;
}

// Sanitize IDs for SQL IN clause
$ids_safe = implode(',', array_map('intval', $ids));

// Fetch product details matching session IDs
$sql = "SELECT productid, productname, price, Imageurl FROM products WHERE productid IN ($ids_safe)";
$result = $conn->query($sql);

$cartData = [];
$totalPrice = 0;
$shipping = 4.99;

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['productid'];
        
        // Retrieve quantity from session
        $qty = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['quantity'] : 0;
        
        if ($qty > 0) {
            $unitPrice = floatval($row['price']);
            $lineTotal = $unitPrice * $qty;
            
            $cartData[] = [
                'id' => $id,
                'name' => $row['productname'],
                'price' => $unitPrice,
                'image' => $row['Imageurl'],
                'qty' => $qty,
                'subtotal' => $lineTotal
            ];
            
            $totalPrice += $lineTotal;
        }
    }
}

// Return JSON response
echo json_encode([
    'empty' => count($cartData) === 0,
    'items' => $cartData,
    'subtotal' => $totalPrice,
    'shipping' => $shipping,
    'total' => $totalPrice + $shipping
]);

$conn->close();
?>