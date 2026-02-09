<?php
session_start();
include "db.php";

$msg = "";
$msg_type = ""; // success or error

// SIGNUP
if(isset($_POST['signup'])){
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if($name=="" || $email=="" || $pass==""){
        $msg = "All fields are required!";
        $msg_type = "error";
    }else{
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

        if(mysqli_num_rows($check) > 0){
            $msg = "Email already exists!";
            $msg_type = "error";
        }else{
            mysqli_query($conn,"INSERT INTO users(fullname,email,password) VALUES('$name','$email','$hash')");
            $msg = "Signup successful! Please login.";
            $msg_type = "success";
        }
    }
}

// LOGIN
if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $query = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($query) == 1){
        $user = mysqli_fetch_assoc($query);
        if(password_verify($pass,$user['password'])){
            $_SESSION['user'] = $user['fullname'];
            header("Location: dashboard.php");
            exit();
        }else{
            $msg = "Wrong password!";
            $msg_type = "error";
        }
    }else{
        $msg = "User not found!";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login & Signup</title>
<link rel="stylesheet" href="login.css">

<style>
.toast{
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    color: white;
    border-radius: 8px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.5s ease;
    z-index: 999;
}
.toast.show{
    opacity: 1;
    transform: translateX(0);
}
.toast.success{ background: #28a745; }
.toast.error{ background: #dc3545; }
</style>

</head>
<body>

<!-- TOAST MESSAGE -->
<?php if($msg!=""): ?>
<div id="toast" class="toast <?php echo $msg_type; ?>">
    <?php echo $msg; ?>
</div>
<?php endif; ?>

<div class="wrapper">

<!-- SIGNUP -->
<div class="form signup">
<header>Signup</header>
<form method="POST">
    <input type="text" name="fullname" placeholder="Full name" required>
    <input type="email" name="email" placeholder="Email address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="signup" value="Signup">
</form>
</div>

<!-- LOGIN -->
<div class="form login">
<header>Login</header>
<form method="POST">
    <input type="email" name="email" placeholder="Email address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login" value="Login">
</form>
</div>

</div>


<script>
const wrapper = document.querySelector(".wrapper"),
signupHeader = document.querySelector(".signup header"),
loginHeader = document.querySelector(".login header");

loginHeader.addEventListener("click", () => wrapper.classList.add("active"));
signupHeader.addEventListener("click", () => wrapper.classList.remove("active"));

function showToast(message, type="error"){
    let toast = document.createElement("div");
    toast.className = "toast show " + type;
    toast.innerText = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

document.querySelector("input[name='signup']").addEventListener("click", function(e){
    let name = document.querySelector("input[name='fullname']").value.trim();
    let email = document.querySelector(".signup input[name='email']").value.trim();
    let pass = document.querySelector(".signup input[name='password']").value.trim();

    if(name === ""){
        e.preventDefault();
        showToast("Please enter full name");
        return;
    }
    if(email === ""){
        e.preventDefault();
        showToast("Please enter email address");
        return;
    }
    if(pass === ""){
        e.preventDefault();
        showToast("Please enter password");
        return;
    }
});

document.querySelector("input[name='login']").addEventListener("click", function(e){
    let email = document.querySelector(".login input[name='email']").value.trim();
    let pass = document.querySelector(".login input[name='password']").value.trim();

    if(email === ""){
        e.preventDefault();
        showToast("Enter your email");
        return;
    }
    if(pass === ""){
        e.preventDefault();
        showToast("Enter your password");
        return;
    }
});

window.onload = function(){
    const toast = document.getElementById("toast");
    if(toast){
        toast.classList.add("show");
        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000);
    }
};

</script>

</body>
</html>

<script>
</script>
