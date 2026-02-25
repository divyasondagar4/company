<?php include "db.php"; ?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">My ePaper</a>

    <div>
      <?php if(isset($_SESSION['user_id'])) {
          $id = $_SESSION['user_id'];
          $user = $conn->query("SELECT name FROM users WHERE id=$id")->fetch_assoc();
      ?>
          <span class="text-white me-3">Hi, <?php echo $user['name']; ?></span>
          <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      <?php } else { ?>
          <a href="login.php" class="btn btn-primary btn-sm">Login</a>
      <?php } ?>
    </div>
  </div>
</nav>

<div class="container mt-5 text-center">
    <h1>Welcome to ePaper Dashboard</h1>
    <p>Read and download latest newspaper.</p>

    <a href="download.php" class="btn btn-success btn-lg mt-3">
        Download ePaper
    </a>
</div>

</body>
</html>