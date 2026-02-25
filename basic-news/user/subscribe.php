<?php
session_start();
include('db.php');

$uid = $_SESSION['user_id'];
$start = date('Y-m-d');
$end = date('Y-m-d', strtotime("+30 days"));

mysqli_query($conn,"
INSERT INTO subscriptions(user_id,plan_name,start_date,end_date,status)
VALUES($uid,'Monthly','$start','$end','active')
");

header("Location: index.php");