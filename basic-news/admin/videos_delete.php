<?php
include("db.php");
$id=$_GET['id'];

$q=mysqli_query($conn,"SELECT video_url FROM videos WHERE id='$id'");
$v=mysqli_fetch_assoc($q);

if(file_exists("uploads/videos/".$v['video_url'])){
    unlink("uploads/videos/".$v['video_url']);
}

mysqli_query($conn,"DELETE FROM videos WHERE id='$id'");
header("Location: videos.php");