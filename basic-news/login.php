<?php
// ============================================================
//  login.php  — Root Login Page
//  PLACE AT: basic-news/login.php
//
//  FIX: Admin session does NOT redirect user-side visitors
//       Admin login → admin/dashboard.php
//       User login  → user/index.php (or redirect param)
//       Already logged in as USER → user/index.php
//       Already logged in as ADMIN → admin/dashboard.php
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();

// ---- Already logged in ----
if (isset($_SESSION['user_id']))  { header("Location: user/index.php");        exit; }
if (isset($_SESSION['admin_id'])) { header("Location: admin/dashboard.php");   exit; }

require_once "./user/db.php";

$error   = '';
$success = '';

// ---- After registration ----
if (isset($_GET['registered'])) $success = "Account created successfully! Please login.";
if (isset($_GET['premium']))    $success = "";  // will show premium notice below

// ---- Process login ----
if (isset($_POST['login'])) {
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']   ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter your email and password.";
    } else {

        // ---- 1. ADMIN CHECK ----
        $adminHash = md5($password);
        $aQ = mysqli_query($conn,
            "SELECT * FROM admins
             WHERE email='$email' AND password='$adminHash' AND status=1
             LIMIT 1");

        if (mysqli_num_rows($aQ) > 0) {
            $admin = mysqli_fetch_assoc($aQ);
            $_SESSION['admin_id'] = $admin['id'];
            mysqli_query($conn, "UPDATE admins SET last_login=NOW() WHERE id='{$admin['id']}'");
            // Admin always goes to admin dashboard — NEVER to user side
            header("Location: admin/dashboard.php");
            exit;
        }

        // ---- 2. USER CHECK ----
        $uQ = mysqli_query($conn,
            "SELECT * FROM users WHERE email='$email' LIMIT 1");

        if (mysqli_num_rows($uQ) > 0) {
            $user = mysqli_fetch_assoc($uQ);

            if ((int)$user['status'] === 0) {
                $error = "Your account has been suspended. Please contact support.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];

                // Redirect to intended page or default user index
                $redirect = (isset($_GET['redirect']) && !empty($_GET['redirect']))
                    ? $_GET['redirect']
                    : 'user/index.php';

                // Safety: only allow relative redirects (prevent open redirect)
                if (strpos($redirect, 'http') === 0) $redirect = 'user/index.php';

                header("Location: " . $redirect);
                exit;
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with this email address.";
        }
    }
}

// ---- Site settings ----
$settings = @mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));
$siteName = $settings['site_name'] ?? 'News Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — <?= htmlspecialchars($siteName) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700&family=Mukta:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--primary:#E8520A;--primary-dark:#C43D00;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Mukta',sans-serif;background:#F0F0F0;min-height:100vh;display:flex;flex-direction:column;}

.auth-topbar{background:var(--primary);padding:13px 0;text-align:center;border-bottom:3px solid var(--primary-dark);}
.auth-topbar a{font-family:'Noto Serif',serif;font-size:28px;font-weight:700;color:#fff;letter-spacing:-1px;}

.auth-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:32px 16px;}
.auth-card{background:#fff;border-radius:14px;box-shadow:0 4px 28px rgba(0,0,0,.1);width:100%;max-width:430px;overflow:hidden;}

.auth-card-header{
    background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);
    color:#fff;padding:28px 32px 22px;position:relative;overflow:hidden;
}
.auth-card-header::after{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.auth-card-header h2{font-family:'Noto Serif',serif;font-size:24px;font-weight:700;margin-bottom:4px;}
.auth-card-header p{font-size:13px;opacity:.85;margin:0;}

.auth-card-body{padding:28px 32px 32px;}
.form-label{font-size:13px;font-weight:600;color:#444;margin-bottom:5px;}
.form-control{border:2px solid #E8E8E8;border-radius:7px;padding:11px 15px;font-size:14px;font-family:'Mukta',sans-serif;transition:border-color .2s,box-shadow .2s;}
.form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(232,82,10,.1);outline:none;}
.icon-wrap{position:relative;}
.icon-wrap .form-control{padding-left:42px;}
.field-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:15px;color:#bbb;pointer-events:none;}
.eye-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:14px;color:#bbb;user-select:none;}
.eye-toggle:hover{color:var(--primary);}
.btn-login{background:var(--primary);color:#fff;border:none;width:100%;padding:13px;border-radius:7px;font-size:15px;font-weight:700;font-family:'Mukta',sans-serif;cursor:pointer;transition:background .2s;letter-spacing:.3px;}
.btn-login:hover{background:var(--primary-dark);}

.msg-error{background:#FFF3F0;border:1px solid #FFCBB8;color:#C43D00;border-radius:7px;padding:11px 14px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.msg-success{background:#F0FFF4;border:1px solid #B7EBC8;color:#276749;border-radius:7px;padding:11px 14px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.msg-premium{background:#FFF8E1;border:1px solid #FFD54F;color:#E65100;border-radius:7px;padding:11px 14px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.divider{display:flex;align-items:center;gap:10px;color:#ccc;font-size:12px;margin:16px 0;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:#EEE;}
.foot-link{font-size:13px;color:#888;text-align:center;margin-top:14px;}
.foot-link a{color:var(--primary);font-weight:600;}
.foot-link a:hover{color:var(--primary-dark);}
.auth-bottom{background:#fff;border-top:1px solid #eee;padding:13px;text-align:center;font-size:12px;color:#aaa;}
</style>
</head>
<body>

<div class="auth-topbar">
  <a href="user/index.php"><?= htmlspecialchars($siteName) ?></a>
</div>

<div class="auth-wrap">
  <div class="auth-card">

    <div class="auth-card-header">
      <h2>Welcome Back</h2>
      <p>Login to access your account &amp; premium content</p>
    </div>

    <div class="auth-card-body">

      <?php if (!empty($error)): ?>
      <div class="msg-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
      <div class="msg-success">✅ <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <?php if (isset($_GET['premium'])): ?>
      <div class="msg-premium">⭐ Please login to access premium content.</div>
      <?php endif; ?>

      <form method="POST">
        <!-- Email -->
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <div class="icon-wrap">
            <span class="field-icon">✉️</span>
            <input type="email" name="email" class="form-control"
              placeholder="your@email.com" required
              value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="mb-2">
          <label class="form-label">Password</label>
          <div class="icon-wrap" style="position:relative">
            <span class="field-icon">🔒</span>
            <input type="password" name="password" id="passField" class="form-control"
              placeholder="Enter your password" required>
            <span class="eye-toggle" onclick="toggleEye()">👁️</span>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 mt-1">
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#666;cursor:pointer">
            <input type="checkbox" style="accent-color:var(--primary);width:14px;height:14px"> Remember me
          </label>
          <a href="forgot_password.php" style="font-size:12px;color:var(--primary);">Forgot password?</a>
        </div>

        <button type="submit" name="login" class="btn-login">Login →</button>
      </form>

      <div class="divider">or</div>

      <div class="foot-link">
        Don't have an account? <a href="register.php">Register Free</a>
      </div>
      <div class="foot-link mt-2">
        <a href="user/index.php">← Back to Homepage</a>
      </div>

    </div>
  </div>
</div>


<div class="auth-bottom">
  © <?= date('Y') ?> <?= htmlspecialchars($siteName) ?> — All rights reserved
</div>

<script>
function toggleEye(){
    var f=document.getElementById('passField');
    f.type=f.type==='password'?'text':'password';
}
</script>
</body>
</html>