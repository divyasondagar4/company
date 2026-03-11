<?php
require_once 'db.php';
$today = date('Y-m-d');
echo "Today is: $today\n";
$res = $conn->query("SELECT id, panchang_date, tithi FROM panchang WHERE DATE(panchang_date) = '$today'");
if ($res && $res->num_rows > 0) {
    while($r = $res->fetch_assoc()) echo $r['id'] . " - " . $r['panchang_date'] . " - " . $r['tithi'] . "\n";
} else {
    echo "No rows found for DATE(panchang_date) = '$today'\n";
    $res2 = $conn->query("SELECT id, panchang_date FROM panchang ORDER BY id DESC LIMIT 5");
    while($r = $res2->fetch_assoc()) echo $r['id'] . " - " . $r['panchang_date'] . "\n";
}
?>
