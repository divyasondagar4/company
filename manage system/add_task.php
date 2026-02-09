<?php
session_start();
include 'db.php';

if($_SESSION['role'] == "manager"){
    die("Access denied");
}

$msg = "";

if(isset($_POST['add'])){
    $name = $_POST['employee_name'];
    $task = $_POST['task'];
    $date = $_POST['task_date'];
    $time = $_POST['task_time'];
    $status = $_POST['task_status'];

    $sql = "INSERT INTO tasks(employee_name,task,task_date,task_time,task_status)
            VALUES('$name','$task','$date','$time','$status')";

    if(mysqli_query($conn,$sql)){
        $msg = "Task Added Successfully!";
    }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3>Add Task</h3>
<div class="text-success"><?php echo $msg; ?></div>

<form method="post">
<input type="text" name="employee_name" class="form-control mb-2" placeholder="Employee Name">
<input type="text" name="task" class="form-control mb-2" placeholder="Task">
<input type="date" name="task_date" class="form-control mb-2">
<input type="time" name="task_time" class="form-control mb-2">
<input type="text" name="task_status" class="form-control mb-2" placeholder="Task Status">

<button name="add" class="btn btn-primary w-100">Add Task</button>
</form>

<a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
</div>
 