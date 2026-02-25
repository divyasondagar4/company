<?php
// ============================================================
//  user/news_detail.php  — Full Article Page
//  PLACE AT: basic-news/user/news_detail.php
//  Premium articles show only first ~300 chars to non-premium
// ============================================================
include "header.php";

// ---- Fetch article ----
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';
$id   = isset($_GET['id'])   ? (int)$_GET['id'] : 0;

if ($slug) {
    $q = mysqli_query($conn, "SELECT n.*, c.category_name FROM news n LEFT JOIN categories c ON n.category_id=c.id WHERE n.slug='$slug' AND n.status=1 LIMIT 1");
} else {
    $q = mysqli_query($conn, "SELECT n.*, c.category_name FROM news n LEFT JOIN categories c ON n.category_id=c.id WHERE n.id=$id AND n.status=1 LIMIT 1");
}

$news = mysqli_fetch_assoc($q);
if (!$news) { header("Location: index.php"); exit; }

// ---- Increment view ----
mysqli_query($conn, "UPDATE news SET view_count=view_count+1 WHERE id='{$news['id']}'");

// ---- Premium access check ----
$userIsPremium = false;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $pq  = mysqli_query($conn, "SELECT id FROM user_subscriptions WHERE user_id='$uid' AND payment_status='success' AND end_date>='".date('Y-m-d')."' LIMIT 1");
    $userIsPremium = mysqli_num_rows($pq) > 0;
}
$isPremiumArticle = (int)$news['is_premium'] === 1;
$canReadFull      = !$isPremiumArticle || $userIsPremium || isset($_SESSION['admin_id']);

// ---- Like toggle ----
if (isset($_POST['toggle_like']) && isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $nid = (int)$news['id'];
    $ex  = mysqli_query($conn, "SELECT id FROM likes WHERE user_id='$uid' AND news_id='$nid' LIMIT 1");
    if (mysqli_num_rows($ex)) {
        mysqli_query($conn, "DELETE FROM likes WHERE user_id='$uid' AND news_id='$nid'");
    } else {
        mysqli_query($conn, "INSERT INTO likes(user_id,news_id,created_at) VALUES('$uid','$nid',NOW())");
    }
    header("Location: ".$_SERVER['REQUEST_URI']); exit;
}

// ---- Bookmark toggle ----
if (isset($_POST['toggle_bookmark']) && isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $nid = (int)$news['id'];
    $ex  = mysqli_query($conn, "SELECT id FROM bookmarks WHERE user_id='$uid' AND news_id='$nid' LIMIT 1");
    if (mysqli_num_rows($ex)) {
        mysqli_query($conn, "DELETE FROM bookmarks WHERE user_id='$uid' AND news_id='$nid'");
    } else {
        mysqli_query($conn, "INSERT INTO bookmarks(user_id,news_id,created_at) VALUES('$uid','$nid',NOW())");
    }
    header("Location: ".$_SERVER['REQUEST_URI']); exit;
}

// ---- Comment submit ----
$commentSuccess = false;
if (isset($_POST['post_comment']) && isset($_SESSION['user_id'])) {
    $uid     = (int)$_SESSION['user_id'];
    $nid     = (int)$news['id'];
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment_text']));
    if (!empty($comment) && strlen($comment) >= 3) {
        mysqli_query($conn, "INSERT INTO comments(user_id,news_id,comment_text,status,created_at) VALUES('$uid','$nid','$comment',1,NOW())");
        $commentSuccess = true;
    }
}

// ---- Counts ----
$likeCount    = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM likes WHERE news_id='{$news['id']}'"   ))['c'];
$commentCount = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM comments WHERE news_id='{$news['id']}' AND status=1"))['c'];

$userLiked      = false;
$userBookmarked = false;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $userLiked      = (bool)mysqli_num_rows(mysqli_query($conn, "SELECT id FROM likes     WHERE user_id='$uid' AND news_id='{$news['id']}' LIMIT 1"));
    $userBookmarked = (bool)mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookmarks WHERE user_id='$uid' AND news_id='{$news['id']}' LIMIT 1"));
}

// ---- Comments list ----
$comments = [];
$cQ = mysqli_query($conn, "SELECT cm.*, u.name FROM comments cm LEFT JOIN users u ON cm.user_id=u.id WHERE cm.news_id='{$news['id']}' AND cm.status=1 ORDER BY cm.created_at DESC");
while ($c = mysqli_fetch_assoc($cQ)) $comments[] = $c;

