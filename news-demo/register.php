<?php include 'db.php';

if(isset($_POST['register'])){
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name,email,password) VALUES ('$name','$email','$password')";
$conn->query($sql);

header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-6">
<div class="card p-4 shadow">
<h3>Register</h3>
<form method="POST">
<input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
<button name="register" class="btn btn-success w-100">Register</button>
</form>
</div>
</div>

</body>
</html>