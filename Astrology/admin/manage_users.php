<?php
require_once 'header.php';

$result = $conn->query("SELECT u.*, s.plan, s.status as sub_status, s.end_date 
                         FROM users u 
                         LEFT JOIN subscriptions s ON u.id = s.user_id AND s.status = 'active' AND s.end_date >= CURDATE()
                         ORDER BY u.id DESC");
?>

<h2 class="mb-4" style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
    <i class="fas fa-users me-2" style="color:var(--chandan-gold);"></i>Manage Users
</h2>





<div class="table-responsive table-sacred shadow-lg" style="border-radius:12px; overflow:hidden; border:1px solid rgba(197,151,59,0.2);">
  <table class="table mb-0">
    <thead style="background:var(--sacred-maroon); color:var(--chandan-light);">
      <tr>
        <th style="padding:1rem; width:80px;">ID</th>
        <th style="padding:1rem;">Name & Email</th>
        <th style="padding:1rem; width:120px;">Role</th>
        <th style="padding:1rem;">Subscription Status</th>
        <th style="padding:1rem; width:150px;">Registered</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): while($r = $result->fetch_assoc()): ?>
      <tr style="vertical-align: middle; border-bottom:1px solid rgba(197,151,59,0.1);">
        <td style="padding:1rem; font-family:'Cinzel', serif; font-weight:bold; color:var(--chandan-gold);">#<?php echo $r['id']; ?></td>
        <td style="padding:1rem;">
          <div class="fw-bold" style="color:var(--sacred-maroon);"><?php echo htmlspecialchars($r['name']); ?></div>
          <div style="font-size:0.8rem; color:var(--text-secondary);"><i class="far fa-envelope me-1"></i><?php echo htmlspecialchars($r['email']); ?></div>
        </td>
        <td style="padding:1rem;">
          <span class="badge" style="background:<?php echo $r['role']==='admin' ? 'var(--sacred-maroon)' : 'rgba(197,151,59,0.1)'; ?>; 
            color:<?php echo $r['role']==='admin' ? 'var(--chandan-gold)' : 'var(--sacred-maroon)'; ?>; 
            border:1px solid var(--chandan-gold); font-size:0.75rem; padding:0.4rem 0.8rem;">
            <i class="fas <?php echo $r['role']==='admin' ? 'fa-user-shield' : 'fa-user'; ?> me-1"></i>
            <?php echo ucfirst($r['role']); ?>
          </span>
        </td>
        <td style="padding:1rem;">
          <?php if($r['sub_status'] === 'active'): ?>
            <div style="display:inline-flex; align-items:center; background:#E8F5E9; color:#2E7D32; padding:0.4rem 0.8rem; border-radius:30px; font-size:0.8rem; border:1px solid #A5D6A7;">
              <i class="fas fa-crown me-2"></i>
              <span><strong><?php echo ucfirst($r['plan']); ?></strong> — ends <?php echo date('d M Y', strtotime($r['end_date'])); ?></span>
            </div>
          <?php else: ?>
            <span class="text-muted" style="font-size:0.85rem; font-style:italic;"><i class="fas fa-times-circle me-1 opacity-50"></i>No active plan</span>
          <?php endif; ?>
        </td>
        <td style="padding:1rem; white-space:nowrap; font-size:0.85rem; color:var(--text-secondary);">
          <i class="far fa-clock me-1"></i> <?php echo date('d M Y', strtotime($r['created_at'])); ?>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-users-slash d-block mb-3" style="font-size:2rem; opacity:0.3;"></i>No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
