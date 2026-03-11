<?php
/**
 * Fix panchang table — change TIME columns to VARCHAR for Gujarati text,
 * add all Excel columns, and truncate old bad data.
 * Run ONCE: http://localhost/vibe_tech_labs/Astrology/fix_panchang.php
 */
require_once __DIR__ . '/db.php';
$conn->set_charset("utf8mb4");

echo "<h2>Fixing Panchang Table</h2>";

// Convert table charset
$conn->query("ALTER TABLE panchang CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "<p> Table charset set to utf8mb4</p>";

// Change TIME columns to VARCHAR (Excel has Gujarati text like 'સવારે 7:23')
$timeToVarchar = [
    'sunrise' => 100, 'sunset' => 100,
    'rahu_start' => 50, 'rahu_end' => 50,
    'gulika_start' => 50, 'gulika_end' => 50,
    'yama_start' => 50, 'yama_end' => 50,
];
foreach ($timeToVarchar as $col => $len) {
    $conn->query("ALTER TABLE panchang MODIFY COLUMN $col VARCHAR($len) DEFAULT NULL");
    echo "<p> Changed $col to VARCHAR($len)</p>";
}

// Add missing columns from Excel
$addCols = [
    "location VARCHAR(255) DEFAULT NULL AFTER day_name",
    "ayan VARCHAR(100) DEFAULT NULL AFTER sunset",
    "gujarati_month VARCHAR(100) DEFAULT NULL AFTER ayan",
    "sun_lon VARCHAR(50) DEFAULT NULL AFTER gujarati_month",
    "moon_lon VARCHAR(50) DEFAULT NULL AFTER sun_lon",
    "tithi_end VARCHAR(50) DEFAULT NULL AFTER tithi",
    "nak_start VARCHAR(50) DEFAULT NULL AFTER nakshatra",
    "nak_end VARCHAR(50) DEFAULT NULL AFTER nak_start",
    "vichudo VARCHAR(10) DEFAULT NULL AFTER nak_end",
    "vichudo_start VARCHAR(50) DEFAULT NULL AFTER vichudo",
    "vichudo_end VARCHAR(50) DEFAULT NULL AFTER vichudo_start",
    "yoga_end VARCHAR(50) DEFAULT NULL AFTER yoga",
    "karana_end VARCHAR(50) DEFAULT NULL AFTER karana",
    "panchak_start VARCHAR(50) DEFAULT NULL AFTER vikram_samvat",
    "panchak_end VARCHAR(50) DEFAULT NULL AFTER panchak_start",
];

foreach ($addCols as $colDef) {
    $colName = explode(' ', $colDef)[0];
    // Check if column exists
    $check = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='" . DB_NAME . "' AND TABLE_NAME='panchang' AND COLUMN_NAME='$colName'");
    if ($check && $check->num_rows == 0) {
        if ($conn->query("ALTER TABLE panchang ADD COLUMN $colDef")) {
            echo "<p> Added column $colName</p>";
        } else {
            echo "<p> Failed to add $colName: " . $conn->error . "</p>";
        }
    } else {
        echo "<p> Column $colName already exists</p>";
    }
}

// Truncate old bad data with ???? characters
$conn->query("TRUNCATE TABLE panchang");
echo "<p> Old bad data removed</p>";

echo "<h3> All Done! Now re-upload your CSV file.</h3>";
echo "<p><a href='" . SITE_URL . "/admin/upload_excel.php' style='color:blue;'>Go to Upload Page →</a></p>";
?>
