<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");

include __DIR__ . "/db.php";

$user_id = 1; // SAME USER

$stmt = $conn->prepare("SELECT subscription_status FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['subscription_status'] === 'active') {
    echo json_encode([
        "status" => "active",
        "download_url" => "http://localhost/vibe_tech_labs/php-backend/download.php"
    ]);
} else {
    echo json_encode(["status" => "inactive"]);
}
?>