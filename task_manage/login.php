<?php 
session_start();
include 'db.php'; 
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3>Login</h3>

<form method="post">
    Username: <input type="text" name="username" class="form-control" required><br>
    Password: <input type="password" name="password" class="form-control" required><br>
    <button name="login" class="btn btn-success">Login</button>
    <br>
    <br>
    <br>
    <butoon> <a href="register.php" class="btn btn-secondary mb-3">register</a></butoon>

</form>
</div>

<?php
if(isset($_POST['login'])){
    $u=$_POST['username'];
    $p=$_POST['password'];

    $res=mysqli_query($conn,"SELECT * FROM users WHERE username='$u' AND password='$p'");
    $row=mysqli_fetch_assoc($res);

    if($row){
        $_SESSION['user']=$row['username'];
        $_SESSION['role']=$row['role'];

        if($row['role']=="Manager"){
            header("Location: manager.php");
        }
        elseif($row['role']=="Employee"){
            header("Location: employee.php");
        }
        elseif($row['role']=="Admin"){
            header("Location: admin.php");
        }
    }else{
        echo "Invalid Login";
    }
}
?>
