<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lang.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Astro Panchang — Your trusted guide for daily Panchang, Muhurat, Vastu Shastra, Temple information, and Hindu Festival Calendar.">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' — ' : ''; ?>Astro Panchang</title>

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

  <!-- Custom -->
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<!-- Navbar — Logo Left | Nav Center | Lang+Login Right -->
<nav class="navbar navbar-expand navbar-sacred">
  <div class="container navbar-container-mobile">
    <!-- LEFT: Brand/Logo -->
    <a class="navbar-brand" href="<?php echo SITE_URL; ?>/">
      <span class="brand-icon"><i class="fas fa-om"></i></span>
      <?php echo t('astro_panchang'); ?>
    </a>

    <div class="collapse navbar-collapse" id="navbarMain">
      <!-- CENTER: Main Navigation -->
      <ul class="navbar-nav mx-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage=='index'?'active':''; ?>" href="<?php echo SITE_URL; ?>/">
            <i class="fas fa-home me-1"></i><?php echo t('home'); ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage=='panchang'||$currentPage=='panchang-details')?'active':''; ?>" href="<?php echo SITE_URL; ?>/panchang.php">
            <i class="fas fa-sun me-1"></i><?php echo t('panchang'); ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage=='muhurat'||$currentPage=='muhurat-calendar')?'active':''; ?>" href="<?php echo SITE_URL; ?>/muhurat.php">
            <i class="fas fa-calendar-check me-1"></i><?php echo t('muhurat'); ?>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage=='festival-calendar'?'active':''; ?>" href="<?php echo SITE_URL; ?>/festival-calendar.php">
            <i class="fas fa-calendar-days me-1"></i><?php echo t('festivals'); ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage=='contact'?'active':''; ?>" href="<?php echo SITE_URL; ?>/contact.php">
            <i class="fas fa-envelope me-1"></i><?php echo t('contact'); ?>
          </a>
        </li>
      </ul>

      <!-- RIGHT: Language + Login -->
      <ul class="navbar-nav align-items-center navbar-right-group">
        <!-- Language Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle py-1 px-2" href="#" data-bs-toggle="dropdown" style="font-size:0.8rem; border:1px solid rgba(197,151,59,0.4); border-radius:5px;">
            <i class="fas fa-globe me-1"></i><?php echo strtoupper($lang); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" style="background:var(--dark-wood); border-color:var(--chandan-gold); min-width:120px;">
            <?php foreach($langLabels as $code => $label): ?>
            <li>
              <a class="dropdown-item" href="?lang=<?php echo $code; ?>"
                 style="color:<?php echo $lang===$code?'var(--chandan-gold)':'var(--chandan-light)'; ?>; background:<?php echo $lang===$code?'rgba(197,151,59,0.15)':'transparent'; ?>; font-size:0.85rem; padding:4px 12px;">
                <?php echo $label; ?> <?php echo $lang===$code?'✓':''; ?>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
        </li>

        <?php if(isLoggedIn()): ?>
          <?php if(isAdmin()): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/"><i class="fas fa-cog me-1"></i><?php echo t('admin_panel'); ?></a>
            </li>
          <?php endif; ?>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#" data-bs-toggle="dropdown" title="<?php echo t('hello'); ?> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>" style="font-size:1.2rem;">
              <i class="fas fa-user-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="background:var(--dark-wood); border-color:var(--chandan-gold); min-width:150px; margin-top:10px;">
              <li class="px-3 py-2 text-center" style="color:var(--chandan-gold); border-bottom:1px solid rgba(197,151,59,0.2); font-weight:600; font-size:0.9rem;">
                <?php echo t('hello'); ?> <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'User')[0]); ?>
              </li>
              <li>
                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout.php" style="color:#e74c3c; padding:8px 12px; font-size:0.85rem;">
                  <i class="fas fa-sign-out-alt me-2"></i><?php echo t('logout'); ?>
                </a>
              </li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link nav-login-brown px-3 py-1" href="<?php echo SITE_URL; ?>/login.php" style="font-size:0.85rem;">
              <i class="fas fa-sign-in-alt me-1"></i><?php echo t('login'); ?>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
