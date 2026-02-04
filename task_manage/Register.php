<?php include 'db.php'; ?>
<?php
if(isset($_POST['register'])){
    $u=$_POST['username'];
    $p=$_POST['password'];
    $r=$_POST['role'];

    mysqli_query($conn,"INSERT INTO users(username,password,role) VALUES('$u','$p','$r')");
    echo "<script>alert('Registered');</script>";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3>Register</h3>

<form method="post">
    Username: <input type="text" name="username" class="form-control" required><br>
    Password: <input type="password" name="password" class="form-control" required><br>

    Role:
    <select name="role" class="form-control">
        <option>Admin</option>
        <option>Manager</option>
        <option>Employee</option>
    </select><br>

    <button name="register" class="btn btn-primary">Register</button>
    <br>
  <br>
    <butoon> <a href="login.php" class="btn btn-secondary mb-3">Login</a></butoon>

</form>
</div>

