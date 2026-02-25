<?php
// ============================================================
//  user/epaper.php  — E-Paper Page
//  PLACE AT: basic-news/user/epaper.php
//
//  THREE STATES:
//   1. Guest (not logged in)   → show subscribe wall
//   2. Logged in, no premium   → show upgrade wall
//   3. Premium / Admin         → show all editions + download
// ============================================================
include "header.php";

// ---- Access level ----
$isGuest   = !isset($_SESSION['user_id']) && !isset($_SESSION['admin_id']);
$isUser    = isset($_SESSION['user_id']);
$isAdmin   = isset($_SESSION['admin_id']);

$isPremium = false;
if ($isUser) {
    $uid = (int)$_SESSION['user_id'];
    $pq  = mysqli_query($conn,
        "SELECT id FROM user_subscriptions
         WHERE user_id='$uid'
           AND payment_status='success'
           AND end_date >= '".date('Y-m-d')."'
         LIMIT 1");
    $isPremium = mysqli_num_rows($pq) > 0;
}
if ($isAdmin) $isPremium = true; // admins always have full access

$hasAccess = $isPremium; // shorthand

// ---- City filter ----
$cityFilter = isset($_GET['city']) ? mysqli_real_escape_string($conn, $_GET['city']) : '';

// ---- Auto-create epapers table if not exists ----
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS `epapers` (
      `id`           INT(11) NOT NULL AUTO_INCREMENT,
      `title`        VARCHAR(200) DEFAULT NULL,
      `city`         VARCHAR(100) DEFAULT NULL,
      `edition_date` DATE DEFAULT NULL,
      `pdf_file`     VARCHAR(255) DEFAULT NULL,
      `cover_image`  VARCHAR(255) DEFAULT NULL,
      `status`       TINYINT(4) DEFAULT 1,
      `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ---- Fetch editions (only if user has access) ----
$editions = [];
if ($hasAccess) {
    $whereCity = !empty($cityFilter) ? "AND city='$cityFilter'" : '';
    $eq = mysqli_query($conn,
        "SELECT * FROM epapers
         WHERE status=1 $whereCity
         ORDER BY edition_date DESC, id DESC
         LIMIT 20");
    while ($r = mysqli_fetch_assoc($eq)) $editions[] = $r;
}

// ---- Fetch available cities for tabs ----
$cities = [];
$cq = mysqli_query($conn, "SELECT DISTINCT city FROM epapers WHERE status=1 AND city != '' ORDER BY city ASC");
while ($r = mysqli_fetch_assoc($cq)) $cities[] = $r['city'];

// ---- Fetch subscription plans for the promo section ----
$plans = [];
$plQ = mysqli_query($conn, "SELECT * FROM subscriptions WHERE status=1 ORDER BY price ASC LIMIT 3");
while ($p = mysqli_fetch_assoc($plQ)) $plans[] = $p;

// ---- Redirect URL for login ----
$loginRedirect = urlencode('epaper.php');
?>

<style>
/* ====== EPAPER PAGE ====== */
.ep-hero{
    background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);
    color:#fff; padding:32px 0 26px; margin-bottom:28px;
    position:relative; overflow:hidden;
}
.ep-hero::after{content:'📰';position:absolute;right:-20px;top:-20px;font-size:180px;opacity:.07;line-height:1;pointer-events:none;}
.ep-hero h1{font-family:'Noto Serif',serif;font-size:clamp(22px,3.5vw,34px);font-weight:700;margin-bottom:6px;}
.ep-hero p{font-size:14px;opacity:.88;margin:0;}
.ep-hero .date-tag{background:rgba(255,255,255,.18);color:#fff;font-size:12px;font-weight:600;padding:3px 12px;border-radius:20px;display:inline-block;margin-top:10px;}

/* ---- CITY TABS ---- */
.city-tabs{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:22px;}
.city-tab{
    padding:7px 18px; border-radius:5px; font-size:13px; font-weight:600;
    border:1.5px solid var(--border); color:#666; background:var(--white);
    text-decoration:none; transition:all .18s;
}
.city-tab:hover{border-color:var(--primary);color:var(--primary);}
.city-tab.active{background:var(--primary);color:#fff;border-color:var(--primary);}

/* ---- EDITION CARDS ---- */
.edition-card{
    background:var(--white); border-radius:8px;
    border:1px solid var(--border); overflow:hidden;
    height:100%; display:flex; flex-direction:column;
    transition:transform .2s,box-shadow .2s;
}
.edition-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(0,0,0,.1);}
.edition-cover{
    background:linear-gradient(135deg,#1a1a1a,#2d2d2d);
    aspect-ratio:3/4; position:relative; overflow:hidden;
    display:flex; align-items:center; justify-content:center;
}
.edition-cover img{width:100%;height:100%;object-fit:cover;opacity:.85;transition:opacity .3s;}
.edition-card:hover .edition-cover img{opacity:1;}
.edition-cover-placeholder{font-size:56px;color:rgba(255,255,255,.25);}
.edition-city-badge{
    position:absolute; top:10px; left:10px;
    background:var(--primary); color:#fff;
    font-size:10px; font-weight:700;
    padding:3px 9px; border-radius:2px; text-transform:uppercase;
}
.edition-today-badge{
    position:absolute; top:10px; right:10px;
    background:#FFA000; color:#333;
    font-size:10px; font-weight:700;
    padding:3px 9px; border-radius:2px;
}
.edition-body{padding:14px;flex:1;display:flex;flex-direction:column;gap:4px;}
.edition-title{font-size:14px;font-weight:700;color:var(--dark);line-height:1.3;}
.edition-date{font-size:12px;color:var(--muted);}
.edition-actions{display:flex;flex-direction:column;gap:7px;margin-top:auto;padding-top:10px;}
.btn-dl{
    display:flex; align-items:center; justify-content:center; gap:7px;
    padding:9px 14px; border-radius:5px; font-size:13px; font-weight:700;
    text-decoration:none; transition:background .2s; border:none; cursor:pointer;
}
.btn-dl-primary{background:var(--primary);color:#fff;}
.btn-dl-primary:hover{background:var(--primary-dark);color:#fff;}
.btn-dl-outline{background:var(--white);color:#1976D2;border:1.5px solid #1976D2;}
.btn-dl-outline:hover{background:#1976D2;color:#fff;}

/* ---- WALLS (guest / upgrade) ---- */
.access-wall{
    background:linear-gradient(135deg,#FFF8E1 0%,#FFF3EC 100%);
    border:2px solid #FFCC80; border-radius:14px;
    padding:44px 30px; text-align:center;
    position:relative; overflow:hidden; margin-bottom:28px;
}
.access-wall::before{content:'🔒';position:absolute;right:-10px;top:-10px;font-size:120px;opacity:.06;line-height:1;}
.wall-icon{font-size:54px;margin-bottom:14px;}
.wall-title{font-family:'Noto Serif',serif;font-size:24px;font-weight:700;color:#BF360C;margin-bottom:10px;}
.wall-desc{font-size:15px;color:#777;max-width:420px;margin:0 auto 24px;}
.wall-features{display:flex;justify-content:center;flex-wrap:wrap;gap:12px 24px;margin-bottom:26px;}
.wall-feature{font-size:13px;color:#555;display:flex;align-items:center;gap:6px;}
.btn-wall{
    display:inline-block; background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    color:#fff; padding:14px 40px; border-radius:8px;
    font-size:16px; font-weight:700;
    transition:transform .2s,box-shadow .2s;
}
.btn-wall:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(232,82,10,.4);color:#fff;}
.wall-secondary{font-size:13px;color:#aaa;margin-top:12px;}
.wall-secondary a{color:var(--primary);font-weight:700;}

/* ---- PLAN CARDS ---- */
.plan-mini{
    background:var(--white); border:2px solid var(--border);
    border-radius:10px; padding:22px 18px; text-align:center;
    transition:border-color .2s,transform .2s;
}
.plan-mini.popular{border-color:var(--primary);transform:scale(1.03);box-shadow:0 6px 20px rgba(232,82,10,.12);}
.plan-mini .name{font-size:16px;font-weight:700;margin-bottom:6px;}
.plan-mini .price{font-family:'Noto Serif',serif;font-size:34px;font-weight:700;color:var(--primary);}
.plan-mini .price sup{font-size:16px;}
.plan-mini .days{font-size:12px;color:var(--muted);margin-bottom:14px;}
.plan-mini .btn-sub{
    display:block; padding:10px; border-radius:6px;
    font-size:14px; font-weight:700;
    background:var(--primary); color:#fff;
    transition:background .2s;
}
.plan-mini.popular .btn-sub{background:var(--primary);}
.plan-mini:not(.popular) .btn-sub{background:var(--white);color:var(--primary);border:2px solid var(--primary);}
.plan-mini .btn-sub:hover{background:var(--primary-dark);color:#fff;}

/* ---- EMPTY STATE ---- */
.empty-ep{text-align:center;padding:40px 20px;color:#aaa;}
.empty-ep .icon{font-size:52px;margin-bottom:12px;opacity:.4;}

/* Blurred preview for non-premium */
.blurred-preview{filter:blur(5px);pointer-events:none;user-select:none;opacity:.5;}
</style>

<!-- HERO -->
<div class="ep-hero">
  <div class="container">
    <h1>📰 Digital E-Paper</h1>
    <p>Read today's newspaper digitally — any time, any device</p>
    <span class="date-tag">📅 <?= date('l, d F Y') ?></span>
  </div>
</div>

<div class="container mb-5">

<?php if ($hasAccess): ?>
<!-- ======================================================
     ✅ STATE 3: PREMIUM USER / ADMIN — FULL ACCESS
====================================================== -->

  <div style="background:#f0fff4;border:1px solid #b7ebc8;border-radius:8px;padding:12px 18px;margin-bottom:22px;font-size:14px;display:flex;align-items:center;gap:10px;">
    ⭐ <strong>Premium Active</strong> — You have full access to all E-Paper editions. Download as PDF or read online.
  </div>

  <!-- City filter tabs -->
  <?php if (!empty($cities)): ?>
  <div class="city-tabs">
    <a href="epaper.php" class="city-tab <?= empty($cityFilter)?'active':'' ?>">All Cities</a>
    <?php foreach ($cities as $ct): ?>
    <a href="epaper.php?city=<?= urlencode($ct) ?>"
       class="city-tab <?= ($cityFilter===$ct)?'active':'' ?>">
      <?= htmlspecialchars($ct) ?>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Edition grid -->
  <?php if (!empty($editions)): ?>
  <div class="row g-3">
    <?php foreach ($editions as $ep):
        $isToday = ($ep['edition_date'] === date('Y-m-d'));
        $coverSrc = (!empty($ep['cover_image']) && file_exists("../admin/uploads/epapers/".$ep['cover_image']))
            ? "../admin/uploads/epapers/".$ep['cover_image']
            : '';
        $pdfExists = (!empty($ep['pdf_file']) && file_exists("../admin/uploads/epapers/".$ep['pdf_file']));
    ?>
    <div class="col-6 col-md-4 col-lg-3">
      <div class="edition-card">

        <!-- Cover -->
        <div class="edition-cover">
          <?php if ($coverSrc): ?>
            <img src="<?= $coverSrc ?>" alt="<?= htmlspecialchars($ep['title']) ?>">
          <?php else: ?>
            <div class="edition-cover-placeholder">📄</div>
          <?php endif; ?>
          <?php if (!empty($ep['city'])): ?>
            <span class="edition-city-badge"><?= htmlspecialchars($ep['city']) ?></span>
          <?php endif; ?>
          <?php if ($isToday): ?>
            <span class="edition-today-badge">TODAY</span>
          <?php endif; ?>
        </div>

        <!-- Info & buttons -->
        <div class="edition-body">
          <div class="edition-title"><?= htmlspecialchars($ep['title']) ?></div>
          <div class="edition-date">📅 <?= date('d M Y', strtotime($ep['edition_date'])) ?></div>

          <div class="edition-actions">
            <?php if ($pdfExists): ?>
              <!-- Download PDF (secure download through PHP) -->
              <a href="epaper_download.php?id=<?= $ep['id'] ?>" class="btn-dl btn-dl-primary">
                ⬇️ Download PDF
              </a>
              <!-- View in browser -->
              <a href="epaper_download.php?id=<?= $ep['id'] ?>&view=1" target="_blank" class="btn-dl btn-dl-outline">
                👁️ Read Online
              </a>
            <?php else: ?>
              <div style="font-size:12px;color:#bbb;text-align:center;padding:6px 0">
                📤 PDF not yet uploaded
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php else: ?>
  <div class="empty-ep">
    <div class="icon">📭</div>
    <h5>No editions available yet</h5>
    <p style="font-size:13px">Ask admin to upload e-paper editions from Admin → E-Papers</p>
  </div>
  <?php endif; ?>


<?php elseif ($isGuest): ?>
<!-- ======================================================
     🔒 STATE 1: GUEST (not logged in)
====================================================== -->

  <!-- Blurred preview tease -->
  <div style="position:relative;margin-bottom:6px;">
    <div class="blurred-preview">
      <div class="row g-3">
        <?php for($i=0;$i<4;$i++): ?>
        <div class="col-6 col-md-3">
          <div class="edition-card">
            <div class="edition-cover" style="background:#ddd;">
              <div class="edition-cover-placeholder" style="color:rgba(0,0,0,.15)">📄</div>
              <span class="edition-city-badge">Ahmedabad</span>
            </div>
            <div class="edition-body">
              <div class="edition-title">Today's Edition</div>
              <div class="edition-date">📅 <?= date('d M Y') ?></div>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- Guest wall -->
  <div class="access-wall">
    <div class="wall-icon">📰🔒</div>
    <div class="wall-title">Create a Free Account to Subscribe</div>
    <div class="wall-desc">Get unlimited access to daily digital newspaper editions from all Gujarat cities — read or download as PDF.</div>
    <div class="wall-features">
      <div class="wall-feature">✅ All City Editions</div>
      <div class="wall-feature">✅ Download PDF</div>
      <div class="wall-feature">✅ Archive Access</div>
      <div class="wall-feature">✅ Ad-Free Reading</div>
      <div class="wall-feature">✅ Premium Articles</div>
    </div>
    <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap;">
      <a href="../register.php" class="btn-wall">Create Free Account →</a>
      <a href="../login.php?redirect=<?= $loginRedirect ?>"
         style="display:inline-block;background:var(--white);color:var(--primary);border:2px solid var(--primary);padding:13px 30px;border-radius:8px;font-size:15px;font-weight:700;">
        Already have account? Login
      </a>
    </div>
  </div>

  <!-- Plans preview -->
  <?php if (!empty($plans)): ?>
  <h2 class="section-title mt-4">Choose a <span>Plan</span></h2>
  <div class="row g-3 justify-content-center mb-4">
    <?php foreach ($plans as $idx => $pl): ?>
    <div class="col-md-4">
      <div class="plan-mini <?= $idx===0?'popular':'' ?>">
        <div class="name"><?= htmlspecialchars($pl['plan_name']) ?></div>
        <div class="price"><sup>₹</sup><?= number_format($pl['price'],0) ?></div>
        <div class="days">⏱ <?= $pl['duration_days'] ?> Days</div>
        <a href="../register.php" class="btn-sub">Register &amp; Subscribe</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>


<?php else: ?>
<!-- ======================================================
     ⬆️  STATE 2: LOGGED IN but NO PREMIUM
====================================================== -->

  <!-- Blurred preview tease -->
  <div style="position:relative;margin-bottom:6px;">
    <div class="blurred-preview">
      <div class="row g-3">
        <?php for($i=0;$i<4;$i++): ?>
        <div class="col-6 col-md-3">
          <div class="edition-card">
            <div class="edition-cover" style="background:#ddd;">
              <div class="edition-cover-placeholder" style="color:rgba(0,0,0,.15)">📄</div>
              <span class="edition-city-badge">Ahmedabad</span>
            </div>
            <div class="edition-body">
              <div class="edition-title">Today's Edition</div>
              <div class="edition-date">📅 <?= date('d M Y') ?></div>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- Upgrade wall -->
  <div class="access-wall">
    <div class="wall-icon">⭐</div>
    <div class="wall-title">Upgrade to Premium</div>
    <div class="wall-desc">You're logged in! Just one step away. Subscribe to unlock full E-Paper access and all premium articles.</div>
    <div class="wall-features">
      <div class="wall-feature">✅ Daily E-Paper (All Cities)</div>
      <div class="wall-feature">✅ PDF Download</div>
      <div class="wall-feature">✅ All Premium Articles</div>
      <div class="wall-feature">✅ Ad-Free Experience</div>
      <div class="wall-feature">✅ Archive Access</div>
    </div>
    <a href="subscription.php" class="btn-wall">⭐ View Subscription Plans</a>
    <div class="wall-secondary">Already subscribed? <a href="../logout.php">Logout</a> and login again to refresh.</div>
  </div>

  <!-- Plans -->
  <?php if (!empty($plans)): ?>
  <h2 class="section-title mt-2">Pick Your <span>Plan</span></h2>
  <div class="row g-3 justify-content-center mb-4">
    <?php foreach ($plans as $idx => $pl): ?>
    <div class="col-md-4">
      <div class="plan-mini <?= $idx===0?'popular':'' ?>">
        <div class="name"><?= htmlspecialchars($pl['plan_name']) ?></div>
        <div class="price"><sup>₹</sup><?= number_format($pl['price'],0) ?></div>
        <div class="days">⏱ <?= $pl['duration_days'] ?> Days</div>
        <a href="subscription.php?plan=<?= $pl['id'] ?>" class="btn-sub">Subscribe Now</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

<?php endif; // end access states ?>

</div><!-- /container -->

<?php include "footer.php"; ?>