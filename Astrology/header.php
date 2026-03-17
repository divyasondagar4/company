<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lang.php';

// Location Logic
$locRes = $conn->query("SELECT DISTINCT location FROM panchang WHERE location IS NOT NULL AND location != '' ORDER BY location ASC");
$availableLocations = [];
if($locRes) { while($r = $locRes->fetch_assoc()) $availableLocations[] = $r['location']; }

if(isset($_GET['loc'])) $_SESSION['user_location'] = $_GET['loc'];
if(!isset($_SESSION['user_location']) || $_SESSION['user_location'] === '') {
    $_SESSION['user_location'] = $availableLocations[0] ?? '';
}
$currentLocation = $_SESSION['user_location']; 
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Astro Panchang — Your trusted guide for daily Panchang, Muhurat, Vastu Shastra, Temple information, and Hindu Festival Calendar.">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' — ' : ''; ?>Astro Panchang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<?php
// Toast Injector
if (isset($_SESSION['toast_message'])) {
    $msg = $_SESSION['toast_message'];
    $type = $_SESSION['toast_type'] ?? 'info';
    $redirect = $_SESSION['toast_redirect'] ?? '';
    echo '<div id="toast-data" data-message="' . htmlspecialchars($msg) . '" data-type="' . htmlspecialchars($type) . '" data-redirect="' . htmlspecialchars($redirect) . '" style="display:none;"></div>';
    unset($_SESSION['toast_message'], $_SESSION['toast_type'], $_SESSION['toast_redirect']);
}
?>

<!-- ========== ROW 1: GOLDEN TOPBAR ========== -->
<div class="navbar-topbar" id="navTopbar">
  <div class="container-fluid px-lg-4">
    <div class="topbar-inner">

      <!-- BRAND -->
      <a class="topbar-brand" href="<?php echo SITE_URL; ?>/">
        <div class="brand-icon"><i class="fas fa-om"></i></div>
        <span><?php echo t('astro_panchang'); ?></span>
      </a>

      <!-- RIGHT ACTIONS -->
      <div class="topbar-right">

        <!-- Location -->
        <?php if(!empty($availableLocations)): 
          $qp = $_GET;
        ?>
        <div class="dropdown">
          <a class="topbar-btn dropdown-toggle <?php echo !$currentLocation?'pulse-gold':''; ?>" href="#" data-bs-toggle="dropdown" id="locDropdown">
            <i class="fas fa-map-marker-alt"></i>
            <span class="topbar-btn-text"><?php echo $currentLocation ? htmlspecialchars($currentLocation) : t('location'); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-sacred">
            <?php foreach($availableLocations as $locName): 
              $qp['loc'] = $locName;
            ?>
            <li><a class="dropdown-item <?php echo $currentLocation===$locName?'active-item':''; ?>" href="?<?php echo http_build_query($qp); ?>"><?php echo htmlspecialchars($locName); ?> <?php echo $currentLocation===$locName?'✓':''; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- Language -->
        <div class="dropdown">
          <a class="topbar-btn dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-globe"></i>
            <span class="topbar-btn-text"><?php echo strtoupper($lang); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-sacred">
            <?php foreach($langLabels as $code => $label): ?>
            <li><a class="dropdown-item <?php echo $lang===$code?'active-item':''; ?>" href="?lang=<?php echo $code; ?>"><?php echo $label; ?> <?php echo $lang===$code?'✓':''; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Login / User -->
        <?php if(isLoggedIn()): ?>
          <?php if(isAdmin()): ?>
          <a class="topbar-btn" href="<?php echo SITE_URL; ?>/admin/" title="<?php echo t('admin_panel'); ?>"><i class="fas fa-cog"></i></a>
          <?php endif; ?>
          <div class="dropdown">
            <a class="topbar-btn dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="fas fa-user-circle"></i>
              <span class="topbar-btn-text d-none d-sm-inline"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'User')[0]); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-sacred">
              <li class="px-3 py-2 text-center" style="color:var(--chandan-gold);border-bottom:1px solid rgba(197,151,59,0.2);font-weight:600;">
                <?php echo t('hello'); ?> <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'User')[0]); ?>
              </li>
              <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout" style="color:#e74c3c;"><i class="fas fa-sign-out-alt me-2"></i><?php echo t('logout'); ?></a></li>
            </ul>
          </div>
        <?php else: ?>
          <a class="topbar-login-btn" href="<?php echo SITE_URL; ?>/login">
            <i class="fas fa-sign-in-alt"></i>
            <span><?php echo t('login'); ?></span>
          </a>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<!-- ========== ROW 2: BROWN MAIN MENU ========== -->
