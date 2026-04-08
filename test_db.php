<?php
include 'c:/xampp/htdocs/ecommerce/config.php';

mysqli_query($db, "ALTER TABLE products MODIFY id int(11) NOT NULL AUTO_INCREMENT;");
echo "Products error: " . mysqli_error($db) . "\n";

mysqli_query($db, "ALTER TABLE product_images MODIFY id int(11) NOT NULL AUTO_INCREMENT;");
echo "Product_images error: " . mysqli_error($db) . "\n";

mysqli_query($db, "ALTER TABLE categories MODIFY id int(11) NOT NULL AUTO_INCREMENT;");
echo "Categories error: " . mysqli_error($db) . "\n";

mysqli_query($db, "ALTER TABLE users MODIFY id int(11) NOT NULL AUTO_INCREMENT;");
echo "Users error: " . mysqli_error($db) . "\n";

echo "Done";
