<?php
session_start();

$conn = new mysqli("localhost", "root", "", "download_epaper");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>