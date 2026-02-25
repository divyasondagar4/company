<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdmin(){
    return isset($_SESSION['admin_id']);
}

function isUser(){
    return isset($_SESSION['user_id']);
}

function isLoggedIn(){
    return isset($_SESSION['admin_id']) || isset($_SESSION['user_id']);
}

function isPremium($conn,$user_id){
    $today = date("Y-m-d");
    $q = mysqli_query($conn,"SELECT * FROM user_subscriptions 
        WHERE user_id='$user_id' 
        AND payment_status='success'
        AND end_date >= '$today'");
    return mysqli_num_rows($q)>0;
}
?>