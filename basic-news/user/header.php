<?php
// ============================================================
//  user/header.php  — Dynamic Header (UPDATED)
//  ✅ Admin logged in  → shows "Admin Dashboard" button
//  ✅ User logged in   → shows username + dropdown
//  ✅ Guest            → shows Login + Subscribe buttons
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($conn)) require_once __DIR__ . "/db.php";

$_isUserLoggedIn = isset($_SESSION['user_id']);
$_isAdminOnly    = !$_isUserLoggedIn && isset($_SESSION['admin_id']);

$_settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));
$_siteName = $_settings['site_name'] ?? 'News Portal';
$_siteLogo = $_settings['logo']      ?? '';

$_navCatsQ = mysqli_query($conn, "SELECT id, category_name FROM categories WHERE status=1 ORDER BY id ASC LIMIT 9");
$_navCats  = [];
while ($r = mysqli_fetch_assoc($_navCatsQ)) $_navCats[] = $r;

// Premium check
$_isPremium = false;
$_userName  = '';
if ($_isUserLoggedIn) {
    $_uid = (int)$_SESSION['user_id'];
    $_pq  = mysqli_query($conn,
        "SELECT id FROM user_subscriptions
         WHERE user_id='$_uid'
           AND payment_status='success'
           AND end_date >= '".date('Y-m-d')."'
         LIMIT 1");
    $_isPremium = mysqli_num_rows($_pq) > 0;

    // Get user name
    $_userRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id='$_uid' LIMIT 1"));
    $_userName = $_userRow['name'] ?? 'User';
}
if ($_isAdminOnly) {
    $_isPremium = true;
    // Get admin name
    $_adminId  = (int)$_SESSION['admin_id'];
    $_adminRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM admins WHERE id='$_adminId' LIMIT 1"));
    $_adminName = $_adminRow['name'] ?? 'Admin';
}

$_curPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($_siteName) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Mukta:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--primary:#E8520A;--primary-dark:#C43D00;--primary-light:#FF7A35;--dark:#1A1A1A;--text:#2C2C2C;--muted:#777;--border:#E8E8E8;--white:#fff;--bg:#F2F2F2;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Mukta',sans-serif;background:var(--bg);color:var(--text);font-size:15px;line-height:1.6;}
a{text-decoration:none;color:inherit;}img{max-width:100%;display:block;}

/* TOP BAR */
.util-bar{background:var(--dark);border-bottom:2px solid var(--primary);padding:5px 0;font-size:12px;color:#aaa;}
.util-bar a{color:#aaa;transition:color .2s;}.util-bar a:hover{color:var(--primary);}
.util-divider{color:#444;margin:0 8px;}
.premium-pill{background:#FFA000;color:#1a1a1a;font-size:10px;font-weight:800;padding:2px 9px;border-radius:20px;letter-spacing:.6px;margin-left:8px;}
.admin-pill{background:#7B1FA2;color:#fff;font-size:10px;font-weight:800;padding:2px 9px;border-radius:20px;letter-spacing:.6px;margin-left:8px;}
.util-social a{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,.07);font-size:11px;font-weight:700;color:#aaa;margin-left:4px;transition:background .2s,color .2s;}
.util-social a:hover{background:var(--primary);color:#fff;}

/* LOGO BAR */
.logo-bar{background:var(--white);padding:11px 0;border-bottom:1px solid var(--border);}
.site-logo-text{font-family:'Noto Serif',serif;font-size:32px;font-weight:700;color:var(--primary);letter-spacing:-1.5px;line-height:1;}
.site-logo-text em{color:var(--dark);font-style:normal;}
.logo-tagline{font-size:11px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
.logo-img{height:52px;object-fit:contain;}
.header-ad{background:#f9f9f9;border:1px dashed #ddd;border-radius:4px;padding:8px;font-size:11px;color:#ccc;text-align:center;min-height:58px;display:flex;align-items:center;justify-content:center;}
.search-form{display:flex;width:100%;}
.search-input{flex:1;border:2px solid var(--border);border-right:none;padding:9px 15px;font-size:13px;font-family:'Mukta',sans-serif;border-radius:5px 0 0 5px;outline:none;transition:border-color .2s;color:var(--text);}
.search-input:focus{border-color:var(--primary);}
.search-input::placeholder{color:#bbb;}
.search-btn{background:var(--primary);color:#fff;border:none;padding:9px 18px;border-radius:0 5px 5px 0;font-size:15px;cursor:pointer;transition:background .2s;}
.search-btn:hover{background:var(--primary-dark);}

/* MAIN NAV */
.main-nav{background:var(--primary);position:sticky;top:0;z-index:1000;box-shadow:0 3px 12px rgba(232,82,10,.35);}
.main-nav .nav-link{color:rgba(255,255,255,.88)!important;font-size:13px;font-weight:600;padding:10px 13px!important;border-radius:4px;letter-spacing:.2px;transition:background .18s,color .18s!important;white-space:nowrap;}
.main-nav .nav-link:hover,.main-nav .nav-link.nav-active{background:rgba(0,0,0,.22)!important;color:#fff!important;}
.main-nav .navbar-brand{color:#fff!important;font-size:20px;padding:8px 10px;}
.main-nav .dropdown-menu{background:var(--white);border:none;border-top:3px solid var(--primary);border-radius:0 0 8px 8px;box-shadow:0 10px 30px rgba(0,0,0,.13);min-width:210px;padding:6px 0;animation:ddFade .15s ease;}
@keyframes ddFade{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.main-nav .dropdown-item{font-size:13px;padding:9px 18px;color:var(--text);font-weight:500;transition:background .15s,color .15s,padding-left .15s;}
.main-nav .dropdown-item:hover{background:var(--primary);color:#fff;padding-left:24px;}
.nav-pro-badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;font-size:9px;font-weight:800;padding:1px 6px;border-radius:8px;margin-left:4px;letter-spacing:.4px;}
.user-avatar{width:30px;height:30px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.5);margin-right:6px;background:rgba(255,255,255,.2);display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;vertical-align:middle;}
.nav-user-name{font-weight:700;color:#fff;font-size:13px;margin-right:4px;}
.nav-subscribe-btn{background:#FFA000!important;color:#1a1a1a!important;border-radius:5px!important;font-weight:700!important;padding:7px 15px!important;}
.nav-subscribe-btn:hover{background:#e69100!important;color:#1a1a1a!important;}
.nav-login-btn{border:1.5px solid rgba(255,255,255,.4)!important;border-radius:5px!important;padding:7px 14px!important;}
.nav-admin-btn{background:rgba(123,31,162,.8)!important;color:#fff!important;border-radius:5px!important;font-weight:700!important;padding:7px 14px!important;}
.nav-admin-btn:hover{background:rgba(123,31,162,1)!important;}
.navbar-toggler{border-color:rgba(255,255,255,.4);}.navbar-toggler-icon{filter:invert(1);}

/* READING PROGRESS */
#readingProgress{position:fixed;top:0;left:0;height:3px;background:var(--primary);width:0%;z-index:9999;transition:width .1s linear;}

/* SHARED STYLES */
.section-title{font-family:'Noto Serif',serif;font-size:20px;font-weight:700;color:var(--dark);border-left:4px solid var(--primary);padding-left:12px;margin-bottom:18px;line-height:1.2;}
.section-title span{color:var(--primary);}
.badge-cat{display:inline-block;background:var(--primary);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:2px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;}
.badge-breaking{background:#D32F2F;}.badge-trending{background:#1976D2;}.badge-premium{background:#FFA000;color:#333;}.badge-national{background:#2E7D32;}.badge-intl{background:#6A1B9A;}
.news-card{background:var(--white);border-radius:6px;border:1px solid var(--border);overflow:hidden;height:100%;display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s;}
.news-card:hover{transform:translateY(-3px);box-shadow:0 8px 22px rgba(0,0,0,.1);}
.news-card-img-wrap{overflow:hidden;}
.news-card img{width:100%;height:175px;object-fit:cover;transition:transform .35s;}
.news-card:hover img{transform:scale(1.04);}
.news-card-body{padding:13px 15px 15px;flex:1;display:flex;flex-direction:column;}
.news-card-title{font-family:'Noto Serif',serif;font-size:14px;font-weight:700;line-height:1.4;color:var(--dark);margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;transition:color .2s;}
.news-card:hover .news-card-title{color:var(--primary);}
.news-card-desc{font-size:12.5px;color:var(--muted);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex:1;}
.news-meta{font-size:11px;color:#bbb;margin-top:9px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.read-more{color:var(--primary);font-size:12px;font-weight:700;margin-top:9px;display:inline-flex;align-items:center;gap:3px;transition:gap .15s;}
.read-more:hover{gap:7px;}
.widget-box{background:var(--white);border-radius:7px;border:1px solid var(--border);overflow:hidden;margin-bottom:24px;}
.widget-header{background:var(--primary);color:#fff;padding:10px 16px;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;}
.widget-body{padding:14px 16px;}
.ad-slot{background:#f9f9f9;border:1px dashed #ddd;border-radius:5px;text-align:center;padding:18px;font-size:11px;color:#ccc;margin-bottom:20px;}
.ad-slot strong{display:block;color:#ddd;margin-bottom:2px;}

/* ============================================================
   SCROLL POPUP MODAL
============================================================ */
#scrollPopup{
    display:none;
    position:fixed;
    inset:0;
    z-index:9990;
    background:rgba(0,0,0,.6);
    backdrop-filter:blur(4px);
    align-items:center;
    justify-content:center;
    padding:20px;
}
#scrollPopup.show{display:flex;animation:popFadeIn .35s ease;}
@keyframes popFadeIn{from{opacity:0}to{opacity:1}}
.popup-card{
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    width:100%;
    max-width:480px;
    box-shadow:0 24px 60px rgba(0,0,0,.3);
    animation:popSlideUp .35s ease;
}
@keyframes popSlideUp{from{transform:translateY(30px);opacity:0}to{transform:translateY(0);opacity:1}}
.popup-header{
    background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    color:#fff;
    padding:28px 28px 24px;
    text-align:center;
    position:relative;
}
.popup-header h3{font-family:'Noto Serif',serif;font-size:22px;font-weight:700;margin-bottom:6px;}
.popup-header p{font-size:13px;opacity:.88;margin:0;}
.popup-icon{font-size:44px;margin-bottom:12px;display:block;}
.popup-close{position:absolute;top:12px;right:14px;background:rgba(255,255,255,.2);border:none;color:#fff;width:28px;height:28px;border-radius:50%;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.popup-close:hover{background:rgba(255,255,255,.35);}
.popup-body{padding:26px 28px 28px;}
.popup-benefits{list-style:none;padding:0;margin:0 0 20px;}
.popup-benefits li{padding:7px 0;font-size:14px;color:#555;border-bottom:1px solid #f5f5f5;display:flex;align-items:center;gap:10px;}
.popup-benefits li:last-child{border:none;}
.popup-btn-primary{display:block;width:100%;background:var(--primary);color:#fff;border:none;padding:13px;border-radius:8px;font-size:15px;font-weight:700;font-family:'Mukta',sans-serif;cursor:pointer;text-align:center;transition:background .2s;margin-bottom:10px;text-decoration:none;}
.popup-btn-primary:hover{background:var(--primary-dark);color:#fff;}
.popup-btn-secondary{display:block;width:100%;background:#f7f7f7;color:#555;border:1px solid #e0e0e0;padding:11px;border-radius:8px;font-size:14px;font-weight:600;font-family:'Mukta',sans-serif;cursor:pointer;text-align:center;transition:background .2s;text-decoration:none;}
.popup-btn-secondary:hover{background:#eee;color:#333;}
.popup-skip{text-align:center;margin-top:14px;font-size:12px;color:#aaa;}
.popup-skip a{color:#aaa;text-decoration:underline;cursor:pointer;}
.popup-skip a:hover{color:#666;}

@media(max-width:991px){.header-ad{display:none;}.search-form{width:100%;}}
</style>
</head>
<body>
<div id="readingProgress"></div>

<!-- ===== TOP BAR ===== -->
<div class="util-bar">
  <div class="container d-flex justify-content-between align-items-center flex-wrap gap-1">
    <div class="d-flex align-items-center gap-1 flex-wrap">
      <span>📅 <?= date('l, d F Y') ?></span>
      <span class="util-divider">|</span>
      <span>Ahmedabad Edition</span>
      <?php if ($_isPremium && $_isUserLoggedIn): ?>
        <span class="premium-pill">⭐ PREMIUM</span>
      <?php endif; ?>
      <?php if ($_isAdminOnly): ?>
        <span class="admin-pill">🛡️ ADMIN</span>
      <?php endif; ?>
    </div>

    <div class="d-flex align-items-center">
      <?php if ($_isUserLoggedIn): ?>
        <a href="profile.php">My Account</a>
        <span class="util-divider">|</span>
        <a href="../logout.php">Logout</a>
      <?php elseif ($_isAdminOnly): ?>
        <a href="../admin/dashboard.php">Admin Panel</a>
        <span class="util-divider">|</span>
        <a href="../logout.php">Logout</a>
      <?php else: ?>
        <a href="../login.php">Login</a>
        <span class="util-divider">|</span>
        <a href="../register.php">Register</a>
      <?php endif; ?>

      <div class="util-social">
        <a href="#" title="Facebook">f</a>
        <a href="#" title="Twitter">𝕏</a>
        <a href="#" title="YouTube">▶</a>
        <a href="#" title="Instagram">◉</a>
      </div>
    </div>
  </div>
</div>

<!-- ===== LOGO + SEARCH ===== -->
<div class="logo-bar">
  <div class="container">
    <div class="row align-items-center g-3">
      <div class="col-md-3">
        <a href="index.php">
          <?php if (!empty($_siteLogo) && file_exists('../admin/uploads/'.$_siteLogo)): ?>
            <img class="logo-img" src="../admin/uploads/<?= htmlspecialchars($_siteLogo) ?>" alt="<?= htmlspecialchars($_siteName) ?>">
          <?php else: ?>
            <div class="site-logo-text"><?= htmlspecialchars($_siteName) ?><em>.</em></div>
            <div class="logo-tagline">Breaking · Trusted · Live</div>
          <?php endif; ?>
        </a>
      </div>
      <div class="col-md-5 d-none d-md-block">
        <div class="header-ad">728 × 90 — Header Advertisement</div>
      </div>
      <div class="col-md-4">
        <form class="search-form" action="search.php" method="GET">
          <input class="search-input" type="text" name="q"
            placeholder="Search news, topics, people…"
            value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
          <button class="search-btn" type="submit">🔍</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ===== MAIN STICKY NAV ===== -->
<nav class="main-nav navbar navbar-expand-lg py-0">
  <div class="container">
    <a class="navbar-brand" href="index.php">🏠</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto align-items-lg-center">

        <li class="nav-item">
          <a class="nav-link <?= (isset($_GET['level'])&&$_GET['level']=='national')?'nav-active':'' ?>"
             href="index.php?level=national">🇮🇳 National</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (isset($_GET['level'])&&$_GET['level']=='state')?'nav-active':'' ?>"
             href="index.php?level=state">Gujarat</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (isset($_GET['level'])&&$_GET['level']=='international')?'nav-active':'' ?>"
             href="index.php?level=international">🌍 World</a>
        </li>

        <?php if (!empty($_navCats)): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= ($_curPage==='category.php')?'nav-active':'' ?>"
             href="#" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu">
            <?php foreach ($_navCats as $_nc): ?>
              <li><a class="dropdown-item" href="category.php?id=<?= $_nc['id'] ?>"><?= htmlspecialchars($_nc['category_name']) ?></a></li>
            <?php endforeach; ?>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item" href="category.php">All Categories →</a></li>
          </ul>
        </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link <?= ($_curPage==='videos.php')?'nav-active':'' ?>" href="videos.php">📹 Videos</a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($_curPage==='epaper.php')?'nav-active':'' ?>" href="epaper.php">
            📰 E-Paper
            <?php if (!$_isPremium): ?>
              <span class="nav-pro-badge">PRO</span>
            <?php endif; ?>
          </a>
        </li>

      </ul>

      <!-- RIGHT SIDE: Smart Login/User/Admin buttons -->
      <ul class="navbar-nav align-items-lg-center gap-1">

        <?php if ($_isUserLoggedIn): ?>
          <!-- ✅ LOGGED IN USER — Show username + dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
              <span class="user-avatar">
                <?= mb_strtoupper(mb_substr($_userName, 0, 1)) ?>
              </span>
              <span class="nav-user-name"><?= htmlspecialchars(explode(' ', $_userName)[0]) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php">👤 My Profile</a></li>
              <li><a class="dropdown-item" href="bookmark.php">🔖 Bookmarks</a></li>
              <?php if ($_isPremium): ?>
                <li><a class="dropdown-item text-success fw-semibold" href="subscription.php">⭐ Premium Active</a></li>
              <?php else: ?>
                <li><a class="dropdown-item fw-bold" style="color:#FFA000" href="subscription.php">⭐ Go Premium</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider my-1"></li>
              <li><a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a></li>
            </ul>
          </li>
          <?php if (!$_isPremium): ?>
          <li class="nav-item">
            <a class="nav-link nav-subscribe-btn" href="subscription.php">⭐ Subscribe</a>
          </li>
          <?php endif; ?>

        <?php elseif ($_isAdminOnly): ?>
          <!-- ✅ ADMIN browsing user side — Show Admin Dashboard button -->
          <li class="nav-item">
            <a class="nav-link nav-admin-btn" href="../admin/dashboard.php">
              🛡️ Admin Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-login-btn" href="../logout.php">
              Logout
            </a>
          </li>

        <?php else: ?>
          <!-- ✅ GUEST — Show Login + Subscribe -->
          <li class="nav-item">
            <a class="nav-link nav-login-btn" href="../login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-subscribe-btn" href="subscription.php">⭐ Subscribe</a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- ===== SCROLL POPUP MODAL ===== -->
<!-- 
  Shows at 50% scroll:
  - Guest      → Login + Register
  - User (no premium) → Subscribe popup
  - Premium/Admin → NOT shown
-->
<?php if (!$_isPremium): ?>
<div id="scrollPopup">
  <div class="popup-card">
    <div class="popup-header">
      <button class="popup-close" onclick="closePopup()">✕</button>
      <?php if (!$_isUserLoggedIn): ?>
        <span class="popup-icon">📰</span>
        <h3>Continue Reading</h3>
        <p>Login or create a free account to keep reading</p>
      <?php else: ?>
        <span class="popup-icon">⭐</span>
        <h3>Unlock Premium Access</h3>
        <p>Subscribe to read unlimited news & download E-Papers</p>
      <?php endif; ?>
    </div>
    <div class="popup-body">
      <?php if (!$_isUserLoggedIn): ?>
        <ul class="popup-benefits">
          <li>✅ Read unlimited breaking news</li>
          <li>📰 Access daily E-Paper editions</li>
          <li>🔖 Save & bookmark articles</li>
          <li>💬 Join the conversation with comments</li>
        </ul>
        <a href="../login.php" class="popup-btn-primary">🔑 Login to Continue</a>
        <a href="../register.php" class="popup-btn-secondary">Create Free Account</a>
      <?php else: ?>
        <ul class="popup-benefits">
          <li>📰 Download E-Paper PDF — All cities</li>
          <li>🔓 Unlock all premium articles</li>
          <li>🚫 Ad-free reading experience</li>
          <li>📦 Full archive access</li>
        </ul>
        <a href="subscription.php" class="popup-btn-primary">⭐ View Subscription Plans</a>
        <a href="#" class="popup-btn-secondary" onclick="closePopup()">Continue for Free</a>
      <?php endif; ?>
      <div class="popup-skip"><a onclick="closePopupForever()">Don't show again</a></div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
// Reading progress bar
window.addEventListener('scroll', function () {
    var el = document.getElementById('readingProgress');
    if (!el) return;
    var doc = document.documentElement;
    el.style.width = Math.min(
        (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100, 100
    ) + '%';
});

// Scroll popup at 50%
(function(){
    var popupShown = false;
    var isPremium = <?= ($_isPremium) ? 'true' : 'false' ?>;
    if(isPremium) return;

    // Check if user dismissed forever
    try { if(localStorage.getItem('popup_dismissed') === '1') return; } catch(e){}

    window.addEventListener('scroll', function(){
        if(popupShown) return;
        var doc = document.documentElement;
        var scrollPct = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
        if(scrollPct >= 50){
            popupShown = true;
            document.getElementById('scrollPopup').classList.add('show');
        }
    });
})();

function closePopup(){
    document.getElementById('scrollPopup').classList.remove('show');
}
function closePopupForever(){
    try { localStorage.setItem('popup_dismissed','1'); } catch(e){}
    closePopup();
}
// Close on backdrop click
document.getElementById('scrollPopup')?.addEventListener('click', function(e){
    if(e.target === this) closePopup();
});
</script>