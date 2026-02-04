<?php
include 'db.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tasks WHERE id=$id"));

if(isset($_POST['update'])) {
    $name = $_POST['employee_name'];
    $task = $_POST['task'];
    $date = $_POST['task_date'];
    $time = $_POST['task_time'];
    $status = $_POST['task_status'];

    mysqli_query($conn,"UPDATE tasks SET 
        employee_name='$name',
        task='$task',
        task_date='$date',
        task_time='$time',
        task_status='$status'
        WHERE id=$id");

    header("Location: admin_dashboard.php");
}
?>

<h4>Edit Task</h4>
<form method="post">
    Employee: <input type="text" name="employee_name" value="<?= $data['employee_name']; ?>"><br><br>
    Task: <input type="text" name="task" value="<?= $data['task']; ?>"><br><br>
    Date: <input type="date" name="task_date" value="<?= $data['task_date']; ?>"><br><br>
    Time: <input type="time" name="task_time" value="<?= $data['task_time']; ?>"><br><br>
    Status: 
    <select name="task_status">
        <option <?= $data['task_status']=='Pending'?'selected':'' ?>>Pending</option>
        <option <?= $data['task_status']=='Completed'?'selected':'' ?>>Completed</option>
    </select><br><br>

    <button name="update">Update</button>
</form>
