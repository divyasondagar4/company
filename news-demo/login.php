<?php
include 'db.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if($user && password_verify($password,$user['password'])){
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid Login";
    }
}
?>

<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>