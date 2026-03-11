<?php
$adminTitle = 'Manage Festivals';
require_once 'header.php';

$success = $error = '';

if (isset($_GET['delete'])) {
    if ($conn->query("DELETE FROM festivals WHERE id=" . (int)$_GET['delete'])) $success = "Festival deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = $conn->real_escape_string($_POST['festival_name'] ?? '');
    $date = $conn->real_escape_string($_POST['festival_date'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');

    if ($id > 0) {
        if ($conn->query("UPDATE festivals SET festival_name='$name', festival_date='$date', description='$desc' WHERE id=$id")) $success = "Festival updated.";
    } else {
        if ($conn->query("INSERT INTO festivals (festival_name, festival_date, description) VALUES ('$name','$date','$desc')")) $success = "Festival added.";
    }
}

$result = $conn->query("SELECT * FROM festivals ORDER BY festival_date ASC");
$editData = isset($_GET['edit']) ? $conn->query("SELECT * FROM festivals WHERE id=" . (int)$_GET['edit'])->fetch_assoc() : null;
?>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-calendar-days me-2" style="color:var(--chandan-gold);"></i>Manage Festivals</h2>
  <span class="text-muted"><?php echo $result ? $result->num_rows : 0; ?> festivals</span>
</div>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success"></div>
<?php endif; ?>

<div class="sacred-card mb-4" style="height:auto;">
  <h4><?php echo $editData ? 'Edit' : 'Add New'; ?> Festival</h4>
  <form method="POST" class="form-sacred mt-3">
    <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
    <div class="row g-3">
      <div class="col-md-4"><label>Festival Name *</label><input type="text" name="festival_name" class="form-control" required value="<?php echo htmlspecialchars($editData['festival_name'] ?? ''); ?>"></div>
      <div class="col-md-3"><label>Date *</label><input type="date" name="festival_date" class="form-control" required value="<?php echo $editData['festival_date'] ?? ''; ?>"></div>
      <div class="col-md-5"><label>Description *</label><textarea name="description" class="form-control" rows="1" required><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea></div>
      <div class="col-12">
        <button type="submit" class="btn-sacred"><i class="fas fa-save"></i> <?php echo $editData ? 'Update' : 'Add'; ?></button>
        <?php if($editData): ?><a href="manage_festivals.php" class="btn-sacred-outline ms-2">Cancel</a><?php endif; ?>
      </div>
    </div>
  </form>
</div>

<div class="table-responsive table-sacred">
  <table class="table mb-0" style="min-width:700px; table-layout:fixed;">
    <thead>
      <tr>
        <th style="width:40px;">#</th>
        <th style="width:180px;">Festival Name</th>
        <th style="width:120px;">Date</th>
        <th>Description</th>
        <th style="width:120px;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): $i=1; while($r = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><strong><?php echo htmlspecialchars($r['festival_name']); ?></strong></td>
        <td style="white-space:nowrap;"><?php echo date('d M Y', strtotime($r['festival_date'])); ?></td>
        <td style="max-width:350px; overflow:hidden; text-overflow:ellipsis; word-break:break-word; white-space:normal;"><?php echo mb_substr(htmlspecialchars($r['description']), 0, 120); ?></td>
        <td style="white-space:nowrap;">
          <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.3rem 0.8rem; font-size:0.8rem;"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.3rem 0.8rem; font-size:0.8rem;" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; else: ?><tr><td colspan="5" class="text-center py-4">No festivals.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
