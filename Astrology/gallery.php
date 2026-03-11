<?php
$pageTitle = 'Gallery';
require_once 'header.php';

$result = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-images me-2"></i><?php echo t('gallery'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('gallery'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">
    <div class="section-header">
      <h2><?php echo t('divine_moments'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('gallery_desc'); ?></p>
    </div>

    <?php if($result && $result->num_rows > 0): ?>
    <div class="gallery-grid">
      <?php while($g = $result->fetch_assoc()): ?>
      <div class="gallery-item animate-on-scroll">
        <img src="<?php echo SITE_URL; ?>/uploads/gallery/<?php echo $g['image']; ?>" alt="<?php echo htmlspecialchars(t($g['title'])); ?>">
        <div class="gallery-overlay">
          <h6><?php echo htmlspecialchars(t($g['title'])); ?></h6>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
      <i class="fas fa-images fa-4x mb-3" style="color:var(--chandan-gold); opacity:0.4;"></i>
      <h4><?php echo t('gallery_coming_soon'); ?></h4>
      <p class="text-muted">Beautiful images will be added here soon.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Lightbox -->
<div class="lightbox-overlay" id="lightbox">
  <img src="" alt="Gallery Image">
</div>

<?php require_once 'footer.php'; ?>
