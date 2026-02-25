<?php
include 'db.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

/* ACTIVATE SUBSCRIPTION */
if(isset($_POST['activate']) && $user){
    $id = $user['id'];

    $conn->query("UPDATE users SET subscription_status='active' WHERE id=$id");

    $_SESSION['user']['subscription_status'] = 'active';

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Epaper</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark p-3">
<div class="container">
<a class="navbar-brand">Epaper</a>

<div>
<?php if($user){ ?>
    <span class="text-white me-3">Hello <?php echo $user['name']; ?></span>
    <a href="logout.php" class="btn btn-danger">Logout</a>
<?php } else { ?>
    <a href="login.php" class="btn btn-light">Login</a>
    <a href="register.php" class="btn btn-light">Register</a>

<?php } ?>

</div>
</div>
</nav>

<div class="container mt-5">

<h2>Blogs</h2>

<!-- Free Blogs -->
<div class="card p-3 mb-3">
<h5>Blog 1</h5>
<p>Free content for everyone.</p>
</div>

<div class="card p-3 mb-3">
<h5>Blog 2</h5>
<p>Free content for everyone.</p>
</div>

<?php if($user){ ?>

<?php if($user['subscription_status']=='active'){ ?>

<!-- Premium Blogs -->
<div class="card p-3 mb-3">
<h5>Premium Blog 1</h5>
<p>Full premium content visible.</p>
</div>

<div class="card p-3 mb-3">
<h5>Premium Blog 2</h5>
<p>More premium content unlocked.</p>
</div>

<!-- Download Button -->
<a href="download.php" class="btn btn-success btn-lg">
Download Epaper
</a>

<?php } else { ?>

<!-- Subscribe Button -->
<a href="index.php?subscribe=1" class="btn btn-warning btn-lg">
Subscribe Now ₹99
</a>

<?php if(isset($_GET['subscribe'])){ ?>

<!-- Subscription Form -->
<div class="card p-4 mt-4 shadow">
<h4>Activate Subscription</h4>
<form method="POST">
    <p>Plan: ₹99 Monthly</p>
    <button name="activate" class="btn btn-success">
        Activate Now
    </button>
</form>
</div>

<?php } ?>

<?php } ?>

<?php } else { ?>

<div class="alert alert-info mt-3">
Login to unlock premium blogs.
</div>

<?php } ?>

</div>
</body>
</html>