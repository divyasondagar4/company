<?php
$pageTitle = 'Panchang Details';
require_once 'header.php';

$date = $_GET['date'] ?? date('Y-m-d');
$result = $conn->query("SELECT * FROM panchang WHERE panchang_date = '" . $conn->real_escape_string($date) . "'");
$p = $result ? $result->fetch_assoc() : null;

// Subscription check — MANDATORY for viewing full details
// Subscription check — MANDATORY for viewing full details
$canViewFull = isLoggedIn() && (isAdmin() || isSubscribed($conn, $_SESSION['user_id']));

$pv = function($val) {
    if ($val === null || trim((string)$val) === '') return '<span class="text-muted">N/A</span>';
    return htmlspecialchars(trim((string)$val));
};
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-sun me-2 nav-icon-spin"></i><?php echo t('panchang_details'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/panchang.php"><?php echo t('panchang'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo date('d M Y', strtotime($date)); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred section-panchang-home" style="padding:40px 0;">
  <div class="container">

    <?php if(!$p): ?>
      <div class="panchang-detail-card text-center py-5">
        <i class="fas fa-calendar-times" style="font-size:3rem; color:var(--chandan-gold); opacity:0.5;"></i>
        <h4 class="mt-3"><?php echo t('no_data'); ?></h4>
        <p class="text-muted mb-3"><?php echo t('panchang_for'); ?> <?php echo date('d M Y', strtotime($date)); ?></p>
        <a href="panchang.php" class="btn-sacred mt-3"><i class="fas fa-arrow-left"></i> <?php echo t('browse_all'); ?></a>
      </div>
    <?php else: ?>

    <!-- Prev / Next Day Navigation -->
    <?php
    $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
    $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
    $stmtPrev = $conn->prepare("SELECT id FROM panchang WHERE panchang_date=?");
    $stmtPrev->bind_param("s", $prevDate);
    $stmtPrev->execute();
    $prevExists = $stmtPrev->get_result()->num_rows > 0;
    $stmtNext = $conn->prepare("SELECT id FROM panchang WHERE panchang_date=?");
    $stmtNext->bind_param("s", $nextDate);
    $stmtNext->execute();
    $nextExists = $stmtNext->get_result()->num_rows > 0;
    ?>

    <!-- Date Header with Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <?php if($prevExists): ?>
          <a href="?date=<?php echo $prevDate; ?>" class="btn-sacred-outline day-nav-btn"><i class="fas fa-arrow-left"></i> <?php echo t('previous'); ?></a>
        <?php endif; ?>
      </div>
      <div class="text-center">
        <h3 style="color:var(--sacred-maroon); margin:0; font-size:1.5rem;">
          <i class="fas fa-calendar-day me-2" style="color:var(--chandan-gold);"></i>
          <?php echo t_date($p['panchang_date']); ?>
        </h3>
        <?php if($p['day_name']): ?>
          <small style="color:var(--text-secondary); font-size:0.85rem;"><?php echo t($p['day_name']); ?></small>
        <?php endif; ?>
      </div>
      <div>
        <?php if($nextExists): ?>
          <a href="?date=<?php echo $nextDate; ?>" class="btn-sacred day-nav-btn"><?php echo t('next'); ?> <i class="fas fa-arrow-right"></i></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="row g-4">

      <!-- Main Panchang Info -->
      <div class="col-lg-8">

        <!-- Sun & Moon — Always Visible -->
        <div class="panchang-detail-card mb-4">
          <h5 style="color:var(--sacred-maroon);">
            <i class="fas fa-sun nav-icon-spin" style="color:#f39c12;"></i>
            <?php echo t('sunrise'); ?> / <?php echo t('sunset'); ?>
          </h5>
          <div class="row g-3">
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center" style="border-left-color:#f39c12;">
                <div style="font-size:1.8rem; color:#f39c12; margin-bottom:0.3rem;"><i class="fas fa-sun"></i></div>
                <div class="info-label"><?php echo t('sunrise'); ?></div>
                <div class="info-value"><?php echo $pv($p['sunrise']); ?></div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center" style="border-left-color:#e67e22;">
                <div style="font-size:1.8rem; color:#e67e22; margin-bottom:0.3rem;"><i class="fas fa-sun" style="opacity:0.6;"></i></div>
                <div class="info-label"><?php echo t('sunset'); ?></div>
                <div class="info-value"><?php echo $pv($p['sunset']); ?></div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center" style="border-left-color:#27ae60;">
                <div style="font-size:1.8rem; color:#27ae60; margin-bottom:0.3rem;"><i class="fas fa-globe"></i></div>
                <div class="info-label"><?php echo t('ayan'); ?></div>
                <div class="info-value" style="font-size:0.95rem;"><?php echo t($pv($p['ayan'])); ?></div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center" style="border-left-color:#3498db;">
                <div style="font-size:1.8rem; color:#3498db; margin-bottom:0.3rem;"><i class="fas fa-calendar-alt"></i></div>
                <div class="info-label"><?php echo t('vikram_samvat'); ?></div>
                <div class="info-value" style="font-size:0.95rem;"><?php echo $pv($p['vikram_samvat']); ?></div>
              </div>
            </div>
          </div>
          <?php if($p['location']): ?>
          <div class="mt-3 text-center" style="color:var(--text-secondary); font-size:0.85rem;">
            <i class="fas fa-map-marker-alt me-1" style="color:var(--chandan-gold);"></i> <?php echo $pv($p['location']); ?>
          </div>
          <?php endif; ?>
          <?php if($p['gujarati_month']): ?>
          <div class="mt-2 text-center" style="color:var(--sacred-maroon); font-size:0.9rem; font-weight:600;">
            <i class="fas fa-moon me-1" style="color:var(--chandan-gold);"></i> <?php echo t('gujarati_month'); ?>: <?php echo t($pv($p['gujarati_month'])); ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Tithi, Nakshatra, Yoga, Karana -->
        <div class="panchang-detail-card mb-4" style="position: relative; overflow: hidden;">
          <h5 style="color:var(--sacred-maroon);">
            <i class="fas fa-star" style="color:var(--chandan-gold);"></i>
            <?php echo t('tithi'); ?> · <?php echo t('nakshatra'); ?> · <?php echo t('yoga'); ?> · <?php echo t('karana'); ?>
          </h5>
          <div class="row g-3">
            <!-- Tithi — Always visible -->
            <div class="col-md-6">
              <div class="panchang-info-box" style="border-left-color:var(--chandan-gold);">
                <div class="info-label"><i class="fas fa-star me-1" style="color:var(--chandan-gold);"></i><?php echo t('tithi'); ?></div>
                <div class="info-value"><?php echo t($pv($p['tithi'])); ?></div>
                <?php if($canViewFull && $p['tithi_end']): ?>
                  <small style="color:var(--text-secondary); display:block; margin-top:4px;"><?php echo t('end'); ?>: <?php echo $pv($p['tithi_end']); ?></small>
                <?php endif; ?>
              </div>
            </div>
            <!-- Nakshatra — Always visible -->
            <div class="col-md-6">
              <div class="panchang-info-box" style="border-left-color:#3498db;">
                <div class="info-label" style="color:#2980b9;"><i class="fas fa-star-half-alt me-1"></i><?php echo t('nakshatra'); ?></div>
                <div class="info-value"><?php echo t($pv($p['nakshatra'])); ?></div>
                <?php if($canViewFull): ?>
                  <?php if($p['nak_start']): ?>
                    <small style="color:var(--text-secondary); display:block; margin-top:4px;"><?php echo t('start'); ?>: <?php echo $pv($p['nak_start']); ?></small>
                  <?php endif; ?>
                  <?php if($p['nak_end']): ?>
                    <small style="color:var(--text-secondary);"><?php echo t('end'); ?>: <?php echo $pv($p['nak_end']); ?></small>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
            
            <?php if($canViewFull): ?>
            <!-- Yoga — Subscribers only -->
            <div class="col-md-6">
              <div class="panchang-info-box" style="border-left-color:#27ae60;">
                <div class="info-label" style="color:#27ae60;"><i class="fas fa-yin-yang me-1"></i><?php echo t('yoga'); ?></div>
                <div class="info-value"><?php echo t($pv($p['yoga'])); ?></div>
                <?php if($p['yoga_end']): ?>
                  <small style="color:var(--text-secondary); display:block; margin-top:4px;"><?php echo t('end'); ?>: <?php echo $pv($p['yoga_end']); ?></small>
                <?php endif; ?>
              </div>
            </div>
            <!-- Karana — Subscribers only -->
            <div class="col-md-6">
              <div class="panchang-info-box" style="border-left-color:#e74c3c;">
                <div class="info-label" style="color:#c0392b;"><i class="fas fa-circle-half-stroke me-1"></i><?php echo t('karana'); ?></div>
                <div class="info-value"><?php echo t($pv($p['karana'])); ?></div>
                <?php if($p['karana_end']): ?>
                  <small style="color:var(--text-secondary); display:block; margin-top:4px;"><?php echo t('end'); ?>: <?php echo $pv($p['karana_end']); ?></small>
                <?php endif; ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
          
          <?php if(!$canViewFull): ?>
          <div style="position:absolute; bottom:0; left:0; right:0; height:120px; background:linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 80%); display:flex; flex-direction:column; align-items:center; justify-content:flex-end; padding-bottom:15px; border-radius:0 0 var(--radius-lg) var(--radius-lg); z-index:10;">
            <p style="margin-bottom:8px; font-weight:600; color:var(--text-secondary); text-shadow:0 0 10px white;"><?php echo t('unlock_full_details'); ?></p>
            <?php if(!isLoggedIn()): ?>
              <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred-outline btn-sm px-4"><i class="fas fa-lock me-1"></i> <?php echo t('login_to_view'); ?></a>
            <?php else: ?>
              <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred btn-sm px-4" style="background:#4A3728; color:var(--chandan-gold); border-color:var(--chandan-gold);"><i class="fas fa-crown me-1"></i> <?php echo t('subscribe_to_view'); ?></a>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Vichudo / Panchak — Subscribers only -->
        <?php if($canViewFull && (($p['vichudo'] ?? '') === 'YES' || ($p['panchak_start'] ?? ''))): ?>
        <div class="inauspicious-card mb-4">
          <h5 style="border-bottom:1px solid rgba(231,76,60,0.15); padding-bottom:10px;">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo t('vichudo_panchak'); ?>
          </h5>
          <div class="row g-3 mt-1">
            <?php if(($p['vichudo'] ?? '') === 'YES'): ?>
            <div class="col-md-6">
              <div class="inauspicious-item">
                <div class="label"><i class="fas fa-exclamation-circle me-1"></i><?php echo t('vichudo'); ?>: <?php echo (($p['vichudo'] ?? '') === 'YES' ? t('yes') : t('no')); ?></div>
                <?php if($p['vichudo_start']): ?>
                  <div class="time-value mt-1"><?php echo t('start'); ?>: <?php echo $pv($p['vichudo_start']); ?></div>
                <?php endif; ?>
                <?php if($p['vichudo_end']): ?>
                  <div class="time-value"><?php echo t('end'); ?>: <?php echo $pv($p['vichudo_end']); ?></div>
                <?php endif; ?>
              </div>
            </div>
            <?php endif; ?>
            <?php if($p['panchak_start'] ?? ''): ?>
            <div class="col-md-6">
              <div class="inauspicious-item">
                <div class="label"><i class="fas fa-exclamation-circle me-1"></i><?php echo t('panchak'); ?></div>
                <div class="time-value mt-1"><?php echo t('start'); ?>: <?php echo $pv($p['panchak_start']); ?></div>
                <div class="time-value"><?php echo t('end'); ?>: <?php echo $pv($p['panchak_end']); ?></div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right Side — Times + PDF -->
      <div class="col-lg-4">
        
        <?php if($canViewFull): ?>
        <!-- Rahu Kaal / Gulika / Yama -->
        <div class="panchang-detail-card mb-4">
          <h5 style="color:var(--sacred-maroon); font-size:0.95rem;">
            <i class="fas fa-clock" style="color:#e74c3c;"></i>
            <?php echo t('rahu_kaal'); ?> · <?php echo t('gulika_kaal'); ?> · <?php echo t('yama_gandam'); ?>
          </h5>
          <div class="inauspicious-item mb-3" style="border-left-color:#e74c3c;">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-skull-crossbones" style="color:#e74c3c; font-size:1.1rem;"></i>
              <div>
                <div class="label"><?php echo t('rahu_kaal'); ?></div>
                <div class="time-value">
                  <?php echo ($p['rahu_start'] && $p['rahu_end']) ? $pv($p['rahu_start']) . ' - ' . $pv($p['rahu_end']) : $pv(''); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="inauspicious-item mb-3" style="border-left-color:#9b59b6;">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-ghost" style="color:#9b59b6; font-size:1.1rem;"></i>
              <div>
                <div class="label"><?php echo t('gulika_kaal'); ?></div>
                <div class="time-value">
                  <?php echo ($p['gulika_start'] && $p['gulika_end']) ? $pv($p['gulika_start']) . ' - ' . $pv($p['gulika_end']) : $pv(''); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="inauspicious-item" style="border-left-color:#e67e22;">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-ban" style="color:#e67e22; font-size:1.1rem;"></i>
              <div>
                <div class="label"><?php echo t('yama_gandam'); ?></div>
                <div class="time-value">
                  <?php echo ($p['yama_start'] && $p['yama_end']) ? $pv($p['yama_start']) . ' - ' . $pv($p['yama_end']) : $pv(''); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php if($p['sun_lon'] || $p['moon_lon']): ?>
        <!-- Sun/Moon Longitudes -->
        <div class="panchang-detail-card mb-4">
          <h5 style="color:var(--sacred-maroon); font-size:0.95rem;">
            <i class="fas fa-compass" style="color:var(--chandan-gold);"></i>
            <?php echo t('graha_position'); ?>
          </h5>
          <div class="panchang-info-box mb-2" style="border-left-color:#f39c12;">
            <div class="d-flex justify-content-between align-items-center">
              <span><i class="fas fa-sun me-2" style="color:#f39c12;"></i><?php echo t('sun_longitude'); ?></span>
              <span class="info-value"><?php echo $pv($p['sun_lon']); ?>°</span>
            </div>
          </div>
          <div class="panchang-info-box" style="border-left-color:#95a5a6;">
            <div class="d-flex justify-content-between align-items-center">
              <span><i class="fas fa-moon me-2" style="color:#95a5a6;"></i><?php echo t('moon_longitude'); ?></span>
              <span class="info-value"><?php echo $pv($p['moon_lon']); ?>°</span>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- PDF Download -->
        <?php if($canViewFull): ?>
        <div class="pdf-download-card mb-4">
          <h5>
            <i class="fas fa-file-pdf me-2"></i><?php echo t('download_pdf'); ?>
          </h5>
          <p style="font-size:0.85rem; color:var(--chandan-light); opacity:0.8; margin-bottom:1rem;">
            <?php echo t('download_pdf'); ?> — <?php echo date('d M Y', strtotime($p['panchang_date'])); ?>
          </p>
          <a href="<?php echo SITE_URL; ?>/download-pdf.php?id=<?php echo $p['id']; ?>" class="btn-pdf-download">
            <i class="fas fa-download" style="font-size:1.2rem;"></i> Download Full PDF
          </a>
        </div>
        <?php endif; ?>


        
        <?php if(!$canViewFull): ?>
        <div class="panchang-detail-card mb-4 text-center" style="background:linear-gradient(135deg, var(--light-sand), var(--chandan-cream)); border-color:var(--chandan-gold);">
            <i class="fas fa-lock" style="font-size:3rem; color:var(--chandan-gold); opacity:0.6;"></i>
            <h5 class="mt-3" style="color:var(--sacred-maroon);"><?php echo t('premium_content'); ?></h5>
            <p class="text-muted" style="font-size:0.9rem;"><?php echo t('premium_content_desc'); ?></p>
            <?php if(!isLoggedIn()): ?>
              <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred mt-2"><i class="fas fa-lock me-1"></i> <?php echo t('login_to_view'); ?></a>
            <?php else: ?>
              <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred mt-2" style="background:#4A3728; color:var(--chandan-gold); border-color:var(--chandan-gold);"><i class="fas fa-crown me-1"></i> <?php echo t('subscribe_to_view'); ?></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

      </div>

    </div>

    <?php endif; ?>

  </div>
</section>

<?php require_once 'footer.php'; ?>
