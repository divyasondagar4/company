<?php
$pageTitle = 'Panchang Details';
require_once 'header.php';

$date = $_GET['date'] ?? date('Y-m-d');
$currentLocation = $_SESSION['user_location'] ?? '';

$p = null;
$isFallback = false;
if (!empty($currentLocation)) {
    $stmt = $conn->prepare("SELECT * FROM panchang WHERE location = ? AND panchang_date = ? LIMIT 1");
    $stmt->bind_param("ss", $currentLocation, $date);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
}

// Subscription check
$canViewFull = isLoggedIn() && (isAdmin() || isSubscribed($conn, $_SESSION['user_id']));

?>

<!-- Page Header -->
<div class="page-header">
  <div class="container text-center">
    <h1><i class="fas fa-sun me-2 nav-icon-spin"></i><?php echo t('panchang_details'); ?></h1>
    <nav aria-label="breadcrumb" class="d-inline-block">
      <ol class="breadcrumb justify-content-center mb-2">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/panchang"><?php echo t('panchang'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo date('d M Y', strtotime($date)); ?></li>
      </ol>
    </nav>
    <?php if($p): ?>
    <div style="color:var(--chandan-light); opacity:0.8; font-size:0.85rem;">
      <span>ID: <span class="fw-bold"><?php echo $p['id']; ?></span></span>
      <span class="mx-2">|</span>
      <span><?php echo t('recorded_at'); ?>: <?php echo render_field($p['created_at']); ?></span>
    </div>
    <?php endif; ?>
  </div>
</div>

