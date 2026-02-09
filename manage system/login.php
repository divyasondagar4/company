<?php
session_start();
include 'db.php';
$msg="";

if(isset($_POST['login'])){
    $u = $_POST['UserName'];
    $p = $_POST['Password'];
    $r = $_POST['role'];

    $sql = "SELECT * FROM users 
            WHERE UserName='$u' AND Password='$p' AND role='$r'";
    $res = mysqli_query($conn,$sql);

    if(mysqli_num_rows($res)>0){
        $row = mysqli_fetch_assoc($res);

        $_SESSION['user_id']=$row['id'];
        $_SESSION['UserName']=$row['UserName'];
        $_SESSION['role']=$row['role'];

        header("Location: dashboard.php");
    } else {
        $msg="Invalid UserName, Password or Role!";
    }
}






// // i want task mangement using html css bootstrap simple php
// // resiter page username password roles 
// // roles are admin employee and Manager
// // now i want register page than logi ony username and password not roles its automaticaly checks from th Database
// // than in login page check if login manger redirect manager dashbored 
// // in that page add emloyee task and view in <table></table>
// // if employee login than view only table 
// // and if admin login than admin view employee table in table from database also edit and delete now i tell you db name manage system
// // 2 table tasks and users 
// // field of user id username password and roles and 
// <td><?= $row['id']; ?></td>
// <td><?= $row['employee_name']; ?></td>
// <td><?= $row['task']; ?></td>
// <td><?= $row['task_date']; ?></td>
// <td><?= $row['task_time']; ?></td>
// <td><?= $row['task_status']; ?></td> use bootstrap and one by one proper send me pages









































?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container mt-5" style="max-width:400px">
<h3>Login</h3>
<div class="text-danger"><?php echo $msg; ?></div>

<form method="post">
<input type="text" name="UserName" class="form-control mb-2" placeholder="UserName">
<input type="Password" name="Password" class="form-control mb-2" placeholder="Password">
<select name="role" class="form-control mb-2">
<option value="">Select Role</option>
<option>admin</option>
<option>manager</option>
<option>employee</option>
</select>
<button name="login" class="btn btn-success w-100">Login</button>
<a href="Register.php">Go to Register</a>

</form>
</div>
