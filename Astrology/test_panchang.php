<?php
require_once 'db.php';
$res = $conn->query("SELECT * FROM panchang WHERE panchang_date = '2026-03-09'");
$row = $res->fetch_assoc();
print_r($row);
?>
