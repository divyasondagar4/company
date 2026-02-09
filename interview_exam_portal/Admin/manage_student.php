<?php
include '../db.php';
include 'sidebar.php';

// Delete student
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM students WHERE id=$id");
    $deleteSuccess = true;
}

// Fetch all students
$students = mysqli_query($conn, "SELECT * FROM students");

// Update student
if(isset($_GET['updated'])){
    $updateSuccess = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets//admin.css//manage_student.css" rel="stylesheet">
</head>
<body>
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h2>
                <i class="fas fa-users-cog"></i>
                Student Management System
            </h2>
        </div>

       

        <!-- Toast Notifications -->
        <?php if(isset($deleteSuccess) && $deleteSuccess): ?>
        <div class="custom-toast">
            <div class="toast-danger">
                <div class="toast-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <strong>Success!</strong><br>
                    Student deleted successfully!
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.custom-toast')?.remove();
            }, 3000);
        </script>
        <?php endif; ?>

        <?php if(isset($updateSuccess) && $updateSuccess): ?>
        <div class="custom-toast">
            <div class="toast-success">
                <div class="toast-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <strong>Success!</strong><br>
                    Student updated successfully!
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.custom-toast')?.remove();
            }, 3000);
        </script>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-header">
                <h4><i class="fas fa-list"></i> Student Records</h4>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search students..." onkeyup="searchTable()">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table" id="studentTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Name</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-lock"></i> Password</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $students = mysqli_query($conn, "SELECT * FROM students");
                        if(mysqli_num_rows($students) > 0):
                            while($s = mysqli_fetch_assoc($students)): 
                        ?>
                        <tr>
                            <form method="post">
                                <td>
                                    <span class="id-badge">#<?= $s['id'] ?></span>
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($s['username']) ?>" required>
                                </td>
                                <td>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($s['email']) ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($s['password']) ?>" required>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                    <a href="update_student.php?id=<?= $s['id'] ?>" class="btn-action btn-update">
    <i class="fas fa-edit"></i> Update
</a>

                                        <a href="?delete=<?= $s['id'] ?>" class="btn-action btn-delete" onclick="return confirm('⚠️ Are you sure you want to delete this student?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>No Students Found</h5>
                                    <p>There are no students in the system yet.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('studentTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length - 1; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }

        // Auto-hide toasts
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.custom-toast');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.animation = 'slideIn 0.5s ease reverse';
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            });
        });
    </script>
</body>
</html>

