<?php session_start(); include 'db.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
<h3>Admin Dashboard</h3>

<table class="table table-bordered table-striped">
<tr class="table-dark">
<th>ID</th>
<th>Employee</th>
<th>Task</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>
<th>Action</th>
</tr>
</tr><a href="login.php">back</a>
<?php
$res=mysqli_query($conn,"SELECT * FROM tasks");
while($row=mysqli_fetch_assoc($res)){
?>
<tr>
<td><?= $row['id']; ?></td>
<td><?= $row['employee_name']; ?></td>
<td><?= $row['task']; ?></td>
<td><?= $row['task_date']; ?></td>
<td><?= $row['task_time']; ?></td>
<td><?= $row['task_status']; ?></td>
<td>
<a href="edit_task.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="delete_task.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>

<?php } ?>


</table>
</div>
