<?php
// ============================================================
//  admin/epaper_add.php  — Upload New E-Paper
//  PLACE AT: basic-news/admin/epaper_add.php
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }

include "db.php";

// Make upload directory
if (!is_dir("uploads/epapers")) mkdir("uploads/epapers", 0755, true);

$errors  = [];
$success = false;

if (isset($_POST['save'])) {
    $title  = mysqli_real_escape_string($conn, trim($_POST['title']));
    $city   = mysqli_real_escape_string($conn, trim($_POST['city']));
    $date   = mysqli_real_escape_string($conn, $_POST['edition_date']);
    $status = (int)$_POST['status'];

    if (empty($title))         $errors[] = "Title is required.";
    if (empty($date))          $errors[] = "Edition date is required.";
    if (empty($_FILES['pdf_file']['name'])) $errors[] = "PDF file is required.";

    if (empty($errors)) {
        // ---- Upload PDF ----
        $pdfName = '';
        $pdf     = $_FILES['pdf_file'];
        $ext     = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $errors[] = "Only PDF files are allowed.";
        } elseif ($pdf['size'] > 30 * 1024 * 1024) {
            $errors[] = "PDF size must be under 30 MB.";
        } else {
            $pdfName = time() . '_' . preg_replace('/[^a-z0-9._-]/i', '_', $pdf['name']);
            move_uploaded_file($pdf['tmp_name'], "uploads/epapers/" . $pdfName);
        }

        // ---- Upload Cover Image (optional) ----
        $coverName = '';
        if (!empty($_FILES['cover_image']['name'])) {
            $cov   = $_FILES['cover_image'];
            $cExt  = strtolower(pathinfo($cov['name'], PATHINFO_EXTENSION));
            $allowedImg = ['jpg','jpeg','png','webp'];
            if (!in_array($cExt, $allowedImg)) {
                $errors[] = "Cover image must be JPG, PNG, or WebP.";
            } elseif ($cov['size'] > 5 * 1024 * 1024) {
                $errors[] = "Cover image must be under 5 MB.";
            } else {
                $coverName = time() . '_cover_' . preg_replace('/[^a-z0-9._-]/i', '_', $cov['name']);
                move_uploaded_file($cov['tmp_name'], "uploads/epapers/" . $coverName);
            }
        }

        if (empty($errors)) {
            mysqli_query($conn, "
                INSERT INTO epapers(title, city, edition_date, pdf_file, cover_image, status, created_at)
                VALUES('$title','$city','$date','$pdfName','$coverName','$status',NOW())
            ");
            header("Location: epapers.php?added=1"); exit;
        }
    }
}

