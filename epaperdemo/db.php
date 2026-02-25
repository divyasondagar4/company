<?php
$conn = new mysqli("localhost", "root", "", "download_epaper");

if ($conn->connect_error) {
    die("Connection failed");
}
session_start();
?>