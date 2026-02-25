<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");
include("header.php");
include("sidebar.php");

if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    if (!empty($name)) {
        mysqli_query($conn, "INSERT INTO categories(category_name) VALUES('$name')");
        header("Location: categories.php");
        exit;
    }
}
?>

<div class="content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Add Category</h4>
            <a href="categories.php" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="post">

                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
                    </div>

                    <button type="submit" name="save" class="btn btn-success">
                        Save Category
                    </button>

                </form>

            </div>
        </div>

    </div>

</div>

<?php include("footer.php"); ?>