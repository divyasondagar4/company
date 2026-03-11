<?php
$pageTitle = 'Register';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name'] ?? '');
    $email = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name && $email && $password) {
        if ($password !== $confirm) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            // Check if email exists
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "An account with this email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->bind_param("sss", $name, $email, $hashed);
                if ($stmt->execute()) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — Astro Panchang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="auth-container">
  <div class="auth-card fade-in">
    <div class="auth-logo">
      <i class="fas fa-om fa-3x" style="color:var(--chandan-gold);"></i>
      <h2 class="mt-3">Create Account</h2>
      <div class="divider"></div>
      <p class="text-muted mt-2">Join Astro Panchang for premium features</p>
    </div>

    <?php if($error): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
    <?php endif; ?>
    <?php if($success): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success"></div>
    <?php endif; ?>

    <form method="POST" class="form-sacred">
      <div class="mb-3">
        <label>Full Name</label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-user" style="color:var(--chandan-gold);"></i></span>
          <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
        </div>
      </div>
      <div class="mb-3">
        <label>Email Address</label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-envelope" style="color:var(--chandan-gold);"></i></span>
          <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-lock" style="color:var(--chandan-gold);"></i></span>
          <input type="password" name="password" id="password" class="form-control" required placeholder="Minimum 6 characters">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light); cursor:pointer;" onclick="togglePassword('password', this)"><i class="fas fa-eye" style="color:var(--chandan-gold);"></i></span>
        </div>
      </div>
      <div class="mb-4">
        <label>Confirm Password</label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-lock" style="color:var(--chandan-gold);"></i></span>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Re-enter your password">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light); cursor:pointer;" onclick="togglePassword('confirm_password', this)"><i class="fas fa-eye" style="color:var(--chandan-gold);"></i></span>
        </div>
      </div>
      <button type="submit" class="btn-sacred w-100 justify-content-center">
        <i class="fas fa-user-plus"></i> Register
      </button>
    </form>

    <div class="text-center mt-4">
      <p class="text-muted">Already have an account?
        <a href="<?php echo SITE_URL; ?>/login.php" style="font-weight:600;">Login here</a>
      </p>
      <a href="<?php echo SITE_URL; ?>/" class="text-muted" style="font-size:0.85rem;">
        <i class="fas fa-arrow-left"></i> Back to Home
      </a>
    </div>
  </div>
</div>

<script>
function togglePassword(fieldId, btn) {
  const field = document.getElementById(fieldId);
  const icon = btn.querySelector('i');
  if (field.type === 'password') {
    field.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    field.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
