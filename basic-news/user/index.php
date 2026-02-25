<?php include "header.php"; ?>

<?php
// ============================================================
// FETCH ALL DATA FROM DB
// ============================================================

// Breaking News (ticker)
$breakingQ = mysqli_query($conn, "
    SELECT title, slug FROM news 
    WHERE is_breaking=1 AND status=1 
    ORDER BY published_at DESC LIMIT 10
");
$breakingNews = [];
while($b = mysqli_fetch_assoc($breakingQ)) $breakingNews[] = $b;

// Top/Slider News
$sliderQ = mysqli_query($conn, "
    SELECT news.*, categories.category_name 
    FROM news 
    LEFT JOIN categories ON news.category_id = categories.id
    WHERE news.is_top=1 AND news.status=1 
    ORDER BY news.published_at DESC LIMIT 5
");
$sliderNews = [];
while($s = mysqli_fetch_assoc($sliderQ)) $sliderNews[] = $s;

// Latest News (8 cards)
$latestQ = mysqli_query($conn, "
    SELECT news.*, categories.category_name 
    FROM news 
    LEFT JOIN categories ON news.category_id = categories.id
    WHERE news.status=1 
    ORDER BY news.published_at DESC LIMIT 8
");
$latestNews = [];
while($n = mysqli_fetch_assoc($latestQ)) $latestNews[] = $n;

// Trending News (sidebar)
$trendingQ = mysqli_query($conn, "
    SELECT news.*, categories.category_name 
    FROM news 
    LEFT JOIN categories ON news.category_id = categories.id
    WHERE news.is_trending=1 AND news.status=1 
    ORDER BY news.view_count DESC LIMIT 6
");
$trendingNews = [];
while($t = mysqli_fetch_assoc($trendingQ)) $trendingNews[] = $t;

// Category wise news sections
$catSectionsQ = mysqli_query($conn, "SELECT * FROM categories WHERE status=1 LIMIT 5");
$catSections = [];
while($c = mysqli_fetch_assoc($catSectionsQ)){
    $newsQ = mysqli_query($conn,"
        SELECT news.*, categories.category_name 
        FROM news 
        LEFT JOIN categories ON news.category_id = categories.id
        WHERE news.category_id='{$c['id']}' AND news.status=1 
        ORDER BY news.published_at DESC LIMIT 4
    ");
    $items = [];
    while($item = mysqli_fetch_assoc($newsQ)) $items[] = $item;
    if(count($items) > 0){
        $c['items'] = $items;
        $catSections[] = $c;
    }
}

// Videos
$videosQ = mysqli_query($conn,"
    SELECT * FROM videos WHERE status=1 AND is_reel=0 
    ORDER BY created_at DESC LIMIT 3
");
$videos = [];
while($v = mysqli_fetch_assoc($videosQ)) $videos[] = $v;

// Reels
$reelsQ = mysqli_query($conn,"
    SELECT * FROM videos WHERE status=1 AND is_reel=1 
    ORDER BY created_at DESC LIMIT 4
");
$reels = [];
while($r = mysqli_fetch_assoc($reelsQ)) $reels[] = $r;

// Subscription Plans
$plansQ = mysqli_query($conn,"SELECT * FROM subscriptions WHERE status=1 LIMIT 3");
$plans = [];
while($p = mysqli_fetch_assoc($plansQ)) $plans[] = $p;

// Helper: thumbnail path
function thumb($filename, $size='news'){
    if(!empty($filename) && file_exists("../admin/uploads/{$size}/{$filename}")){
        return "../admin/uploads/{$size}/{$filename}";
    }
    return "https://placehold.co/400x250/FF6B00/ffffff?text=News";
}
function thumbReel($filename){
    if(!empty($filename) && file_exists("../admin/uploads/videos/{$filename}")){
        return "../admin/uploads/videos/{$filename}";
    }
    return "https://placehold.co/250x400/FF6B00/ffffff?text=Reel";
}
function timeAgo($datetime){
    $time = strtotime($datetime);
    $diff = time() - $time;
    if($diff < 60) return $diff." sec ago";
    if($diff < 3600) return round($diff/60)." min ago";
    if($diff < 86400) return round($diff/3600)." hrs ago";
    return date("d M Y", $time);
}
?>

<style>
/* ============================================================
   DIVYA BHASKAR INSPIRED — ORANGE / WHITE PROFESSIONAL NEWS
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Mukta:wght@400;600;700&display=swap');

:root {
    --primary: #E8520A;
    --primary-dark: #C43D00;
    --primary-light: #FF7A35;
    --accent: #FFF3EC;
    --dark: #1A1A1A;
    --text: #2C2C2C;
    --muted: #777;
    --border: #E8E8E8;
    --white: #FFFFFF;
    --bg: #F5F5F5;
    --badge-breaking: #D32F2F;
    --badge-trending: #1976D2;
    --badge-premium: #FFA000;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Mukta', sans-serif;
    background: var(--bg);
    color: var(--text);
    font-size: 15px;
    line-height: 1.6;
}

a { text-decoration: none; color: inherit; }
img { max-width: 100%; display: block; }

/* ---- BREAKING TICKER ---- */
.breaking-bar {
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    overflow: hidden;
    font-size: 13px;
    font-weight: 600;
    height: 36px;
}
.breaking-label {
    background: var(--primary-dark);
    padding: 0 18px;
    height: 100%;
    display: flex;
    align-items: center;
    white-space: nowrap;
    letter-spacing: 1px;
    font-size: 12px;
    text-transform: uppercase;
    flex-shrink: 0;
    gap: 6px;
}
.ticker-wrap {
    flex: 1;
    overflow: hidden;
    position: relative;
    height: 100%;
    display: flex;
    align-items: center;
}
.ticker-content {
    display: flex;
    gap: 60px;
    animation: ticker 30s linear infinite;
    white-space: nowrap;
    align-items: center;
}
.ticker-content a {
    color: white;
    opacity: .92;
    transition: opacity .2s;
}
.ticker-content a:hover { opacity: 1; text-decoration: underline; }
.ticker-sep { color: var(--primary-light); font-size: 18px; }
@keyframes ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* ---- DATE BAR ---- */
.date-bar {
    background: var(--white);
    border-bottom: 1px solid var(--border);
    padding: 6px 0;
    font-size: 12px;
    color: var(--muted);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ---- SECTION TITLE ---- */
.section-title {
    font-family: 'Noto Serif', serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
    border-left: 4px solid var(--primary);
    padding-left: 12px;
    margin-bottom: 18px;
    line-height: 1.2;
}
.section-title span { color: var(--primary); }

/* ---- BADGE ---- */
.badge-cat {
    display: inline-block;
    background: var(--primary);
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
}
.badge-breaking { background: var(--badge-breaking); }
.badge-premium  { background: var(--badge-premium); color: #333; }
.badge-trending { background: var(--badge-trending); }

/* ---- SLIDER ---- */
.hero-slider {
    position: relative;
    background: #111;
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 16/7;
}
.hero-slider img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: .85;
    transition: opacity .4s;
}
.hero-slider .carousel-item.active img { opacity: .9; }
.hero-caption {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,.85) 80%);
    padding: 30px 20px 18px;
    color: white;
}
.hero-caption h2 {
    font-family: 'Noto Serif', serif;
    font-size: clamp(15px, 2vw, 22px);
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 6px;
}
.hero-caption p { font-size: 13px; opacity: .85; }
.carousel-control-prev, .carousel-control-next {
    width: 40px;
}

/* ---- NEWS CARD ---- */
.news-card {
    background: white;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid var(--border);
    transition: transform .2s, box-shadow .2s;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.news-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.1);
}
.news-card img {
    width: 100%;
    height: 170px;
    object-fit: cover;
}
.news-card-body {
    padding: 12px 14px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.news-card-body h6 {
    font-family: 'Noto Serif', serif;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.4;
    color: var(--dark);
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.news-card-body h6:hover { color: var(--primary); }
.news-card-body p {
    font-size: 12px;
    color: var(--muted);
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.news-meta {
    font-size: 11px;
    color: #aaa;
    margin-top: 8px;
    display: flex;
    gap: 10px;
    align-items: center;
}
.news-meta i { color: var(--primary); }
.read-more {
    color: var(--primary);
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.read-more:hover { color: var(--primary-dark); }

/* ---- TRENDING SIDEBAR ---- */
.trending-item {
    display: flex;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
    align-items: flex-start;
}
.trending-item:last-child { border-bottom: none; }
.trending-num {
    font-size: 22px;
    font-weight: 700;
    color: var(--border);
    line-height: 1;
    min-width: 28px;
    font-family: 'Noto Serif', serif;
}
.trending-item:hover .trending-num { color: var(--primary); }
.trending-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.trending-title:hover { color: var(--primary); }

/* ---- CATEGORY SECTION ---- */
.cat-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid var(--border);
    margin-bottom: 30px;
}
.cat-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary);
}
.cat-section-header a {
    font-size: 12px;
    color: var(--primary);
    font-weight: 600;
}
.cat-feature {
    background: #111;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
    height: 220px;
}
.cat-feature img {
    width: 100%; height: 100%; object-fit: cover; opacity: .75;
}
.cat-feature-caption {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,.8));
    padding: 20px 12px 12px;
    color: white;
}
.cat-feature-caption h6 {
    font-family: 'Noto Serif', serif;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.35;
}
.cat-small-item {
    display: flex;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px dashed var(--border);
    align-items: flex-start;
}
.cat-small-item:last-child { border-bottom: none; padding-bottom: 0; }
.cat-small-img {
    width: 70px; height: 55px;
    object-fit: cover; border-radius: 4px; flex-shrink: 0;
}
.cat-small-title {
    font-size: 13px; font-weight: 600; line-height: 1.35;
    color: var(--dark);
    display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.cat-small-title:hover { color: var(--primary); }
.cat-small-meta { font-size: 11px; color: #aaa; margin-top: 4px; }

/* ---- VIDEOS ---- */
.video-card {
    background: white;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid var(--border);
    transition: transform .2s;
}
.video-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
.video-thumb-wrap {
    position: relative;
    background: #000;
    aspect-ratio: 16/9;
    overflow: hidden;
}
.video-thumb-wrap img {
    width: 100%; height: 100%; object-fit: cover; opacity: .8;
    transition: opacity .3s;
}
.video-card:hover .video-thumb-wrap img { opacity: .95; }
.play-btn {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 50px; height: 50px;
    background: rgba(232,82,10,.9);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 18px; padding-left: 4px;
    transition: background .2s, transform .2s;
}
.video-card:hover .play-btn { background: var(--primary); transform: translate(-50%, -50%) scale(1.1); }
.video-card-body { padding: 12px 14px; }
.video-card-body h6 {
    font-size: 14px; font-weight: 600; line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.video-card-body h6:hover { color: var(--primary); }

/* ---- REELS ---- */
.reel-card {
    background: #111;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    aspect-ratio: 9/16;
    cursor: pointer;
    transition: transform .2s;
}
.reel-card:hover { transform: scale(1.02); }
.reel-card img { width: 100%; height: 100%; object-fit: cover; opacity: .7; }
.reel-overlay {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,.85));
    padding: 20px 10px 12px;
    color: white;
}
.reel-overlay h6 { font-size: 12px; font-weight: 600; line-height: 1.3; }
.reel-play {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 44px; height: 44px;
    background: rgba(255,255,255,.2);
    border: 2px solid rgba(255,255,255,.6);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 16px; padding-left: 3px;
    backdrop-filter: blur(4px);
}

/* ---- SUBSCRIPTION ---- */
.plan-card {
    background: white;
    border-radius: 10px;
    border: 2px solid var(--border);
    padding: 28px 20px;
    text-align: center;
    transition: border-color .2s, transform .2s;
    position: relative;
    overflow: hidden;
}
.plan-card.featured {
    border-color: var(--primary);
    transform: scale(1.04);
    box-shadow: 0 10px 30px rgba(232,82,10,.15);
}
.plan-card.featured::before {
    content: 'POPULAR';
    position: absolute; top: 12px; right: -22px;
    background: var(--primary); color: white;
    font-size: 10px; font-weight: 700; letter-spacing: 1px;
    padding: 4px 30px;
    transform: rotate(35deg);
}
.plan-price {
    font-size: 36px; font-weight: 700; color: var(--primary);
    font-family: 'Noto Serif', serif;
}
.plan-price small { font-size: 14px; color: var(--muted); font-weight: 400; font-family: 'Mukta', sans-serif; }
.plan-name { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.plan-days { font-size: 13px; color: var(--muted); margin-bottom: 16px; }
.plan-desc { font-size: 13px; color: #666; margin-bottom: 20px; }
.btn-subscribe {
    background: var(--primary);
    color: white; border: none;
    padding: 10px 28px; border-radius: 4px;
    font-weight: 600; font-size: 14px;
    cursor: pointer; transition: background .2s;
    display: inline-block; width: 100%;
}
.btn-subscribe:hover { background: var(--primary-dark); color: white; }
.plan-card:not(.featured) .btn-subscribe {
    background: white; color: var(--primary); border: 2px solid var(--primary);
}
.plan-card:not(.featured) .btn-subscribe:hover { background: var(--primary); color: white; }

/* ---- FOOTER ---- */
.site-footer {
    background: #1A1A1A;
    color: #ccc;
    padding: 40px 0 20px;
    margin-top: 50px;
}
.footer-logo { font-size: 28px; font-weight: 700; color: var(--primary); font-family: 'Noto Serif', serif; }
.footer-desc { font-size: 13px; color: #999; margin-top: 8px; line-height: 1.6; }
.footer-title { font-size: 14px; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; border-bottom: 2px solid var(--primary); padding-bottom: 6px; display: inline-block; }
.footer-links { list-style: none; }
.footer-links li { margin-bottom: 7px; }
.footer-links a { font-size: 13px; color: #aaa; transition: color .2s; }
.footer-links a:hover { color: var(--primary); }
.footer-bottom { border-top: 1px solid #333; margin-top: 30px; padding-top: 16px; font-size: 12px; color: #666; text-align: center; }

/* ---- SIDEBAR WIDGET ---- */
.widget-box {
    background: white;
    border-radius: 6px;
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 24px;
}
.widget-header {
    background: var(--primary);
    color: white;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.widget-body { padding: 14px 16px; }

/* ---- AD BANNER ---- */
.ad-banner {
    background: linear-gradient(135deg, #fff3ec, #ffe0cc);
    border: 1px dashed var(--primary-light);
    border-radius: 6px;
    text-align: center;
    padding: 20px;
    color: var(--muted);
    font-size: 12px;
    margin-bottom: 24px;
}
.ad-banner strong { display: block; color: var(--primary); font-size: 14px; margin-bottom: 4px; }

/* ---- E-PAPER ---- */
.epaper-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border-radius: 8px;
    padding: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.epaper-card h5 { font-family: 'Noto Serif', serif; font-size: 20px; margin-bottom: 4px; }
.epaper-card p { font-size: 13px; opacity: .85; margin-bottom: 12px; }
.btn-epaper {
    background: white; color: var(--primary);
    border: none; padding: 10px 24px;
    border-radius: 4px; font-weight: 700;
    font-size: 14px; cursor: pointer;
    transition: transform .2s;
}
.btn-epaper:hover { transform: scale(1.04); }

/* Responsive */
@media(max-width: 768px){
    .hero-caption h2 { font-size: 14px; }
    .cat-feature { height: 180px; }
    .plan-card.featured { transform: scale(1); }
}
</style>

<!-- ============================================================
     BREAKING NEWS TICKER
============================================================ -->
<div class="breaking-bar">
    <div class="breaking-label">
        <span>⚡</span> BREAKING
    </div>
    <div class="ticker-wrap">
        <div class="ticker-content" id="ticker">
            <?php
            $tickerItems = !empty($breakingNews) ? $breakingNews : [
                ['title'=>'Stay tuned for latest updates','slug'=>'#'],
                ['title'=>'Welcome to News Portal','slug'=>'#']
            ];
            // Duplicate for seamless loop
            for($i=0;$i<2;$i++){
                foreach($tickerItems as $idx => $bi){
                    echo '<a href="news_detail.php?slug='.htmlspecialchars($bi['slug']).'">'.htmlspecialchars($bi['title']).'</a>';
                    echo '<span class="ticker-sep">|</span>';
                }
            }
            ?>
        </div>
    </div>
</div>

<!-- DATE BAR -->
<div class="date-bar">
    <div class="container d-flex justify-content-between">
        <span>📅 <?= date("l, d F Y") ?></span>
        <span>🌡️ Ahmedabad Edition</span>
    </div>
</div>

<!-- ============================================================
     MAIN CONTENT
============================================================ -->
<div class="container mt-3">

    <!-- ---- HERO: SLIDER + TRENDING SIDEBAR ---- -->
    <div class="row g-3 mb-4">

        <!-- Slider -->
        <div class="col-lg-8">
            <?php if(!empty($sliderNews)): ?>
            <div id="heroSlider" class="hero-slider carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                <?php foreach($sliderNews as $idx => $s): ?>
                    <div class="carousel-item <?= $idx===0?'active':'' ?>">
                        <img src="<?= thumb($s['thumbnail']) ?>" alt="<?= htmlspecialchars($s['title']) ?>">
                        <div class="hero-caption">
                            <span class="badge-cat"><?= htmlspecialchars($s['category_name']) ?></span>
                            <h2><a href="news_detail.php?slug=<?= $s['slug'] ?>" class="text-white">
                                <?= htmlspecialchars($s['title']) ?>
                            </a></h2>
                            <p><?= mb_substr(htmlspecialchars($s['short_description']),0,100).'...' ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" data-bs-target="#heroSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" data-bs-target="#heroSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <!-- Indicators -->
                <div class="carousel-indicators" style="margin-bottom:0">
                    <?php foreach($sliderNews as $idx => $s): ?>
                    <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $idx ?>"
                        <?= $idx===0?'class="active"':'' ?>></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="hero-slider d-flex align-items-center justify-content-center bg-secondary text-white">
                <p>No slider news found. Mark news as "Top" in admin.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Trending Sidebar -->
        <div class="col-lg-4">
            <div class="widget-box h-100">
                <div class="widget-header">🔥 Trending Now</div>
                <div class="widget-body">
                    <?php if(!empty($trendingNews)):
                        foreach($trendingNews as $idx => $t): ?>
                        <a href="news_detail.php?slug=<?= $t['slug'] ?>" class="trending-item d-flex">
                            <div class="trending-num"><?= str_pad($idx+1,2,'0',STR_PAD_LEFT) ?></div>
                            <div>
                                <div class="trending-title"><?= htmlspecialchars($t['title']) ?></div>
                                <div class="news-meta">
                                    <span>👁️ <?= number_format($t['view_count']) ?></span>
                                    <span><?= timeAgo($t['published_at']) ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach;
                    else: ?>
                        <p class="text-muted small py-3">No trending news. Mark news as trending in admin.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- ---- AD BANNER ---- -->
    <div class="ad-banner mb-4">
        <strong>Advertisement</strong>
        728 × 90 — Ad Space Available
    </div>

    <!-- ---- LATEST NEWS ---- -->
    <div class="row g-0 align-items-center mb-3">
        <div class="col"><h2 class="section-title">Latest <span>News</span></h2></div>
        <div class="col-auto"><a href="category.php" class="read-more">See All →</a></div>
    </div>

    <div class="row g-3 mb-4">
        <?php if(!empty($latestNews)):
            foreach($latestNews as $n): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="news-card">
                    <a href="news_detail.php?slug=<?= $n['slug'] ?>">
                        <img src="<?= thumb($n['thumbnail']) ?>" alt="<?= htmlspecialchars($n['title']) ?>">
                    </a>
                    <div class="news-card-body">
                        <span class="badge-cat <?= $n['is_breaking']?'badge-breaking':'' ?>">
                            <?= htmlspecialchars($n['category_name']) ?>
                        </span>
                        <?php if($n['is_premium']): ?>
                        <span class="badge-cat badge-premium">Premium</span>
                        <?php endif; ?>
                        <a href="news_detail.php?slug=<?= $n['slug'] ?>">
                            <h6><?= htmlspecialchars($n['title']) ?></h6>
                        </a>
                        <p><?= htmlspecialchars($n['short_description']) ?></p>
                        <div class="news-meta">
                            <span>🕐 <?= timeAgo($n['published_at']) ?></span>
                            <span>👁️ <?= $n['view_count'] ?></span>
                        </div>
                        <a href="news_detail.php?slug=<?= $n['slug'] ?>" class="read-more">Read More →</a>
                    </div>
                </div>
            </div>
        <?php endforeach;
        else: ?>
            <div class="col-12 text-center text-muted py-5">
                <p>No news articles found. Add news from admin panel.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ---- CATEGORY WISE SECTIONS ---- -->
    <?php foreach($catSections as $cat): ?>
    <div class="cat-section">
        <div class="cat-section-header">
            <h2 class="section-title mb-0"><?= htmlspecialchars($cat['category_name']) ?></h2>
            <a href="category.php?id=<?= $cat['id'] ?>">More News →</a>
        </div>
        <div class="row g-3">
            <!-- Feature (first item) -->
            <?php $first = $cat['items'][0]; ?>
            <div class="col-md-5">
                <a href="news_detail.php?slug=<?= $first['slug'] ?>">
                    <div class="cat-feature">
                        <img src="<?= thumb($first['thumbnail']) ?>" alt="">
                        <div class="cat-feature-caption">
                            <span class="badge-cat" style="font-size:10px"><?= htmlspecialchars($cat['category_name']) ?></span>
                            <h6><?= htmlspecialchars($first['title']) ?></h6>
                            <div class="news-meta" style="color:#ddd">
                                <span><?= timeAgo($first['published_at']) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Other 3 items -->
            <div class="col-md-7">
                <?php for($i=1;$i<min(4,count($cat['items']));$i++):
                    $item = $cat['items'][$i]; ?>
                <a href="news_detail.php?slug=<?= $item['slug'] ?>">
                    <div class="cat-small-item">
                        <img class="cat-small-img" src="<?= thumb($item['thumbnail']) ?>" alt="">
                        <div>
                            <div class="cat-small-title"><?= htmlspecialchars($item['title']) ?></div>
                            <div class="cat-small-meta">🕐 <?= timeAgo($item['published_at']) ?></div>
                        </div>
                    </div>
                </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- ---- VIDEOS ---- -->
    <?php if(!empty($videos)): ?>
    <div class="row g-0 align-items-center mb-3">
        <div class="col"><h2 class="section-title">Latest <span>Videos</span></h2></div>
        <div class="col-auto"><a href="videos.php" class="read-more">All Videos →</a></div>
    </div>
    <div class="row g-3 mb-4">
        <?php foreach($videos as $v): ?>
        <div class="col-md-4">
            <div class="video-card">
                <a href="video_detail.php?id=<?= $v['id'] ?>">
                    <div class="video-thumb-wrap">
                        <img src="<?= thumbReel($v['thumbnail']) ?>" alt="">
                        <div class="play-btn">▶</div>
                    </div>
                </a>
                <div class="video-card-body">
                    <a href="video_detail.php?id=<?= $v['id'] ?>">
                        <h6><?= htmlspecialchars($v['title']) ?></h6>
                    </a>
                    <div class="news-meta mt-1">
                        <span>👁️ <?= $v['view_count'] ?> views</span>
                        <span>🕐 <?= timeAgo($v['created_at']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ---- REELS ---- -->
    <?php if(!empty($reels)): ?>
    <h2 class="section-title mb-3">📱 <span>Reels</span></h2>
    <div class="row g-3 mb-4">
        <?php foreach($reels as $r): ?>
        <div class="col-6 col-md-3">
            <a href="video_detail.php?id=<?= $r['id'] ?>">
                <div class="reel-card">
                    <img src="<?= thumbReel($r['thumbnail']) ?>" alt="">
                    <div class="reel-play">▶</div>
                    <div class="reel-overlay">
                        <h6><?= htmlspecialchars($r['title']) ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ---- E-PAPER ---- -->
    <div class="epaper-card mb-4">
        <div>
            <h5>📰 Today's E-Paper</h5>
            <p><?= date("d F Y") ?> — Ahmedabad Edition</p>
            <a href="epaper.php" class="btn-epaper">Read E-Paper</a>
        </div>
        <div style="font-size:80px; opacity:.15">📰</div>
    </div>

    <!-- ---- SUBSCRIPTION PLANS ---- -->
    <?php if(!empty($plans) && !isset($_SESSION['user_id'])): ?>
    <div class="text-center mb-4">
        <h2 class="section-title d-inline-block">Premium <span>Subscription</span></h2>
        <p class="text-muted small mt-1">Unlock premium news, exclusive content & ad-free experience</p>
    </div>
    <div class="row g-3 justify-content-center mb-5">
        <?php foreach($plans as $idx => $plan): ?>
        <div class="col-md-4">
            <div class="plan-card <?= $idx===0?'featured':'' ?>">
                <div class="plan-name"><?= htmlspecialchars($plan['plan_name']) ?></div>
                <div class="plan-price">₹<?= number_format($plan['price'],0) ?><small>/plan</small></div>
                <div class="plan-days">⏱️ <?= $plan['duration_days'] ?> Days Access</div>
                <div class="plan-desc"><?= htmlspecialchars($plan['description']) ?></div>
                <a href="subscription.php?plan=<?= $plan['id'] ?>" class="btn-subscribe">
                    <?= isset($_SESSION['user_id']) ? 'Subscribe Now' : 'Login to Subscribe' ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div><!-- /container -->

<!-- ============================================================
     FOOTER
============================================================ -->
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-logo">📰 News Portal</div>
                <p class="footer-desc">Your trusted source for breaking news, politics, sports, entertainment and more. Powered by local journalists across Gujarat.</p>
            </div>
            <div class="col-md-2">
                <div class="footer-title">News</div>
                <ul class="footer-links">
                    <li><a href="category.php">Latest</a></li>
                    <li><a href="category.php?type=national">National</a></li>
                    <li><a href="category.php?type=international">World</a></li>
                    <li><a href="category.php?type=state">Gujarat</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <div class="footer-title">Sections</div>
                <ul class="footer-links">
                    <li><a href="videos.php">Videos</a></li>
                    <li><a href="reels.php">Reels</a></li>
                    <li><a href="epaper.php">E-Paper</a></li>
                    <li><a href="subscription.php">Premium</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <div class="footer-title">Account</div>
                <ul class="footer-links">
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="bookmark.php">Bookmarks</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                    <?php else: ?>
                    <li><a href="../login.php">Login</a></li>
                    <li><a href="../register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-2">
                <div class="footer-title">Company</div>
                <ul class="footer-links">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> News Portal. All rights reserved. Built with ❤️ in Gujarat.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>