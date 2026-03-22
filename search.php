<?php
require_once 'config.php';

if(isset($_GET['q'])) {
    $q = trim($_GET['q']);
    
    // 2 characters needed to search
    if(strlen($q) > 1) {
        // 5 product limit
        $sql = "SELECT productid, productname, imageurl, price FROM products WHERE productname LIKE :search LIMIT 5";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['search' => "%{$q}%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($results) {
            foreach($results as $item) {
                // takes them to search results when clicked
                echo '<a href="products.php?search='.urlencode($item['productname']).'" class="search-dropdown-item">';
                if(!empty($item['imageurl'])) {
                    echo '<img src="'.htmlspecialchars($item['imageurl']).'" alt="product">';
                }
                echo '<div class="search-item-info">';
                echo '<h4>'.htmlspecialchars($item['productname']).'</h4>';
                echo '<span class="price">£'.number_format($item['price'], 2).'</span>';
                echo '</div></a>';
            }
        } else {
            echo '<div class="no-results">No products found...</div>';
        }
    }
}
?>