<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");

if(isset($_POST['save'])){
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $price = $_POST['price'];
    $days = $_POST['days'];
    $desc = mysqli_real_escape_string($conn,$_POST['description']);

    mysqli_query($conn,"
    INSERT INTO subscriptions(plan_name,price,duration_days,description)
    VALUES('$name','$price','$days','$desc')
    ");

    header("Location: subscriptions.php");
    exit;
}

include("header.php");
include("sidebar.php");
?>

<div class="content">
<div class="container-fluid">

<h4 class="mb-4">Add Subscription Plan</h4>

<div class="card shadow-sm">
<div class="card-body">

<form method="post">

<input name="name" class="form-control mb-3" placeholder="Plan Name" required>
<input name="price" type="number" class="form-control mb-3" placeholder="Price" required>
<input name="days" type="number" class="form-control mb-3" placeholder="Duration Days" required>
<textarea name="description" class="form-control mb-3" placeholder="Description"></textarea>

<button name="save" class="btn btn-success">Save Plan</button>

</form>

</div>
</div>

</div>
</div>

<?php include("footer.php"); ?>