<?php
include "db.php";

// If not logged in → send to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=download");
    exit;
}

$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT subscription_status FROM users WHERE id=$user_id");
$user = $result->fetch_assoc();

if ($user['subscription_status'] == 'active') {

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=epaper.pdf");
    readfile("epaper.pdf");
    exit;

} else {

    header("Location: subscribe.php");
    exit;
}
?>