<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location:../login.php");
    exit;
}
include "header.php";
include "sidebar.php";

?>

<div class="content">
    <h3>Dashboard</h3>
    <p>Welcome, Admin</p>
</div>

<?php include "footer.php"; ?>