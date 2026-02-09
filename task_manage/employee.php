<?php session_start(); include 'db.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h3>Employee Dashboard</h3>

    <a href="login.php" class="btn btn-secondary mb-3">Back</a>

    <table class="table table-bordered">
        <tr class="table-dark">
            <th>ID</th>
            <th>Employee</th>
            <th>Task</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>

        <?php
        $res = mysqli_query($conn, "SELECT * FROM tasks ORDER BY id ASC");
        while($row = mysqli_fetch_assoc($res)){
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
        
    </table>
</div>


