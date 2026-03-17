<?php
$pageTitle = 'Login';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';
require_once 'lang.php';

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

            // Set success toast (translated)
            $_SESSION['toast_message'] = sprintf(t('welcome_back_msg'), $user['name']);
            $_SESSION['toast_type'] = "success";

            // Determine Redirect URL
            $redirectUrl = SITE_URL . "/";
            if ($user['role'] === 'admin') {
                $redirectUrl = SITE_URL . "/admin/";
            } elseif ($redirect) {
                $redirectUrl = SITE_URL . "/$redirect";
                $params = [];
                if (isset($_GET['id'])) $params[] = 'id=' . $_GET['id'];
                if (isset($_GET['date'])) $params[] = 'date=' . $_GET['date'];
                if ($params) $redirectUrl .= '?' . implode('&', $params);
            }
            
            $_SESSION['toast_redirect'] = $redirectUrl;

            // Redirect back to login to show the toast
            header("Location: " . SITE_URL . "/login");
            exit();
        } else {
            $error = t('invalid_credentials');
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
  <title><?php echo t('login'); ?> — <?php echo t('astro_panchang'); ?></title>
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
      <h2 class="mt-3"><?php echo t('welcome_back'); ?></h2>
      <div class="divider"></div>
      <p class="text-muted mt-2"><?php echo t('login_to_account'); ?></p>
    </div>

    <?php 
    $toastMsg = $error;
    $toastType = 'error';
    $toastRedirect = '';
    if (isset($_SESSION['toast_message'])) {
        $toastMsg = $_SESSION['toast_message'];
        $toastType = $_SESSION['toast_type'] ?? 'success';
        $toastRedirect = $_SESSION['toast_redirect'] ?? '';
        unset($_SESSION['toast_message']);
        unset($_SESSION['toast_type']);
        unset($_SESSION['toast_redirect']);
    }
    if($toastMsg): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($toastMsg); ?>" data-type="<?php echo $toastType; ?>" data-redirect="<?php echo htmlspecialchars($toastRedirect); ?>"></div>
    <?php endif; ?>

    <form method="POST" class="form-sacred">
      <div class="mb-3">
        <label><?php echo t('email_address'); ?></label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-envelope" style="color:var(--chandan-gold);"></i></span>
          <input type="email" name="email" class="form-control" required placeholder="<?php echo t('enter_email'); ?>">
        </div>
      </div>
      <div class="mb-4">
        <label><?php echo t('password'); ?></label>
        <div class="input-group">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light);"><i class="fas fa-lock" style="color:var(--chandan-gold);"></i></span>
          <input type="password" name="password" id="password" class="form-control" required placeholder="<?php echo t('enter_password'); ?>">
          <span class="input-group-text" style="background:var(--chandan-cream); border-color:var(--chandan-light); cursor:pointer;" onclick="togglePassword('password', this)"><i class="fas fa-eye" style="color:var(--chandan-gold);"></i></span>
        </div>
      </div>
      <button type="submit" class="btn-sacred w-100 justify-content-center">
        <i class="fas fa-sign-in-alt"></i> <?php echo t('login'); ?>
      </button>
    </form>

    <div class="text-center mt-4">
      <p class="text-muted"><?php echo t('dont_have_account'); ?>
        <a href="<?php echo SITE_URL; ?>/register" style="font-weight:600;"><?php echo t('register_here'); ?></a>
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
