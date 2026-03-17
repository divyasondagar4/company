<?php
require_once 'header.php';

$success = $error = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $img = $conn->query("SELECT image FROM gallery WHERE id=$id")->fetch_assoc();
    if ($img && $img['image']) {
        @unlink(__DIR__ . '/../uploads/gallery/' . $img['image']);
    }
    if ($conn->query("DELETE FROM gallery WHERE id=$id")) $success = "Image deleted.";
    else $error = "Delete failed: " . $conn->error;
}

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $file = $_FILES['image'];

    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'gallery_' . time() . '_' . rand(100,999) . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/gallery/' . $filename)) {
            if ($conn->query("INSERT INTO gallery (title, image) VALUES ('$title', '$filename')")) {
                $success = "Image uploaded successfully.";
            } else {
                $error = "Database add failed: " . $conn->error;
            }
        }
    } else {
        $error = "Upload failed. Please try again.";
    }
}

$result = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<h2 class="mb-4" style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
    <i class="fas fa-images me-2" style="color:var(--chandan-gold);"></i>Manage Gallery
</h2>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="manage_gallery.php"></div>
<?php endif; ?>
<?php if($error): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
<?php endif; ?>

<!-- Upload Form -->
<div class="sacred-card mb-4" style="border-left:5px solid var(--chandan-gold);">
  <h4 style="color:var(--sacred-maroon); font-size:1.1rem; margin-bottom:1.2rem;"><i class="fas fa-upload me-2" style="color:var(--chandan-gold);"></i>Upload New Image</h4>
  <form method="POST" enctype="multipart/form-data" class="form-sacred">
    <div class="row g-4">
      <div class="col-md-5">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.85rem;">Title / Description</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent" style="border-color:rgba(197,151,59,0.3); border-right:none;"><i class="fas fa-font" style="color:var(--chandan-gold);"></i></span>
          <input type="text" name="title" class="form-control" placeholder="Describe the image...">
        </div>
      </div>
      <div class="col-md-5">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.85rem;">Choose File *</label>
        <input type="file" name="image" class="form-control" accept="image/*" required>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn-sacred w-100"><i class="fas fa-cloud-upload-alt me-1"></i> Upload</button>
      </div>
    </div>
  </form>
</div>

<!-- Gallery Grid -->
<div class="row g-4">
  <?php if($result && $result->num_rows > 0): while($g = $result->fetch_assoc()): ?>
  <div class="col-lg-3 col-md-4 col-sm-6">
    <div class="sacred-card p-2" style="transition: transform 0.3s ease; box-shadow:0 4px 15px rgba(0,0,0,0.1); overflow:hidden;">
      <div style="position:relative; group">
        <img src="<?php echo SITE_URL; ?>/uploads/gallery/<?php echo $g['image']; ?>" style="width:100%; height:180px; object-fit:cover; border-radius:8px; border:1px solid rgba(197,151,59,0.2);" alt="">
        <div style="padding:0.4rem 0.2rem;">
          <p class="mb-2 text-truncate fw-bold" style="font-size:0.85rem; color:var(--sacred-maroon);" title="<?php echo htmlspecialchars($g['title'] ?: 'Untitled'); ?>">
            <?php echo htmlspecialchars($g['title'] ?: 'Untitled Image'); ?>
          </p>
          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted" style="font-size:0.7rem;">ID: #<?php echo $g['id']; ?></small>
            <a href="?delete=<?php echo $g['id']; ?>" class="btn-maroon" style="padding:0.2rem 0.6rem; font-size:0.7rem; color:var(--chandan-light);" onclick="return confirm('Delete this image permanently?')">
              <i class="fas fa-trash-alt me-1"></i> Delete
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; else: ?>
  <div class="col-12 text-center py-5">
    <div style="opacity:0.3;">
      <i class="fas fa-images" style="font-size:4rem; color:var(--chandan-gold);"></i>
      <h5 class="mt-3">No images found in your gallery.</h5>
      <p>Start by uploading your first sacred image above.</p>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