// ---- Related news ----
$related = [];
$rQ = mysqli_query($conn, "SELECT n.*, c.category_name FROM news n LEFT JOIN categories c ON n.category_id=c.id WHERE n.category_id='{$news['category_id']}' AND n.id!='{$news['id']}' AND n.status=1 ORDER BY n.published_at DESC LIMIT 5");
while ($r = mysqli_fetch_assoc($rQ)) $related[] = $r;

// ---- Trending sidebar ----
$trending = [];
$tQ = mysqli_query($conn, "SELECT id, title, slug, thumbnail, view_count FROM news WHERE status=1 AND id!='{$news['id']}' ORDER BY view_count DESC LIMIT 5");
while ($t = mysqli_fetch_assoc($tQ)) $trending[] = $t;

// ---- Helpers ----
function imgPath($f, $type='news') {
    if (!empty($f) && file_exists("../admin/uploads/$type/$f")) return "../admin/uploads/$type/$f";
    return "https://placehold.co/800x450/E8520A/ffffff?text=News";
}
function timeAgo($d) {
    $diff = time() - strtotime($d);
    if ($diff < 60)    return $diff . 's ago';
    if ($diff < 3600)  return round($diff/60) . 'm ago';
    if ($diff < 86400) return round($diff/3600) . 'h ago';
    return date('d M Y', strtotime($d));
}

// ---- Share URLs ----
$pageUrl    = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$shareUrl   = urlencode($pageUrl);
$shareTitle = urlencode($news['title']);
?>

