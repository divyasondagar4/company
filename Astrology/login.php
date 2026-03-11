<?php
$pageTitle = 'Login';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Check subscription
            $today = date('Y-m-d');
            $stmtSub = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' AND start_date <= ? AND end_date >= ? LIMIT 1");
            $stmtSub->bind_param("iss", $user['id'], $today, $today);
            $stmtSub->execute();
            $sub = $stmtSub->get_result();
            if ($sub && $sub->num_rows > 0) {
                $_SESSION['subscription'] = true;
            }

            // Redirect
            if ($user['role'] === 'admin') {
                header("Location: " . SITE_URL . "/admin/");
            } elseif ($redirect) {
                header("Location: " . SITE_URL . "/$redirect.php" . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
            } else {
                header("Location: " . SITE_URL . "/");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
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
  <title>Login — Astro Panchang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="auth-container">
  <div class="auth-card fade-in">
    <div class="auth-logo">
      <i class="fas fa-om fa-3x" style="color:var(--chandan-gold);"></i>
      <h2 class="mt-3">Welcome Back</h2>
      <div class="divider"></div>
      <p class="text-muted mt-2">Login to your Astro Panchang account</p>
    </div>

    <?php if($error): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
    <?php endif; ?>

    <form method="POST" class="form-sacred">
      <div class="mb-3">
        <label>Email Address</label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-envelope" style="color:var(--chandan-gold);"></i></span>
          <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
      </div>
      <div class="mb-4">
        <label>Password</label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-lock" style="color:var(--chandan-gold);"></i></span>
          <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light); cursor:pointer;" onclick="togglePassword('password', this)"><i class="fas fa-eye" style="color:var(--chandan-gold);"></i></span>
        </div>
      </div>
      <button type="submit" class="btn-sacred w-100 justify-content-center">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </form>

    <div class="text-center mt-4">
      <p class="text-muted">Don't have an account?
        <a href="<?php echo SITE_URL; ?>/register.php" style="font-weight:600;">Register here</a>
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
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
