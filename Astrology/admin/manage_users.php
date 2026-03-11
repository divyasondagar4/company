<?php
$adminTitle = 'Manage Users';
require_once 'header.php';

$result = $conn->query("SELECT u.*, s.plan, s.status as sub_status, s.end_date 
                         FROM users u 
                         LEFT JOIN subscriptions s ON u.id = s.user_id AND s.status = 'active' AND s.end_date >= CURDATE()
                         ORDER BY u.id DESC");
?>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-users me-2" style="color:var(--chandan-gold);"></i>Manage Users</h2>
</div>

<div class="table-responsive table-sacred">
  <table class="table mb-0">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Subscription</th><th>Registered</th></tr></thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td>
          <span class="badge" style="background:<?php echo $r['role']==='admin' ? 'var(--sacred-maroon)' : 'var(--chandan-gold)'; ?>; color:<?php echo $r['role']==='admin' ? 'var(--chandan-gold)' : 'var(--sacred-maroon)'; ?>;">
            <?php echo ucfirst($r['role']); ?>
          </span>
        </td>
        <td>
          <?php if($r['sub_status'] === 'active'): ?>
            <span class="badge" style="background:#E8F5E9; color:#2E7D32;">
              Active (<?php echo ucfirst($r['plan']); ?>) — until <?php echo date('d M Y', strtotime($r['end_date'])); ?>
            </span>
          <?php else: ?>
            <span class="text-muted">None</span>
          <?php endif; ?>
        </td>
        <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
      </tr>
      <?php endwhile; else: ?><tr><td colspan="6" class="text-center">No users.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
