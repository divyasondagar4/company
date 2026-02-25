<?php include "db.php"; ?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Register</h2>

<form method="POST">
  <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
  <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
  <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
  <button class="btn btn-primary">Register</button>
</form>

<?php
if ($_POST) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $conn->query("INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')");
  echo "<div class='alert alert-success mt-3'>Registered Successfully</div>";
}
?>

<a href="login.php">Login</a>

</body>
</html>