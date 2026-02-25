<?php
include "db.php";
include "auth.php";
include "header.php";

$r = mysqli_query($conn,"SELECT * FROM videos WHERE is_reel=1 AND status=1");
?>

<div class="container my-4">
<h4>Reels</h4>
<?php while($re=mysqli_fetch_assoc($r)){ ?>
<div class="card mb-3">
<div class="card-body">
<h6><?= $re['title'] ?></h6>
<a href="<?= $re['video_url'] ?>" target="_blank">Play Reel</a>
</div>
</div>
<?php } ?>
</div>

<?php include "footer.php"; ?>