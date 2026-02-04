<?php
$conn = mysqli_connect("localhost", "root", "", "login system");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
