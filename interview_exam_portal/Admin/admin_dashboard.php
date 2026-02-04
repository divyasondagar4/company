<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets//admin.css//admin_dash.css" rel="stylesheet">

    <style>
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>Dashboard Overview</h2>
            </div>
            <div class="topbar-right">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <?= strtoupper(substr($_SESSION['admin'], 0, 1)) ?>
                    </div>
                    <span class="admin-name"><?php echo $_SESSION['admin']; ?></span>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>👋 Welcome back, <?php echo $_SESSION['admin']; ?>!</h1>
                <p>Here's what's happening with your quiz platform today.</p>
            </div>

            <!-- Stats Cards
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-header">
                        <div class="stat-title">Total Students</div>
                        <div class="stat-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value">245</div>
                </div>

                <div class="stat-card green">
                    <div class="stat-header">
                        <div class="stat-title">Total Categories</div>
                        <div class="stat-icon green">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                    <div class="stat-value">12</div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-header">
                        <div class="stat-title">Total Questions</div>
                        <div class="stat-icon orange">
                            <i class="fas fa-question-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value">486</div>
                </div>

                <div class="stat-card red">
                    <div class="stat-header">
                        <div class="stat-title">Quizzes Taken</div>
                        <div class="stat-icon red">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                    </div>
                    <div class="stat-value">1,234</div>
                </div>
            </div> -->

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="actions-grid">
                    <a href="catagory.php" class="action-btn">
                        <i class="fas fa-plus"></i> Add Category
                    </a>
                    <a href="add_question.php" class="action-btn green">
                        <i class="fas fa-plus"></i> Add Question
                    </a>
                    <a href="manage_student.php" class="action-btn orange">
                        <i class="fas fa-users"></i> View Students
                    </a>
                    <a href="results.php" class="action-btn">
                        <i class="fas fa-chart-line"></i> View Results
                    </a>
                </div>
            </div>


    <script src="../assets//js//admin_dash.js">
    </script>

</body>
</html>