<section class="section-sacred section-panchang-home" style="padding:40px 0;">
  <div class="container">
    <?php if($isFallback && $p): ?>
    <div class="alert alert-sacred-outline py-2 px-3 mb-4 mx-auto" style="max-width:800px; border-color:var(--chandan-gold); color:var(--chandan-gold); background:rgba(197,151,59,0.05); font-size:0.9rem; border-radius:10px;">
        <i class="fas fa-info-circle me-2"></i><?php echo t('showing_data_for'); ?> <strong><?php echo htmlspecialchars($p['location']); ?></strong> (<?php echo t('selected_location_no_data'); ?>)
    </div>
    <?php endif; ?>

    <?php if(!$p): ?>
      <div class="panchang-detail-card text-center py-5">
        <i class="fas fa-calendar-times" style="font-size:3rem; color:var(--chandan-gold); opacity:0.5;"></i>
        <h4 class="mt-3"><?php echo t('no_data'); ?></h4>
        <p class="text-muted mb-3"><?php echo t('panchang_for'); ?> <?php echo date('d M Y', strtotime($date)); ?> <?php echo $currentLocation ? ' - ' . htmlspecialchars($currentLocation) : ''; ?></p>
        <a href="panchang" class="btn-sacred mt-3"><i class="fas fa-arrow-left"></i> <?php echo t('browse_all'); ?></a>
      </div>
    <?php else: ?>

    <!-- Navigation Header -->
    <?php
    $prevDate = date('Y-m-d', strtotime($date . ' -1 day'));
    $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
    $locFilter = !empty($currentLocation) ? "AND location = ?" : "";
    
    $stmtPrev = $conn->prepare("SELECT id FROM panchang WHERE panchang_date=? $locFilter LIMIT 1");
    if(!empty($currentLocation)) $stmtPrev->bind_param("ss", $prevDate, $currentLocation);
    else $stmtPrev->bind_param("s", $prevDate);
    $stmtPrev->execute();
    $prevExists = $stmtPrev->get_result()->num_rows > 0;
    
    $stmtNext = $conn->prepare("SELECT id FROM panchang WHERE panchang_date=? $locFilter LIMIT 1");
    if(!empty($currentLocation)) $stmtNext->bind_param("ss", $nextDate, $currentLocation);
    else $stmtNext->bind_param("s", $nextDate);
    $stmtNext->execute();
    $nextExists = $stmtNext->get_result()->num_rows > 0;
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <a href="<?php echo $prevExists ? '?date='.$prevDate : '#'; ?>" class="btn-sacred-outline day-nav-btn <?php echo !$prevExists ? 'disabled opacity-50' : ''; ?>">
        <i class="fas fa-arrow-left"></i> <?php echo t('previous'); ?>
      </a>
      <div class="text-center">
        <h3 style="color:var(--sacred-maroon); margin:0; font-size:1.5rem;">
          <i class="fas fa-calendar-alt me-2" style="color:var(--chandan-gold);"></i><?php echo t_date($date); ?>
        </h3>
        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($p['location']); ?></small>
      </div>
      <a href="<?php echo $nextExists ? '?date='.$nextDate : '#'; ?>" class="btn-sacred day-nav-btn <?php echo !$nextExists ? 'disabled opacity-50' : ''; ?>">
        <?php echo t('next'); ?> <i class="fas fa-arrow-right"></i>
      </a>
    </div>

    <!-- MAIN UI GRID -->
    <div class="row g-4">
      
      <!-- COLUMN 1: Solar & Vedic Elements -->
      <div class="col-lg-8">
        
        <!-- CARD 1: Solar & Regional Details -->
        <div class="panchang-detail-card mb-4" style="border-left:4px solid #f39c12;">
          <h5 style="color:var(--sacred-maroon); margin-bottom:1.2rem;">
            <i class="fas fa-sun me-2" style="color:#f39c12;"></i><?php echo t('solar_lunar_details'); ?>
          </h5>
          <div class="row g-3">
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center">
                <div class="info-label"><?php echo t('sunrise'); ?></div>
                <div class="info-value"><?php echo render_field($p['sunrise'] ?? null, true); ?></div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center">
                <div class="info-label"><?php echo t('sunset'); ?></div>
                <div class="info-value"><?php echo render_field($p['sunset'] ?? null, true); ?></div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center">
                <div class="info-label"><?php echo t('ayan'); ?><?php if(!empty($p['ayan_no'])): ?> <small>(<?php echo $p['ayan_no']; ?>)</small><?php endif; ?></div>
                <div class="info-value"><?php echo render_translated_field($p['ayan'] ?? null); ?></div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="panchang-info-box text-center">
                <div class="info-label"><?php echo t('gujarati_month'); ?><?php if(!empty($p['gujarati_month_no'])): ?> <small>(<?php echo $p['gujarati_month_no']; ?>)</small><?php endif; ?></div>
                <div class="info-value"><?php echo render_translated_field($p['gujarati_month'] ?? null); ?></div>
              </div>
            </div>
            <!-- Expanded Details -->
            <div class="col-md-6">
              <div class="d-flex justify-content-between border-bottom py-1">
                <span class="text-muted font-sm"><?php echo t('vikram_samvat'); ?>:</span>
                <span class="fw-bold"><?php echo render_field($p['vikram_samvat'] ?? null); ?><?php if(!empty($p['year']) || !empty($p['month'])): ?> <small class="text-muted">(<?php echo render_field($p['year'] ?? null); ?>-<?php echo render_field($p['month'] ?? null); ?>)</small><?php endif; ?></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between border-bottom py-1">
                <span class="text-muted font-sm"><?php echo t('day'); ?>:</span>
                <span class="fw-bold"><?php echo render_translated_field($p['day_name'] ?? null); ?><?php if(!empty($p['vara_no'])): ?> <small class="text-muted">(<?php echo $p['vara_no']; ?>)</small><?php endif; ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD 2: Vedic Elements (Tithi, Nakshatra, Yoga, Karana) -->
        <div class="panchang-detail-card mb-4" style="border-left:4px solid var(--chandan-gold); position:relative;">
          <h5 style="color:var(--sacred-maroon); margin-bottom:1.2rem;">
            <i class="fas fa-om me-2" style="color:var(--chandan-gold);"></i><?php echo t('panchang_elements'); ?>
          </h5>
          <div class="row g-3">
            <!-- Tithi -->
            <div class="col-md-6">
              <div class="panchang-info-box">
                <div class="info-label"><?php echo t('tithi'); ?><?php if(!empty($p['tithi_no'])): ?> <small>(<?php echo $p['tithi_no']; ?>)</small><?php endif; ?></div>
                <div class="info-value"><?php echo render_translated_field($p['tithi'] ?? null); ?></div>
                <div class="text-muted font-xs mt-1"><?php echo t('end'); ?>: <?php echo render_field($p['tithi_end'] ?? null); ?></div>
              </div>
            </div>
            <!-- Nakshatra -->
            <div class="col-md-6">
              <div class="panchang-info-box">
                <div class="info-label"><?php echo t('nakshatra'); ?><?php if(!empty($p['nak_no'])): ?> <small>(<?php echo $p['nak_no']; ?>)</small><?php endif; ?></div>
                <div class="info-value"><?php echo render_translated_field($p['nakshatra'] ?? null); ?></div>
                <div class="text-muted font-xs mt-1">
                  <?php echo t('start'); ?>: <?php echo render_field($p['nak_start'] ?? null); ?> | <?php echo t('end'); ?>: <?php echo render_field($p['nak_end'] ?? null); ?>
                </div>
              </div>
            </div>
            
            <?php if($canViewFull): ?>
            <!-- Yoga -->
            <div class="col-md-6">
              <div class="panchang-info-box">
                <div class="info-label"><?php echo t('yoga'); ?><?php if(!empty($p['yoga_no'])): ?> <small>(<?php echo $p['yoga_no']; ?>)</small><?php endif; ?></div>
                <div class="info-value"><?php echo render_translated_field($p['yoga'] ?? null); ?></div>
                <div class="text-muted font-xs mt-1"><?php echo t('end'); ?>: <?php echo render_field($p['yoga_end'] ?? null); ?></div>
              </div>
            </div>
            <!-- Karana -->
            <div class="col-md-6">
              <div class="panchang-info-box">
                <div class="info-label"><?php echo t('karana'); ?><?php if(!empty($p['karana_no'])): ?> <small>(<?php echo $p['karana_no']; ?>)</small><?php endif; ?></div>
                <div class="info-value"><?php echo render_translated_field($p['karana'] ?? null); ?></div>
                <div class="text-muted font-xs mt-1"><?php echo t('end'); ?>: <?php echo render_field($p['karana_end'] ?? null); ?></div>
              </div>
            </div>
            <?php else: ?>
            <div class="col-12 text-center py-2" style="background:rgba(197,151,59,0.05); border-radius:8px;">
              <p class="mb-1 fw-semibold font-sm"><?php echo t('unlock_yoga_karana'); ?></p>
              <a href="subscribe" class="btn-sacred-outline btn-xs px-3"><i class="fas fa-lock me-1"></i> <?php echo t('subscribe_to_view'); ?></a>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- CARD 3: Specialized Periods (Vichudo/Panchak) -->
        <?php if($canViewFull): ?>
        <div class="panchang-detail-card inauspicious-card mb-4">
           <h5 style="color:#c0392b; margin-bottom:1.2rem;">
             <i class="fas fa-exclamation-triangle me-2"></i><?php echo t('vichudo_panchak'); ?>
           </h5>
           <div class="row g-3">
             <div class="col-md-6">
               <div class="inauspicious-item" style="border-left-color:#c0392b;">
                 <div class="label"><?php echo t('vichudo'); ?>: <span class="fw-bold"><?php echo render_field($p['vichudo'] ?? null); ?></span></div>
                 <div class="font-xs text-muted mt-1"><?php echo t('start'); ?>: <?php echo render_field($p['vichudo_start'] ?? null); ?></div>
                 <div class="font-xs text-muted"><?php echo t('end'); ?>: <?php echo render_field($p['vichudo_end'] ?? null); ?></div>
               </div>
             </div>

             <div class="col-md-6">
               <div class="inauspicious-item" style="border-left-color:#e67e22;">
                 <div class="label"><?php echo t('panchak'); ?></div>
                 <div class="font-xs text-muted mt-1"><?php echo t('start'); ?>: <?php echo render_field($p['panchak_start'] ?? null); ?></div>
                 <div class="font-xs text-muted"><?php echo t('end'); ?>: <?php echo render_field($p['panchak_end'] ?? null); ?></div>
               </div>
             </div>
           </div>
        </div>
        <?php endif; ?>

      </div>

      <!-- COLUMN 2: Longitudes & Inauspicious Times -->
      <div class="col-lg-4">
        
        <!-- CARD 4: Planetary Positions -->
        <div class="panchang-detail-card mb-4" style="border-bottom: 4px solid #3498db;">
          <h5 style="color:var(--sacred-maroon);">
            <i class="fas fa-globe-asia me-2" style="color:#3498db;"></i><?php echo t('graha_position'); ?>
          </h5>
          <div class="mt-3">
            <div class="d-flex justify-content-between border-bottom py-2">
              <span class="text-muted font-sm"><i class="fas fa-sun me-1 text-warning"></i> <?php echo t('sun_longitude'); ?></span>
              <span class="fw-bold"><?php echo render_field($p['sun_lon'] ?? null); ?><?php if(!empty($p['sun_lon'])): ?>°<?php endif; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom py-2">
              <span class="text-muted font-sm"><i class="fas fa-moon me-1 text-secondary"></i> <?php echo t('moon_longitude'); ?></span>
              <span class="fw-bold"><?php echo render_field($p['moon_lon'] ?? null); ?><?php if(!empty($p['moon_lon'])): ?>°<?php endif; ?></span>
            </div>
          </div>
        </div>

        <!-- CARD 5: Inauspicious Kaal -->
        <div class="panchang-detail-card inauspicious-card mb-4">
          <h5 style="color:#c0392b; font-size:1.1rem; margin-bottom:1.2rem;">
            <i class="fas fa-skull-crossbones me-2"></i><?php echo t('inauspicious_timings'); ?>
          </h5>
          <?php if($canViewFull): ?>
            <!-- Rahu Kaal -->
            <div class="inauspicious-item mb-3" style="border-left-color:#e74c3c;">
               <div class="label"><?php echo t('rahu_kaal'); ?></div>
               <div class="time-value"><?php echo render_field($p['rahu_start'] ?? null, true); ?> - <?php echo render_field($p['rahu_end'] ?? null, true); ?></div>
            </div>
            <!-- Gulika Kaal -->
            <div class="inauspicious-item mb-3" style="border-left-color:#9b59b6;">
               <div class="label"><?php echo t('gulika_kaal'); ?></div>
               <div class="time-value"><?php echo render_field($p['gulika_start'] ?? null, true); ?> - <?php echo render_field($p['gulika_end'] ?? null, true); ?></div>
            </div>
            <!-- Yama Gandam -->
            <div class="inauspicious-item" style="border-left-color:#f39c12;">
               <div class="label"><?php echo t('yama_gandam'); ?></div>
               <div class="time-value"><?php echo render_field($p['yama_start'] ?? null, true); ?> - <?php echo render_field($p['yama_end'] ?? null, true); ?></div>
            </div>
          <?php else: ?>
            <div class="text-center py-3">
              <i class="fas fa-lock mb-2" style="font-size:1.5rem; color:var(--chandan-gold); opacity:0.6;"></i>
              <p class="font-sm mb-2"><?php echo t('unlock_kaal_timings'); ?></p>
              <a href="subscribe" class="btn-sacred-outline btn-sm px-4"><?php echo t('subscribe'); ?></a>
            </div>
          <?php endif; ?>
        </div>

        <!-- CARD 6: PDF & Technical Details -->
        <div class="panchang-detail-card mb-4" style="background:var(--light-sand);">
           <h5 style="color:var(--sacred-maroon); font-size:1rem;">
             <i class="fas fa-file-pdf me-2"></i><?php echo t('download_full_report'); ?>
           </h5>
           <div class="mt-3 font-sm text-muted mb-3" style="line-height:1.5; font-style:italic;">
             <?php echo !empty($p['details']) ? nl2br(htmlspecialchars($p['details'])) : render_field(null); ?>
           </div>
           <?php if($canViewFull): ?>
             <a href="download-pdf.php?id=<?php echo $p['id']; ?>" class="btn-sacred btn-sm w-100 mt-3">
               <i class="fas fa-file-pdf me-2"></i><?php echo t('download_full_report'); ?>
             </a>
           <?php else: ?>
             <div class="text-center py-2 mt-2" style="background:rgba(197,151,59,0.05); border-radius:8px;">
               <p class="mb-1 fw-semibold font-sm"><?php echo t('unlock_pdf_report'); ?></p>
               <a href="subscribe" class="btn-sacred-outline btn-xs px-3"><i class="fas fa-lock me-1"></i> <?php echo t('subscribe_to_download'); ?></a>
             </div>
           <?php endif; ?>
        </div>

      </div>

    </div> <!-- End Row -->

    <?php endif; ?>
  </div>
</section>

<style>
  .font-xs { font-size: 0.7rem; }
  .font-sm { font-size: 0.85rem; }

  .panchang-info-box .info-label { font-weight: 600; font-size: 0.78rem; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 2px; }
  .panchang-info-box .info-value { font-weight: 700; font-size: 1.1rem; color: var(--sacred-maroon); }
</style>

<?php require_once 'footer.php'; ?>
