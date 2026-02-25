<?php
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user']['id'];

$conn->query("UPDATE users SET subscription_status='active' WHERE id=$id");

// Update session also
$_SESSION['user']['subscription_status'] = 'active';

header("Location: index.php"); // RETURN TO HOME
exit;
?>