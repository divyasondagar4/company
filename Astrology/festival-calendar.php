<?php
$pageTitle = 'Festival Calendar';
require_once 'header.php';

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$result = $conn->query("SELECT * FROM festivals WHERE YEAR(festival_date) = $year ORDER BY festival_date ASC");

// Gating check removed - all guests can view
$canViewFullToday = true;
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-calendar-days me-2"></i><?php echo t('festival_calendar'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('festivals'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">

    <!-- Year Filter -->
    <div class="text-center mb-4">
      <a href="?year=<?php echo $year - 1; ?>" class="btn-sacred-outline me-2">
        <i class="fas fa-arrow-left"></i> <?php echo $year - 1; ?>
      </a>
      <span class="btn-sacred" style="cursor:default; font-size:1.2rem;"><?php echo $year; ?></span>
      <a href="?year=<?php echo $year + 1; ?>" class="btn-sacred-outline ms-2">
        <?php echo $year + 1; ?> <i class="fas fa-arrow-right"></i>
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php if(empty($currentLocation)): ?>
          <div class="text-center py-5 sacred-card mb-4" style="background:var(--chandan-cream); border:2px dashed var(--chandan-gold);">
            <i class="fas fa-map-marker-alt fa-3x mb-3" style="color:var(--chandan-gold); opacity:0.6;"></i>
            <h4 style="color:var(--sacred-maroon);"><?php echo t('select_location'); ?></h4>
            <p class="text-muted mb-4"><?php echo t('select_location_desc') ?? 'Please select a location to view Festival details.'; ?></p>
            <a href="#" class="btn-sacred trigger-location-select px-4 py-2">
              <i class="fas fa-map-marker-alt me-2"></i><?php echo t('select_location'); ?>
            </a>
          </div>
        <?php elseif($result && $result->num_rows > 0): ?>
          <?php
          $currentMonth = '';
          $fCount = 0;
          while($f = $result->fetch_assoc()):
            $fCount++;
            if(!$canViewFullToday && $fCount > 10) break;

            $monthKey = strtolower(date('F', strtotime($f['festival_date'])));
            $monthYearLabel = t($monthKey) . ' ' . date('Y', strtotime($f['festival_date']));
            if ($monthYearLabel !== $currentMonth):
              $currentMonth = $monthYearLabel;
          ?>
            <h4 class="mt-4 mb-3" style="color:var(--chandan-gold); font-size:1.1rem;">
              <i class="fas fa-calendar-alt me-2"></i><?php echo $monthYearLabel; ?>
            </h4>
          <?php endif; ?>

          <div class="festival-item animate-on-scroll">
            <div class="festival-date-box">
              <div class="day"><?php echo date('d', strtotime($f['festival_date'])); ?></div>
              <div class="month"><?php echo mb_substr(t(strtolower(date('F', strtotime($f['festival_date'])))), 0, 3); ?></div>
            </div>
            <div style="flex:1;">
              <h5 style="margin-bottom:0.3rem;"><?php echo htmlspecialchars(t($f['festival_name'])); ?></h5>
              <p class="mb-0" style="font-size:0.85rem; color:var(--text-secondary);">
                <i class="fas fa-calendar me-1"></i>
                <?php echo t(strtolower(date('l', strtotime($f['festival_date'])))); ?>
              </p>
              <?php if($f['description']): ?>
              <p class="mb-0 mt-1" style="font-size:0.9rem; color:var(--text-secondary);">
                <?php echo htmlspecialchars(t($f['description'])); ?>
              </p>
              <?php endif; ?>
            </div>
          </div>
          <?php endwhile; ?>

          <?php if(!$canViewFullToday && $result->num_rows > 10): ?>
            <div class="text-center mt-5">
              <div class="panchang-detail-card py-5" style="background:rgba(255,255,255,0.6); border: 2px dashed var(--chandan-gold);">
                <i class="fas fa-lock fa-3x mb-3" style="color:var(--chandan-gold);"></i>
                <h3><?php echo t('unlock_all_festivals'); ?></h3>
                <p class="text-muted mb-4"><?php echo t('premium_access_desc'); ?></p>
                <a href="<?php echo SITE_URL; ?>/subscribe" class="btn-sacred btn-lg px-5"><?php echo t('subscribe_now'); ?></a>
              </div>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="text-center py-5">
            <i class="fas fa-calendar-xmark fa-4x mb-3" style="color:var(--chandan-gold); opacity:0.4;"></i>
            <h4><?php echo t('no_data'); ?></h4>
            <p class="text-muted"><?php echo t('no_festival_available'); ?> <?php echo $year; ?>.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>

<?php require_once 'footer.php'; ?>
