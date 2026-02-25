<?php
include "db.php";

$user_id = $_SESSION['user_id'];
$plan = $_POST['plan'];

$conn->query("UPDATE users 
              SET subscription_status='active',
                  subscription_plan='$plan'
              WHERE id=$user_id");

header("Location: download.php");
exit;
?>