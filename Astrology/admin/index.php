<?php
$adminTitle = 'Dashboard';
require_once 'header.php';

// Statistics
$stats = [];
$tables = [
    'panchang' => ['label' => 'Panchang Entries', 'icon' => 'fa-sun', 'color' => '#F4A83D'],
    'muhurat' => ['label' => 'Muhurat Dates', 'icon' => 'fa-calendar-check', 'color' => '#C5973B'],
    'users' => ['label' => 'Registered Users', 'icon' => 'fa-users', 'color' => '#5B1A18'],
    'subscriptions' => ['label' => 'Active Subscriptions', 'icon' => 'fa-crown', 'color' => '#D4A017'],
    'temples' => ['label' => 'Temples', 'icon' => 'fa-place-of-worship', 'color' => '#A67C2E'],
    'festivals' => ['label' => 'Festivals', 'icon' => 'fa-calendar-days', 'color' => '#7B2D2A'],
    'gallery' => ['label' => 'Gallery Images', 'icon' => 'fa-images', 'color' => '#4A3728'],
    'contact_messages' => ['label' => 'Messages', 'icon' => 'fa-envelope', 'color' => '#C0392B'],
];

foreach ($tables as $table => $info) {
    $where = $table === 'subscriptions' ? "WHERE status='active'" : '';
    $r = $conn->query("SELECT COUNT(*) as c FROM $table $where");
    $stats[$table] = $r ? $r->fetch_assoc()['c'] : 0;
}

// Recent contacts
$recentContacts = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
?>

<div class="admin-header">
  <div>
    <h2 style="margin:0;">Dashboard</h2>
    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
  </div>
  <div>
    <span class="text-muted"><?php echo date('l, d F Y'); ?></span>
  </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
  <?php foreach($tables as $table => $info): ?>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-number"><?php echo $stats[$table]; ?></div>
          <div class="stat-label"><?php echo $info['label']; ?></div>
        </div>
        <div class="stat-icon" style="background:<?php echo $info['color']; ?>20; color:<?php echo $info['color']; ?>;">
          <i class="fas <?php echo $info['icon']; ?>"></i>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
  <div class="col-md-6">
    <div class="sacred-card">
      <h4><i class="fas fa-bolt me-2" style="color:var(--chandan-gold);"></i>Quick Actions</h4>
      <div class="d-flex flex-wrap gap-2 mt-3">
        <a href="upload_excel.php" class="btn-sacred"><i class="fas fa-file-excel"></i> Upload Excel</a>
        <a href="manage_panchang.php" class="btn-sacred-outline"><i class="fas fa-plus"></i> Add Panchang</a>
        <a href="manage_muhurat.php" class="btn-sacred-outline"><i class="fas fa-plus"></i> Add Muhurat</a>
        <a href="manage_gallery.php" class="btn-sacred-outline"><i class="fas fa-upload"></i> Upload Image</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="sacred-card">
      <h4><i class="fas fa-envelope me-2" style="color:var(--chandan-gold);"></i>Recent Messages</h4>
      <?php if($recentContacts && $recentContacts->num_rows > 0): ?>
      <div class="mt-3">
        <?php while($c = $recentContacts->fetch_assoc()): ?>
        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--light-sand);">
          <div>
            <strong style="font-size:0.9rem;"><?php echo htmlspecialchars($c['name']); ?></strong>
            <p class="mb-0 text-muted" style="font-size:0.8rem;"><?php echo mb_substr(htmlspecialchars($c['message']), 0, 50); ?>...</p>
          </div>
          <small class="text-muted"><?php echo date('d M', strtotime($c['created_at'])); ?></small>
        </div>
        <?php endwhile; ?>
      </div>
      <?php else: ?>
      <p class="text-muted mt-2">No messages yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>
