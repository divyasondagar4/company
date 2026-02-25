<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include("db.php");

/* ================= SAVE VIDEO ================= */

if(isset($_POST['save'])){

    $cat       = intval($_POST['category_id']);
    $title     = mysqli_real_escape_string($conn,$_POST['title']);
    $desc      = mysqli_real_escape_string($conn,$_POST['description']);
    $is_reel   = intval($_POST['is_reel']);
    $status    = intval($_POST['status']);
    $admin_id  = $_SESSION['admin_id'];

    /* ---------- VIDEO UPLOAD ---------- */
    $video_name = '';
    if(!empty($_FILES['video']['name'])){
        $video_name = time().'_'.basename($_FILES['video']['name']);
        $target = "uploads/videos/".$video_name;

        move_uploaded_file($_FILES['video']['tmp_name'], $target);
    }

    /* ---------- THUMBNAIL UPLOAD ---------- */
    $thumb_name = '';
    if(!empty($_FILES['thumbnail']['name'])){
        $thumb_name = time().'_thumb_'.basename($_FILES['thumbnail']['name']);
        $target2 = "uploads/videos/".$thumb_name;

        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target2);
    }

    /* ---------- INSERT ---------- */
    mysqli_query($conn,"
        INSERT INTO videos
        (category_id,title,description,video_url,thumbnail,is_reel,status,created_by)
        VALUES
        ('$cat','$title','$desc','$video_name','$thumb_name','$is_reel','$status','$admin_id')
    ");

    header("Location: videos.php");
    exit;
}

/* ================= FETCH CATEGORIES ================= */

$cats = mysqli_query($conn,"SELECT * FROM categories WHERE status=1");

include("header.php");
include("sidebar.php");
?>

<div class="content">
<div class="container-fluid">

<div class="card shadow-sm">
<div class="card-body">

<h4 class="mb-4">Add Video / Reel</h4>

<form method="post" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>Category</label>
<select name="category_id" class="form-control" required>
<option value="">Select Category</option>
<?php while($c=mysqli_fetch_assoc($cats)){ ?>
<option value="<?= $c['id'] ?>">
    <?= htmlspecialchars($c['category_name']) ?>
</option>
<?php } ?>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="col-md-12 mb-3">
<label>Description</label>
<textarea name="description" rows="4" class="form-control"></textarea>
</div>

<div class="col-md-6 mb-3">
<label>Upload Video</label>
<input type="file" name="video" class="form-control" accept="video/*" required>
</div>

<div class="col-md-6 mb-3">
<label>Thumbnail</label>
<input type="file" name="thumbnail" class="form-control" accept="image/*">
</div>

<div class="col-md-4 mb-3">
<label>Type</label>
<select name="is_reel" class="form-control">
<option value="0">Normal Video</option>
<option value="1">Reel</option>
</select>
</div>

<div class="col-md-4 mb-3">
<label>Status</label>
<select name="status" class="form-control">
<option value="1">Publish</option>
<option value="0">Draft</option>
</select>
</div>

</div>

<button type="submit" name="save" class="btn btn-success">
Save Video
</button>

<a href="videos.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>
</div>

</div>
</div>

<?php include("footer.php"); ?>