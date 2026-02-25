<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");
include("header.php");
include("sidebar.php");

$q = mysqli_query($conn,"
SELECT videos.*, categories.category_name 
FROM videos 
LEFT JOIN categories ON videos.category_id = categories.id
ORDER BY videos.id DESC
");
?>

<div class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Videos / Reels</h4>
    <a href="videos_add.php" class="btn btn-primary">
        Add Video
    </a>
</div>

<div class="card shadow-sm">
<div class="card-body">

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
<th width="60">ID</th>
<th>Title</th>
<th>Category</th>
<th width="120">Type</th>
<th width="120">Action</th>
</tr>
</thead>

<tbody>
<?php while($v=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?= $v['id'] ?></td>

<td>
    <?= htmlspecialchars($v['title']) ?>
</td>

<td>
    <?= htmlspecialchars($v['category_name']) ?>
</td>

<td>
    <?php if($v['is_reel']){ ?>
        <span class="badge bg-warning text-dark">Reel</span>
    <?php } else { ?>
        <span class="badge bg-primary">Video</span>
    <?php } ?>
</td>

<td>
    <a href="video_delete.php?id=<?= $v['id'] ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Are you sure you want to delete this video?')">
       Delete
    </a>
</td>

</tr>
<?php } ?>
</tbody>

</table>

</div>
</div>

</div>
</div>

<?php include("footer.php"); ?>