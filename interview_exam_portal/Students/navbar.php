<!-- Navbar -->
<nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="logo-text">QuizMaster</span>
                </div>
                <div class="welcome-badge">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <span class="welcome">Welcome, </span>
                        <span class="username"><?= htmlspecialchars($student['username']) ?></span>
                    </div>
                </div>
            </div>
            <div class="nav-right">
                <a href="../index.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>