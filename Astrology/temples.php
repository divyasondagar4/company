<?php
$pageTitle = 'Temples';
require_once 'header.php';

$result = $conn->query("SELECT * FROM temples ORDER BY name ASC");
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-place-of-worship me-2"></i><?php echo t('sacred_temples'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('temples'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">
    <div class="section-header">
      <h2><?php echo t('sacred_temples'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('temple_info'); ?></p>
    </div>

    <div class="row g-4">
      <?php if($result && $result->num_rows > 0): ?>
        <?php while($t = $result->fetch_assoc()): ?>
          <div class="col-md-6 col-lg-4 animate-on-scroll">
            <div class="temple-card">
              <div class="temple-img">
                <?php if($t['image'] && file_exists('uploads/gallery/' . $t['image'])): ?>
                  <img src="<?php echo SITE_URL; ?>/uploads/gallery/<?php echo $t['image']; ?>" alt="<?php echo htmlspecialchars($t['name']); ?>">
                <?php else: ?>
                  <i class="fas fa-place-of-worship"></i>
                <?php endif; ?>
              </div>
              <div class="temple-info">
                <h5><?php echo htmlspecialchars(t($t['name'])); ?></h5>
                <div class="temple-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <?php echo htmlspecialchars(t($t['location'])); ?>
                </div>
                <p style="font-size:0.9rem; color:var(--text-secondary);">
                  <?php echo htmlspecialchars(t($t['description'])); ?>
                </p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
          <i class="fas fa-place-of-worship fa-4x mb-3" style="color:var(--chandan-gold); opacity:0.4;"></i>
          <h4><?php echo t('no_data'); ?></h4>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>
