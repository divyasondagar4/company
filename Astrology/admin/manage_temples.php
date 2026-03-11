<?php
$adminTitle = 'Manage Temples';
require_once 'header.php';

$success = $error = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM temples WHERE id=$id")) $success = "Temple deleted.";
}

// Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $area = $conn->real_escape_string($_POST['area'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = 'temple_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/gallery/' . $image);
    }

    if ($id > 0) {
        $imgUpdate = $image ? ", image='$image'" : '';
        if ($conn->query("UPDATE temples SET name='$name', location='$location', area='$area', description='$desc' $imgUpdate WHERE id=$id")) $success = "Temple updated.";
    } else {
        if ($conn->query("INSERT INTO temples (name, location, area, description, image) VALUES ('$name','$location','$area','$desc','$image')")) $success = "Temple added.";
    }
}

$result = $conn->query("SELECT * FROM temples ORDER BY name ASC");
$editData = isset($_GET['edit']) ? $conn->query("SELECT * FROM temples WHERE id=" . (int)$_GET['edit'])->fetch_assoc() : null;
?>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-place-of-worship me-2" style="color:var(--chandan-gold);"></i>Manage Temples</h2>
</div>

<?php if($success): ?><div class="alert-sacred alert-success mb-3"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-sacred alert-danger mb-3"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div><?php endif; ?>

<div class="sacred-card mb-4">
  <h4><?php echo $editData ? 'Edit' : 'Add New'; ?> Temple</h4>
  <form method="POST" enctype="multipart/form-data" class="form-sacred mt-3">
    <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
    <div class="row g-3">
      <div class="col-md-3"><label>Name *</label><input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>"></div>
      <div class="col-md-3"><label>Location *</label><input type="text" name="location" class="form-control" required value="<?php echo htmlspecialchars($editData['location'] ?? ''); ?>"></div>
      <div class="col-md-3"><label>Area *</label><input type="text" name="area" class="form-control" required value="<?php echo htmlspecialchars($editData['area'] ?? ''); ?>"></div>
      <div class="col-md-3"><label>Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
      <div class="col-12"><label>Description *</label><textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea></div>
      <div class="col-12">
        <button type="submit" class="btn-sacred"><i class="fas fa-save"></i> <?php echo $editData ? 'Update' : 'Add'; ?></button>
        <?php if($editData): ?><a href="manage_temples.php" class="btn-sacred-outline ms-2">Cancel</a><?php endif; ?>
      </div>
    </div>
  </form>
</div>

<div class="table-responsive table-sacred">
  <table class="table mb-0">
    <thead><tr><th>Image</th><th>Name</th><th>Location</th><th>Area</th><th>Actions</th></tr></thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr>
        <td style="width:60px;">
          <?php if($r['image']): ?><img src="<?php echo SITE_URL; ?>/uploads/gallery/<?php echo $r['image']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:6px;"><?php else: ?><i class="fas fa-place-of-worship" style="color:var(--chandan-gold);"></i><?php endif; ?>
        </td>
        <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
        <td><?php echo htmlspecialchars($r['location']); ?></td>
        <td><?php echo htmlspecialchars($r['area'] ?? ''); ?></td>
        <td>
          <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.3rem 0.8rem; font-size:0.8rem;"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.3rem 0.8rem; font-size:0.8rem;" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; else: ?><tr><td colspan="5" class="text-center">No temples.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
