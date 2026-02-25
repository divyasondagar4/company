<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include "db.php";

$id = intval($_GET['id']);

mysqli_query($conn, "DELETE FROM categories WHERE id=$id");

header("Location: categories.php");
exit;