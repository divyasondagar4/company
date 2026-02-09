<?php
session_start();
include '../db.php';

$sql = "
SELECT r.id, s.username AS student_name, c.category_name, 
       r.score, r.total_question, r.correct_answer, r.wrong_answer, r.exam_date
FROM res r
JOIN students s ON r.student_id = s.id
JOIN categories c ON r.category_id = c.id
ORDER BY r.exam_date DESC
";

$res = mysqli_query($conn, $sql);

include "sidebar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Quiz Results</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets//admin.css//view_result.css" rel="stylesheet">
</head>
<body>

<div class="main-container">
<div class="table-container">
<h4 class="mb-4">📊 Detailed Quiz Results</h4>

<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Category</th>
    <th>Total Q</th>
    <th>Correct</th>
    <th>Wrong</th>
    <th>Score %</th>
    <th>Date</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($res)>0): ?>
<?php while($row=mysqli_fetch_assoc($res)): ?>
<tr>
    <td>#<?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['student_name']) ?></td>
    <td><?= htmlspecialchars($row['category_name']) ?></td>
    <td><?= $row['total_question'] ?></td>
    <td class="correct"><?= $row['correct_answer'] ?></td>
    <td class="wrong"><?= $row['wrong_answer'] ?></td>
    <td><?= $row['score'] ?>%</td>
    <td><?= date('M d, Y', strtotime($row['exam_date'])) ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="8">No Results Found</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>
</div>

</body>
</html>
