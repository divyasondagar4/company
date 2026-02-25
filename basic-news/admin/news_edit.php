<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";

$id = intval($_GET['id']);
$n = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM news WHERE id='$id'"));

if(isset($_POST['update'])){

    $cat   = $_POST['category_id'];
    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $desc  = mysqli_real_escape_string($conn,$_POST['description']);
    $level = $_POST['news_level'];
    $city  = $_POST['city_name'];
    $state = $_POST['state_name'];
    $status = $_POST['status'];

    if(!empty($_FILES['image']['name'])){
        $image = time().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/news/".$image);
        mysqli_query($conn,"UPDATE news SET image='$image' WHERE id='$id'");
    }

    mysqli_query($conn,"
    UPDATE news SET
    category_id='$cat',
    news_level='$level',
    city_name='$city',
    state_name='$state',
    title='$title',
    description='$desc',
    status='$status'
    WHERE id='$id'
    ");

    header("Location: news.php");
    exit;
}

$cats = mysqli_query($conn,"SELECT * FROM categories WHERE status=1");

include "header.php";
include "sidebar.php";
?>

<div class="content">
<div class="container-fluid">

<h4>Edit News</h4>

<div class="card">
<div class="card-body">

<form method="post" enctype="multipart/form-data">

<select name="category_id" class="form-control mb-3">
<?php while($c=mysqli_fetch_assoc($cats)){ ?>
<option value="<?= $c['id'] ?>" <?= $c['id']==$n['category_id']?'selected':'' ?>>
<?= $c['category_name'] ?>
</option>
<?php } ?>
</select>

<select name="news_level" class="form-control mb-3">
<option value="city" <?= $n['news_level']=='city'?'selected':'' ?>>City</option>
<option value="state" <?= $n['news_level']=='state'?'selected':'' ?>>State</option>
<option value="national" <?= $n['news_level']=='national'?'selected':'' ?>>National</option>
<option value="international" <?= $n['news_level']=='international'?'selected':'' ?>>International</option>
</select>

<input type="text" name="city_name" value="<?= $n['city_name'] ?>" class="form-control mb-3">
<input type="text" name="state_name" value="<?= $n['state_name'] ?>" class="form-control mb-3">

<input type="text" name="title" value="<?= htmlspecialchars($n['title']) ?>" class="form-control mb-3">

<textarea name="description" class="form-control mb-3" rows="5"><?= htmlspecialchars($n['description']) ?></textarea>

<input type="file" name="image" class="form-control mb-3">

<select name="status" class="form-control mb-3">
<option value="1" <?= $n['status']?'selected':'' ?>>Publish</option>
<option value="0" <?= !$n['status']?'selected':'' ?>>Draft</option>
</select>

<button name="update" class="btn btn-success">Update</button>
<a href="news.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>

</div>
</div>

<?php include "footer.php"; ?>