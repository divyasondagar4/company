<?php
$pageTitle = 'About';
require_once 'header.php';

// Try to load about sections from DB
$dbSections = [];
$tableExists = $conn->query("SHOW TABLES LIKE 'about_content'");
if ($tableExists && $tableExists->num_rows > 0) {
    $result = $conn->query("SELECT * FROM about_content WHERE is_active = 1 ORDER BY display_order ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) $dbSections[] = $row;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-info-circle me-2"></i><?php echo t('about'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('about'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="sacred-card">
          <div class="text-center mb-4">
            <i class="fas fa-om fa-3x" style="color:var(--chandan-gold);"></i>
            <h2 class="mt-3"><?php echo t('about'); ?> — Astro Panchang</h2>
            <div class="header-line mx-auto"></div>
          </div>
          
          <p style="font-size:1.05rem; line-height:1.9; color:var(--text-secondary);">
            <?php echo t('about_intro'); ?>
          </p>
          
          <div class="row g-4 mt-4">
            <?php if(!empty($dbSections)): ?>
              <?php foreach($dbSections as $sec): ?>
              <div class="col-md-6">
                <div class="sacred-card text-center" style="background:var(--chandan-cream); border:1px solid var(--chandan-light);">
                  <i class="fas <?php echo htmlspecialchars($sec['section_icon']); ?> fa-2x mb-3" style="color:var(--chandan-gold);"></i>
                  <h5><?php echo htmlspecialchars($sec['section_title']); ?></h5>
                  <p class="mb-0" style="font-size:0.9rem; color:var(--text-secondary);"><?php echo htmlspecialchars($sec['section_content']); ?></p>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <!-- Fallback hardcoded sections -->
              <div class="col-md-6">
                <div class="sacred-card text-center" style="background:var(--chandan-cream); border:1px solid var(--chandan-light);">
                  <i class="fas fa-sun fa-2x mb-3" style="color:var(--chandan-gold);"></i>
                  <h5><?php echo t('daily_panchang_title'); ?></h5>
                  <p class="mb-0" style="font-size:0.9rem; color:var(--text-secondary);"><?php echo t('daily_panchang_desc'); ?></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="sacred-card text-center" style="background:var(--chandan-cream); border:1px solid var(--chandan-light);">
                  <i class="fas fa-calendar-check fa-2x mb-3" style="color:var(--chandan-gold);"></i>
                  <h5><?php echo t('muhurat_timings_title'); ?></h5>
                  <p class="mb-0" style="font-size:0.9rem; color:var(--text-secondary);"><?php echo t('muhurat_timings_desc'); ?></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="sacred-card text-center" style="background:var(--chandan-cream); border:1px solid var(--chandan-light);">
                  <i class="fas fa-calendar-days fa-2x mb-3" style="color:var(--chandan-gold);"></i>
                  <h5><?php echo t('festival_calendar_title'); ?></h5>
                  <p class="mb-0" style="font-size:0.9rem; color:var(--text-secondary);"><?php echo t('festival_calendar_desc'); ?></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="sacred-card text-center" style="background:var(--chandan-cream); border:1px solid var(--chandan-light);">
                  <i class="fas fa-file-pdf fa-2x mb-3" style="color:var(--chandan-gold);"></i>
                  <h5><?php echo t('pdf_downloads_title'); ?></h5>
                  <p class="mb-0" style="font-size:0.9rem; color:var(--text-secondary);"><?php echo t('pdf_downloads_desc'); ?></p>
                </div>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="text-center mt-5">
            <a href="<?php echo SITE_URL; ?>/contact" class="btn-sacred">
              <i class="fas fa-envelope"></i> <?php echo t('contact_us'); ?>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>
