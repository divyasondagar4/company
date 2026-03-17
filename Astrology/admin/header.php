<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../lang.php';
// Admin check
if (!isLoggedIn() || !isAdmin()) {
    header("Location: " . SITE_URL . "/login");
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
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<?php
// Toast Notifications Injector
if (isset($_SESSION['toast_message'])) {
    $msg = $_SESSION['toast_message'];
    $type = $_SESSION['toast_type'] ?? 'info';
    $redirect = $_SESSION['toast_redirect'] ?? '';
    echo '<div id="toast-data" data-message="' . htmlspecialchars($msg) . '" data-type="' . htmlspecialchars($type) . '" data-redirect="' . htmlspecialchars($redirect) . '" style="display:none;"></div>';
    unset($_SESSION['toast_message']);
    unset($_SESSION['toast_type']);
    unset($_SESSION['toast_redirect']);
}
?>

<!-- Admin Hamburger Button (mobile) -->
<button class="admin-hamburger-btn" id="adminHamburger">
  <span></span><span></span><span></span>
</button>

<!-- Admin Sidebar Overlay -->
<div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

<!-- Admin Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand px-3">
    <span><i class="fas fa-om me-2"></i><?php echo t('admin_panel'); ?></span>
  </div>
  <nav class="nav flex-column mt-3">
    <a class="nav-link <?php echo $currentAdminPage=='index'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/">
      <i class="fas fa-chart-line"></i> <?php echo t('dashboard'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='upload_excel'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/upload_excel">
      <i class="fas fa-file-excel"></i> <?php echo t('upload_excel_label'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_panchang'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_panchang">
      <i class="fas fa-sun"></i> <?php echo t('panchang'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_muhurat'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_muhurat">
      <i class="fas fa-calendar-check"></i> <?php echo t('muhurat'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_festivals'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_festivals">
      <i class="fas fa-calendar-days"></i> <?php echo t('festivals'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_gallery'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_gallery">
      <i class="fas fa-images"></i> <?php echo t('gallery'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_contacts'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_contacts">
      <i class="fas fa-envelope"></i> <?php echo t('messages_label'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_users'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_users">
      <i class="fas fa-users"></i> <?php echo t('users_label'); ?>
    </a>
    <a class="nav-link <?php echo $currentAdminPage=='manage_books'?'active':''; ?>" href="<?php echo SITE_URL; ?>/admin/manage_books">
      <i class="fas fa-book"></i> <?php echo t('books_label'); ?>
    </a>

    <hr style="border-color:rgba(197,151,59,0.2); margin:1rem 1.5rem;">
    <a class="nav-link" href="<?php echo SITE_URL; ?>/">
      <i class="fas fa-globe"></i> <?php echo t('view_site'); ?>
    </a>
    <a class="nav-link" href="<?php echo SITE_URL; ?>/logout">
      <i class="fas fa-sign-out-alt"></i> <?php echo t('logout'); ?>
    </a>
  </nav>
</div>

<!-- Main Content -->
<div class="admin-main">
  <div class="admin-top-bar justify-content-between">
    <div class="d-flex align-items-center">
      <div class="admin-user-info">
        <i class="fas fa-user-shield me-2"></i><?php echo t('hello_admin'); ?>
      </div>
    </div>
    <a href="<?php echo SITE_URL; ?>/logout" class="admin-logout-btn">
      <i class="fas fa-sign-out-alt me-1"></i><?php echo t('logout'); ?>
    </a>
  </div>

  <div class="admin-content-inner">

<script>
document.addEventListener('DOMContentLoaded', function() {
  var sidebar = document.getElementById('adminSidebar');
  var hamburger = document.getElementById('adminHamburger');
  var overlay = document.getElementById('adminSidebarOverlay');
  
  if (hamburger && sidebar) {
    hamburger.addEventListener('click', function(e) {
      e.stopPropagation();
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
      hamburger.classList.toggle('open');
    });
    
    if(overlay) {
      overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        hamburger.classList.remove('open');
      });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 991 && sidebar.classList.contains('active')) {
        if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
          hamburger.classList.remove('open');
        }
      }
    });
  }
});
</script>
