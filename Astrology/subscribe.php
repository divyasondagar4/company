<?php
$pageTitle = 'Subscribe';
require_once 'header.php';

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$success = '';
$error = '';

// Handle subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $plan = sanitize($conn, $_POST['plan'] ?? '');
    $userId = $_SESSION['user_id'];

    if ($plan === 'monthly' || $plan === 'yearly') {
        $startDate = date('Y-m-d');
        $endDate = $plan === 'monthly' ? date('Y-m-d', strtotime('+1 month')) : date('Y-m-d', strtotime('+1 year'));

        // Deactivate old subscriptions
        $stmtExp = $conn->prepare("UPDATE subscriptions SET status='expired' WHERE user_id=? AND status='active'");
        $stmtExp->bind_param("i", $userId);
        $stmtExp->execute();

        // Insert new subscription
        $stmt = $conn->prepare("INSERT INTO subscriptions (user_id, plan, status, start_date, end_date) VALUES (?, ?, 'active', ?, ?)");
        $stmt->bind_param("isss", $userId, $plan, $startDate, $endDate);
        if ($stmt->execute()) {
            $_SESSION['subscription'] = true;
            $success = "Subscription activated successfully! You now have premium access.";
        } else {
            $error = "Failed to activate subscription. Please try again.";
        }
    }
}

// Check current subscription
$currentSub = null;
if (isLoggedIn()) {
    $uid = $_SESSION['user_id'];
    $today = date('Y-m-d');
    $stmtSub = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND status = 'active' AND end_date >= ? ORDER BY end_date DESC LIMIT 1");
    $stmtSub->bind_param("is", $uid, $today);
    $stmtSub->execute();
    $subResult = $stmtSub->get_result();
    $currentSub = $subResult ? $subResult->fetch_assoc() : null;
}
?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-crown me-2"></i><?php echo t('subscribe'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('subscribe'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="section-sacred">
  <div class="container">

    <?php if($msg === 'subscription_required'): ?>
    <div class="alert-sacred mb-4">
      <i class="fas fa-lock"></i>
      <span><?php echo t('choose_plan_below'); ?></span>
    </div>
    <?php endif; ?>

    <?php if($success): ?>
    <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="subscribe.php"></div>
    <?php endif; ?>
    <?php if($error): ?>
    <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
    <?php endif; ?>

    <!-- Current Subscription Status -->
    <?php if($currentSub): ?>
    <div class="sacred-card mb-4 text-center" style="border-color:var(--chandan-gold);">
      <i class="fas fa-check-circle fa-3x mb-3" style="color:var(--chandan-gold);"></i>
      <h4><?php echo t('currently_subscribed'); ?></h4>
      <p class="text-muted">
        <?php echo t('plan_label'); ?> <strong><?php echo ucfirst($currentSub['plan']); ?></strong> |
        <?php echo t('valid_until_label'); ?> <strong><?php echo date('d M Y', strtotime($currentSub['end_date'])); ?></strong>
      </p>
      <a href="<?php echo SITE_URL; ?>/" class="btn-sacred">
        <i class="fas fa-home"></i> <?php echo t('go_to_home'); ?>
      </a>
    </div>
    <?php else: ?>

    <div class="section-header">
      <h2><?php echo t('choose_plan'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('unlock_premium_access'); ?></p>
    </div>

    <?php if(!isLoggedIn()): ?>
    <div class="text-center mb-4">
      <p class="text-muted"><?php echo t('login_register_to_subscribe'); ?></p>
    </div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
      <!-- Monthly Plan -->
      <div class="col-md-5">
        <div class="plan-card">
          <h3><?php echo t('monthly'); ?></h3>
          <div class="plan-price"><?php echo t('monthly_price'); ?></div>
          <ul class="plan-features">
            <li><i class="fas fa-check"></i> <?php echo t('daily_panchang_details'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('panchang_pdf_download'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('muhurat_calendar_access'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('detailed_muhurat_info'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('email_support'); ?></li>
          </ul>
          <?php if(isLoggedIn()): ?>
          <form method="POST">
            <input type="hidden" name="plan" value="monthly">
            <button type="submit" class="btn-sacred-outline w-100 justify-content-center">
              <i class="fas fa-crown"></i> <?php echo t('subscribe_monthly'); ?>
            </button>
          </form>
          <?php else: ?>
          <a href="<?php echo SITE_URL; ?>/login" class="btn-sacred-outline w-100 justify-content-center">
            <i class="fas fa-sign-in-alt"></i> <?php echo t('login_to_subscribe'); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Yearly Plan -->
      <div class="col-md-5">
        <div class="plan-card featured">
          <h3><?php echo t('yearly'); ?></h3>
          <div class="plan-price"><?php echo t('yearly_price'); ?></div>
          <ul class="plan-features">
            <li><i class="fas fa-check"></i> <?php echo t('everything_in_monthly'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('save_per_year'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('all_panchang_pdfs'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('full_muhurat_calendar'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('priority_support'); ?></li>
            <li><i class="fas fa-check"></i> <?php echo t('festival_reminders'); ?></li>
          </ul>
          <?php if(isLoggedIn()): ?>
          <form method="POST">
            <input type="hidden" name="plan" value="yearly">
            <button type="submit" class="btn-sacred w-100 justify-content-center">
              <i class="fas fa-crown"></i> <?php echo t('subscribe_yearly'); ?>
            </button>
          </form>
          <?php else: ?>
          <a href="<?php echo SITE_URL; ?>/login" class="btn-sacred w-100 justify-content-center">
            <i class="fas fa-sign-in-alt"></i> <?php echo t('login_to_subscribe'); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Features -->
    <div class="row g-4 mt-5">
      <div class="col-md-4 text-center animate-on-scroll">
        <i class="fas fa-file-pdf fa-2x mb-2" style="color:var(--chandan-gold);"></i>
        <h5><?php echo t('pdf_features'); ?></h5>
        <p class="text-muted" style="font-size:0.9rem;"><?php echo t('pdf_features_desc'); ?></p>
      </div>
      <div class="col-md-4 text-center animate-on-scroll">
        <i class="fas fa-calendar-check fa-2x mb-2" style="color:var(--chandan-gold);"></i>
        <h5><?php echo t('muhurat_features'); ?></h5>
        <p class="text-muted" style="font-size:0.9rem;"><?php echo t('muhurat_features_desc'); ?></p>
      </div>
      <div class="col-md-4 text-center animate-on-scroll">
        <i class="fas fa-headset fa-2x mb-2" style="color:var(--chandan-gold);"></i>
        <h5><?php echo t('premium_support_features'); ?></h5>
        <p class="text-muted" style="font-size:0.9rem;"><?php echo t('premium_support_desc'); ?></p>
      </div>
    </div>

    <?php endif; ?>

  </div>
</section>

<?php require_once 'footer.php'; ?>
