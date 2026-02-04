<?php
include 'db.php';
$msg = "";

if(isset($_POST['submit'])){
    $UserName = $_POST['UserName'];
    $Password = $_POST['Password'];
    $role = $_POST['role'];

    if($UserName=="" || $Password==""){
        $msg = "All fields required!";
    } else {

        $sql = "INSERT INTO users(UserName,Password,role) 
                VALUES('$UserName','$Password','$role')";

        if(mysqli_query($conn,$sql)){
            $msg = "Registered Successfully! Your ID is ".mysqli_insert_id($conn);
        } else {
            $msg = "Error!";
        }
    }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5" style="max-width:400px">
<h3>User Register</h3>

<div class="text-success"><?php echo $msg; ?></div>

<form method="post">
    <input type="text" name="UserName" class="form-control mb-2" placeholder="Enter UserName">

    <input type="Password" name="Password" class="form-control mb-2" placeholder="Enter Password">

    <select name="role" class="form-control mb-2">
        <option value="">Select Role</option>
        <option>admin</option>
        <option>manager</option>
        <option>employee</option>
    </select>

    <button name="submit" class="btn btn-primary w-100">Register</button>
    <a href="login.php">Go to Login</a>
</form>
</div>
</form>
</div>
