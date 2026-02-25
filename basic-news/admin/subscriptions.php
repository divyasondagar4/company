<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");
include("header.php");
include("sidebar.php");

$q=mysqli_query($conn,"SELECT * FROM subscriptions ORDER BY id DESC");
?>

<div class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between mb-4">
<h4>Subscription Plans</h4>
<a href="subscription_add.php" class="btn btn-primary">Add Plan</a>
</div>

<div class="card shadow-sm">
<div class="card-body">

<table class="table table-bordered table-hover">
<tr>
<th>Name</th>
<th>Price</th>
<th>Days</th>
<th>Description</th>
</tr>

<?php while($s=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?= $s['plan_name'] ?></td>
<td>₹ <?= $s['price'] ?></td>
<td><?= $s['duration_days'] ?> Days</td>
<td><?= $s['description'] ?></td>
</tr>
<?php } ?>

</table>

</div>
</div>

</div>
</div>

<?php include("footer.php"); ?>