<?php
// ============================================================
//  user/epaper_download.php — Secure PDF Serve
//  ✅ Only premium users can download/view E-Paper PDF
//  ✅ Direct URL access blocked for non-premium users
//  ✅ Inline view (?view=1) or force download (default)
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/db.php";

// ---- 1. Must be logged in ----
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: ../login.php?premium=1&redirect=" . urlencode("epaper.php"));
    exit;
}

// ---- 2. Premium check ----
$isPremium = false;
if (isset($_SESSION['admin_id'])) {
    $isPremium = true; // admin always has access
} elseif (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $pq  = mysqli_query($conn,
        "SELECT id FROM user_subscriptions
         WHERE user_id='$uid'
           AND payment_status='success'
           AND end_date >= '" . date('Y-m-d') . "'
         LIMIT 1");
    $isPremium = mysqli_num_rows($pq) > 0;
}

if (!$isPremium) {
    // Not premium — redirect with message
    header("Location: epaper.php?msg=premium_required");
    exit;
}

// ---- 3. Get epaper ID ----
$epaperId = intval($_GET['id'] ?? 0);
if ($epaperId <= 0) {
    header("Location: epaper.php?msg=invalid");
    exit;
}

// ---- 4. Fetch epaper from DB ----
$eq = mysqli_query($conn, "SELECT * FROM epapers WHERE id='$epaperId' AND status=1 LIMIT 1");
if (mysqli_num_rows($eq) === 0) {
    header("Location: epaper.php?msg=not_found");
    exit;
}
$epaper = mysqli_fetch_assoc($eq);

// ---- 5. Resolve file path ----
$pdfFile = $epaper['pdf_file'];
$filePath = __DIR__ . "/../admin/uploads/epapers/" . $pdfFile;

if (empty($pdfFile) || !file_exists($filePath)) {
    header("Location: epaper.php?msg=file_missing");
    exit;
}

// ---- 6. Serve the PDF securely ----
$viewMode = isset($_GET['view']) && $_GET['view'] == '1';
$safeTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $epaper['title']);
$filename  = $safeTitle . '_' . date('Y-m-d', strtotime($epaper['edition_date'])) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

if ($viewMode) {
    // Open in browser tab
    header('Content-Disposition: inline; filename="' . $filename . '"');
} else {
    // Force download
    header('Content-Disposition: attachment; filename="' . $filename . '"');
}

// Stream file
readfile($filePath);
exit;