<?php
$navItems = [
  ['page'=>'index','icon'=>'fa-home','label'=>t('home'),'url'=>SITE_URL.'/'],
  ['page'=>'panchang','icon'=>'fa-sun','label'=>t('panchang'),'url'=>SITE_URL . '/panchang','alt'=>'panchang-details'],
  ['page'=>'muhurat','icon'=>'fa-calendar-check','label'=>t('muhurat'),'url'=>SITE_URL . '/muhurat','alt'=>'muhurat-calendar'],
  ['page'=>'festival-calendar','icon'=>'fa-calendar-days','label'=>t('festivals'),'url'=>SITE_URL . '/festival-calendar'],
  ['page'=>'about','icon'=>'fa-info-circle','label'=>t('about'),'url'=>SITE_URL . '/about'],
  ['page'=>'book','icon'=>'fa-book','label'=>t('book'),'url'=>SITE_URL . '/book'],
  ['page'=>'gallery','icon'=>'fa-images','label'=>t('gallery'),'url'=>SITE_URL . '/gallery'],
  ['page'=>'contact','icon'=>'fa-envelope','label'=>t('contact'),'url'=>SITE_URL . '/contact'],
];
?>
<nav class="navbar-mainmenu" id="navMainMenu">
  <div class="container-fluid px-lg-4">
    <div class="mainmenu-inner">
      <!-- Mobile Hamburger -->
      <button class="mainmenu-hamburger" id="mainmenuHamburger" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>

      <!-- Nav Links -->
      <ul class="mainmenu-links" id="mainmenuLinks">
        <?php foreach($navItems as $ni):
          $isActive = ($currentPage === $ni['page'] || (isset($ni['alt']) && $currentPage === $ni['alt']));
        ?>
        <li>
          <a class="<?php echo $isActive?'active':''; ?>" href="<?php echo $ni['url']; ?>">
            <i class="fas <?php echo $ni['icon']; ?>"></i>
            <span><?php echo $ni['label']; ?></span>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Mobile Overlay for off-canvas menu -->
<div class="mainmenu-overlay" id="mainmenuOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var hamburger = document.getElementById('mainmenuHamburger');
  var links = document.getElementById('mainmenuLinks');
  var overlay = document.getElementById('mainmenuOverlay');

  function toggleMenu() {
    hamburger.classList.toggle('open');
    links.classList.toggle('open');
    overlay.classList.toggle('open');
    document.body.classList.toggle('menu-open');
  }

  function closeMenu() {
    hamburger.classList.remove('open');
    links.classList.remove('open');
    overlay.classList.remove('open');
    document.body.classList.remove('menu-open');
  }

  if(hamburger) hamburger.addEventListener('click', toggleMenu);
  if(overlay) overlay.addEventListener('click', closeMenu);

  // Close on link click (mobile)
  document.querySelectorAll('.mainmenu-links li a').forEach(function(a) {
    a.addEventListener('click', closeMenu);
  });

  // Location trigger from hero section
  document.querySelectorAll('.trigger-location-select').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      var locBtn = document.getElementById('locDropdown');
      if(locBtn) {
        window.scrollTo({top:0,behavior:'smooth'});
        new bootstrap.Dropdown(locBtn).toggle();
      }
    });
  });
});
</script>
