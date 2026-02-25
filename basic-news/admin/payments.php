<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");
include("header.php");
include("sidebar.php");

$q=mysqli_query($conn,"
SELECT payments.*, users.name 
FROM payments 
LEFT JOIN users ON payments.user_id=users.id
ORDER BY payments.id DESC
");
?>

<div class="content">
<div class="container-fluid">

<h4 class="mb-4">Payments</h4>

<div class="card shadow-sm">
<div class="card-body">

<table class="table table-bordered table-hover">
<thead class="table-light">
<tr>
<th>User</th>
<th>Amount</th>
<th>Method</th>
<th>Status</th>
</tr>
</thead>

<tbody>
<?php while($p=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?= htmlspecialchars($p['name']) ?></td>
<td>₹ <?= $p['amount'] ?></td>
<td><?= $p['payment_method'] ?></td>
<td>
<span class="badge bg-<?= $p['payment_status']=='Success'?'success':'warning' ?>">
<?= $p['payment_status'] ?>
</span>
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