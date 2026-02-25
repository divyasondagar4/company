<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include "db.php";

$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));

if (!$settings) {
    mysqli_query($conn, "INSERT INTO settings (site_name) VALUES ('News Portal')");
    $settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));
}

if (isset($_POST['save'])) {

    $site_name = mysqli_real_escape_string($conn,$_POST['site_name']);
    $email = mysqli_real_escape_string($conn,$_POST['contact_email']);
    $mobile = mysqli_real_escape_string($conn,$_POST['contact_mobile']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);

    if (!empty($_FILES['logo']['name'])) {
        $logo = time().'_'.$_FILES['logo']['name'];
        move_uploaded_file($_FILES['logo']['tmp_name'],"uploads/".$logo);

        mysqli_query($conn,"UPDATE settings SET
            site_name='$site_name',
            logo='$logo',
            contact_email='$email',
            contact_mobile='$mobile',
            address='$address'
        ");
    } else {
        mysqli_query($conn,"UPDATE settings SET
            site_name='$site_name',
            contact_email='$email',
            contact_mobile='$mobile',
            address='$address'
        ");
    }

    header("Location: settings.php");
    exit;
}

include "header.php";
include "sidebar.php";
?>

<div class="content">
<div class="container-fluid">

<h4 class="mb-4">Site Settings</h4>

<div class="card shadow-sm">
<div class="card-body">

<form method="post" enctype="multipart/form-data">

<div class="mb-3">
<label>Site Name</label>
<input type="text" name="site_name" class="form-control"
value="<?= htmlspecialchars($settings['site_name']) ?>" required>
</div>

<div class="mb-3">
<label>Logo</label>
<input type="file" name="logo" class="form-control">
<?php if(!empty($settings['logo'])){ ?>
<img src="uploads/<?= $settings['logo'] ?>" height="60" class="mt-2">
<?php } ?>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="contact_email" class="form-control"
value="<?= htmlspecialchars($settings['contact_email']) ?>">
</div>

<div class="mb-3">
<label>Mobile</label>
<input type="text" name="contact_mobile" class="form-control"
value="<?= htmlspecialchars($settings['contact_mobile']) ?>">
</div>

<div class="mb-3">
<label>Address</label>
<textarea name="address" class="form-control"><?= htmlspecialchars($settings['address']) ?></textarea>
</div>

<button name="save" class="btn btn-success">Save Settings</button>

</form>

</div>
</div>

</div>
</div>

<?php include "footer.php"; ?>