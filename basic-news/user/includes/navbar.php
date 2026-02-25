<?php
// Fetch categories
$cat_sql = mysqli_query($conn, "
    SELECT * FROM categories 
    WHERE status = 1
    ORDER BY category_name ASC
");
?>

<!-- TOP BAR -->
<div class="top-bar">
    <div class="container d-flex justify-content-between">
        <div><?= date('l, d M Y'); ?></div>
        <div>
            <?php if($is_logged_in): ?>
                Welcome, <?= $_SESSION['user_name']; ?> |
                <a href="logout.php" class="text-white">Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-white">Login</a> |
                <a href="register.php" class="text-white">Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MAIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
<div class="container">
    <a class="navbar-brand" href="index.php">NEWS</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <?php while($cat = mysqli_fetch_assoc($cat_sql)): ?>
            <li class="nav-item">
                <a class="nav-link" href="category.php?id=<?= $cat['id']; ?>">
                    <?= $cat['category_name']; ?>
                </a>
            </li>
            <?php endwhile; ?>

        </ul>

        <!-- SEARCH -->
        <form class="d-flex" action="search.php" method="get">
            <input class="form-control me-2" type="search" name="q" placeholder="Search news">
            <button class="btn btn-danger">Search</button>
        </form>

        <!-- PREMIUM BADGE -->
        <?php if($is_premium_user): ?>
            <span class="badge bg-warning text-dark ms-3">PREMIUM</span>
        <?php else: ?>
            <a href="subscribe.php" class="btn btn-warning ms-3">Subscribe</a>
        <?php endif; ?>
    </div>
</div>
</nav>