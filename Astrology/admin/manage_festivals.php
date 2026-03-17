<?php
require_once 'header.php';

$success = $error = '';

if (isset($_GET['delete'])) {
    if ($conn->query("DELETE FROM festivals WHERE id=" . (int)$_GET['delete'])) $success = "Festival deleted.";
    else $error = "Delete failed: " . $conn->error;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = $conn->real_escape_string($_POST['festival_name'] ?? '');
    $date = $conn->real_escape_string($_POST['festival_date'] ?? '');
    $desc = $conn->real_escape_string($_POST['description'] ?? '');

    if ($id > 0) {
        if ($conn->query("UPDATE festivals SET festival_name='$name', festival_date='$date', description='$desc' WHERE id=$id")) $success = "Festival updated.";
        else $error = "Update failed: " . $conn->error;
    } else {
        if ($conn->query("INSERT INTO festivals (festival_name, festival_date, description) VALUES ('$name','$date','$desc')")) $success = "Festival added.";
        else $error = "Add failed: " . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM festivals ORDER BY festival_date ASC");
$editData = isset($_GET['edit']) ? $conn->query("SELECT * FROM festivals WHERE id=" . (int)$_GET['edit'])->fetch_assoc() : null;
?>

<h2 class="mb-4" style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
    <i class="fas fa-calendar-days me-2" style="color:var(--chandan-gold);"></i>Manage Festivals
</h2>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="manage_festivals.php"></div>
<?php endif; ?>
<?php if($error): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
<?php endif; ?>

<div class="sacred-card mb-4">
  <h4 style="color:var(--sacred-maroon); border-bottom:1px solid rgba(197,151,59,0.2); padding-bottom:0.8rem; margin-bottom:1.5rem;">
    <i class="fas <?php echo $editData ? 'fa-edit' : 'fa-plus-circle'; ?> me-2" style="color:var(--chandan-gold);"></i><?php echo $editData ? 'Edit' : 'Add New'; ?> Festival
  </h4>
  <form method="POST" class="form-sacred">
    <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
    <div class="row g-4">
      <div class="col-md-5">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Festival Name *</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent" style="border-color:rgba(197,151,59,0.3); border-right:none;"><i class="fas fa-om" style="color:var(--chandan-gold);"></i></span>
          <input type="text" name="festival_name" class="form-control" required value="<?php echo htmlspecialchars($editData['festival_name'] ?? ''); ?>" placeholder="e.g. Diwali - Festival of Lights">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Festival Date *</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent" style="border-color:rgba(197,151,59,0.3); border-right:none;"><i class="fas fa-calendar-star" style="color:var(--chandan-gold);"></i></span>
          <input type="date" name="festival_date" class="form-control" required value="<?php echo $editData['festival_date'] ?? ''; ?>">
        </div>
      </div>
      <div class="col-md-12">
        <label class="form-label fw-bold" style="color:var(--sacred-maroon); font-size:0.9rem;">Description *</label>
        <textarea name="description" class="form-control" rows="3" required placeholder="Describe the significance, tithi, or celebration methods of this festival..."><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
      </div>
      <div class="col-12 mt-4 text-end">
        <?php if($editData): ?><a href="manage_festivals" class="btn-sacred-outline me-2"><i class="fas fa-times me-1"></i> Cancel</a><?php endif; ?>
        <button type="submit" class="btn-sacred"><i class="fas fa-save me-1"></i> <?php echo $editData ? 'Update Festival' : 'Save Festival'; ?></button>
      </div>
    </div>
  </form>
</div>

<div class="table-responsive table-sacred shadow-sm" style="border-radius:12px; overflow:hidden; border:1px solid rgba(197,151,59,0.2);">
  <table class="table mb-0" style="min-width:700px;">
    <thead style="background:var(--sacred-maroon); color:var(--chandan-light);">
      <tr>
        <th style="padding:1rem; width:180px;">Festival Name</th>
        <th style="padding:1rem; width:140px;">Date</th>
        <th style="padding:1rem;">Description Snippet</th>
        <th style="padding:1rem;" class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr style="vertical-align: middle; border-bottom:1px solid rgba(197,151,59,0.1);">
        <td style="padding:1rem;">
          <div class="fw-bold" style="color:var(--sacred-maroon); font-size:1rem;"><?php echo htmlspecialchars($r['festival_name']); ?></div>
        </td>
        <td style="padding:1rem; white-space:nowrap;">
          <span style="color:var(--chandan-gold); font-weight:600;"><i class="far fa-calendar-check me-1"></i> <?php echo date('d M Y', strtotime($r['festival_date'])); ?></span>
        </td>
        <td style="padding:1rem;">
          <div style="font-size:0.85rem; color:var(--text-secondary); max-width:400px;"><?php echo mb_substr(htmlspecialchars($r['description']), 0, 100) . '...'; ?></div>
        </td>
        <td style="padding:1rem;" class="text-end">
          <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.3rem 0.6rem; font-size:0.75rem;"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.3rem 0.6rem; font-size:0.75rem;" onclick="return confirm('Delete this festival record permanently?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fas fa-star-and-crescent d-block mb-3" style="font-size:2rem; opacity:0.3;"></i>No festival records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
