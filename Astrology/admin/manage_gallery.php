<?php
$adminTitle = 'Manage Gallery';
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
            }
        }
    } else {
        $error = "Upload failed. Please try again.";
    }
}

$result = $conn->query("SELECT * FROM gallery ORDER BY id DESC");
?>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-images me-2" style="color:var(--chandan-gold);"></i>Manage Gallery</h2>
</div>

<?php if($success): ?><div class="alert-sacred alert-success mb-3"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-sacred alert-danger mb-3"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div><?php endif; ?>

<!-- Upload Form -->
<div class="sacred-card mb-4">
  <h4>Upload New Image</h4>
  <form method="POST" enctype="multipart/form-data" class="form-sacred mt-3">
    <div class="row g-3">
      <div class="col-md-5"><label>Title</label><input type="text" name="title" class="form-control" placeholder="Image description"></div>
      <div class="col-md-5"><label>Image *</label><input type="file" name="image" class="form-control" accept="image/*" required></div>
      <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn-sacred w-100"><i class="fas fa-upload"></i> Upload</button></div>
    </div>
  </form>
</div>

<!-- Gallery Grid -->
<div class="row g-3">
  <?php if($result && $result->num_rows > 0): while($g = $result->fetch_assoc()): ?>
  <div class="col-md-3">
    <div class="sacred-card p-2 text-center">
      <img src="<?php echo SITE_URL; ?>/uploads/gallery/<?php echo $g['image']; ?>" style="width:100%; height:150px; object-fit:cover; border-radius:8px;" alt="">
      <p class="mt-2 mb-1" style="font-size:0.85rem; font-weight:500;"><?php echo htmlspecialchars($g['title'] ?: 'Untitled'); ?></p>
      <a href="?delete=<?php echo $g['id']; ?>" class="btn-maroon" style="padding:0.2rem 0.6rem; font-size:0.75rem;" onclick="return confirm('Delete this image?')"><i class="fas fa-trash"></i> Delete</a>
    </div>
  </div>
  <?php endwhile; else: ?>
  <div class="col-12 text-center py-4"><p class="text-muted">No images in gallery.</p></div>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
