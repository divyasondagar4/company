<?php
session_start();
require "db.php";

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=subscribe.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ep_id   = (int)($_GET['id'] ?? 0);

if (!$ep_id) {
    header("Location: subscribe.php");
    exit;
}

// Check active subscription
$sub = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT id FROM subscriptions WHERE user_id='$user_id' AND status='active' LIMIT 1"
));

if (!$sub) {
    header("Location: subscribe.php");
    exit;
}

// Fetch e-paper
$ep = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM epapers WHERE id='$ep_id' AND is_active=1 LIMIT 1"
));

if (!$ep) {
    header("Location: subscribe.php");
    exit;
}

$file_path = __DIR__ . '/epapers/' . $ep['filename'];

if (!file_exists($file_path)) {
    // File missing on server
    header("Location: subscribe.php?error=file_missing");
    exit;
}

// Serve the PDF for download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($ep['filename']) . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: private');
readfile($file_path);
exit;