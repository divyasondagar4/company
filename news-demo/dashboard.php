<?php include 'db.php';

if(!isset($_SESSION['user'])) header("Location: login.php");

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark p-3">
<div class="container">
<span class="navbar-brand">Dashboard</span>
<a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</nav>

<div class="container mt-5">
<div class="card p-4 shadow">
<h3>Hello <?php echo $user['name']; ?> 👋</h3>

<?php if($user['subscription_status'] == 'active') { ?>
    <a href="download.php" class="btn btn-primary">Download Epaper</a>
<?php } else { ?>
    <p>Please subscribe to download epaper.</p>
<?php } ?>

</div>
</div>

</body>
</html>