<?php
$pageTitle = 'Book';
require_once 'header.php';

// Try to load books from DB
$dbBooks = [];
$tableExists = $conn->query("SHOW TABLES LIKE 'books'");
if ($tableExists && $tableExists->num_rows > 0) {
    $result = $conn->query("SELECT * FROM books WHERE status IN ('active','coming_soon') ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) $dbBooks[] = $row;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-book me-2"></i><?php echo t('book'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('book'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">
    <div class="section-header">
      <h2><i class="fas fa-book-open me-2" style="color:var(--chandan-gold);"></i><?php echo t('our_publications'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('explore_books'); ?></p>
    </div>

    <div class="row g-4 justify-content-center">
      <?php if(!empty($dbBooks)): ?>
        <?php foreach($dbBooks as $book): ?>
        <div class="col-md-6 col-lg-4">
          <div class="sacred-card text-center d-flex flex-column h-100">
            <div style="width:100%;height:240px;background:linear-gradient(135deg,var(--sacred-maroon),var(--dark-wood));border-radius:var(--radius-md) var(--radius-md) 0 0;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;overflow:hidden;border-bottom:1px solid rgba(197,151,59,0.2);">
              <?php if($book['cover_image']): ?>
              <img src="<?php echo SITE_URL . '/' . $book['cover_image']; ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" style="width:100%;height:100%;object-fit:cover;">
              <?php else: ?>
              <i class="fas fa-book fa-5x" style="color:var(--chandan-gold); opacity:0.6;"></i>
              <?php endif; ?>
            </div>
            
            <div class="px-3 pb-3 d-flex flex-column flex-grow-1">
              <h4 class="mb-2" style="font-size:1.25rem; font-weight:700; color:var(--dark-wood); word-break:break-word;"><?php echo htmlspecialchars($book['title']); ?></h4>
              <?php if($book['author']): ?>
              <p style="font-size:0.85rem; color:var(--chandan-gold); font-weight:600; margin-bottom:0.75rem;">
                <i class="fas fa-feather-alt me-1"></i><?php echo htmlspecialchars($book['author']); ?>
              </p>
              <?php endif; ?>
              
              <div class="flex-grow-1 text-start" style="font-size:0.9rem; color:var(--text-secondary); line-height:1.6; word-wrap:break-word; overflow-wrap:break-word; margin-bottom:1.5rem;">
                <?php echo nl2br(htmlspecialchars($book['description'])); ?>
              </div>
              
              <div class="mt-auto pt-3 border-top" style="border-color:rgba(197,151,59,0.2) !important;">
              <?php if($book['status'] === 'coming_soon'): ?>
                <span class="badge" style="background:rgba(197,151,59,0.1); color:var(--chandan-deep); padding:0.6rem 1.2rem; border-radius:30px; border:1px solid rgba(197,151,59,0.3); font-size:0.85rem;">
                  <i class="fas fa-hourglass-half me-1"></i> <?php echo t('coming_soon_badge'); ?>
                </span>
              <?php else: ?>
                <?php if(isLoggedIn() && (isAdmin() || (isset($_SESSION['user_id']) && isSubscribed($conn, $_SESSION['user_id'])))): ?>
                  <?php if(!empty($book['book_pdf'])): ?>
                    <a href="<?php echo SITE_URL . '/' . htmlspecialchars($book['book_pdf']); ?>" target="_blank" class="btn-sacred w-100 py-2">
                      <i class="fas fa-file-pdf me-2"></i> <?php echo t('download_pdf_btn'); ?>
                    </a>
                  <?php else: ?>
                    <button class="btn btn-secondary w-100" disabled style="border-radius:30px; font-size:0.9rem; opacity:0.6;">
                      <i class="fas fa-info-circle me-1"></i> <?php echo t('pdf_coming_soon'); ?>
                    </button>
                  <?php endif; ?>
                <?php else: ?>
                  <a href="<?php echo SITE_URL; ?>/subscribe" class="btn-sacred w-100 py-2">
                    <i class="fas fa-crown me-2"></i> <?php echo t('subscribe_to_download'); ?>
                  </a>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
           <i class="fas fa-book-reader fa-3x mb-3" style="color:var(--chandan-gold); opacity:0.5;"></i>
           <p style="color:var(--chandan-gold); font-size:1.1rem;"><?php echo t('no_books_found'); ?></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>
