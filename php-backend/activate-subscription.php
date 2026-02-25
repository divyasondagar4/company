<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include __DIR__ . "/db.php";

$user_id = $_POST['user_id'] ?? 1;
$plan = $_POST['plan'] ?? '';

if (!$plan) {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET subscription_status='active', subscription_plan=? WHERE id=?");
$stmt->bind_param("si", $plan, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "download_url" => "http://localhost/vibe_tech_labs/php-backend/download.php"
    ]);
} else {
    echo json_encode(["status" => "error"]);
}
?>