<style>
/* ====== DETAIL PAGE STYLES ====== */
.breadcrumb-bar{background:var(--white);border-bottom:1px solid var(--border);padding:8px 0;font-size:12px;}
.breadcrumb-bar a{color:var(--primary);}
.breadcrumb-bar a:hover{text-decoration:underline;}
.breadcrumb-sep{color:#ccc;margin:0 6px;}

/* Article */
.article-wrap{background:var(--white);border-radius:8px;border:1px solid var(--border);padding:28px 30px 30px;margin-bottom:24px;}
.article-badges{margin-bottom:10px;display:flex;flex-wrap:wrap;gap:6px;align-items:center;}
.article-title{font-family:'Noto Serif',serif;font-size:clamp(20px,2.8vw,30px);font-weight:700;line-height:1.3;color:var(--dark);margin-bottom:12px;}
.article-lead{font-size:16px;color:#555;font-style:italic;border-left:4px solid var(--primary);padding-left:16px;line-height:1.65;margin-bottom:18px;}
.article-meta-row{display:flex;flex-wrap:wrap;gap:14px;align-items:center;font-size:12.5px;color:#aaa;padding:12px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:20px;}
.article-meta-row span{display:inline-flex;align-items:center;gap:5px;}
.article-thumb{width:100%;border-radius:8px;object-fit:cover;max-height:430px;margin-bottom:22px;}

.article-content{font-size:16px;line-height:1.88;color:#333;}
.article-content p{margin-bottom:14px;}
.article-content h2,.article-content h3{font-family:'Noto Serif',serif;margin:22px 0 10px;}

/* ---- PREMIUM LOCK ---- */
.content-preview{position:relative;overflow:hidden;max-height:160px;}
.content-preview::after{content:'';position:absolute;bottom:0;left:0;right:0;height:100px;background:linear-gradient(transparent,var(--white));}

.premium-lock{
    background:linear-gradient(135deg,#FFF8E1 0%,#FFF3EC 100%);
    border:2px solid #FFCC80;border-radius:12px;
    padding:36px 28px;text-align:center;
    margin:6px 0 24px;position:relative;overflow:hidden;
}
.premium-lock::before{content:'⭐';position:absolute;right:-10px;top:-20px;font-size:100px;opacity:.07;line-height:1;}
.premium-lock-icon{font-size:44px;margin-bottom:12px;}
.premium-lock h3{font-family:'Noto Serif',serif;font-size:21px;font-weight:700;color:#BF360C;margin-bottom:8px;}
.premium-lock p{font-size:14px;color:#777;margin-bottom:22px;max-width:380px;margin-left:auto;margin-right:auto;}
.btn-unlock{
    display:inline-block;background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    color:#fff;padding:13px 36px;border-radius:8px;font-size:15px;font-weight:700;
    transition:transform .2s,box-shadow .2s;
}
.btn-unlock:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(232,82,10,.4);color:#fff;}
.lock-features{display:flex;justify-content:center;flex-wrap:wrap;gap:14px;margin-bottom:22px;}
.lock-feature{font-size:13px;color:#666;display:flex;align-items:center;gap:5px;}

/* ---- ACTION BAR ---- */
.action-bar{display:flex;flex-wrap:wrap;gap:8px;padding:16px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin:20px 0;}
.action-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;border:2px solid var(--border);background:var(--white);color:#555;transition:all .2s;text-decoration:none;}
.action-btn:hover{border-color:var(--primary);color:var(--primary);}
.action-btn.liked{background:var(--primary);color:#fff;border-color:var(--primary);}
.action-btn.bookmarked{background:#1976D2;color:#fff;border-color:#1976D2;}

/* ---- SHARE ---- */
.share-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:20px;}
.share-label{font-size:13px;font-weight:700;color:#666;}
.share-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:4px;font-size:12px;font-weight:600;color:#fff;transition:opacity .2s;}
.share-btn:hover{opacity:.82;color:#fff;}
.s-fb{background:#1877F2;}.s-tw{background:#1DA1F2;}.s-wa{background:#25D366;}.s-copy{background:#555;}

/* ---- COMMENTS ---- */
.comment-wrap{margin-top:28px;}
.comment-input{width:100%;border:2px solid var(--border);border-radius:8px;padding:13px 16px;font-family:'Mukta',sans-serif;font-size:14px;resize:vertical;min-height:88px;transition:border-color .2s;outline:none;}
.comment-input:focus{border-color:var(--primary);}
.btn-comment{background:var(--primary);color:#fff;border:none;padding:10px 26px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Mukta',sans-serif;transition:background .2s;}
.btn-comment:hover{background:var(--primary-dark);}
.comment-item{background:#F9F9F9;border-radius:8px;padding:14px 16px;margin-bottom:12px;border-left:3px solid var(--primary);}
.comment-author{font-size:13px;font-weight:700;color:var(--dark);margin-bottom:4px;}
.comment-text{font-size:14px;color:#555;line-height:1.5;}
.comment-date{font-size:11px;color:#bbb;margin-top:5px;}
.login-nudge{background:#FFF3EC;border:1px solid #FFD0B5;border-radius:7px;padding:13px 16px;font-size:13px;color:#666;margin-bottom:16px;}
.login-nudge a{color:var(--primary);font-weight:700;}

/* ---- SIDEBAR ---- */
.trending-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px dashed var(--border);align-items:flex-start;}
.trending-item:last-child{border-bottom:none;}
.trending-num{font-size:22px;font-weight:700;color:var(--border);min-width:28px;font-family:'Noto Serif',serif;line-height:1;transition:color .2s;}
.trending-item:hover .trending-num{color:var(--primary);}
.trending-title{font-size:13px;font-weight:600;color:var(--dark);line-height:1.35;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.trending-title:hover{color:var(--primary);}

.related-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px dashed var(--border);align-items:flex-start;}
.related-item:last-child{border-bottom:none;}
.related-img{width:78px;height:60px;object-fit:cover;border-radius:4px;flex-shrink:0;}
.related-title{font-size:13px;font-weight:600;color:var(--dark);line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.related-title:hover{color:var(--primary);}
.related-meta{font-size:11px;color:#bbb;margin-top:4px;}

.promo-box{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;border-radius:10px;padding:22px;text-align:center;}
.promo-box h5{font-family:'Noto Serif',serif;font-size:17px;margin-bottom:7px;}
.promo-box p{font-size:13px;opacity:.9;margin-bottom:16px;}
.btn-promo{background:#fff;color:var(--primary);padding:10px 22px;border-radius:6px;font-weight:700;font-size:14px;display:inline-block;transition:transform .2s;}
.btn-promo:hover{transform:scale(1.04);color:var(--primary);}

.success-msg{background:#f0fff4;border:1px solid #b7ebc8;color:#276749;border-radius:6px;padding:10px 14px;font-size:13px;margin-bottom:14px;}

@media(max-width:768px){.article-wrap{padding:18px 16px;}.article-title{font-size:20px;}}
</style>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="container">
    <a href="index.php">Home</a>
    <span class="breadcrumb-sep">›</span>
    <a href="category.php?id=<?= $news['category_id'] ?>"><?= htmlspecialchars($news['category_name']) ?></a>
    <span class="breadcrumb-sep">›</span>
    <span style="color:#888"><?= mb_substr(htmlspecialchars($news['title']),0,55) ?>…</span>
  </div>
</div>

<div class="container mt-3 mb-5">
<div class="row g-4">

<!-- ===================== MAIN ARTICLE ===================== -->
<div class="col-lg-8">
<div class="article-wrap">

  <!-- Badges -->
  <div class="article-badges">
    <span class="badge-cat <?= $news['is_breaking'] ? 'badge-breaking' : '' ?>"><?= htmlspecialchars($news['category_name']) ?></span>
    <?php if($news['is_breaking']): ?><span class="badge-cat badge-breaking">🔴 Breaking</span><?php endif; ?>
    <?php if($news['is_trending']): ?><span class="badge-cat badge-trending">🔥 Trending</span><?php endif; ?>
    <?php if($isPremiumArticle): ?><span class="badge-cat badge-premium">⭐ Premium</span><?php endif; ?>
    <span class="badge-cat" style="background:#607D8B"><?= ucfirst($news['news_level']) ?></span>
  </div>

  <!-- Title -->
  <h1 class="article-title"><?= htmlspecialchars($news['title']) ?></h1>

  <!-- Short desc / lead -->
  <?php if(!empty($news['short_description'])): ?>
  <p class="article-lead"><?= htmlspecialchars($news['short_description']) ?></p>
  <?php endif; ?>

  <!-- Meta row -->
  <div class="article-meta-row">
    <span>📅 <?= date('d M Y, h:i A', strtotime($news['published_at'])) ?></span>
    <span>👁️ <?= number_format($news['view_count']) ?> views</span>
    <span>❤️ <?= $likeCount ?> likes</span>
    <span>💬 <?= $commentCount ?> comments</span>
    <?php if(!empty($news['city_name'])): ?><span>📍 <?= htmlspecialchars($news['city_name']) ?></span><?php endif; ?>
  </div>

  <!-- Thumbnail -->
  <img class="article-thumb" src="<?= imgPath($news['thumbnail']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">

  <!-- ============ FULL CONTENT or PREMIUM GATE ============ -->
  <?php if($canReadFull): ?>

    <div class="article-content">
      <?= nl2br(htmlspecialchars($news['full_content'])) ?>
    </div>

    <!-- Action bar -->
    <div class="action-bar">
      <!-- Like -->
      <form method="POST" style="margin:0">
        <?php if(isset($_SESSION['user_id'])): ?>
          <button type="submit" name="toggle_like" class="action-btn <?= $userLiked?'liked':'' ?>">
            <?= $userLiked ? '❤️ Liked' : '🤍 Like' ?> (<?= $likeCount ?>)
          </button>
        <?php else: ?>
          <a href="../login.php?redirect=<?= urlencode('news_detail.php?slug='.$news['slug']) ?>" class="action-btn">🤍 Like (<?= $likeCount ?>)</a>
        <?php endif; ?>
      </form>
      <!-- Bookmark -->
      <form method="POST" style="margin:0">
        <?php if(isset($_SESSION['user_id'])): ?>
          <button type="submit" name="toggle_bookmark" class="action-btn <?= $userBookmarked?'bookmarked':'' ?>">
            <?= $userBookmarked ? '🔖 Saved' : '🔖 Save' ?>
          </button>
        <?php else: ?>
          <a href="../login.php?redirect=<?= urlencode('news_detail.php?slug='.$news['slug']) ?>" class="action-btn">🔖 Save</a>
        <?php endif; ?>
      </form>
      <a href="javascript:window.print()" class="action-btn">🖨️ Print</a>
    </div>

    <!-- Share bar -->
    <div class="share-bar">
      <span class="share-label">Share:</span>
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="share-btn s-fb">f Facebook</a>
      <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="share-btn s-tw">𝕏 Twitter</a>
      <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" class="share-btn s-wa">📱 WhatsApp</a>
      <a href="javascript:void(0)" onclick="copyLink()" id="copyBtn" class="share-btn s-copy">🔗 Copy</a>
    </div>

    <!-- ============ COMMENTS ============ -->
    <div class="comment-wrap">
      <h2 class="section-title" style="font-size:17px">💬 Comments (<?= $commentCount ?>)</h2>

      <?php if($commentSuccess): ?>
        <div class="success-msg">✅ Comment posted successfully!</div>
      <?php endif; ?>

      <?php if(isset($_SESSION['user_id'])): ?>
      <form method="POST" style="margin-bottom:20px">
        <textarea class="comment-input" name="comment_text" placeholder="Share your thoughts on this article…" required></textarea>
        <button type="submit" name="post_comment" class="btn-comment mt-2">Post Comment →</button>
      </form>
      <?php else: ?>
      <div class="login-nudge">
        <a href="../login.php?redirect=<?= urlencode('news_detail.php?slug='.$news['slug']) ?>">Login</a> or
        <a href="../register.php">Register</a> to post a comment.
      </div>
      <?php endif; ?>

      <?php if(!empty($comments)): ?>
        <?php foreach($comments as $cm): ?>
        <div class="comment-item">
          <div class="comment-author">👤 <?= htmlspecialchars($cm['name'] ?? 'Anonymous') ?></div>
          <div class="comment-text"><?= htmlspecialchars($cm['comment_text']) ?></div>
          <div class="comment-date">🕐 <?= timeAgo($cm['created_at']) ?></div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color:#bbb;font-size:14px;text-align:center;padding:16px 0">No comments yet. Be the first! 👆</p>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <!-- ============ PREMIUM LOCK ============ -->
    <div class="content-preview">
      <div class="article-content">
        <?= nl2br(htmlspecialchars(mb_substr($news['full_content'], 0, 300))) ?>…
      </div>
    </div>

    <div class="premium-lock">
      <div class="premium-lock-icon">🔒</div>
      <h3>Premium Article</h3>
      <p>This article is for premium subscribers only. Subscribe to read the full story and access all exclusive content.</p>
      <div class="lock-features">
        <div class="lock-feature">✅ Full Article Access</div>
        <div class="lock-feature">✅ Daily E-Paper</div>
        <div class="lock-feature">✅ Ad-Free Reading</div>
        <div class="lock-feature">✅ Unlimited Downloads</div>
      </div>
      <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="../login.php?premium=1&redirect=<?= urlencode('news_detail.php?slug='.$news['slug']) ?>" class="btn-unlock">Login to Continue Reading</a>
        <p style="margin-top:12px;font-size:13px;color:#aaa">No account? <a href="../register.php" style="color:var(--primary);font-weight:700">Register Free</a></p>
      <?php else: ?>
        <a href="subscription.php" class="btn-unlock">⭐ View Subscription Plans</a>
        <p style="margin-top:10px;font-size:13px;color:#aaa">Starting from just ₹99/month</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div><!-- /article-wrap -->
</div><!-- /col-lg-8 -->

<!-- ===================== SIDEBAR ===================== -->
<div class="col-lg-4">

  <div class="ad-slot mb-4">
    <strong>Advertisement</strong>300 × 250 Ad Space
  </div>

  <!-- Related News -->
  <?php if(!empty($related)): ?>
  <div class="widget-box">
    <div class="widget-header">📰 Related News</div>
    <div class="widget-body">
      <?php foreach($related as $r): ?>
      <a href="news_detail.php?slug=<?= $r['slug'] ?>">
        <div class="related-item">
          <img class="related-img" src="<?= imgPath($r['thumbnail']) ?>" alt="">
          <div>
            <div class="related-title"><?= htmlspecialchars($r['title']) ?></div>
            <div class="related-meta">🕐 <?= timeAgo($r['published_at']) ?></div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Trending -->
  <?php if(!empty($trending)): ?>
  <div class="widget-box">
    <div class="widget-header">🔥 Most Read</div>
    <div class="widget-body">
      <?php foreach($trending as $i => $t): ?>
      <a href="news_detail.php?slug=<?= $t['slug'] ?>">
        <div class="trending-item">
          <div class="trending-num"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></div>
          <div>
            <div class="trending-title"><?= htmlspecialchars($t['title']) ?></div>
            <div style="font-size:11px;color:#bbb;margin-top:3px">👁️ <?= number_format($t['view_count']) ?> views</div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Premium promo (only for non-premium) -->
  <?php if(!$userIsPremium): ?>
  <div class="promo-box mb-4">
    <div style="font-size:32px;margin-bottom:8px">⭐</div>
    <h5>Go Premium</h5>
    <p>Unlock all articles, E-Paper & ad-free experience</p>
    <a href="subscription.php" class="btn-promo">View Plans</a>
  </div>
  <?php endif; ?>

  <div class="ad-slot">
    <strong>Advertisement</strong>300 × 600 Ad Space
  </div>

</div>
</div>
</div><!-- /container -->

<script>
function copyLink(){
  navigator.clipboard.writeText(window.location.href).then(function(){
    var btn=document.getElementById('copyBtn');
    btn.textContent='✅ Copied!';
    setTimeout(function(){btn.textContent='🔗 Copy';},2500);
  });
}
</script>

<?php include "footer.php"; ?>