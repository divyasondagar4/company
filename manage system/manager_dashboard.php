
<?php
include 'db.php';


$res = mysqli_query($conn,"SELECT * FROM tasks");
?>

<h4 class="mt-3">Manager Panel</h4>

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
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['employee_name']; ?></td>
    <td><?php echo $row['task']; ?></td>
    <td><?php echo $row['task_date']; ?></td>
    <td><?php echo $row['task_time']; ?></td>
    <td><?php echo $row['task_status']; ?></td>
</tr>
<?php } ?>
</table>
