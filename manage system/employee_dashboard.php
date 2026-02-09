<?php

include 'db.php';

if($_SESSION['role'] != "employee"){
    die("Access Denied");
}

$UserName = $_SESSION['UserName']; 

$res = mysqli_query($conn, "SELECT * FROM tasks WHERE employee_name='$UserName'");
?>

<h4 class="mt-4">My Tasks</h4>

<table class="table table-bordered table-striped mt-3">
<tr class="table-dark">
    <th>ID</th>
    <th>Employee Name</th>
    <th>Task</th>
    <th>Task Date</th>
    <th>Task Time</th>
    <th>Status</th>
</tr>

<?php while($row=mysqli_fetch_assoc($res)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['employee_name']; ?></td>
    <td><?php echo $row['task']; ?></td>
    <td><?php echo $row['task_date']; ?></td>
    <td><?php echo $row['task_time']; ?></td>
    <td>
        <?php
            if($row['task_status'] == "Pending"){
                echo "<span class='badge bg-warning'>Pending</span>";
            } elseif($row['task_status'] == "Completed"){
                echo "<span class='badge bg-success'>Completed</span>";
            } else {
                echo "<span class='badge bg-secondary'>{$row['task_status']}</span>";
            }
        ?>
    </td>
</tr>
<?php } ?>

</table>
