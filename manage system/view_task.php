<?php
session_start();
include 'db.php';

if($_SESSION['role'] != "manager"){
    die("Access Denied");
}

$msg = "";

$res = mysqli_query($conn,"SELECT * FROM tasks");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3 class="mb-4">All Tasks</h3>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo $_GET['msg']; ?></div>
<?php endif; ?>

<table class="table table-bordered table-striped">
<tr class="table-dark">
    <th>ID</th>
    <th>Employee Name</th>
    <th>Task</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($res)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['employee_name']; ?></td>
    <td><?php echo $row['task']; ?></td>
    <td><?php echo $row['task_date']; ?></td>
    <td><?php echo $row['task_time']; ?></td>
    <td><?php echo $row['task_status']; ?></td>
    <td>
        <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete this task?')">Delete</a>
    </td>
</tr>
<?php } ?>
</table>

<a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
