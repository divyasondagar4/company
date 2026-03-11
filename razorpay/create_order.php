<?php
require 'config.php';

header('Content-Type: application/json');

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$plan   = $data['plan']   ?? 'Pro Plan';
$amount = $data['amount'] ?? 999;

$amountInPaise = $amount * 100;

$userId = 1;

$orderData = [
    'receipt'         => 'order_' . time(),
    'amount'          => $amountInPaise,
    'currency'        => 'INR',
    'payment_capture' => 1   // auto-capture payment
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_USERPWD,
    RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);   // Basic Auth

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create Razorpay order']);
    exit;
}

$razorpayOrder = json_decode($response, true);

// ---- Save order in your DB ----
$stmt = $conn->prepare(
    "INSERT INTO subscriptions
        (user_id, plan_name, amount, razorpay_order_id, status)
     VALUES (?, ?, ?, ?, 'pending')"
);
$stmt->bind_param('isds', $userId, $plan, $amount, $razorpayOrder['id']);
$stmt->execute();
$dbOrderId = $stmt->insert_id;
$stmt->close();

// ---- Return to frontend ----
echo json_encode([
    'id'          => $razorpayOrder['id'],    
    'amount'      => $razorpayOrder['amount'],
    'currency'    => $razorpayOrder['currency'],
    'key_id'      => RAZORPAY_KEY_ID,         
    'db_order_id' => $dbOrderId
]);
?>