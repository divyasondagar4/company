<?php
require_once 'db.php';
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT * FROM panchang WHERE panchang_date = ? LIMIT 1");
$stmt->bind_param("s", $today);
$stmt->execute();
$panchangResult = $stmt->get_result();
$todayPanchang = $panchangResult ? $panchangResult->fetch_assoc() : null;

echo "Today: $today\n";
echo "Rows found: " . ($panchangResult ? $panchangResult->num_rows : 'Error') . "\n";
if ($todayPanchang) {
    echo "Panchang tithi: " . $todayPanchang['tithi'] . "\n";
} else {
    echo "No panchang data found for today.\n";
}
?>
