<?php
// ============================================================
//  admin/epaper_edit.php  — Edit E-Paper Edition
//  PLACE AT: basic-news/admin/epaper_edit.php
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }

include "db.php";

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ep  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM epapers WHERE id=$id LIMIT 1"));
if (!$ep) { header("Location: epapers.php"); exit; }

$errors = [];

if (isset($_POST['update'])) {
    $title  = mysqli_real_escape_string($conn, trim($_POST['title']));
    $city   = mysqli_real_escape_string($conn, trim($_POST['city']));
    $date   = mysqli_real_escape_string($conn, $_POST['edition_date']);
    $status = (int)$_POST['status'];

    if (empty($title)) $errors[] = "Title is required.";
    if (empty($date))  $errors[] = "Edition date is required.";

    if (empty($errors)) {
        $pdfName   = $ep['pdf_file'];
        $coverName = $ep['cover_image'];

        // New PDF
        if (!empty($_FILES['pdf_file']['name'])) {
            $pdf = $_FILES['pdf_file'];
            $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $errors[] = "Only PDF files allowed.";
            } else {
                if (!empty($pdfName) && file_exists("uploads/epapers/$pdfName")) unlink("uploads/epapers/$pdfName");
                $pdfName = time().'_'.preg_replace('/[^a-z0-9._-]/i','_',$pdf['name']);
                move_uploaded_file($pdf['tmp_name'], "uploads/epapers/$pdfName");
            }
        }

        // New cover
        if (!empty($_FILES['cover_image']['name'])) {
            $cov  = $_FILES['cover_image'];
            $cExt = strtolower(pathinfo($cov['name'], PATHINFO_EXTENSION));
            if (in_array($cExt, ['jpg','jpeg','png','webp'])) {
                if (!empty($coverName) && file_exists("uploads/epapers/$coverName")) unlink("uploads/epapers/$coverName");
                $coverName = time().'_cover_'.preg_replace('/[^a-z0-9._-]/i','_',$cov['name']);
                move_uploaded_file($cov['tmp_name'], "uploads/epapers/$coverName");
            }
        }

        if (empty($errors)) {
            mysqli_query($conn, "
                UPDATE epapers SET
                title='$title', city='$city', edition_date='$date',
                pdf_file='$pdfName', cover_image='$coverName', status='$status'
                WHERE id=$id
            ");
            header("Location: epapers.php?updated=1"); exit;
        }
    }
    $ep = array_merge($ep, $_POST); // repopulate
}

include "header.php";
include "sidebar.php";
?>
<style>
.content{margin-left:240px;padding:30px;}
.form-label{font-weight:600;font-size:13px;}
.form-control:focus,.form-select:focus{border-color:#E8520A;box-shadow:0 0 0 3px rgba(232,82,10,.12);}
.current-file{background:#f9f9f9;border:1px solid #eee;border-radius:6px;padding:10px 14px;font-size:13px;display:flex;align-items:center;gap:10px;}
.current-cover{height:80px;border-radius:4px;border:1px solid #ddd;}
.btn-save{background:#E8520A;color:#fff;border:none;padding:11px 32px;border-radius:6px;font-size:15px;font-weight:700;cursor:pointer;}
.btn-save:hover{background:#C43D00;}
</style>

<div class="content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>✏️ Edit E-Paper Edition</h4>
    <a href="epapers.php" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">

          <div class="col-md-8">
            <label class="form-label">Edition Title *</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($ep['title']) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">City</label>
            <select name="city" class="form-select">
              <option value="">-- Select City --</option>
              <?php foreach(['Ahmedabad','Surat','Vadodara','Rajkot','Gandhinagar','Bhavnagar','Junagadh'] as $ci): ?>
              <option value="<?= $ci ?>" <?= ($ep['city']===$ci)?'selected':'' ?>><?= $ci ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Edition Date *</label>
            <input type="date" name="edition_date" class="form-control" value="<?= $ep['edition_date'] ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="1" <?= $ep['status']=='1'?'selected':'' ?>>Active</option>
              <option value="0" <?= $ep['status']=='0'?'selected':'' ?>>Hidden</option>
            </select>
          </div>

          <!-- Current PDF -->
          <div class="col-md-6">
            <label class="form-label">Replace PDF (leave blank to keep current)</label>
            <?php if(!empty($ep['pdf_file'])): ?>
            <div class="current-file mb-2">
              <span>📄</span>
              <span><?= htmlspecialchars($ep['pdf_file']) ?></span>
              <a href="uploads/epapers/<?= $ep['pdf_file'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary ms-auto">View</a>
            </div>
            <?php endif; ?>
            <input type="file" name="pdf_file" class="form-control" accept=".pdf">
            <div class="form-text">Only upload a new file if you want to replace the current PDF</div>
          </div>

          <!-- Current Cover -->
          <div class="col-md-6">
            <label class="form-label">Replace Cover Image (optional)</label>
            <?php if(!empty($ep['cover_image']) && file_exists("uploads/epapers/".$ep['cover_image'])): ?>
            <div class="mb-2">
              <img class="current-cover" src="uploads/epapers/<?= $ep['cover_image'] ?>" alt="">
            </div>
            <?php endif; ?>
            <input type="file" name="cover_image" class="form-control" accept="image/*">
          </div>

          <div class="col-12">
            <button type="submit" name="update" class="btn-save">💾 Update E-Paper</button>
            <a href="epapers.php" class="btn btn-outline-secondary ms-2">Cancel</a>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>