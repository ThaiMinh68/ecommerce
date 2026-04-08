<?php
require_once 'config.php';
$res = mysqli_query($db, "SHOW COLUMNS FROM orders");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
