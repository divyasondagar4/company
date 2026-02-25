<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "news_portal_db");

if (!$conn) {
    die("Database connection failed");
}

// Check login
$is_logged_in = isset($_SESSION['user_id']);

// Check premium
$is_premium_user = false;
if ($is_logged_in) {
    $uid = $_SESSION['user_id'];
    $q = mysqli_query($conn, "
        SELECT * FROM subscriptions 
        WHERE user_id='$uid' 
        AND end_date >= CURDATE()
    ");
    if (mysqli_num_rows($q) > 0) {
        $is_premium_user = true;
    }
}
?>