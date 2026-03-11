<?php
$adminTitle = 'Manage Muhurat';
require_once 'header.php';

$success = $error = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM muhurat WHERE id=$id")) $success = "Muhurat deleted.";
}

// Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $date = $conn->real_escape_string($_POST['muhurat_date'] ?? '');
    $start = $conn->real_escape_string($_POST['start_time'] ?? '');
    $end = $conn->real_escape_string($_POST['end_time'] ?? '');
    $type = $conn->real_escape_string($_POST['type'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');

    if ($id > 0) {
        $sql = "UPDATE muhurat SET title='$title', muhurat_date='$date', start_time=" . ($start?"'$start'":"NULL") . ", end_time=" . ($end?"'$end'":"NULL") . ", type='$type', description='$desc' WHERE id=$id";
        if ($conn->query($sql)) $success = "Muhurat updated.";
        else $error = "Update failed.";
    } else {
        $sql = "INSERT INTO muhurat (title, muhurat_date, start_time, end_time, type, description) VALUES ('$title','$date'," . ($start?"'$start'":"NULL") . "," . ($end?"'$end'":"NULL") . ",'$type','$desc')";
        if ($conn->query($sql)) $success = "Muhurat added.";
        else $error = "Failed: " . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM muhurat ORDER BY muhurat_date DESC");
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $conn->query("SELECT * FROM muhurat WHERE id=" . (int)$_GET['edit'])->fetch_assoc();
}
?>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-calendar-check me-2" style="color:var(--chandan-gold);"></i>Manage Muhurat</h2>
</div>

<?php if($success): ?>
  <div class="alert-sacred alert-success mb-3"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
<?php endif; ?>
<?php if($error): ?>
  <div class="alert-sacred alert-danger mb-3"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
<?php endif; ?>

<div class="sacred-card mb-4">
  <h4><?php echo $editData ? 'Edit' : 'Add New'; ?> Muhurat</h4>
  <form method="POST" class="form-sacred mt-3">
    <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
    <div class="row g-3">
      <div class="col-md-4">
        <label>Title *</label>
        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>">
      </div>
      <div class="col-md-3">
        <label>Date *</label>
        <input type="date" name="muhurat_date" class="form-control" required value="<?php echo $editData['muhurat_date'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Start Time *</label>
        <input type="time" name="start_time" class="form-control" required value="<?php echo $editData['start_time'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>End Time *</label>
        <input type="time" name="end_time" class="form-control" required value="<?php echo $editData['end_time'] ?? ''; ?>">
      </div>
      <div class="col-md-3">
        <label>Type *</label>
        <select name="type" class="form-select" required>
          <option value="Marriage" <?php echo ($editData['type']??'')==='Marriage'?'selected':''; ?>>Marriage</option>
          <option value="Griha Pravesh" <?php echo ($editData['type']??'')==='Griha Pravesh'?'selected':''; ?>>Griha Pravesh</option>
          <option value="Vastu" <?php echo ($editData['type']??'')==='Vastu'?'selected':''; ?>>Vastu</option>
          <option value="Temple Sthapna" <?php echo ($editData['type']??'')==='Temple Sthapna'?'selected':''; ?>>Temple Sthapna</option>
        </select>
      </div>
      <div class="col-md-6">
        <label>Description *</label>
        <textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
      </div>
      <div class="col-12">
        <button type="submit" class="btn-sacred"><i class="fas fa-save"></i> <?php echo $editData ? 'Update' : 'Add'; ?></button>
        <?php if($editData): ?><a href="manage_muhurat.php" class="btn-sacred-outline ms-2">Cancel</a><?php endif; ?>
      </div>
    </div>
  </form>
</div>

<div class="table-responsive table-sacred">
  <table class="table mb-0">
    <thead><tr><th>Title</th><th>Date</th><th>Time</th><th>Type</th><th>Actions</th></tr></thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['title']); ?></td>
        <td><?php echo date('d M Y', strtotime($r['muhurat_date'])); ?></td>
        <td><?php echo ($r['start_time'] ? date('h:i A', strtotime($r['start_time'])) : '') . ($r['end_time'] ? ' - ' . date('h:i A', strtotime($r['end_time'])) : ''); ?></td>
        <td><span class="badge badge-muhurat badge-<?php echo strtolower(str_replace(' ','',$r['type'])); ?>"><?php echo $r['type']; ?></span></td>
        <td>
          <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.3rem 0.8rem; font-size:0.8rem;"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.3rem 0.8rem; font-size:0.8rem;" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="5" class="text-center">No muhurat entries.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
