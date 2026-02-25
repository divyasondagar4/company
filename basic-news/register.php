<?php
if(session_status()===PHP_SESSION_NONE) session_start();
if(isset($_SESSION['user_id']))  { header("Location: user/index.php"); exit; }

include "./user/db.php";

$error = '';
$success = '';

if(isset($_POST['register'])){
    $name     = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $mobile   = trim(mysqli_real_escape_string($conn, $_POST['mobile']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if(empty($name)||empty($email)||empty($password)){
        $error = "Please fill in all required fields.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address.";
    } elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters.";
    } elseif($password !== $confirm){
        $error = "Passwords do not match.";
    } else {
        // Check duplicate email
        $check = mysqli_query($conn,"SELECT id FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "This email is already registered. Please login.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn,"INSERT INTO users(name,email,mobile,password,status,created_at)
                VALUES('$name','$email','$mobile','$hash',1,NOW())");
            header("Location: login.php?registered=1");
            exit;
        }
    }
}

$settings = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM settings LIMIT 1"));
$siteName = $settings['site_name'] ?? 'News Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — <?= htmlspecialchars($siteName) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700&family=Mukta:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--primary:#E8520A;--primary-dark:#C43D00;--dark:#1A1A1A;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Mukta',sans-serif;background:#f0f0f0;min-height:100vh;display:flex;flex-direction:column;}
.auth-topbar{background:var(--primary);padding:12px 0;text-align:center;}
.auth-topbar a{font-family:'Noto Serif',serif;font-size:26px;font-weight:700;color:#fff;letter-spacing:-1px;}
.auth-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:30px 16px;}
.auth-card{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.10);width:100%;max-width:480px;overflow:hidden;}
.auth-card-header{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);
  color:#fff;padding:28px 32px 20px;position:relative;overflow:hidden;}
.auth-card-header::after{content:'';position:absolute;top:-30px;right:-30px;
  width:120px;height:120px;background:rgba(255,255,255,.07);border-radius:50%;}
.auth-card-header h2{font-family:'Noto Serif',serif;font-size:24px;font-weight:700;margin-bottom:4px;}
.auth-card-header p{font-size:13px;opacity:.85;margin:0;}
.auth-card-body{padding:28px 32px 32px;}
.form-label{font-size:13px;font-weight:600;color:#444;margin-bottom:5px;}
.form-control{border:2px solid #E8E8E8;border-radius:6px;padding:10px 14px;
  font-size:14px;font-family:'Mukta',sans-serif;transition:border-color .2s,box-shadow .2s;}
.form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(232,82,10,.1);outline:none;}
.input-icon-wrap{position:relative;}
.input-icon-wrap .form-control{padding-left:40px;}
.input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#aaa;font-size:15px;}
.pass-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:14px;user-select:none;}
.btn-register{background:var(--primary);color:#fff;border:none;width:100%;padding:12px;
  border-radius:6px;font-size:15px;font-weight:700;font-family:'Mukta',sans-serif;
  cursor:pointer;transition:background .2s;letter-spacing:.3px;}
.btn-register:hover{background:var(--primary-dark);}
.alert-error{background:#FFF3F0;border:1px solid #FFCBB8;color:#C43D00;
  border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:16px;}
.strength-bar{height:4px;border-radius:2px;background:#eee;margin-top:6px;overflow:hidden;}
.strength-fill{height:100%;width:0%;border-radius:2px;transition:width .3s,background .3s;}
.strength-text{font-size:11px;color:#aaa;margin-top:3px;}
.auth-footer-text{font-size:13px;color:#888;text-align:center;margin-top:20px;}
.auth-footer-text a{color:var(--primary);font-weight:600;}
.auth-footer-text a:hover{color:var(--primary-dark);}
.terms-text{font-size:12px;color:#aaa;text-align:center;margin-top:12px;line-height:1.5;}
.terms-text a{color:var(--primary);}
.auth-bottom{background:#fff;border-top:1px solid #eee;padding:12px;text-align:center;font-size:12px;color:#aaa;}
</style>
</head>
<body>

<div class="auth-topbar">
  <a href="user/index.php"><?= htmlspecialchars($siteName) ?></a>
</div>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-card-header">
      <h2>Create Account</h2>
      <p>Join thousands of readers for free</p>
    </div>
    <div class="auth-card-body">

      <?php if(!empty($error)): ?>
      <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" id="regForm">

        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Full Name <span style="color:red">*</span></label>
            <div class="input-icon-wrap">
              <span class="input-icon">👤</span>
              <input type="text" name="name" class="form-control"
                placeholder="Your full name" required
                value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Email Address <span style="color:red">*</span></label>
            <div class="input-icon-wrap">
              <span class="input-icon">✉️</span>
              <input type="email" name="email" class="form-control"
                placeholder="your@email.com" required
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Mobile Number</label>
            <div class="input-icon-wrap">
              <span class="input-icon">📱</span>
              <input type="tel" name="mobile" class="form-control"
                placeholder="10-digit mobile number"
                value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>">
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Password <span style="color:red">*</span></label>
            <div class="input-icon-wrap" style="position:relative">
              <span class="input-icon">🔒</span>
              <input type="password" name="password" id="passField" class="form-control"
                placeholder="Min 6 characters" required oninput="checkStrength(this.value)">
              <span class="pass-toggle" onclick="togglePass('passField')">👁️</span>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
            <div class="strength-text" id="strengthText"></div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Confirm Password <span style="color:red">*</span></label>
            <div class="input-icon-wrap" style="position:relative">
              <span class="input-icon">🔒</span>
              <input type="password" name="confirm_password" id="confirmField" class="form-control"
                placeholder="Repeat password" required>
              <span class="pass-toggle" onclick="togglePass('confirmField')">👁️</span>
            </div>
          </div>
        </div>

        <button type="submit" name="register" class="btn-register mt-4">
          Create My Account →
        </button>
      </form>

      <p class="terms-text mt-3">
        By registering you agree to our <a href="user/terms.php">Terms of Use</a> and <a href="user/privacy.php">Privacy Policy</a>
      </p>

      <div class="auth-footer-text mt-3">
        Already have an account? <a href="login.php">Login here</a>
      </div>
      <div class="auth-footer-text mt-1">
        <a href="user/index.php">← Back to Homepage</a>
      </div>

    </div>
  </div>
</div>

<div class="auth-bottom">
  © <?= date('Y') ?> <?= htmlspecialchars($siteName) ?> — All rights reserved
</div>

<script>
function togglePass(id){
  const f=document.getElementById(id);
  f.type=f.type==='password'?'text':'password';
}
function checkStrength(val){
  const fill=document.getElementById('strengthFill');
  const text=document.getElementById('strengthText');
  let score=0;
  if(val.length>=6) score++;
  if(val.length>=10) score++;
  if(/[A-Z]/.test(val)) score++;
  if(/[0-9]/.test(val)) score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;
  const levels=[
    {w:'0%',c:'#eee',t:''},
    {w:'25%',c:'#e53935',t:'Weak'},
    {w:'50%',c:'#FB8C00',t:'Fair'},
    {w:'75%',c:'#FDD835',t:'Good'},
    {w:'100%',c:'#43A047',t:'Strong'},
  ];
  const l=levels[Math.min(score,4)];
  fill.style.width=l.w; fill.style.background=l.c;
  text.textContent=l.t; text.style.color=l.c;
}
</script>
</body>
</html>