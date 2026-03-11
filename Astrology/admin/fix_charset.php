<?php
/**
 * One-time script to fix MySQL database/table charset for multilingual support.
 * Run this once via browser: http://localhost/vibe_tech_labs/Astrology/admin/fix_charset.php
 * After running, Hindi, Gujarati, and all other languages will store correctly.
 */

require_once __DIR__ . '/../db.php';

$results = [];

// 1. Alter database charset
$sql = "ALTER DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql)) {
    $results[] = " Database charset updated to utf8mb4_unicode_ci";
} else {
    $results[] = " Database alter failed: " . $conn->error;
}

// 2. Get all tables and alter each
$tablesResult = $conn->query("SHOW TABLES");
while ($row = $tablesResult->fetch_row()) {
    $table = $row[0];
    
    // Alter table default charset
    $sql = "ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        $results[] = " Table `$table` converted to utf8mb4_unicode_ci";
    } else {
        $results[] = " Table `$table` failed: " . $conn->error;
    }
}

// 3. Verify connection charset
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES utf8mb4");
$results[] = " Connection charset set to utf8mb4";

echo "<!DOCTYPE html><html><head><title>Charset Fix</title>
<style>body{font-family:sans-serif;padding:2rem;background:#1a0f0a;color:#E8D5A3;}
h1{color:#C5973B;}li{margin:0.5rem 0;font-size:0.95rem;}</style></head><body>";
echo "<h1>🔧 MySQL Charset Fix Results</h1><ul>";
foreach ($results as $r) {
    echo "<li>$r</li>";
}
echo "</ul>";
echo "<p style='margin-top:2rem;color:#A09080;'>All tables have been converted. You can now re-upload Excel files with Hindi/Gujarati text.</p>";
echo "<a href='upload_excel.php' style='color:#C5973B;'>← Back to Upload</a>";
echo "</body></html>";
?>
