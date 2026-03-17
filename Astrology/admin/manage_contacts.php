<?php
require_once 'header.php';

// Handle Actions
if (isset($_GET['action'])) {
    $id = intval($_GET['id'] ?? 0);
    if ($id > 0) {
        if ($_GET['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['toast_message'] = "Message deleted successfully.";
                $_SESSION['toast_type'] = "success";
            } else {
                $_SESSION['toast_message'] = "Delete failed: " . $conn->error;
                $_SESSION['toast_type'] = "error";
            }
            $_SESSION['toast_redirect'] = SITE_URL . "/admin/manage_contacts";
            header("Location: " . SITE_URL . "/admin/manage_contacts");
            exit();
        } elseif ($_GET['action'] === 'read') {
            $status = intval($_GET['status'] ?? 1);
            $stmt = $conn->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
            $stmt->bind_param("ii", $status, $id);
            if ($stmt->execute()) {
                $_SESSION['toast_message'] = "Message status updated.";
                $_SESSION['toast_type'] = "success";
            } else {
                $_SESSION['toast_message'] = "Update failed: " . $conn->error;
                $_SESSION['toast_type'] = "error";
            }
            $_SESSION['toast_redirect'] = SITE_URL . "/admin/manage_contacts";
            header("Location: " . SITE_URL . "/admin/manage_contacts");
            exit();
        }
    }
}
$result = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
?>

<h2 class="mb-4" style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
    <i class="fas fa-envelope me-2" style="color:var(--chandan-gold);"></i>Manage Messages
</h2>








<div class="table-responsive table-sacred shadow-lg mt-4" style="border-radius:12px; overflow:hidden; border:1px solid rgba(197,151,59,0.2);">
  <table class="table mb-0">
    <thead style="background:var(--sacred-maroon); color:var(--chandan-light);">
      <tr>
        <th style="padding:1rem; width:140px;">Received On</th>
        <th style="padding:1rem;">Sender Details</th>
        <th style="padding:1rem;">Message Content</th>
        <th style="padding:1rem; width:100px;">Status</th>
        <th style="padding:1rem;" class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr style="vertical-align: top; border-bottom:1px solid rgba(197,151,59,0.1); <?php echo $r['is_read'] ? 'opacity:0.8;' : 'background:rgba(197,151,59,0.03);'; ?>">
        <td style="padding:1rem; white-space:nowrap;">
          <div style="font-weight:bold; color:var(--sacred-maroon); font-size:0.85rem;"><?php echo date('d M Y', strtotime($r['created_at'])); ?></div>
          <div class="text-muted" style="font-size:0.75rem;"><?php echo date('h:i A', strtotime($r['created_at'])); ?></div>
        </td>
        <td style="padding:1rem;">
          <div class="fw-bold" style="color:var(--sacred-maroon);"><?php echo htmlspecialchars($r['name']); ?></div>
          <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:2px;"><i class="fas fa-envelope me-1 opacity-50"></i><?php echo htmlspecialchars($r['email']); ?></div>
          <div style="font-size:0.8rem; color:var(--text-secondary);"><i class="fas fa-phone me-1 opacity-50"></i><?php echo htmlspecialchars($r['phone']); ?></div>
        </td>
        <td style="padding:1rem;">
          <div style="max-width:400px; font-size:0.9rem; line-height:1.5; color:var(--sacred-maroon); font-style: <?php echo $r['is_read'] ? 'normal' : 'italic'; ?>;">
            <?php echo nl2br(htmlspecialchars($r['message'])); ?>
          </div>
        </td>
        <td style="padding:1rem; vertical-align:middle;">
          <?php if($r['is_read']): ?>
            <span style="display:inline-flex; align-items:center; background:#F5F5F5; color:#757575; padding:0.3rem 0.6rem; border-radius:20px; font-size:0.7rem; border:1px solid #E0E0E0;">
              <i class="fas fa-check-double me-1"></i>Read
            </span>
          <?php else: ?>
            <span style="display:inline-flex; align-items:center; background:#FFF9C4; color:#F57F17; padding:0.3rem 0.6rem; border-radius:20px; font-size:0.7rem; border:1px solid #FFF176; font-weight:bold;">
              <i class="fas fa-bolt me-1"></i>New
            </span>
          <?php endif; ?>
        </td>
        <td style="padding:1rem; vertical-align:middle;" class="text-end">
          <div class="btn-group shadow-sm" style="border-radius:8px; overflow:hidden; border:1px solid rgba(197,151,59,0.3);">
            <?php if($r['is_read']): ?>
              <a href="?action=read&id=<?php echo $r['id']; ?>&status=0" class="btn btn-light btn-sm" style="background:#fff; color:var(--text-secondary);" title="Mark as Unread">
                <i class="fas fa-envelope"></i>
              </a>
            <?php else: ?>
              <a href="?action=read&id=<?php echo $r['id']; ?>&status=1" class="btn btn-light btn-sm" style="background:#fff; color:#2E7D32;" title="Mark as Read">
                <i class="fas fa-check"></i>
              </a>
            <?php endif; ?>
            <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn btn-light btn-sm" style="background:#fff; color:var(--sacred-maroon);" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')">
              <i class="fas fa-trash-alt"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-inbox d-block mb-3" style="font-size:2rem; opacity:0.3;"></i>No messages received yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
