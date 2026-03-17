<?php
$pageTitle = 'Muhurat';
require_once 'header.php';

$type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
$where = "WHERE muhurat_date >= CURDATE()";
if ($type) {
    $where .= " AND type = '$type'";
}

$result = $conn->query("SELECT * FROM muhurat $where ORDER BY muhurat_date ASC");

// Gating check removed - all guests can view
$canViewFullToday = true;
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-calendar-check me-2"></i><?php echo t('muhurat'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('muhurat'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">

    <!-- Type Filters -->
    <div class="text-center mb-4">
      <a href="<?php echo SITE_URL; ?>/muhurat" class="btn-sacred<?php echo !$type ? '' : '-outline'; ?> me-2 mb-2"><?php echo t('all_types'); ?></a>
      <a href="?type=Marriage" class="btn-sacred<?php echo $type=='Marriage' ? '' : '-outline'; ?> me-2 mb-2">
        <i class="fas fa-heart"></i> <?php echo t('marriage'); ?>
      </a>
      <a href="?type=Griha Pravesh" class="btn-sacred<?php echo $type=='Griha Pravesh' ? '' : '-outline'; ?> me-2 mb-2">
        <i class="fas fa-house"></i> <?php echo t('griha_pravesh'); ?>
      </a>
      <a href="?type=Vastu" class="btn-sacred<?php echo $type=='Vastu' ? '' : '-outline'; ?> me-2 mb-2">
        <i class="fas fa-compass"></i> <?php echo t('vastu'); ?>
      </a>
      <a href="?type=Temple Sthapna" class="btn-sacred<?php echo $type=='Temple Sthapna' ? '' : '-outline'; ?> me-2 mb-2">
        <i class="fas fa-gopuram"></i> <?php echo t('temple_sthapna'); ?>
      </a>
    </div>

    <!-- Calendar View Link -->
    <div class="text-center mb-4">
      <a href="<?php echo SITE_URL; ?>/muhurat-calendar" class="btn-maroon">
        <i class="fas fa-calendar"></i> <?php echo t('view_full_calendar'); ?>
      </a>
    </div>

    <!-- Muhurat List -->
    <div class="row g-4">
      <?php if(empty($currentLocation)): ?>
        <div class="col-12 text-center py-5">
          <div class="sacred-card" style="background:var(--chandan-cream); border:2px dashed var(--chandan-gold);">
            <i class="fas fa-map-marker-alt fa-3x mb-3" style="color:var(--chandan-gold); opacity:0.6;"></i>
            <h4 style="color:var(--sacred-maroon);"><?php echo t('select_location'); ?></h4>
            <p class="text-muted mb-4"><?php echo t('select_location_desc') ?? 'Please select a location to view Muhurat details.'; ?></p>
            <a href="#" class="btn-sacred trigger-location-select px-4 py-2">
              <i class="fas fa-map-marker-alt me-2"></i><?php echo t('select_location'); ?>
            </a>
          </div>
        </div>
      <?php elseif($result && $result->num_rows > 0): ?>
        <?php 
          $mCount = 0;
          while($m = $result->fetch_assoc()): 
            $mCount++;
            if(!$canViewFullToday && $mCount > 6) break;
        ?>
          <div class="col-md-6 col-lg-4 animate-on-scroll">
            <div class="sacred-card">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="card-icon">
                  <?php
                    $icon = 'fa-star';
                    $badgeClass = 'badge-marriage';
                    switch(strtolower($m['type'])) {
                      case 'marriage': $icon = 'fa-heart'; $badgeClass = 'badge-marriage'; break;
                      case 'griha pravesh': $icon = 'fa-house'; $badgeClass = 'badge-griha'; break;
                      case 'vastu': $icon = 'fa-compass'; $badgeClass = 'badge-vastu'; break;
                      case 'temple sthapna': $icon = 'fa-gopuram'; $badgeClass = 'badge-business'; break;
                    }
                  ?>
                  <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <span class="badge badge-muhurat <?php echo $badgeClass; ?>"><?php echo t($m['type']); ?></span>
              </div>
              <h4><?php echo htmlspecialchars(t($m['title'])); ?></h4>
              <p style="color:var(--chandan-gold); font-weight:600; margin-bottom:0.5rem;">
                <i class="fas fa-calendar me-1"></i>
                <?php echo t_date($m['muhurat_date']); ?>
              </p>
              <p class="mb-2" style="font-size:0.9rem;">
                <i class="fas fa-clock me-1" style="color:var(--text-secondary);"></i>
                <?php echo ($m['start_time'] && $m['start_time'] !== '00:00:00') ? date('h:i A', strtotime($m['start_time'])) : ''; ?>
                <?php echo ($m['end_time'] && $m['end_time'] !== '00:00:00') ? ' — ' . date('h:i A', strtotime($m['end_time'])) : ''; ?>
              </p>
              <?php if($m['description']): ?>
                <p style="font-size:0.85rem; color:var(--text-secondary);">
                  <?php echo htmlspecialchars(t($m['description'])); ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>

        <?php if(!$canViewFullToday && $result->num_rows > 6): ?>
          <div class="col-12 text-center mt-5">
            <div class="panchang-detail-card py-5" style="background:rgba(255,255,255,0.6); border: 2px dashed var(--chandan-gold);">
              <i class="fas fa-lock fa-3x mb-3" style="color:var(--chandan-gold);"></i>
              <h3><?php echo t('unlock_full_muhurats'); ?></h3>
              <p class="text-muted mb-4"><?php echo t('premium_access_desc'); ?></p>
              <a href="<?php echo SITE_URL; ?>/subscribe" class="btn-sacred btn-lg px-5"><?php echo t('subscribe_now'); ?></a>
            </div>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
          <i class="fas fa-calendar-xmark fa-4x mb-3" style="color:var(--chandan-gold); opacity:0.4;"></i>
          <h4><?php echo t('no_data'); ?></h4>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php require_once 'footer.php'; ?>
