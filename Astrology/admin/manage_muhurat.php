<?php
require_once 'header.php';

$success = $error = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM muhurat WHERE id=$id")) $success = "Muhurat deleted.";
    else $error = "Delete failed: " . $conn->error;
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

<h2 class="mb-4" style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
    <i class="fas fa-calendar-check me-2" style="color:var(--chandan-gold);"></i>Manage Muhurat
</h2>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="manage_muhurat.php"></div>
<?php endif; ?>
<?php if($error): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
<?php endif; ?>

<div class="sacred-card mb-4">
  <h4 style="color:var(--sacred-maroon); border-bottom:1px solid rgba(197,151,59,0.2); padding-bottom:0.8rem; margin-bottom:1.5rem;">
    <i class="fas <?php echo $editData ? 'fa-edit' : 'fa-plus-circle'; ?> me-2" style="color:var(--chandan-gold);"></i><?php echo $editData ? 'Edit' : 'Add New'; ?> Muhurat
  </h4>
  <form method="POST" class="form-sacred">
    <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-5">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Title *</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent" style="border-color:rgba(197,151,59,0.3); border-right:none;"><i class="fas fa-heading" style="color:var(--chandan-gold);"></i></span>
          <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>" placeholder="e.g. Grand Wedding Muhurat">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Date *</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent" style="border-color:rgba(197,151,59,0.3); border-right:none;"><i class="fas fa-calendar-day" style="color:var(--chandan-gold);"></i></span>
          <input type="date" name="muhurat_date" class="form-control" required value="<?php echo $editData['muhurat_date'] ?? ''; ?>">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Start Time *</label>
        <input type="time" name="start_time" class="form-control" required value="<?php echo $editData['start_time'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">End Time *</label>
        <input type="time" name="end_time" class="form-control" required value="<?php echo $editData['end_time'] ?? ''; ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Muhurat Type *</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent" style="border-color:rgba(197,151,59,0.3); border-right:none;"><i class="fas fa-tag" style="color:var(--chandan-gold);"></i></span>
          <select name="type" class="form-select" required>
            <option value="Marriage" <?php echo ($editData['type']??'')==='Marriage'?'selected':''; ?>>Marriage</option>
            <option value="Griha Pravesh" <?php echo ($editData['type']??'')==='Griha Pravesh'?'selected':''; ?>>Griha Pravesh</option>
            <option value="Vastu" <?php echo ($editData['type']??'')==='Vastu'?'selected':''; ?>>Vastu</option>
            <option value="Temple Sthapna" <?php echo ($editData['type']??'')==='Temple Sthapna'?'selected':''; ?>>Temple Sthapna</option>
          </select>
        </div>
      </div>
      <div class="col-md-8">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Description *</label>
        <textarea name="description" class="form-control" rows="2" required placeholder="Enter significance or specific rituals..."><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
      </div>
      <div class="col-12 mt-4 text-end">
        <?php if($editData): ?><a href="manage_muhurat" class="btn-sacred-outline me-2"><i class="fas fa-times me-1"></i> Cancel</a><?php endif; ?>
        <button type="submit" class="btn-sacred"><i class="fas fa-save me-1"></i> <?php echo $editData ? 'Update Muhurat' : 'Save Muhurat'; ?></button>
      </div>
    </div>
  </form>
</div>

<div class="table-responsive table-sacred shadow-sm" style="border-radius:12px; overflow:hidden; border:1px solid rgba(197,151,59,0.2);">
  <table class="table mb-0">
    <thead style="background:var(--sacred-maroon); color:var(--chandan-light);">
      <tr>
        <th style="padding:1rem;">Title</th>
        <th style="padding:1rem;">Date</th>
        <th style="padding:1rem;">Time Window</th>
        <th style="padding:1rem;">Type</th>
        <th style="padding:1rem;" class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr style="vertical-align: middle; border-bottom:1px solid rgba(197,151,59,0.1);">
        <td style="padding:1rem;">
          <div class="fw-bold" style="color:var(--sacred-maroon);"><?php echo htmlspecialchars($r['title']); ?></div>
          <div style="font-size:0.75rem; color:var(--text-secondary);"><?php echo mb_substr(htmlspecialchars($r['description']), 0, 60) . '...'; ?></div>
        </td>
        <td style="padding:1rem; white-space:nowrap;">
          <i class="far fa-calendar-alt me-1 text-muted"></i> <?php echo date('d M Y', strtotime($r['muhurat_date'])); ?>
        </td>
        <td style="padding:1rem; white-space:nowrap;">
          <span style="color:var(--chandan-gold); font-weight:600;">
            <?php echo ($r['start_time'] && $r['start_time'] !== '00:00:00' ? date('h:i A', strtotime($r['start_time'])) : '--:--') . 
                       ' to ' . 
                       ($r['end_time'] && $r['end_time'] !== '00:00:00' ? date('h:i A', strtotime($r['end_time'])) : '--:--'); ?>
          </span>
        </td>
        <td style="padding:1rem;">
          <span class="badge badge-muhurat badge-<?php echo strtolower(str_replace(' ','',$r['type'])); ?>">
            <?php echo $r['type']; ?>
          </span>
        </td>
        <td style="padding:1rem;" class="text-end">
          <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.3rem 0.6rem; font-size:0.75rem;"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.3rem 0.6rem; font-size:0.75rem;" onclick="return confirm('Delete this Muhurat permanently?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-folder-open d-block mb-3" style="font-size:2rem; opacity:0.3;"></i>No muhurat records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
