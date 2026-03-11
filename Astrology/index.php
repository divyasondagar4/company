<?php
$pageTitle = 'Home';
require_once 'header.php';

// Get exactly today's panchang
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT * FROM panchang WHERE panchang_date = ? LIMIT 1");
$stmt->bind_param("s", $today);
$stmt->execute();
$panchangResult = $stmt->get_result();
$todayPanchang = $panchangResult ? $panchangResult->fetch_assoc() : null;

// Get upcoming muhurats
$stmt = $conn->prepare("SELECT * FROM muhurat WHERE muhurat_date >= ? ORDER BY muhurat_date ASC LIMIT 4");
$stmt->bind_param("s", $today);
$stmt->execute();
$muhuratResult = $stmt->get_result();

// Get upcoming festivals
$stmt = $conn->prepare("SELECT * FROM festivals WHERE festival_date >= ? ORDER BY festival_date ASC LIMIT 5");
$stmt->bind_param("s", $today);
$stmt->execute();
$festivalResult = $stmt->get_result();

// Get gallery
$galleryResult = $conn->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 6");

// Subscription check
$canViewFullToday = isLoggedIn() && (isAdmin() || (isset($_SESSION['user_id']) && isSubscribed($conn, $_SESSION['user_id'])));

// Helper for null display
function pv($val) { return ($val !== null && trim((string)$val) !== '') ? htmlspecialchars(trim((string)$val)) : '<span class="text-muted">N/A</span>'; }
?>

<!-- Hero Section — Light to Dark Gradient with Golden Om Animation -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="hero-content fade-in">
          <p style="color:#3a1f14; font-size:0.95rem; letter-spacing:3px; text-transform:uppercase; font-weight:bold; text-shadow:0 1px 2px rgba(232,213,163,0.3);">
            <i class="fas fa-star me-1 nav-icon-spin"></i> <?php echo t('your_divine_guide'); ?>
          </p>
          <h1 style="color:#1a0f0a; font-size:3.5rem; font-weight:bold; text-shadow:1px 1px 3px rgba(232,213,163,0.3);"><?php echo t('astro_panchang'); ?></h1>
          <?php if(isLoggedIn()): ?>
            <p style="color:var(--sacred-maroon); font-size:1.3rem; font-weight:600; font-family:'Cinzel', serif; margin-top:-0.5rem; margin-bottom:1.5rem;"><?php echo t('welcome_user'); ?> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
          <?php endif; ?>
          <div class="hero-divine-line" style="width:100px; height:4px;"></div>
          <p style="font-size:1.15rem; font-weight:500; line-height:1.8; max-width:550px;"><?php echo t('hero_desc'); ?></p>
          <div class="mt-4 d-flex flex-wrap gap-3">
            <a href="<?php echo SITE_URL; ?>/panchang.php" class="btn-sacred btn-lg" style="padding:0.9rem 2.5rem; font-size:1rem;">
              <i class="fas fa-sun nav-icon-spin"></i> <?php echo t('view_panchang'); ?>
            </a>
            <a href="<?php echo SITE_URL; ?>/muhurat.php" class="btn-sacred-outline btn-lg" style="color:var(--chandan-light); border-color:var(--chandan-light); padding:0.85rem 2.5rem; font-size:1rem;">
              <i class="fas fa-calendar-check nav-icon-spin"></i> <?php echo t('muhurat_calendar'); ?>
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center">
        <!-- Celestial Universe Container -->
        <div id="hero-universe-container" style="position:relative; width:450px; height:450px; display:flex; justify-content:center; align-items:center;">
          <!-- Nebulas -->
          <div class="nebula nebula-1"></div>
          <div class="nebula nebula-2"></div>
          <div class="nebula nebula-3"></div>

          <!-- Stars injected via JS -->
          <div class="stars" id="stars"></div>

          <!-- Central Glowing Om -->
          <div class="om-container">
              <div class="energy-wave"></div>
              <div class="energy-wave"></div>
              <div class="energy-wave"></div>
              <div class="om-text">ॐ</div>
          </div>

          <!-- Outer Solar System -->
          <div class="solar-system">
              <div class="orbit orbit-sun">
                  <div class="planet-container pc-sun"><div class="planet sun"></div></div>
              </div>
              <div class="orbit orbit-moon">
                  <div class="planet-container pc-moon"><div class="planet moon"></div></div>
              </div>
              <div class="orbit orbit-mercury">
                  <div class="planet-container pc-mercury"><div class="planet mercury"></div></div>
              </div>
              <div class="orbit orbit-venus">
                  <div class="planet-container pc-venus"><div class="planet venus"></div></div>
              </div>
              <div class="orbit orbit-earth">
                  <div class="planet-container pc-earth"><div class="planet earth"></div></div>
              </div>
              <div class="orbit orbit-mars">
                  <div class="planet-container pc-mars"><div class="planet mars"></div></div>
              </div>
              <div class="orbit orbit-jupiter">
                  <div class="planet-container pc-jupiter"><div class="planet jupiter"></div></div>
              </div>
              <div class="orbit orbit-saturn">
                  <div class="planet-container pc-saturn">
                      <div class="saturn-rings"></div>
                      <div class="planet saturn"></div>
                  </div>
              </div>
              <div class="orbit orbit-uranus">
                  <div class="planet-container pc-uranus"><div class="planet uranus"></div></div>
              </div>
              <div class="orbit orbit-neptune">
                  <div class="planet-container pc-neptune"><div class="planet neptune"></div></div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Today's Panchang Section -->
