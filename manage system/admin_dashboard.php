<?php
include 'db.php';

$res = mysqli_query($conn,"SELECT * FROM tasks");
?>

<h4 class="mt-3">Admin Task Management</h4>

<a href="add_task.php" class="btn btn-primary mb-3">Add Task</a>

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

<?php while($row=mysqli_fetch_assoc($res)) { ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><?= $row['employee_name']; ?></td>
    <td><?= $row['task']; ?></td>
    <td><?= $row['task_date']; ?></td>
    <td><?= $row['task_time']; ?></td>
    <td><?= $row['task_status']; ?></td>
    <td>
        <a href="edit_user.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="delete_user.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
           onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php } ?>
</table>
