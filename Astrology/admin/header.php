<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../lang.php';
// Admin check
if (!isLoggedIn() || !isAdmin()) {
    header("Location: " . SITE_URL . "/login.php");
    exit();
}

$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($adminTitle) ? $adminTitle . ' — ' : ''; ?>Admin Panel — Astro Panchang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Admin Sidebar -->
<div class="admin-sidebar">
  <div class="sidebar-brand">
    <i class="fas fa-om"></i> Admin Panel
  </div>
  <nav class="nav flex-column mt-3">
    <a class="nav-link <?php echo $currentAdminPage=='index'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/">
      <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='upload_excel'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/upload_excel.php">
      <i class="fas fa-file-excel"></i> Upload Excel
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_panchang'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_panchang.php">
      <i class="fas fa-sun"></i> Panchang
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_muhurat'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_muhurat.php">
      <i class="fas fa-calendar-check"></i> Muhurat
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_festivals'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_festivals.php">
      <i class="fas fa-calendar-days"></i> Festivals
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_gallery'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_gallery.php">
      <i class="fas fa-images"></i> Gallery
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_contacts'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_contacts.php">
      <i class="fas fa-envelope"></i> Messages
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_users'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_users.php">
      <i class="fas fa-users"></i> Users
    </a>
    <hr style="border-color:rgba(197,151,59,0.2); margin:1rem 1.5rem;">
    <a class="nav-link" href="<?php echo SITE_URL; ?>/">
      <i class="fas fa-globe"></i> View Site
    </a>
    <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </nav>
</div>

<!-- Main Content -->
<div class="admin-main">
