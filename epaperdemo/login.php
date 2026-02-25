<?php
include "db.php";

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_POST) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];

        if ($redirect == "download") {
            header("Location: download.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;

    } else {
        echo "<div class='alert alert-danger'>Invalid Login</div>";
    }
}
?>