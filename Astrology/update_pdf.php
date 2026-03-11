<?php
require_once 'db.php';
$conn->query("UPDATE panchang SET pdf_file='panchang_2026_03_09_test.pdf' WHERE DATE(panchang_date) = '2026-03-09'");
echo "PDF column updated!\n";
?>
