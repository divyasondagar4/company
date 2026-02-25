<?php
session_start();
require "db.php";

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=subscribe.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error   = '';

// Check if already subscribed
$existing = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM subscriptions WHERE user_id='$user_id' LIMIT 1"
));

// Handle subscribe form
if (isset($_POST['subscribe'])) {
    if ($existing) {
        // Update to active
        mysqli_query($conn,
            "UPDATE subscriptions SET status='active', subscribed_at=NOW(),
             expires_at=DATE_ADD(NOW(), INTERVAL 1 YEAR)
             WHERE user_id='$user_id'"
        );
    } else {
        // New subscription
        mysqli_query($conn,
            "INSERT INTO subscriptions (user_id, status, subscribed_at, expires_at)
             VALUES ('$user_id', 'active', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))"
        );
    }
    $success = 'subscribed';
    // Refresh subscription status
    $existing = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM subscriptions WHERE user_id='$user_id' LIMIT 1"
    ));
}

$is_active = ($existing && $existing['status'] === 'active');

include "user-header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe - Yarnify</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@300;400;600;700&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #EDE6DB;
            --sage: #8BA378;
            --sage-dark: #6B8460;
            --sage-light: #B5C7A8;
            --pink: #D9A5A9;
            --pink-dark: #C48E92;
            --charcoal: #2D2D2D;
            --warm-white: #F5F0E8;
            --success: #1c5c2e;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Manrope', sans-serif;
            background: linear-gradient(135deg, var(--cream) 0%, var(--warm-white) 50%, #E3DDD1 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 680px;
            margin: 60px auto;
            padding: 0 20px;
            flex: 1;
        }

        .sub-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 50px 44px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.09);
            text-align: center;
        }

        .sub-icon { font-size: 3.5rem; margin-bottom: 16px; }

        .sub-title {
            font-family: 'Fraunces', serif;
            font-size: 2.2rem; font-weight: 600;
            color: var(--sage-dark); margin-bottom: 10px;
        }

        .sub-subtitle {
            font-size: 0.95rem; color: #666;
            margin-bottom: 32px; line-height: 1.6;
        }

        /* Benefits */
        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 34px;
            text-align: left;
        }

        .benefit-item {
            background: rgba(139,163,120,0.08);
            border: 1.5px solid rgba(139,163,120,0.2);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex; align-items: flex-start; gap: 10px;
        }

        .benefit-icon { font-size: 1.3rem; flex-shrink: 0; }

        .benefit-text { font-size: 0.84rem; color: var(--charcoal); font-weight: 500; }

        /* Status Badge */
        .status-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px; border-radius: 50px;
            font-size: 0.88rem; font-weight: 700;
            margin-bottom: 24px;
        }

        .status-active {
            background: rgba(28,92,46,0.1);
            color: var(--success);
            border: 1.5px solid rgba(28,92,46,0.25);
        }

        .status-inactive {
            background: rgba(192,57,43,0.08);
            color: #c0392b;
            border: 1.5px solid rgba(192,57,43,0.2);
        }

        /* Subscribe Button */
        .btn-subscribe {
            display: block; width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, var(--sage), var(--sage-dark));
            color: white; border: none; border-radius: 14px;
            font-family: 'Manrope', sans-serif;
            font-size: 1.1rem; font-weight: 700;
            cursor: pointer; transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(107,132,96,0.35);
            margin-bottom: 14px;
        }
        .btn-subscribe:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(107,132,96,0.45); }

        /* E-paper section */
        .epaper-section {
            margin-top: 36px;
            border-top: 2px solid rgba(139,163,120,0.2);
            padding-top: 32px;
        }

        .epaper-title {
            font-family: 'Fraunces', serif;
            font-size: 1.4rem; color: var(--charcoal);
            margin-bottom: 18px; display: flex; align-items: center;
            justify-content: center; gap: 10px;
        }

        .epaper-card {
            background: linear-gradient(135deg, rgba(181,199,168,0.15), rgba(217,165,169,0.1));
            border: 2px solid rgba(139,163,120,0.25);
            border-radius: 16px;
            padding: 20px 24px;
            display: flex; align-items: center;
            justify-content: space-between; gap: 16px;
            margin-bottom: 14px;
        }

        .epaper-info { text-align: left; }
        .epaper-name { font-weight: 700; font-size: 0.95rem; color: var(--charcoal); margin-bottom: 4px; }
        .epaper-meta { font-size: 0.78rem; color: #888; }

        .btn-download {
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            color: white; border: none; border-radius: 10px;
            font-family: 'Manrope', sans-serif;
            font-size: 0.88rem; font-weight: 700;
            cursor: pointer; transition: all 0.3s;
            text-decoration: none; white-space: nowrap;
            flex-shrink: 0;
        }
        .btn-download:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(196,142,146,0.4); }

        .btn-download-locked {
            padding: 10px 22px;
            background: #eee; color: #aaa;
            border: none; border-radius: 10px;
            font-size: 0.88rem; font-weight: 700;
            cursor: not-allowed; flex-shrink: 0;
        }

        /* Success toast */
        .toast {
            display: none;
            background: rgba(28,92,46,0.1);
            border: 1.5px solid rgba(28,92,46,0.3);
            color: var(--success);
            border-radius: 12px;
            padding: 14px 20px;
            font-weight: 600; font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: center;
        }
        .toast.show { display: block; }

        /* Responsive */
        @media (max-width: 600px) {
            .sub-card { padding: 34px 22px; }
            .sub-title { font-size: 1.8rem; }
            .benefits-grid { grid-template-columns: 1fr; gap: 10px; }
            .epaper-card { flex-direction: column; text-align: center; }
            .epaper-info { text-align: center; }
        }
        @media (max-width: 400px) {
            .sub-card { padding: 26px 16px; }
            .sub-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="sub-card">

        <div class="sub-icon">🎟️</div>
        <h1 class="sub-title">
            <?= $is_active ? 'You\'re Subscribed!' : 'Subscribe to Yarnify' ?>
        </h1>
        <p class="sub-subtitle">
            <?= $is_active
                ? 'Enjoy full access to all blogs, patterns, and your monthly e-paper download below.'
                : 'Get unlimited access to all our content, exclusive patterns, and monthly e-paper — completely free!' ?>
        </p>

        <!-- Status Badge -->
        <?php if ($existing): ?>
            <div class="status-badge <?= $is_active ? 'status-active' : 'status-inactive' ?>">
                <?= $is_active ? '✅ Subscription Active' : '⚠️ Subscription Inactive' ?>
                <?php if ($is_active && $existing['expires_at']): ?>
                    &nbsp;· Expires <?= date('d M Y', strtotime($existing['expires_at'])) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Success toast -->
        <?php if ($success === 'subscribed'): ?>
        <div class="toast show">🎉 Successfully subscribed! Welcome to Yarnify Premium.</div>
        <?php endif; ?>

        <!-- Benefits Grid -->
        <div class="benefits-grid">
            <div class="benefit-item">
                <span class="benefit-icon">📰</span>
                <span class="benefit-text">Unlimited blog access</span>
            </div>
            <div class="benefit-item">
                <span class="benefit-icon">📥</span>
                <span class="benefit-text">Monthly e-paper download</span>
            </div>
            <div class="benefit-item">
                <span class="benefit-icon">🧶</span>
                <span class="benefit-text">Exclusive crochet patterns</span>
            </div>
            <div class="benefit-item">
                <span class="benefit-icon">🔔</span>
                <span class="benefit-text">Early product access</span>
            </div>
        </div>

        <!-- Subscribe Button -->
        <?php if (!$is_active): ?>
        <form method="POST">
            <button type="submit" name="subscribe" class="btn-subscribe">
                🚀 Subscribe Now — Free!
            </button>
        </form>
        <?php endif; ?>

        <!-- ══ E-PAPER SECTION ══ -->
        <div class="epaper-section">
            <h2 class="epaper-title">📄 E-Paper Downloads</h2>

            <?php
            $epapers = [];
            $ep_result = mysqli_query($conn, "SELECT * FROM epapers ORDER BY uploaded_at DESC");
            while ($ep = mysqli_fetch_assoc($ep_result)) {
                $epapers[] = $ep;
            }
            ?>

            <?php if (empty($epapers)): ?>
                <p style="color:#aaa;font-size:0.9rem;">No e-papers available yet.</p>
            <?php else: ?>
                <?php foreach ($epapers as $ep): ?>
                <div class="epaper-card">
                    <div class="epaper-info">
                        <div class="epaper-name">📰 <?= htmlspecialchars($ep['title']) ?></div>
                        <div class="epaper-meta">
                            Uploaded: <?= date('d M Y', strtotime($ep['uploaded_at'])) ?>
                            &nbsp;·&nbsp;
                            <?= $ep['is_active'] ? '🟢 Available' : '🔴 Unavailable' ?>
                        </div>
                    </div>

                    <?php if ($is_active && $ep['is_active']): ?>
                        <!-- Subscribed & e-paper active → show download -->
                        <a href="epaper_download.php?id=<?= $ep['id'] ?>"
                           class="btn-download">
                            ⬇️ Download
                        </a>
                    <?php elseif (!$is_active): ?>
                        <!-- Not subscribed → locked -->
                        <span class="btn-download-locked">🔒 Subscribe</span>
                    <?php else: ?>
                        <!-- Subscribed but e-paper inactive -->
                        <span class="btn-download-locked">⏳ Coming Soon</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include "footer.php"; ?>
</body>
</html>