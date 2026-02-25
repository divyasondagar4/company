<?php
include "db.php";
include "auth.php";
include "header.php";

$id = $_GET['id'];
$q = mysqli_query($conn,"SELECT * FROM news WHERE category_id='$id' AND status=1");
?>

<div class="container my-4">
<h4>Category News</h4>
<?php while($n=mysqli_fetch_assoc($q)){ ?>
<div class="card mb-2">
<div class="card-body">
<h5>
<a href="news-details.php?slug=<?= $n['slug'] ?>">
<?= $n['title'] ?>
</a>
</h5>
</div>
</div>
<?php } ?>
</div>

<?php include "footer.php"; ?>