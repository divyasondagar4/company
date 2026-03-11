<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'astro_panchang');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set default timezone for India
date_default_timezone_set('Asia/Kolkata');

// Set charset — full utf8mb4 for all languages (Hindi, Gujarati, etc.)
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES utf8mb4");

// Site configuration
define('SITE_NAME', 'Astro Panchang');
define('SITE_URL', '/vibe_tech_labs/Astrology');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Helper function to check subscription
function isSubscribed($conn, $user_id) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT id FROM subscriptions WHERE user_id = ? AND status = 'active' AND start_date <= ? AND end_date >= ?");
    $stmt->bind_param("iss", $user_id, $today, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Sanitize input
function sanitize($conn, $input) {
    return $conn->real_escape_string(trim($input));
}
?>