<section class="section-sacred section-panchang-home" style="padding-bottom: 30px;">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2><i class="fas fa-sun me-2 nav-icon-spin" style="color:var(--chandan-gold);"></i><?php echo t('todays_panchang'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('panchang_for'); ?> <?php echo date('d F Y'); ?></p>
    </div>

    <?php if($todayPanchang): ?>
    <div class="today-panchang-widget glow-sacred">
      <div class="widget-header d-flex align-items-center flex-wrap gap-2" style="background:var(--sacred-maroon); color:var(--chandan-gold); padding:0.8rem 1.2rem; border-radius:12px 12px 0 0; border-bottom:2px solid var(--chandan-gold);">
        <div class="d-flex align-items-center me-auto">
          <i class="fas fa-calendar-day me-2"></i>
          <span style="font-weight:600;"><?php echo t_date($todayPanchang['panchang_date']); ?></span>
        </div>
        <span style="font-size:0.85rem; font-family:'Poppins',sans-serif; background:rgba(255,255,255,0.1); padding:2px 10px; border-radius:4px; border:1px solid rgba(197,151,59,0.3);">
          <?php echo t('vikram_samvat'); ?>: <?php echo pv($todayPanchang['vikram_samvat']); ?>
        </span>
      </div>
      <div class="widget-body">
        <!-- Always visible — Basic Info -->
        <div class="panchang-grid">
          <div class="panchang-item">
            <div class="item-label"><i class="fas fa-sun me-1"></i> <?php echo t('sunrise'); ?></div>
            <div class="item-value"><?php echo ($todayPanchang['sunrise']) ? date('h:i A', strtotime($todayPanchang['sunrise'])) : 'N/A'; ?></div>
          </div>
          <div class="panchang-item">
            <div class="item-label"><i class="fas fa-moon me-1"></i> <?php echo t('sunset'); ?></div>
            <div class="item-value"><?php echo ($todayPanchang['sunset']) ? date('h:i A', strtotime($todayPanchang['sunset'])) : 'N/A'; ?></div>
          </div>
          <div class="panchang-item">
            <div class="item-label"><i class="fas fa-star me-1"></i> <?php echo t('tithi'); ?></div>
            <div class="item-value"><?php echo t(pv($todayPanchang['tithi'])); ?></div>
          </div>
          <div class="panchang-item">
            <div class="item-label"><i class="fas fa-star-half-alt me-1"></i> <?php echo t('nakshatra'); ?></div>
            <div class="item-value"><?php echo t(pv($todayPanchang['nakshatra'])); ?></div>
          </div>
        </div>

        <?php if($canViewFullToday): ?>
        <!-- Full details for subscribed users -->
        <div class="panchang-grid mt-3">
          <div class="panchang-item">
            <div class="item-label"><i class="fas fa-yin-yang me-1"></i> <?php echo t('yoga'); ?></div>
            <div class="item-value"><?php echo t(pv($todayPanchang['yoga'])); ?></div>
          </div>
          <div class="panchang-item">
            <div class="item-label"><i class="fas fa-circle-half-stroke me-1"></i> <?php echo t('karana'); ?></div>
            <div class="item-value"><?php echo t(pv($todayPanchang['karana'])); ?></div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-md-4">
            <div class="panchang-item" style="border-left-color:var(--sacred-kumkum);">
              <div class="item-label" style="color:var(--sacred-kumkum);"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo t('rahu_kaal'); ?></div>
              <div class="item-value"><?php echo ($todayPanchang['rahu_start'] && $todayPanchang['rahu_end']) ? date('h:i A', strtotime($todayPanchang['rahu_start'])) . ' - ' . date('h:i A', strtotime($todayPanchang['rahu_end'])) : 'N/A'; ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panchang-item" style="border-left-color:var(--sacred-kumkum);">
              <div class="item-label" style="color:var(--sacred-kumkum);"><i class="fas fa-exclamation-circle me-1"></i> <?php echo t('gulika_kaal'); ?></div>
              <div class="item-value"><?php echo ($todayPanchang['gulika_start'] && $todayPanchang['gulika_end']) ? date('h:i A', strtotime($todayPanchang['gulika_start'])) . ' - ' . date('h:i A', strtotime($todayPanchang['gulika_end'])) : 'N/A'; ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="panchang-item" style="border-left-color:var(--sacred-kumkum);">
              <div class="item-label" style="color:var(--sacred-kumkum);"><i class="fas fa-clock me-1"></i> <?php echo t('yama_gandam'); ?></div>
              <div class="item-value"><?php echo ($todayPanchang['yama_start'] && $todayPanchang['yama_end']) ? date('h:i A', strtotime($todayPanchang['yama_start'])) . ' - ' . date('h:i A', strtotime($todayPanchang['yama_end'])) : 'N/A'; ?></div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <div class="text-center mt-4">
          <?php if(!$canViewFullToday): 
            if(!isLoggedIn()):
          ?>
                <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred"><i class="fas fa-lock me-1"></i> <?php echo t('login_view_full'); ?></a>
          <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred" style="background:#4A3728; color:var(--chandan-gold); border-color:var(--chandan-gold);"><i class="fas fa-crown me-1"></i> <?php echo t('subscribe_view_full'); ?></a>
          <?php 
              endif;
            else: 
          ?>
              <a href="<?php echo SITE_URL; ?>/panchang-details.php?date=<?php echo $todayPanchang['panchang_date']; ?>" class="btn-sacred">
                <i class="fas fa-eye"></i> <?php echo t('view_full_details'); ?>
              </a>
              <a href="<?php echo SITE_URL; ?>/download-pdf.php?id=<?php echo $todayPanchang['id']; ?>" class="btn-sacred-outline ms-2">
                <i class="fas fa-download"></i> <?php echo t('download_pdf'); ?>
              </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="text-center">
      <div class="sacred-card d-inline-block px-5 py-4">
        <i class="fas fa-calendar-xmark fa-3x mb-3" style="color:var(--chandan-gold);"></i>
        <p class="mb-0"><?php echo t('no_panchang_today'); ?></p>
        <a href="<?php echo SITE_URL; ?>/panchang.php" class="btn-sacred mt-3"><?php echo t('browse_all'); ?></a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Muhurat Section -->
<section class="section-sacred section-muhurat-home" style="padding-top: 30px; padding-bottom: 30px;">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2><i class="fas fa-calendar-check me-2" style="color:var(--chandan-gold);"></i><?php echo t('upcoming_muhurat'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('auspicious_timings'); ?></p>
    </div>

    <div class="row g-4">
      <?php if(!isLoggedIn()): ?>
        <div class="col-12 text-center">
          <div class="sacred-card py-5">
            <i class="fas fa-lock fa-3x mb-3" style="color:var(--chandan-gold);"></i>
            <h4><?php echo t('login_to_view_muhurats'); ?></h4>
            <p class="text-muted"><?php echo t('auspicious_timings_desc'); ?></p>
            <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred mt-2"><?php echo t('login_now'); ?></a>
          </div>
        </div>
      <?php elseif($muhuratResult && $muhuratResult->num_rows > 0): ?>
        <?php 
          $mCount = 0;
          $maxShow = $canViewFullToday ? 100 : 4; 
          while($m = $muhuratResult->fetch_assoc()): 
            $mCount++;
            if($mCount > $maxShow) break;
        ?>
          <div class="col-md-6 col-lg-3 animate-on-scroll">
            <div class="sacred-card">
              <div class="card-icon">
                <?php
                  $icon = 'fa-star';
                  $badgeClass = 'badge-marriage';
                  switch(strtolower($m['type'])) {
                    case 'marriage': $icon = 'fa-rings-wedding'; $badgeClass = 'badge-marriage'; break;
                    case 'griha pravesh': $icon = 'fa-house'; $badgeClass = 'badge-griha'; break;
                    case 'vastu': $icon = 'fa-compass'; $badgeClass = 'badge-vastu'; break;
                    case 'temple sthapna': $icon = 'fa-gopuram'; $badgeClass = 'badge-business'; break;
                  }
                ?>
                <i class="fas <?php echo $icon; ?>"></i>
              </div>
              <span class="badge badge-muhurat <?php echo $badgeClass; ?> mb-2"><?php echo t($m['type']); ?></span>
              <h4><?php echo htmlspecialchars(t($m['title'])); ?></h4>
              <p class="mb-2" style="color:var(--chandan-gold); font-weight:600;">
                <i class="fas fa-calendar me-1"></i>
                <?php echo t_date($m['muhurat_date']); ?>
              </p>
              <p style="font-size:0.85rem;">
                <i class="fas fa-clock me-1"></i>
                <?php echo $m['start_time'] ? date('h:i A', strtotime($m['start_time'])) : ''; ?>
                <?php echo $m['end_time'] ? ' - ' . date('h:i A', strtotime($m['end_time'])) : ''; ?>
              </p>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted"><?php echo t('no_upcoming_muhurat'); ?></p>
        </div>
      <?php endif; ?>
    </div>

    <div class="text-center mt-4">
      <?php if($canViewFullToday): ?>
        <a href="<?php echo SITE_URL; ?>/muhurat-calendar.php" class="btn-sacred">
          <i class="fas fa-calendar"></i> <?php echo t('view_full_calendar'); ?>
        </a>
      <?php else: ?>
        <div class="sacred-card d-inline-block px-4 py-3" style="background:rgba(255,255,255,0.7); border:1px dashed var(--chandan-gold);">
          <p class="mb-2" style="font-weight:600; color:var(--dark-wood);"><i class="fas fa-lock me-1"></i> <?php echo t('unlock_all_muhurats'); ?></p>
          <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred btn-sm"><?php echo t('login_to_unlock'); ?></a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Subscribe Section — Aesthetic Light Brown & Golden Gradient -->
<!-- Subscribe Section — Aesthetic Light Brown & Golden Gradient -->
<section class="subscribe-section-home" style="padding: 30px 0;">
  <div class="container">
    <div class="row align-items-center justify-content-center">
      <div class="col-lg-8 text-center">
        <?php if(isLoggedIn() && isset($_SESSION['user_id']) && isSubscribed($conn, $_SESSION['user_id'])): ?>
          <div class="subscribe-icon-wrapper animate-on-scroll">
            <i class="fas fa-praying-hands" style="font-size:3.5rem; color:#8C6239; filter:drop-shadow(0 2px 6px rgba(197,151,59,0.4));"></i>
          </div>
          <h2 class="animate-on-scroll" style="color:#4A3728; font-size:2.2rem; margin:1rem 0; font-weight:bold;"><?php echo t('thank_you_subscribing'); ?></h2>
          <div class="header-line" style="width:60px; height:3px; background:linear-gradient(to right, #8C6239, var(--chandan-gold)); margin:0 auto 1.5rem; border-radius:2px;"></div>
          <p class="animate-on-scroll" style="color:#5c4b3c; max-width:550px; margin:0 auto 1.5rem; font-size:1.1rem; line-height:1.8; font-weight:500;">
            <?php echo t('premium_access_desc'); ?>
          </p>
          <a href="<?php echo SITE_URL; ?>/panchang.php" class="btn-sacred btn-lg animate-on-scroll" style="background: #4A3728; color: var(--chandan-gold); padding:0.9rem 3rem; font-size:1.05rem; border: 1px solid var(--chandan-gold); box-shadow: 0 4px 15px rgba(197,151,59,0.3);">
            <i class="fas fa-sun"></i> <?php echo t('view_full_panchang'); ?>
          </a>

        <?php else: ?>
          <div class="subscribe-icon-wrapper animate-on-scroll">
            <i class="fas fa-crown" style="font-size:3rem; color:#8C6239; filter:drop-shadow(0 2px 6px rgba(197,151,59,0.3));"></i>
          </div>
          <h2 class="animate-on-scroll" style="color:#4A3728; font-size:2.2rem; margin:1rem 0; font-weight:bold;"><?php echo t('subscribe_premium'); ?></h2>
          <div class="header-line" style="width:60px; height:3px; background:linear-gradient(to right, #8C6239, var(--chandan-gold)); margin:0 auto 1.5rem; border-radius:2px;"></div>
          <p class="animate-on-scroll" style="color:#5c4b3c; max-width:550px; margin:0 auto 1.5rem; font-size:1.05rem; line-height:1.8;">
            <?php echo t('subscribe_premium_desc'); ?>
          </p>
          <div class="row g-3 justify-content-center mb-4">
            <div class="col-auto animate-on-scroll">
              <div class="subscribe-feature" style="background:rgba(255,255,255,0.7); padding:0.6rem 1.2rem; border-radius:8px; border: 1px solid rgba(197,151,59,0.3);">
                <i class="fas fa-file-pdf" style="color:#8C6239;"></i>
                <span style="color:#4A3728; font-weight:600;"><?php echo t('pdf_downloads'); ?></span>
              </div>
            </div>
            <div class="col-auto animate-on-scroll">
              <div class="subscribe-feature" style="background:rgba(255,255,255,0.7); padding:0.6rem 1.2rem; border-radius:8px; border: 1px solid rgba(197,151,59,0.3);">
                <i class="fas fa-sun nav-icon-spin" style="color:var(--chandan-gold);"></i>
                <span style="color:#4A3728; font-weight:600;"><?php echo t('full_panchang'); ?></span>
              </div>
            </div>
            <div class="col-auto animate-on-scroll">
              <div class="subscribe-feature" style="background:rgba(255,255,255,0.7); padding:0.6rem 1.2rem; border-radius:8px; border: 1px solid rgba(197,151,59,0.3);">
                <i class="fas fa-calendar-check" style="color:#8C6239;"></i>
                <span style="color:#4A3728; font-weight:600;"><?php echo t('muhurat_access'); ?></span>
              </div>
            </div>
          </div>
          <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred btn-lg animate-on-scroll" style="background: #4A3728; color: var(--chandan-gold); padding:0.9rem 3rem; font-size:1.05rem; border: 1px solid var(--chandan-gold); box-shadow: 0 4px 15px rgba(197,151,59,0.3);">
            <i class="fas fa-crown"></i> <?php echo t('subscribe_now'); ?>
          </a>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<!-- Festivals Section -->
<section class="section-sacred section-festivals-home" style="padding-top: 30px; padding-bottom: 30px;">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2><i class="fas fa-calendar-days me-2" style="color:var(--chandan-gold);"></i><?php echo t('upcoming_festivals'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('festival_calendar'); ?></p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php if(!isLoggedIn()): ?>
          <div class="text-center p-5 sacred-card">
            <i class="fas fa-lock fa-3x mb-3" style="color:var(--chandan-gold);"></i>
            <h4><?php echo t('login_to_view_festivals'); ?></h4>
            <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred mt-2"><?php echo t('login_now'); ?></a>
          </div>
        <?php elseif($festivalResult && $festivalResult->num_rows > 0): ?>
          <?php 
            $fCount = 0;
            $maxShowF = $canViewFullToday ? 100 : 3;
            while($f = $festivalResult->fetch_assoc()): 
              $fCount++;
              if($fCount > $maxShowF) break;
          ?>
          <div class="festival-item animate-on-scroll">
            <div class="festival-date-box">
              <div class="day"><?php echo date('d', strtotime($f['festival_date'])); ?></div>
              <div class="month"><?php echo mb_substr(t(strtolower(date('F', strtotime($f['festival_date'])))), 0, 3); ?></div>
            </div>
            <div>
              <h5 style="margin-bottom:0.3rem;"><?php echo htmlspecialchars(t($f['festival_name'])); ?></h5>
              <p class="mb-0" style="font-size:0.9rem; color:var(--text-secondary);">
                <?php echo htmlspecialchars(t($f['description'])); ?>
              </p>
            </div>
          </div>
          <?php endwhile; ?>
          
          <?php if(!$canViewFullToday && $festivalResult->num_rows > 3): ?>
            <div class="text-center mt-4 p-4 border-dashed rounded" style="border: 2px dashed rgba(197,151,59,0.3); background: rgba(255,255,255,0.4);">
              <i class="fas fa-lock fa-2x mb-2" style="color:var(--chandan-gold);"></i>
              <h5><?php echo t('unlock_all_festivals'); ?></h5>
              <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred mt-2"><?php echo t('subscribe_to_unlock'); ?></a>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <p class="text-center text-muted"><?php echo t('festival_coming_soon'); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="text-center mt-3">
      <a href="<?php echo SITE_URL; ?>/festival-calendar.php" class="btn-sacred-outline">
        <i class="fas fa-calendar"></i> <?php echo t('view_full_calendar'); ?>
      </a>
    </div>
  </div>
</section>

<!-- Gallery Preview -->
<section class="section-sacred section-gallery-home" style="padding-top: 30px; padding-bottom: 60px;">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2><i class="fas fa-images me-2" style="color:var(--chandan-gold);"></i><?php echo t('gallery'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('divine_moments_desc'); ?></p>
    </div>

    <div class="gallery-grid">
      <?php if($galleryResult && $galleryResult->num_rows > 0): ?>
        <?php while($g = $galleryResult->fetch_assoc()): ?>
          <div class="gallery-item animate-on-scroll">
            <img src="<?php echo SITE_URL; ?>/uploads/gallery/<?php echo $g['image']; ?>" alt="<?php echo htmlspecialchars(t($g['title'])); ?>">
            <div class="gallery-overlay">
              <h6><?php echo htmlspecialchars(t($g['title'])); ?></h6>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <?php for($i = 0; $i < 3; $i++): ?>
        <div class="gallery-item animate-on-scroll" style="background:linear-gradient(135deg, var(--chandan-cream), var(--light-sand)); display:flex; align-items:center; justify-content:center;">
          <i class="fas fa-om nav-icon-spin" style="font-size:3rem; color:var(--chandan-gold); opacity:0.3;"></i>
        </div>
        <?php endfor; ?>
      <?php endif; ?>
    </div>

    <div class="text-center mt-4">
      <a href="<?php echo SITE_URL; ?>/gallery.php" class="btn-sacred-outline">
        <i class="fas fa-images"></i> <?php echo t('view_full_gallery'); ?>
      </a>
    </div>
  </div>
</section>

<script>
function generateHeroStars() {
    const starsContainer = document.getElementById('stars');
    if (!starsContainer) return;
    
    // Scale down star count if container is small
    const numStars = window.innerWidth < 700 ? 50 : 150; 
    
    for (let i = 0; i < numStars; i++) {
        const star = document.createElement('div');
        star.classList.add('star');
        
        const size = Math.random() * 2 + 1; 
        const x = Math.random() * 100;
        const y = Math.random() * 100;
        const duration = Math.random() * 4 + 3; 
        const delay = Math.random() * 5;
        
        star.style.width = `${size}px`;
        star.style.height = `${size}px`;
        star.style.left = `${x}%`;
        star.style.top = `${y}%`;
        star.style.animationDuration = `${duration}s`;
        star.style.animationDelay = `-${delay}s`;
        
        const colors = ['#ffffff', '#fff5e6', '#eaf2ff', '#ffeaea', '#e6f7ff'];
        star.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        
        starsContainer.appendChild(star);
    }
}
document.addEventListener('DOMContentLoaded', generateHeroStars);
</script>

<?php require_once 'footer.php'; ?>
