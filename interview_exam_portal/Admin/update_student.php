<?php
include '../db.php';

if(!isset($_GET['id'])){
    header("Location: manage_student.php");
    exit();
}

$id = intval($_GET['id']);

if(isset($_POST['update_student'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    mysqli_query($conn, "UPDATE students SET 
        username='$username',
        email='$email',
        password='$password'
        WHERE id=$id");

    header("Location: manage_student.php?updated=1");
    exit();
}

$result  = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
$student = mysqli_fetch_assoc($result);

include 'sidebar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f6f9;">

<div class="main-container" style="margin-left:250px;padding:2rem;">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-edit"></i> Update Student</h4>
        </div>
        <div class="card-body">

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username"
                        value="<?= htmlspecialchars($student['username']) ?>"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                        value="<?= htmlspecialchars($student['email']) ?>"
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="password"
                        value="<?= htmlspecialchars($student['password']) ?>"
                        class="form-control" required>
                </div>

                <button type="submit" name="update_student" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Student
                </button>

                <a href="manage_student.php" class="btn btn-secondary">
                    Cancel
                </a>
            </form>

        </div>
    </div>
</div>

</body>
</html>
