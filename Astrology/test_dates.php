<?php
$conn = new mysqli('localhost', 'root', '', 'astro_panchang');
$today = date('Y-m-d');
$res = $conn->query("SELECT panchang_date FROM panchang WHERE panchang_date = '$today'");
if ($res->num_rows > 0) {
    echo "Found today's panchang!\n";
} else {
    echo "NO panchang for today ($today).\n";
    $res2 = $conn->query("SELECT panchang_date FROM panchang WHERE panchang_date >= '2026-01-01' AND panchang_date <= '2026-12-31' LIMIT 5");
    echo "Dates in 2026:\n";
    while($r = $res2->fetch_assoc()) echo $r['panchang_date'] . "\n";
    
    $res3 = $conn->query("SELECT panchang_date FROM panchang ORDER BY panchang_date ASC LIMIT 5");
    echo "Oldest dates:\n";
    while($r = $res3->fetch_assoc()) echo $r['panchang_date'] . "\n";
}
?>
