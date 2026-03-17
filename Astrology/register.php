<?php
$pageTitle = 'Register';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';
require_once 'lang.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name'] ?? '');
    $email = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name && $email && $password) {
        if ($password !== $confirm) {
            $error = t('passwords_not_match');
        } elseif (strlen($password) < 6) {
            $error = t('password_min_length');
        } else {
            // Check if email exists
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = t('email_exists');
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->bind_param("sss", $name, $email, $hashed);
                if ($stmt->execute()) {
                    $_SESSION['toast_message'] = t('account_created');
                    $_SESSION['toast_type'] = "success";
                    $url = "login.php";
                    $params = [];
                    if (isset($_GET['redirect'])) $params[] = 'redirect=' . $_GET['redirect'];
                    if (isset($_GET['id'])) $params[] = 'id=' . $_GET['id'];
                    if (isset($_GET['date'])) $params[] = 'date=' . $_GET['date'];
                    if ($params) $url .= '?' . implode('&', $params);
                    $_SESSION['toast_redirect'] = $url;
                    header("Location: " . SITE_URL . "/register");
                    exit();
                } else {
                    $error = t('registration_failed');
                }
            }
        }
    } else {
        $error = t('fill_all_fields');
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo t('register'); ?> — <?php echo t('astro_panchang'); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<div class="auth-container">
  <div class="auth-card fade-in">


    <div class="auth-logo">
      <i class="fas fa-om fa-3x" style="color:var(--chandan-gold);"></i>
      <h2 class="mt-3"><?php echo t('create_account'); ?></h2>
      <div class="divider"></div>
      <p class="text-muted mt-2"><?php echo t('register_subtitle'); ?></p>
    </div>

    <?php 
    $toastRedirect = '';
    if (isset($_SESSION['toast_message'])) {
        $success = $_SESSION['toast_message'];
        $toastRedirect = $_SESSION['toast_redirect'] ?? '';
        unset($_SESSION['toast_message']);
        unset($_SESSION['toast_redirect']);
        unset($_SESSION['toast_type']); // just in case
    }
    ?>
    <?php if($error): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
    <?php endif; ?>
    <?php if($success): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="<?php echo htmlspecialchars($toastRedirect); ?>"></div>
    <?php endif; ?>

    <form method="POST" class="form-sacred">
      <div class="mb-3">
        <label><?php echo t('full_name'); ?></label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-user" style="color:var(--chandan-gold);"></i></span>
          <input type="text" name="name" class="form-control" required placeholder="<?php echo t('enter_name'); ?>">
        </div>
      </div>
      <div class="mb-3">
        <label><?php echo t('email_address'); ?></label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-envelope" style="color:var(--chandan-gold);"></i></span>
          <input type="email" name="email" class="form-control" required placeholder="<?php echo t('enter_email'); ?>">
        </div>
      </div>
      <div class="mb-3">
        <label><?php echo t('password'); ?></label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-lock" style="color:var(--chandan-gold);"></i></span>
          <input type="password" name="password" id="password" class="form-control" required placeholder="<?php echo t('min_6_chars'); ?>">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light); cursor:pointer;" onclick="togglePassword('password', this)"><i class="fas fa-eye" style="color:var(--chandan-gold);"></i></span>
        </div>
      </div>
      <div class="mb-4">
        <label><?php echo t('confirm_password'); ?></label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-lock" style="color:var(--chandan-gold);"></i></span>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="<?php echo t('reenter_password'); ?>">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light); cursor:pointer;" onclick="togglePassword('confirm_password', this)"><i class="fas fa-eye" style="color:var(--chandan-gold);"></i></span>
        </div>
      </div>
      <button type="submit" class="btn-sacred w-100 justify-content-center">
        <i class="fas fa-user-plus"></i> <?php echo t('register'); ?>
      </button>
    </form>

    <div class="text-center mt-4">
      <p class="text-muted"><?php echo t('already_have_account'); ?>
        <a href="<?php echo SITE_URL; ?>/login" style="font-weight:600;"><?php echo t('login_here'); ?></a>
      </p>
      <a href="<?php echo SITE_URL; ?>/" class="text-muted" style="font-size:0.85rem;">
        <i class="fas fa-arrow-left"></i> <?php echo t('back_to_home'); ?>
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
