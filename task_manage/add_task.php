<?php
include 'db.php';

$e=$_POST['employee_name'];
$t=$_POST['task'];
$d=$_POST['task_date'];
$ti=$_POST['task_time'];
$s=$_POST['task_status'];

mysqli_query($conn,"INSERT INTO tasks(employee_name,task,task_date,task_time,task_status) VALUES('$e','$t','$d','$ti','$s')");

header("Location: manager_dashboard.php");
?>
