<?php session_start(); include 'db.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
<h3>Manager Dashboard</h3>

<form method="post" class="border p-3">
Employee Name <input type="text" name="emp" class="form-control" required><br>
Task <input type="text" name="task" class="form-control"><br>
Date <input type="date" name="date" class="form-control"><br>
Time <input type="time" name="time" class="form-control"><br>
Status 
<select name="status" class="form-control">
    <option>Pending</option>
    <option>Completed</option>
</select><br>
<button name="add" class="btn btn-primary">Add Task</button>
</form>

<?php
if(isset($_POST['add'])){
    mysqli_query($conn,"INSERT INTO tasks(employee_name,task,task_date,task_time,task_status)
    VALUES('$_POST[emp]','$_POST[task]','$_POST[date]','$_POST[time]','$_POST[status]')");
}
?>

<h4 class="mt-4">All Tasks</h4>
<table class="table table-bordered">
<tr class="table-dark">
<th>ID</th><th>Employee</th><th>Task</th><th>Date</th><th>Time</th><th>Status</th>
</tr>

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
</tr>
<?php } ?>
<a href="login.php">back</a>
</table>
</div>
