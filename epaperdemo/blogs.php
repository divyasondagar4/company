<?php
session_start();
require "db.php";
include "user-header.php";

// Fetch blogs (adjust query to your table)
$blogs = [];
$result = mysqli_query($conn, "SELECT * FROM blogs ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $blogs[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs - Yarnify</title>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Manrope', sans-serif;
            background: linear-gradient(135deg, var(--cream) 0%, var(--warm-white) 50%, #E3DDD1 100%);
            color: var(--charcoal);
            min-height: 100vh;
        }

        .container { max-width: 1100px; margin: 0 auto; padding: 60px 30px; }

        .page-title {
            font-family: 'Fraunces', serif;
            font-size: 2.8rem; font-weight: 300;
            color: var(--sage-dark); text-align: center;
            margin-bottom: 50px;
        }

        /* Blog Cards */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .blog-card {
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .blog-card img {
            width: 100%; height: 200px; object-fit: cover;
        }

        .blog-card-body { padding: 22px; }

        .blog-card-title {
            font-family: 'Fraunces', serif;
            font-size: 1.2rem; font-weight: 600;
            color: var(--charcoal); margin-bottom: 10px;
        }

        .blog-card-excerpt {
            font-size: 0.9rem; color: #666;
            line-height: 1.6; margin-bottom: 16px;
        }

        .blog-card-date { font-size: 0.78rem; color: #aaa; }

        .read-more {
            display: inline-block; margin-top: 14px;
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--sage), var(--sage-dark));
            color: white; border-radius: 8px;
            font-size: 0.85rem; font-weight: 600;
            text-decoration: none; transition: all 0.3s;
        }
        .read-more:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(107,132,96,0.3); }

        /* ══════════════════════════════
           SUBSCRIPTION POPUP OVERLAY
        ══════════════════════════════ */
        #subOverlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        #subOverlay.show { display: flex; }

        .sub-popup {
            background: linear-gradient(135deg, #fff 0%, #F5F0E8 100%);
            border-radius: 24px;
            padding: 44px 40px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1);
            position: relative;
        }

        .sub-popup .popup-icon { font-size: 3rem; margin-bottom: 14px; }

        .sub-popup h2 {
            font-family: 'Fraunces', serif;
            font-size: 1.8rem; font-weight: 600;
            color: var(--sage-dark); margin-bottom: 10px;
        }

        .sub-popup p {
            font-size: 0.95rem; color: #666;
            line-height: 1.6; margin-bottom: 28px;
        }

        .popup-benefits {
            list-style: none;
            text-align: left;
            background: rgba(139,163,120,0.08);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 26px;
        }

        .popup-benefits li {
            font-size: 0.88rem; color: var(--charcoal);
            padding: 5px 0; display: flex; align-items: center; gap: 8px;
        }

        .popup-benefits li::before { content: '✅'; font-size: 0.8rem; }

        .popup-btn {
            display: block; width: 100%;
            padding: 14px 20px; margin-bottom: 12px;
            border: none; border-radius: 12px;
            font-family: 'Manrope', sans-serif;
            font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: all 0.3s;
            text-decoration: none;
        }

        .popup-btn-login {
            background: linear-gradient(135deg, var(--sage), var(--sage-dark));
            color: white; box-shadow: 0 4px 16px rgba(107,132,96,0.3);
        }
        .popup-btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(107,132,96,0.4); }

        .popup-btn-later {
            background: transparent; color: #aaa;
            font-size: 0.85rem; font-weight: 500;
            border: none; cursor: pointer; padding: 8px;
        }
        .popup-btn-later:hover { color: #888; }

        @keyframes fadeIn { from{opacity:0} to{opacity:1} }
        @keyframes popIn  { from{opacity:0;transform:scale(0.85)} to{opacity:1;transform:scale(1)} }

        /* Responsive */
        @media (max-width: 768px) {
            .container { padding: 40px 16px; }
            .page-title { font-size: 2rem; }
            .blog-grid { grid-template-columns: 1fr; gap: 20px; }
            .sub-popup { padding: 32px 24px; }
            .sub-popup h2 { font-size: 1.5rem; }
        }
        @media (max-width: 480px) {
            .page-title { font-size: 1.7rem; }
            .sub-popup { padding: 26px 18px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="page-title">📰 Our Blog</h1>

    <div class="blog-grid" id="blogGrid">
        <?php if (empty($blogs)): ?>
            <!-- Sample cards if no blogs in DB yet -->
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="blog-card">
                <img src="https://picsum.photos/seed/blog<?= $i ?>/400/200" alt="Blog <?= $i ?>">
                <div class="blog-card-body">
                    <div class="blog-card-title">Crochet Tips & Tricks #<?= $i ?></div>
                    <div class="blog-card-excerpt">Discover beautiful patterns and techniques to elevate your crochet journey with our handpicked guides...</div>
                    <div class="blog-card-date">February <?= $i + 10 ?>, 2026</div>
                    <a href="#" class="read-more">Read More →</a>
                </div>
            </div>
            <?php endfor; ?>
        <?php else: ?>
            <?php foreach ($blogs as $blog): ?>
            <div class="blog-card">
                <?php if (!empty($blog['image'])): ?>
                    <img src="images/<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
                <?php endif; ?>
                <div class="blog-card-body">
                    <div class="blog-card-title"><?= htmlspecialchars($blog['title']) ?></div>
                    <div class="blog-card-excerpt"><?= htmlspecialchars(substr($blog['content'] ?? '', 0, 120)) ?>...</div>
                    <div class="blog-card-date"><?= date('F d, Y', strtotime($blog['created_at'])) ?></div>
                    <a href="blog_detail.php?id=<?= $blog['id'] ?>" class="read-more">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ══ SUBSCRIPTION POPUP ══ -->
<div id="subOverlay">
    <div class="sub-popup">
        <div class="popup-icon">🎉</div>
        <h2>Enjoying Our Content?</h2>
        <p>Subscribe to get full access to all blogs, exclusive crochet patterns, and our monthly e-paper!</p>

        <ul class="popup-benefits">
            <li>Unlimited blog access</li>
            <li>Monthly e-paper download</li>
            <li>Exclusive crochet patterns</li>
            <li>Early access to new products</li>
        </ul>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Not logged in → go to login -->
            <a href="login.php?redirect=subscribe.php" class="popup-btn popup-btn-login">
                🔐 Login to Subscribe
            </a>
        <?php else: ?>
            <!-- Logged in → go to subscribe -->
            <a href="subscribe.php" class="popup-btn popup-btn-login">
                ✨ Subscribe Now — It's Free!
            </a>
        <?php endif; ?>

        <button class="popup-btn-later" onclick="closePopup()">Maybe Later</button>
    </div>
</div>

<script>
    // ── Show popup after user scrolls past 2 blog cards ──
    let popupShown = sessionStorage.getItem('subPopupShown');
    const overlay  = document.getElementById('subOverlay');

    <?php if (!isset($_SESSION['user_id']) || !isSubscribed()): ?>
    // Only show if guest OR logged-in but not subscribed

    if (!popupShown) {
        const cards = document.querySelectorAll('.blog-card');
        const triggerCard = cards[1]; // 2nd card (index 1)

        if (triggerCard) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !popupShown) {
                        setTimeout(() => {
                            overlay.classList.add('show');
                            sessionStorage.setItem('subPopupShown', '1');
                            popupShown = '1';
                        }, 600); // small delay for smooth UX
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(triggerCard);
        }
    }
    <?php endif; ?>

    function closePopup() {
        overlay.classList.remove('show');
        sessionStorage.setItem('subPopupShown', '1');
    }

    // Close on overlay background click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closePopup();
    });
</script>

<?php
// Helper: check if current user is subscribed
function isSubscribed() {
    global $conn;
    if (!isset($_SESSION['user_id'])) return false;
    $uid = $_SESSION['user_id'];
    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT id FROM subscriptions WHERE user_id='$uid' AND status='active' LIMIT 1"
    ));
    return !empty($r);
}
?>

<?php include "footer.php"; ?>
</body>
</html>

i want one new website all page home page than login btn register and download epaper bueetton user vist site ok scroll blogs after 2 blog set login page open  after login when user click epaper download subscription page open plan slect use subscrption satuts activate than on dash  hello user also epaper download and also scroll full with bootstrap ui