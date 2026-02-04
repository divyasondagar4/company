<?php
session_start();
include 'db.php';

$id = $_GET['id'];
$res = mysqli_query($conn,"SELECT * FROM tasks WHERE id=$id");
$row = mysqli_fetch_assoc($res);

if(isset($_POST['update'])){
    $emp = $_POST['emp'];
    $task = $_POST['task'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];

    mysqli_query($conn,"UPDATE tasks SET 
        employee_name='$emp',
        task='$task',
        task_date='$date',
        task_time='$time',
        task_status='$status'
        WHERE id=$id");

    header("Location: admin.php");
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3>Edit Task</h3>

<form method="post" class="border p-4">
Employee Name
<input type="text" name="emp" value="<?= $row['employee_name']; ?>" class="form-control"><br>

Task
<input type="text" name="task" value="<?= $row['task']; ?>" class="form-control"><br>

Date
<input type="date" name="date" value="<?= $row['task_date']; ?>" class="form-control"><br>

Time
<input type="time" name="time" value="<?= $row['task_time']; ?>" class="form-control"><br>

Status
<select name="status" class="form-control">
    <option <?= $row['task_status']=="Pending"?"selected":"" ?>>Pending</option>
    <option <?= $row['task_status']=="Completed"?"selected":"" ?>>Completed</option>
</select><br>

<button name="update" class="btn btn-success">Update Task</button>
</form>
</div>
