<?php
session_start();
include 'db.php'; 

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['UserName']; 
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
<h3>Welcome <?php echo $username; ?> (<?php echo ucfirst($role); ?>)</h3>

<?php
if($role = "admin"){
    include 'admin_dashboard.php';
}
else if($role = "manager"){
    include 'manager_dashboard.php'; 
}
elseif($role = "employee"){
    include 'employee_dashboard.php';
}
?>

<a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</div>
