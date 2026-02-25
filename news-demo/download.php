<?php
include 'db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['subscription_status']!='active'){
    header("Location: index.php");
    exit;
}

$file = "epaper.pdf";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="epaper.pdf"');
readfile($file);
exit;
?>