include "header.php";
include "sidebar.php";
?>
<style>
.content{margin-left:240px;padding:30px;}
.form-label{font-weight:600;font-size:13px;}
.form-control:focus,.form-select:focus{border-color:#E8520A;box-shadow:0 0 0 3px rgba(232,82,10,.12);}
.upload-zone{border:2px dashed #ddd;border-radius:8px;padding:28px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;}
.upload-zone:hover{border-color:#E8520A;background:#FFF3EC;}
.upload-zone .icon{font-size:36px;margin-bottom:8px;}
.upload-zone p{font-size:13px;color:#888;margin:0;}
.upload-zone input[type=file]{display:none;}
.preview-wrap{margin-top:12px;}
.preview-wrap img{max-height:200px;border-radius:6px;border:1px solid #ddd;}
.pdf-name-preview{background:#f0f0f0;border-radius:6px;padding:10px 14px;font-size:13px;color:#555;margin-top:10px;display:none;}
.pdf-name-preview span{font-weight:700;color:#E8520A;}
.btn-save{background:#E8520A;color:#fff;border:none;padding:11px 32px;border-radius:6px;font-size:15px;font-weight:700;cursor:pointer;transition:background .2s;}
.btn-save:hover{background:#C43D00;}
</style>

<div class="content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📰 Add New E-Paper Edition</h4>
    <a href="epapers.php" class="btn btn-secondary btn-sm">← Back</a>
  </div>

  <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">

          <!-- Title -->
          <div class="col-md-8">
            <label class="form-label">Edition Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control"
              placeholder="e.g. Ahmedabad Edition – 25 Feb 2026"
              value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>" required>
          </div>

          <!-- City -->
          <div class="col-md-4">
            <label class="form-label">City / Edition</label>
            <select name="city" class="form-select">
              <option value="">-- Select City --</option>
              <?php
              $cities = ['Ahmedabad','Surat','Vadodara','Rajkot','Gandhinagar','Bhavnagar','Junagadh'];
              foreach($cities as $ci):
                $sel = (isset($_POST['city']) && $_POST['city']===$ci) ? 'selected' : '';
              ?>
              <option value="<?= $ci ?>" <?= $sel ?>><?= $ci ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Edition Date -->
          <div class="col-md-4">
            <label class="form-label">Edition Date <span class="text-danger">*</span></label>
            <input type="date" name="edition_date" class="form-control"
              value="<?= isset($_POST['edition_date']) ? $_POST['edition_date'] : date('Y-m-d') ?>" required>
          </div>

          <!-- Status -->
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="1" <?= (!isset($_POST['status']) || $_POST['status']=='1') ? 'selected' : '' ?>>Active (Visible)</option>
              <option value="0" <?= (isset($_POST['status']) && $_POST['status']=='0') ? 'selected' : '' ?>>Hidden</option>
            </select>
          </div>

          <!-- PDF Upload -->
          <div class="col-md-6">
            <label class="form-label">PDF File <span class="text-danger">*</span></label>
            <label class="upload-zone" id="pdfZone">
              <div class="icon">📄</div>
              <p><strong style="color:#E8520A">Click to upload PDF</strong> or drag &amp; drop</p>
              <p style="margin-top:4px;font-size:11px">Max size: 30 MB &nbsp;|&nbsp; Format: .pdf only</p>
              <input type="file" name="pdf_file" id="pdfInput" accept=".pdf" required onchange="showPdfName(this)">
            </label>
            <div class="pdf-name-preview" id="pdfNamePreview">
              Selected: <span id="pdfNameText"></span>
            </div>
          </div>

          <!-- Cover Image Upload -->
          <div class="col-md-6">
            <label class="form-label">Cover Image <small class="text-muted">(optional — thumbnail for E-Paper card)</small></label>
            <label class="upload-zone" id="coverZone">
              <div class="icon">🖼️</div>
              <p><strong style="color:#E8520A">Click to upload cover</strong></p>
              <p style="margin-top:4px;font-size:11px">JPG / PNG / WebP &nbsp;|&nbsp; Max 5 MB</p>
              <input type="file" name="cover_image" id="coverInput" accept="image/*" onchange="previewCover(this)">
            </label>
            <div class="preview-wrap" id="coverPreviewWrap" style="display:none">
              <img id="coverPreviewImg" src="" alt="Cover preview">
            </div>
          </div>

          <!-- Submit -->
          <div class="col-12">
            <button type="submit" name="save" class="btn-save">
              📤 Upload E-Paper
            </button>
            <a href="epapers.php" class="btn btn-outline-secondary ms-2">Cancel</a>
          </div>

        </div>
      </form>
    </div>
  </div>

  <!-- Instructions -->
  <div class="card mt-3 border-0" style="background:#FFF8E1;">
    <div class="card-body">
      <h6 style="color:#E65100;margin-bottom:8px">📌 Instructions</h6>
      <ul style="font-size:13px;color:#666;margin:0;padding-left:18px;">
        <li>Upload the newspaper in <strong>PDF format</strong> only.</li>
        <li>The <strong>cover image</strong> is displayed as a thumbnail on the E-Paper page. If not uploaded, a placeholder icon will show.</li>
        <li>Make sure the <code>admin/uploads/epapers/</code> folder exists and has <strong>write permission</strong> (chmod 755).</li>
        <li>Only users with an <strong>active premium subscription</strong> can access E-Papers on the user side.</li>
      </ul>
    </div>
  </div>
</div>

<script>
function showPdfName(input){
  if(input.files[0]){
    document.getElementById('pdfNamePreview').style.display='block';
    document.getElementById('pdfNameText').textContent=input.files[0].name;
    document.getElementById('pdfZone').style.borderColor='#E8520A';
    document.getElementById('pdfZone').style.background='#FFF3EC';
  }
}
function previewCover(input){
  if(input.files[0]){
    const r=new FileReader();
    r.onload=function(e){
      document.getElementById('coverPreviewWrap').style.display='block';
      document.getElementById('coverPreviewImg').src=e.target.result;
    };
    r.readAsDataURL(input.files[0]);
    document.getElementById('coverZone').style.borderColor='#E8520A';
  }
}
// Drag & drop visual feedback
['pdfZone','coverZone'].forEach(function(id){
  const el=document.getElementById(id);
  el.addEventListener('dragover',function(e){e.preventDefault();el.style.borderColor='#E8520A';el.style.background='#FFF3EC';});
  el.addEventListener('dragleave',function(){el.style.borderColor='';el.style.background='';});
});
</script>

<?php include "footer.php"; ?>