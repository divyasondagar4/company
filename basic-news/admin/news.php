<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";
include "header.php";
include "sidebar.php";

$where = "";
if (!empty($_GET['level'])) {
    $level = $_GET['level'];
    $where = "WHERE news.news_level='$level'";
}

$q = mysqli_query($conn,"
SELECT news.*, categories.category_name
FROM news
LEFT JOIN categories ON news.category_id = categories.id
$where
ORDER BY news.id DESC
");
?>

<div class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">
<h4>News</h4>
<a href="news_add.php" class="btn btn-primary">Add News</a>
</div>

<form method="get" class="mb-3">
<select name="level" class="form-control w-25 d-inline">
<option value="">All Coverage</option>
<option value="city">City</option>
<option value="state">State</option>
<option value="national">National</option>
<option value="international">International</option>
</select>
<button class="btn btn-dark btn-sm">Filter</button>
</form>

<div class="card">
<div class="card-body">

<table class="table table-bordered table-hover">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Title</th>
<th>Category</th>
<th>Coverage</th>
<th>Status</th>
<th width="150">Action</th>
</tr>
</thead>

<tbody>
<?php while($n=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?= $n['id'] ?></td>
<td><?= htmlspecialchars($n['title']) ?></td>
<td><?= htmlspecialchars($n['category_name']) ?></td>
<td>
<span class="badge bg-info text-dark">
<?= ucfirst($n['news_level']) ?>
</span>
</td>
<td>
<span class="badge bg-<?= $n['status'] ? 'success':'secondary' ?>">
<?= $n['status'] ? 'Published':'Draft' ?>
</span>
</td>
<td>
<a href="news_edit.php?id=<?= $n['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="news_delete.php?id=<?= $n['id'] ?>"
onclick="return confirm('Delete this news?')"
class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

</div>
</div>

<?php include "footer.php"; ?>