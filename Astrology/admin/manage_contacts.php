<?php
$adminTitle = 'Manage Messages';
require_once 'header.php';

// Handle Actions
if (isset($_GET['action'])) {
    $id = intval($_GET['id'] ?? 0);
    if ($id > 0) {
        if ($_GET['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            header("Location: manage_contacts.php?deleted=1");
            exit();
        } elseif ($_GET['action'] === 'read') {
            $status = intval($_GET['status'] ?? 1);
            $stmt = $conn->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
            $stmt->bind_param("ii", $status, $id);
            $stmt->execute();
            header("Location: manage_contacts.php?updated=1");
            exit();
        }
    }
}

$result = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
?>

<div class="admin-header d-flex justify-content-between align-items-center">
  <h2 style="margin:0;"><i class="fas fa-envelope me-2" style="color:var(--chandan-gold);"></i>Manage Messages</h2>
</div>

<?php if(isset($_GET['deleted'])): ?>
  <div class="alert alert-success mt-3">Message deleted successfully.</div>
<?php endif; ?>
<?php if(isset($_GET['updated'])): ?>
  <div class="alert alert-success mt-3">Message status updated.</div>
<?php endif; ?>

<div class="table-responsive table-sacred mt-3">
  <table class="table mb-0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Date</th>
        <th>Name</th>
        <th>Contact Info</th>
        <th>Message</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr class="<?php echo $r['is_read'] ? 'text-muted' : 'fw-bold'; ?>">
        <td><?php echo $r['id']; ?></td>
        <td><small><?php echo date('d M Y, h:i A', strtotime($r['created_at'])); ?></small></td>
        <td><?php echo htmlspecialchars($r['name']); ?></td>
        <td>
          <div><i class="fas fa-envelope me-1 small"></i> <?php echo htmlspecialchars($r['email']); ?></div>
          <div><i class="fas fa-phone me-1 small"></i> <?php echo htmlspecialchars($r['phone']); ?></div>
        </td>
        <td>
          <div style="max-width:300px; white-space: normal;"><?php echo nl2br(htmlspecialchars($r['message'])); ?></div>
        </td>
        <td>
          <?php if($r['is_read']): ?>
            <span class="badge bg-light text-dark">Read</span>
          <?php else: ?>
            <span class="badge bg-warning text-dark">New</span>
          <?php endif; ?>
        </td>
        <td>
          <div class="btn-group btn-group-sm">
            <?php if($r['is_read']): ?>
              <a href="?action=read&id=<?php echo $r['id']; ?>&status=0" class="btn btn-outline-secondary" title="Mark as Unread">
                <i class="fas fa-envelope"></i>
              </a>
            <?php else: ?>
              <a href="?action=read&id=<?php echo $r['id']; ?>&status=1" class="btn btn-outline-success" title="Mark as Read">
                <i class="fas fa-check"></i>
              </a>
            <?php endif; ?>
            <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="7" class="text-center py-4">No messages found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
