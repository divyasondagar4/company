<?php
session_start();
include 'db.php';

$loginError = "";
$registerMsg = "";

// =================== LOGIN ===================
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    // Check admin
    $resAdmin = mysqli_query($conn,"SELECT * FROM admin WHERE username='$username'");
    if(mysqli_num_rows($resAdmin) > 0){
        $admin = mysqli_fetch_assoc($resAdmin);
        if(password_verify($pass, $admin['password']) || $pass === $admin['password']){
            $_SESSION['admin'] = $username;
            header("Location:Admin/admin_dashboard.php");
            exit();
        }
    }

    // Check student
    $resStudent = mysqli_query($conn,"SELECT * FROM students WHERE username='$username'");
    if(mysqli_num_rows($resStudent) > 0){
        $student = mysqli_fetch_assoc($resStudent);
        if(password_verify($pass, $student['password']) || $pass === $student['password']){
            $_SESSION['student'] = $student;
            header("Location:Students/student_dash.php");
            exit();
        }
    }

    $loginError = "Invalid Username or Password!";
}

// =================== REGISTER ===================
if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, $_POST['reg_username']);
    $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
    $pass = $_POST['reg_password'];
    $created_at = date('Y-m-d H:i:s');

    // Validation
    if(strlen($username) < 3){
        $registerMsg = "Username must be at least 3 characters!";
    } elseif(strlen($pass) < 6){
        $registerMsg = "Password must be at least 6 characters!";
    } else {
        $check = mysqli_query($conn,"SELECT * FROM students WHERE username='$username' OR email='$email'");
        if(mysqli_num_rows($check) > 0){
            $registerMsg = "Username or Email already exists!";
        } else {
            $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            mysqli_query($conn,"INSERT INTO students (username,email,password,created_at) VALUES ('$username','$email','$hashedPass','$created_at')");
            $registerMsg = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/index.css">

</head>
<body>

<div class="container-wrapper">
    <div class="card-container">
        <div class="card-flip" id="cardFlip">
            
            <!-- LOGIN FACE -->
            <div class="card-face card-front">
                <div class="card-header">
                    <h3><i class="fas fa-sign-in-alt"></i> Welcome Back</h3>
                    <p>Login to access your account</p>
                </div>

                <?php if($loginError!="" && !isset($_POST['register'])): ?>
                    <div class="alert alert-danger alert-custom">
                        <i class="fas fa-exclamation-circle"></i> <?= $loginError ?>
                    </div>
                <?php endif; ?>

                <form method="post" id="loginForm">
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">                        
                            <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Enter your password" required>
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('loginPassword', this)"></i>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn-custom btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div class="switch-text">
                    Don't have an account? <span class="switch-link" onclick="flipCard()">Register Now</span>
                </div>
            </div>

            <!-- REGISTER FACE -->
            <div class="card-face card-back">
                <div class="card-header">
                    <h3><i class="fas fa-user-plus"></i> Create Account</h3>
                    <p>Register to get started</p>
                </div>

                <?php if($registerMsg!="" && isset($_POST['register'])): ?>
                    <?php if($registerMsg === "success"): ?>
                        <div class="alert alert-success alert-custom">
                            <i class="fas fa-check-circle"></i> Registered Successfully! Please login.
                        </div>
                        <script>
                            setTimeout(() => {
                                document.getElementById('cardFlip').classList.remove('flipped');
                            }, 2000);
                        </script>
                    <?php else: ?>
                        <div class="alert alert-danger alert-custom">
                            <i class="fas fa-exclamation-circle"></i> <?= $registerMsg ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form method="post" id="registerForm">
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="reg_username" class="form-control" placeholder="Choose a username" required minlength="3">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="reg_email" class="form-control" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="reg_password" id="registerPassword" class="form-control" placeholder="Create a password" required minlength="6">
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('registerPassword', this)"></i>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn-custom btn-register">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                </form>

                <div class="switch-text">
                    Already have an account? <span class="switch-link" onclick="flipCard()">Login Here</span>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="./assets//js//index.js"></script>
<script>
    // Auto-flip to register if there's a register message
<?php if($registerMsg !== "" && isset($_POST['register']) && $registerMsg !== "success"): ?>
    document.getElementById('cardFlip').classList.add('flipped');
<?php endif; ?>

</script>

</body>
</html>