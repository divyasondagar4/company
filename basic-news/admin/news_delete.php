<?php
include("db.php");
$id = $_GET['id'];

$q = mysqli_query($conn,"SELECT thumbnail FROM news WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if(file_exists("uploads/news/".$data['thumbnail'])){
    unlink("uploads/news/".$data['thumbnail']);
}

mysqli_query($conn,"DELETE FROM news WHERE id='$id'");
header("Location: news.php");