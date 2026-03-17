<?php
require_once 'header.php';

// Statistics
$stats = [];
$tables = [
    'panchang' => ['label' => 'Panchang Entries', 'icon' => 'fa-sun', 'color' => '#F4A83D'],
    'muhurat' => ['label' => 'Muhurat Dates', 'icon' => 'fa-calendar-check', 'color' => '#C5973B'],
    'users' => ['label' => 'Registered Users', 'icon' => 'fa-users', 'color' => '#5B1A18'],
    'subscriptions' => ['label' => 'Active Subscriptions', 'icon' => 'fa-crown', 'color' => '#D4A017'],
    'locations' => ['label' => 'Active Locations', 'icon' => 'fa-map-marker-alt', 'color' => '#A67C2E'],
    'festivals' => ['label' => 'Festivals', 'icon' => 'fa-calendar-days', 'color' => '#7B2D2A'],
    'gallery' => ['label' => 'Gallery Images', 'icon' => 'fa-images', 'color' => '#4A3728'],
    'contact_messages' => ['label' => 'Messages', 'icon' => 'fa-envelope', 'color' => '#C0392B'],
];

foreach ($tables as $table => $info) {
    if ($table === 'locations') {
        $r = $conn->query("SELECT COUNT(DISTINCT location) as c FROM panchang WHERE location IS NOT NULL AND location != ''");
    } else {
        $where = $table === 'subscriptions' ? "WHERE status='active'" : '';
        $r = $conn->query("SELECT COUNT(*) as c FROM $table $where");
    }
    $stats[$table] = $r ? $r->fetch_assoc()['c'] : 0;
}

// Recent contacts
$recentContacts = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
?>

<h2 class="mb-4" style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
    <i class="fas fa-chart-line me-2" style="color:var(--chandan-gold);"></i>Dashboard
</h2>





<!-- Stats -->
<div class="row g-4 mb-4">
  <?php foreach($tables as $table => $info): ?>
  <div class="col-md-3">
    <div class="stat-card shadow-sm" style="border-radius:15px; border-left: 4px solid <?php echo $info['color']; ?>; transition: transform 0.2s ease;">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-number" style="font-family:'Cinzel', serif; font-size:1.8rem; color:var(--sacred-maroon);"><?php echo number_format($stats[$table]); ?></div>
          <div class="stat-label" style="text-transform:uppercase; font-size:0.7rem; letter-spacing:0.5px; font-weight:bold; color:var(--text-secondary);"><?php echo $info['label']; ?></div>
        </div>
        <div class="stat-icon shadow-sm" style="background:<?php echo $info['color']; ?>15; color:<?php echo $info['color']; ?>; width:50px; height:50px; display:flex; align-items:center; justify-content:center; border-radius:12px; font-size:1.2rem;">
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
        <a href="upload_excel" class="btn-sacred"><i class="fas fa-file-excel"></i> Upload Excel</a>
        <a href="manage_panchang" class="btn-sacred-outline"><i class="fas fa-plus"></i> Add Panchang</a>
        <a href="manage_muhurat" class="btn-sacred-outline"><i class="fas fa-plus"></i> Add Muhurat</a>
        <a href="manage_gallery" class="btn-sacred-outline"><i class="fas fa-upload"></i> Upload Image</a>
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
