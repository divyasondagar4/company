<?php
include '../db.php';
include 'sidebar.php';

// Delete question
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM questions WHERE id=$id");
    $deleteSuccess = true;
}

// Fetch all questions
$questions = mysqli_query($conn, "SELECT q.*, c.category_name FROM questions q JOIN categories c ON q.category_id=c.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link href="../assets//admin.css//manage_que.css" rel="stylesheet">

</head>
<body>

<div class="main-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h2>
                <i class="fas fa-tasks"></i>
                Manage Questions
            </h2>
            <p>View, edit, and delete your quiz questions</p>
        </div>
        <div class="header-stats">
            <div class="stat-card">
                <span class="number"><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM questions")) ?></span>
                <span class="label">Total Questions</span>
            </div>
            <div class="stat-card">
                <span class="number"><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM categories")) ?></span>
                <span class="label">Categories</span>
            </div>
        </div>
    </div>

    <!-- Toast Message -->
    <?php if(isset($deleteSuccess) && $deleteSuccess): ?>
    <div class="toast-container-custom">
        <div class="toast-custom">
            <div class="toast-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="toast-content">
                <strong>Deleted Successfully!</strong>
                <span>The question has been removed from the database</span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast-custom').style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => {
                document.querySelector('.toast-container-custom').remove();
            }, 300);
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Content Card -->
    <div class="content-card">
        <div class="table-header">
            <div class="table-title">
                <i class="fas fa-list"></i>
                Questions List
            </div>
            <a href="add_question.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Add New Question
            </a>
        </div>

        <!-- Data Table -->
        <table id="questionsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Question</th>
                    <th>Option 1</th>
                    <th>Option 2</th>
                    <th>Option 3</th>
                    <th>Option 4</th>
                    <th>Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                mysqli_data_seek($questions, 0);
                while($q = mysqli_fetch_assoc($questions)): 
                ?>
                <tr>
                    <td><span class="id-badge">#<?= $q['id'] ?></span></td>
                    <td><span class="category-badge"><?= $q['category_name'] ?></span></td>
                    <td><div class="question-text" title="<?= htmlspecialchars($q['question']) ?>"><?= $q['question'] ?></div></td>
                    <td><div class="option-text" title="<?= htmlspecialchars($q['option1']) ?>"><?= $q['option1'] ?></div></td>
                    <td><div class="option-text" title="<?= htmlspecialchars($q['option2']) ?>"><?= $q['option2'] ?></div></td>
                    <td><div class="option-text" title="<?= htmlspecialchars($q['option3']) ?>"><?= $q['option3'] ?></div></td>
                    <td><div class="option-text" title="<?= htmlspecialchars($q['option4']) ?>"><?= $q['option4'] ?></div></td>
                    <td><span class="answer-badge">Option <?= $q['correct_answer'] ?></span></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit_question.php?id=<?= $q['id'] ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?= $q['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this question?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#questionsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            scrollX: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search questions...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ questions",
                infoEmpty: "No questions available",
                infoFiltered: "(filtered from _MAX_ total questions)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 },
                { width: "60px", targets: 0 },
                { width: "100px", targets: 1 },
                { width: "180px", targets: 2 },
                { width: "110px", targets: [3, 4, 5, 6] },
                { width: "90px", targets: 7 },
                { width: "160px", targets: 8 }
            ]
        });
    });
</script>

</body>
</html>