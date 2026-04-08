<?php

define('BASE_URL', 'http://localhost/ecommerce/');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ecommerce');

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$db ) {
    die("Database connection failed: " . mysqli_connect_error());
}

function getProductImage($product) {
    if (!empty($product['filename'])) {
        $path = 'assets/images/products/' . $product['filename'];
        if (file_exists(__DIR__ . '/' . $path)) {
            return $path;
        }
    }
    return 'assets/images/homepage-one/product-img/product-img-1.webp';
}