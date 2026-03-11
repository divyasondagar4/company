<?php
require 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$orderId    = $data['razorpay_order_id']   ?? '';
$paymentId  = $data['razorpay_payment_id'] ?? '';
$signature  = $data['razorpay_signature']  ?? '';
$dbOrderId  = $data['db_order_id']         ?? 0;

$expectedSignature = hash_hmac(
    'sha256',
    $orderId . '|' . $paymentId,
    RAZORPAY_KEY_SECRET
);

if ($expectedSignature !== $signature) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid signature']);
    exit;
}

$startDate = date('Y-m-d H:i:s');
$endDate   = date('Y-m-d H:i:s', strtotime('+30 days'));  

$stmt = $conn->prepare(
    "UPDATE subscriptions
     SET razorpay_payment_id = ?,
         razorpay_signature  = ?,
         status              = 'active',
         start_date          = ?,
         end_date            = ?
     WHERE id = ?"
);
$stmt->bind_param('ssssi',
    $paymentId, $signature, $startDate, $endDate, $dbOrderId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>