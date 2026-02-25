<?php
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Subscribe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Select Subscription Plan</h2>

<form action="activate.php" method="POST">
  <select name="plan" class="form-control mb-3" required>
    <option value="">Select Plan</option>
    <option value="month">Monthly</option>
    <option value="year">Yearly</option>
  </select>

  <button class="btn btn-success">Activate Subscription</button>
</form>

</body>
</html>