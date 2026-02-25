<?php
// ============================================================
// user/subscription.php
// FIX: ALL PHP logic (session check, DB queries, redirects)
//      MUST run BEFORE include "header.php"
//      Because header.php outputs HTML — after that, no
//      header() redirect is possible.
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/db.php";

// ---- Must be logged in as user ----
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?premium=1&redirect=" . urlencode("subscription.php"));
    exit;
}

$uid   = (int)$_SESSION['user_id'];
$today = date('Y-m-d');

// ---- Check current active subscription ----
$currentSub = null;
$csQ = mysqli_query($conn, "
    SELECT us.*, s.plan_name
    FROM user_subscriptions us
    LEFT JOIN subscriptions s ON us.subscription_id = s.id
    WHERE us.user_id = '$uid'
      AND us.payment_status = 'success'
      AND us.end_date >= '$today'
    ORDER BY us.end_date DESC
    LIMIT 1
");
if (mysqli_num_rows($csQ) > 0) {
    $currentSub = mysqli_fetch_assoc($csQ);
}

// ---- Handle plan purchase ----
$errorMsg = '';
if (isset($_POST['buy_plan'])) {
    $planId = intval($_POST['plan_id'] ?? 0);

    if ($planId > 0) {
        $planQ = mysqli_query($conn, "SELECT * FROM subscriptions WHERE id='$planId' AND status=1 LIMIT 1");

        if (mysqli_num_rows($planQ) > 0) {
            $plan      = mysqli_fetch_assoc($planQ);
            $startDate = date('Y-m-d');
            $endDate   = date('Y-m-d', strtotime("+{$plan['duration_days']} days"));
            $amount    = floatval($plan['price']);
            $txnId     = mysqli_real_escape_string($conn, 'TXN' . time() . rand(1000, 9999));
            $subId     = intval($plan['id']);

            $ins1 = mysqli_query($conn, "
                INSERT INTO user_subscriptions
                    (user_id, subscription_id, start_date, end_date, payment_status, created_at)
                VALUES
                    ('$uid', '$subId', '$startDate', '$endDate', 'success', NOW())
            ");

            $ins2 = mysqli_query($conn, "
                INSERT INTO payments
                    (user_id, subscription_id, amount, payment_method, transaction_id, payment_status, payment_date)
                VALUES
                    ('$uid', '$subId', '$amount', 'Online', '$txnId', 'Success', NOW())
            ");

            if ($ins1 && $ins2) {
                header("Location: subscription.php?success=1");
                exit;
            } else {
                $errorMsg = "Database error: " . mysqli_error($conn);
            }
        } else {
            $errorMsg = "Invalid or inactive plan selected.";
        }
    } else {
        $errorMsg = "Please select a valid plan.";
    }
}

// ---- Fetch all active plans ----
$plans = [];
$plQ = mysqli_query($conn, "SELECT * FROM subscriptions WHERE status=1 ORDER BY price ASC");
while ($p = mysqli_fetch_assoc($plQ)) $plans[] = $p;

// ---- User info ----
$user     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$uid' LIMIT 1"));
$userName = $user['name'] ?? 'User';

// NOW safe to include header.php (outputs HTML)
?>
<?php include "header.php"; ?>

<style>
.sub-hero{background:linear-gradient(135deg,#1A1A1A 0%,#2d2d2d 100%);color:#fff;padding:36px 0 28px;margin-bottom:30px;}
.sub-hero h1{font-family:'Noto Serif',serif;font-size:clamp(22px,3.5vw,34px);font-weight:700;margin-bottom:8px;}
.sub-hero p{font-size:14px;opacity:.8;}
.active-sub-card{background:linear-gradient(135deg,#E8520A,#C43D00);color:#fff;border-radius:10px;padding:22px 26px;margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;}
.active-sub-card h5{font-family:'Noto Serif',serif;font-size:18px;margin-bottom:4px;}
.active-sub-card p{font-size:13px;opacity:.9;margin:0;}
.sub-badge{background:#FFA000;color:#333;font-size:11px;font-weight:700;padding:3px 10px;border-radius:10px;}
.plan-card{background:#fff;border-radius:12px;border:2px solid #eee;padding:30px 24px;text-align:center;transition:transform .2s,border-color .2s,box-shadow .2s;height:100%;display:flex;flex-direction:column;position:relative;overflow:hidden;}
.plan-card.popular{border-color:#E8520A;box-shadow:0 8px 28px rgba(232,82,10,.15);}
.plan-card.popular::before{content:'⭐ POPULAR';position:absolute;top:0;left:50%;transform:translateX(-50%);background:#E8520A;color:#fff;font-size:10px;font-weight:700;padding:4px 20px;letter-spacing:1px;border-radius:0 0 8px 8px;}
.plan-name{font-size:19px;font-weight:700;margin-bottom:6px;margin-top:10px;}
.plan-price{font-family:'Noto Serif',serif;font-size:42px;font-weight:700;color:#E8520A;line-height:1;}
.plan-price sup{font-size:18px;top:-12px;position:relative;}
.plan-duration{font-size:13px;color:#888;margin-top:4px;margin-bottom:16px;}
.plan-features{list-style:none;padding:0;margin:0 0 24px;text-align:left;}
.plan-features li{font-size:13px;color:#555;padding:5px 0;border-bottom:1px dashed #f0f0f0;display:flex;align-items:center;gap:8px;}
.plan-features li:last-child{border:none;}
.plan-features li::before{content:'✅';font-size:12px;}
.btn-buy{background:#E8520A;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;width:100%;transition:background .2s;margin-top:auto;font-family:'Mukta',sans-serif;}
.btn-buy:hover{background:#C43D00;}
.plan-card:not(.popular) .btn-buy{background:#fff;color:#E8520A;border:2px solid #E8520A;}
.plan-card:not(.popular) .btn-buy:hover{background:#E8520A;color:#fff;}
.benefit-item{display:flex;align-items:flex-start;gap:14px;padding:16px;background:#fff;border-radius:8px;border:1px solid #eee;}
.benefit-icon{font-size:28px;flex-shrink:0;}
.benefit-title{font-size:15px;font-weight:700;margin-bottom:3px;}
.benefit-desc{font-size:13px;color:#777;}
.success-banner{background:#f0fff4;border:2px solid #b7ebc8;border-radius:10px;padding:22px 24px;text-align:center;margin-bottom:28px;}
.success-banner h4{color:#276749;font-size:18px;font-weight:700;margin-bottom:6px;}
.success-banner p{color:#2d6a4f;font-size:14px;}
.error-banner{background:#fff3f0;border:2px solid #ffcbb8;border-radius:10px;padding:16px 20px;margin-bottom:20px;color:#c43d00;font-size:14px;}
</style>

<div class="sub-hero">
  <div class="container">
    <h1>⭐ Premium Subscription</h1>
    <p>Hello <?= htmlspecialchars($userName) ?>! Unlock exclusive content, E-Paper, ad-free reading and much more.</p>
  </div>
</div>

<div class="container mb-5">

  <?php if (isset($_GET['success'])): ?>
  <div class="success-banner">
    <div style="font-size:40px;margin-bottom:10px;">🎉</div>
    <h4>Subscription Activated!</h4>
    <p>Welcome to Premium! You now have full access to all exclusive content.</p>
    <a href="index.php" style="display:inline-block;margin-top:12px;background:#E8520A;color:#fff;padding:10px 24px;border-radius:6px;font-weight:700;font-size:14px;">Start Reading →</a>
  </div>
  <?php endif; ?>

  <?php if (!empty($errorMsg)): ?>
  <div class="error-banner">⚠️ <?= htmlspecialchars($errorMsg) ?></div>
  <?php endif; ?>

  <?php if ($currentSub): ?>
  <div class="active-sub-card">
    <div>
      <span class="sub-badge">✅ ACTIVE</span>
      <h5 class="mt-2">Premium — <?= htmlspecialchars($currentSub['plan_name']) ?></h5>
      <p>
        Valid until: <strong><?= date('d M Y', strtotime($currentSub['end_date'])) ?></strong>
        &nbsp;|&nbsp;
        <?= max(0, (int)((strtotime($currentSub['end_date']) - time()) / 86400)) ?> days remaining
      </p>
    </div>
    <a href="epaper.php" style="background:#fff;color:#E8520A;padding:10px 22px;border-radius:6px;font-weight:700;font-size:14px;text-decoration:none;">Read E-Paper →</a>
  </div>
  <?php endif; ?>

  <h2 style="font-family:'Noto Serif',serif;font-size:20px;font-weight:700;border-left:4px solid #E8520A;padding-left:12px;margin-bottom:20px;">
    What You Get With Premium
  </h2>
  <div class="row g-3 mb-4">
    <?php
    $benefits = [
      ['📰','Daily E-Paper','Download and read the digital newspaper from all city editions every day.'],
      ['🔓','Exclusive Articles','Access premium news articles hidden from free users.'],
      ['🚫','Ad-Free Reading','Enjoy a clean, distraction-free reading experience.'],
      ['📦','Full Archive Access','Read past editions and archived premium articles anytime.'],
      ['💬','Priority Comments','Your comments get highlighted in articles.'],
      ['📱','All Devices','Read on mobile, tablet, and desktop with one subscription.'],
    ];
    foreach ($benefits as $b): ?>
    <div class="col-md-4">
      <div class="benefit-item">
        <div class="benefit-icon"><?= $b[0] ?></div>
        <div>
          <div class="benefit-title"><?= $b[1] ?></div>
          <div class="benefit-desc"><?= $b[2] ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <h2 style="font-family:'Noto Serif',serif;font-size:20px;font-weight:700;border-left:4px solid #E8520A;padding-left:12px;margin:28px 0 20px;">
    Choose Your Plan
  </h2>

  <?php if (!empty($plans)): ?>
  <div class="row g-4 justify-content-center mb-4">
    <?php foreach ($plans as $idx => $plan): ?>
    <div class="col-md-4">
      <div class="plan-card <?= ($idx === 0) ? 'popular' : '' ?>">
        <div class="plan-name"><?= htmlspecialchars($plan['plan_name']) ?></div>
        <div class="plan-price"><sup>₹</sup><?= number_format($plan['price'], 0) ?></div>
        <div class="plan-duration">⏱ <?= (int)$plan['duration_days'] ?> Days Full Access</div>
        <ul class="plan-features">
          <li>All Premium Articles</li>
          <li>Daily E-Paper (All Cities)</li>
          <li>PDF Download</li>
          <li>Ad-Free Experience</li>
          <li>Archive Access</li>
          <?php if ((int)$plan['duration_days'] >= 180): ?>
          <li>Priority Support</li>
          <?php endif; ?>
        </ul>
        <?php if ($currentSub): ?>
          <button class="btn-buy" disabled style="opacity:.55;cursor:not-allowed;">✅ Already Subscribed</button>
        <?php else: ?>
          <form method="POST" onsubmit="return confirm('Subscribe to <?= htmlspecialchars(addslashes($plan['plan_name'])) ?> for ₹<?= number_format($plan['price'], 0) ?>?')">
            <input type="hidden" name="plan_id" value="<?= (int)$plan['id'] ?>">
            <button type="submit" name="buy_plan" class="btn-buy">
              Subscribe for ₹<?= number_format($plan['price'], 0) ?>
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div style="background:#f9f9f9;border:1px dashed #ddd;border-radius:8px;padding:24px;text-align:center;color:#aaa;">
    No subscription plans available yet. Add plans from <strong>Admin → Subscriptions</strong>.
  </div>
  <?php endif; ?>

  <div style="background:#FFF8E1;border:1px solid #FFD54F;border-radius:8px;padding:14px 18px;font-size:13px;color:#666;margin-top:16px;">
    💡 <strong>Note:</strong> Payment is currently simulated. To accept real payments, integrate
    <a href="https://razorpay.com" target="_blank" style="color:#E8520A;">Razorpay</a> or PayU gateway in <code>subscription.php</code>.
  </div>

</div>

<?php include "footer.php"; ?>
