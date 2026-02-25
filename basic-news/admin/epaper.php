<?php
// ============================================================
//  admin/epapers.php  — E-Paper Management List
//  PLACE AT: basic-news/admin/epapers.php
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }

include "db.php";

// ---- Auto-create epapers table if not exists ----
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS `epapers` (
  `id`           INT(11) NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(200) DEFAULT NULL,
  `city`         VARCHAR(100) DEFAULT NULL,
  `edition_date` DATE DEFAULT NULL,
  `pdf_file`     VARCHAR(255) DEFAULT NULL,
  `cover_image`  VARCHAR(255) DEFAULT NULL,
  `status`       TINYINT(4) DEFAULT 1,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ---- Delete ----
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $row   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM epapers WHERE id=$delId"));
    if ($row) {
        if (!empty($row['pdf_file'])    && file_exists("uploads/epapers/".$row['pdf_file']))    unlink("uploads/epapers/".$row['pdf_file']);
        if (!empty($row['cover_image']) && file_exists("uploads/epapers/".$row['cover_image'])) unlink("uploads/epapers/".$row['cover_image']);
        mysqli_query($conn, "DELETE FROM epapers WHERE id=$delId");
    }
    header("Location: epapers.php"); exit;
}

// ---- Toggle status ----
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE epapers SET status=IF(status=1,0,1) WHERE id=$tid");
    header("Location: epapers.php"); exit;
}

$list = [];
$q = mysqli_query($conn, "SELECT * FROM epapers ORDER BY edition_date DESC, id DESC");
while ($r = mysqli_fetch_assoc($q)) $list[] = $r;

include "header.php";
include "sidebar.php";
?>
<style>
.content{margin-left:240px;padding:30px;}
.ep-cover{width:55px;height:70px;object-fit:cover;border-radius:3px;border:1px solid #ddd;}
.ep-cover-placeholder{width:55px;height:70px;background:#f0f0f0;border-radius:3px;display:flex;align-items:center;justify-content:center;font-size:22px;border:1px dashed #ddd;}
.status-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:10px;}
.badge-on{background:#d4edda;color:#155724;}
.badge-off{background:#f8d7da;color:#721c24;}
</style>

<div class="content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📰 E-Paper Management</h4>
    <a href="epaper_add.php" class="btn btn-primary btn-sm">+ Add E-Paper</a>
  </div>

  <?php if(empty($list)): ?>
  <div class="alert alert-info">No e-papers uploaded yet. <a href="epaper_add.php">Add the first one →</a></div>
  <?php else: ?>
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th width="70">Cover</th>
            <th>Title</th>
            <th>City / Edition</th>
            <th>Date</th>
            <th>PDF</th>
            <th>Status</th>
            <th width="160">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($list as $ep): ?>
          <tr>
            <td>
              <?php if(!empty($ep['cover_image']) && file_exists("uploads/epapers/".$ep['cover_image'])): ?>
                <img class="ep-cover" src="uploads/epapers/<?= $ep['cover_image'] ?>" alt="">
              <?php else: ?>
                <div class="ep-cover-placeholder">📄</div>
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($ep['title']) ?></strong></td>
            <td><?= htmlspecialchars($ep['city'] ?? '—') ?></td>
            <td><?= date('d M Y', strtotime($ep['edition_date'])) ?></td>
            <td>
              <?php if(!empty($ep['pdf_file'])): ?>
                <a href="uploads/epapers/<?= $ep['pdf_file'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">View PDF</a>
              <?php else: ?>
                <span class="text-muted small">No PDF</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="status-badge <?= $ep['status'] ? 'badge-on' : 'badge-off' ?>">
                <?= $ep['status'] ? 'Active' : 'Hidden' ?>
              </span>
            </td>
            <td>
              <a href="epaper_edit.php?id=<?= $ep['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="?toggle=<?= $ep['id'] ?>" class="btn btn-secondary btn-sm"><?= $ep['status'] ? 'Hide' : 'Show' ?></a>
              <a href="?delete=<?= $ep['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this e-paper?')">Del</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include "footer.php"; ?>