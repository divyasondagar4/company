<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');         
define('DB_PASS', '');             
define('DB_NAME', 'subscription_db');

define('RAZORPAY_KEY_ID',     'key id');
define('RAZORPAY_KEY_SECRET', 'secret');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>