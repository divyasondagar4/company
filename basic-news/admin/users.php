<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");

if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    mysqli_query($conn,"
    UPDATE users SET status=IF(status=1,0,1) WHERE id='$id'
    ");
    header("Location: users.php");
    exit;
}

include("header.php");
include("sidebar.php");

$q=mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<div class="content">
<div class="container-fluid">

<h4 class="mb-4">Users</h4>

<div class="card shadow-sm">
<div class="card-body">

<table class="table table-bordered table-hover">
<tr>
<th>Name</th>
<th>Email</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($u=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?= $u['name'] ?></td>
<td><?= $u['email'] ?></td>
<td>
<span class="badge bg-<?= $u['status']? 'success':'danger' ?>">
<?= $u['status']?'Active':'Blocked' ?>
</span>
</td>
<td>
<a href="?toggle=<?= $u['id'] ?>"
class="btn btn-warning btn-sm">Toggle</a>
</td>
</tr>
<?php } ?>

</table>

</div>
</div>

</div>
</div>

<?php include("footer.php"); ?>