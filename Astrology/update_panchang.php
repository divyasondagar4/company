<?php
require_once 'db.php';
$conn->query("UPDATE panchang SET sunrise='06:45:00', sunset='18:30:00', tithi='Pratipada', nakshatra='Ashwini', yoga='Vishkumbha', karana='Bava', ayan='Uttarayan', vikram_samvat='2082', gujarati_month='Phalguna', location='Ahmedabad, India' WHERE panchang_date = '2026-03-09'");
echo "Updated 9 March Panchang.\n";
?>
