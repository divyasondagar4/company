<?php
include "db.php";
include "auth.php";
include "header.php";

$v = mysqli_query($conn,"SELECT * FROM videos WHERE is_reel=0 AND status=1");
?>

<div class="container my-4">
<h4>Videos</h4>
<?php while($vd=mysqli_fetch_assoc($v)){ ?>
<div class="card mb-2">
<div class="card-body">
<h5><?= $vd['title'] ?></h5>
<a href="<?= $vd['video_url'] ?>" target="_blank">Watch</a>
</div>
</div>
<?php } ?>
</div>

<?php include "footer.php"; ?>