<?php
include "db.php";
session_destroy();
header("Location: dashboard.php");
exit;
?>