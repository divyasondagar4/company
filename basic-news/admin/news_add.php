<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "db.php";

if(isset($_POST['save'])){

    $cat   = $_POST['category_id'];
    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $desc  = mysqli_real_escape_string($conn,$_POST['description']);
    $level = $_POST['news_level'];
    $city  = $_POST['city_name'] ?? '';
    $state = $_POST['state_name'] ?? '';
    $status = $_POST['status'];

    $image = '';
    if(!empty($_FILES['image']['name'])){
        $image = time().'_'.$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/news/".$image);
    }

    mysqli_query($conn,"
    INSERT INTO news
    (category_id, news_level, city_name, state_name, title, description, image, status)
    VALUES
    ('$cat','$level','$city','$state','$title','$desc','$image','$status')
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

<h4 class="mb-4">Add News</h4>

<div class="card">
<div class="card-body">

<form method="post" enctype="multipart/form-data">

<select name="category_id" class="form-control mb-3" required>
<option value="">Select Category</option>
<?php while($c=mysqli_fetch_assoc($cats)){ ?>
<option value="<?= $c['id'] ?>"><?= $c['category_name'] ?></option>
<?php } ?>
</select>

<select name="news_level" class="form-control mb-3" required>
<option value="">Select Coverage</option>
<option value="city">City</option>
<option value="state">State</option>
<option value="national">National</option>
<option value="international">International</option>
</select>

<input type="text" name="city_name" class="form-control mb-3" placeholder="City (only for city news)">
<input type="text" name="state_name" class="form-control mb-3" placeholder="State (only for state news)">

<input type="text" name="title" class="form-control mb-3" placeholder="News Title" required>

<textarea name="description" class="form-control mb-3" rows="5" placeholder="News Content"></textarea>

<input type="file" name="image" class="form-control mb-3">

<select name="status" class="form-control mb-3">
<option value="1">Publish</option>
<option value="0">Draft</option>
</select>

<button name="save" class="btn btn-success">Save News</button>
<a href="news.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

</div>
</div>

<?php include "footer.php"; ?>