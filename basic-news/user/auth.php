<?php
function isLoggedIn(){
    return isset($_SESSION['user_id']);
}

function isPremium($conn){
    if(!isLoggedIn()) return false;

    $uid = $_SESSION['user_id'];
    $q = mysqli_query($conn,"
        SELECT id FROM user_subscriptions
        WHERE user_id='$uid'
        AND end_date >= CURDATE()
        AND payment_status='success'
    ");

    return ($q && mysqli_num_rows($q) > 0);
}