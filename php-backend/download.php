<?php

$file = "epaper.pdf"; 
$path = __DIR__ . "/" . $file;

if (!file_exists($path)) {
    die("File not found.");
}

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"epaper.pdf\"");
header("Content-Length: " . filesize($path));

readfile($path);
exit;
?>