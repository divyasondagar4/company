<?php
$current_page = basename($_SERVER['PHP_SELF']);
?><style>
body {
    margin: 0;
}

.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
}

.content {
    margin-left: 240px;
    padding: 30px;
}

.nav-link {
    border-radius: 6px;
    margin-bottom: 5px;
}
</style>

<div class="sidebar bg-dark text-white">
    <div class="p-3">
        <h4 class="text-center mb-4">Admin Panel</h4>

        <ul class="nav nav-pills flex-column mb-auto">

            <li class="nav-item">
                <a href="dashboard.php"
                   class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active bg-primary' : '' ?>">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="categories.php"
                   class="nav-link text-white <?= $current_page == 'categories.php' ? 'active bg-primary' : '' ?>">
                    Categories
                </a>
            </li>

            <li>
                <a href="news.php"
                   class="nav-link text-white <?= $current_page == 'news.php' ? 'active bg-primary' : '' ?>">
                    News
                </a>
            </li>

            <li>
                <a href="videos.php"
                   class="nav-link text-white <?= $current_page == 'videos.php' ? 'active bg-primary' : '' ?>">
                    Videos
                </a>
            </li>

            <li>
                <a href="users.php"
                   class="nav-link text-white <?= $current_page == 'users.php' ? 'active bg-primary' : '' ?>">
                    Users
                </a>
            </li>

            <li>
                <a href="subscriptions.php"
                   class="nav-link text-white <?= $current_page == 'subscriptions.php' ? 'active bg-primary' : '' ?>">
                    Subscriptions
                </a>
            </li>

            <li>
                <a href="payments.php"
                   class="nav-link text-white <?= $current_page == 'payments.php' ? 'active bg-primary' : '' ?>">
                    Payments
                </a>
            </li>

            <li>
                <a href="epaper.php"
                   class="nav-link text-white <?= $current_page == 'epaper.php' ? 'active bg-primary' : '' ?>">
                    epaper
                </a>
            </li>

            <li>
                <a href="settings.php"
                   class="nav-link text-white <?= $current_page == 'settings.php' ? 'active bg-primary' : '' ?>">
                    Settings
                </a>
            </li>

            <hr class="text-secondary">

            <li>
                <a href="../user/index.php" class="nav-link text-danger">
                    Logout
                </a>
            </li>

        </ul>
    </div>
</div>