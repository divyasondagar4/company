<?php
require_once 'db.php';
$res = $conn->query("SELECT id, panchang_date FROM panchang ORDER BY panchang_date DESC LIMIT 10");
while($r = $res->fetch_assoc()) {
    echo $r['id'] . " - " . $r['panchang_date'] . "\n";
}
?>
