<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include("db.php");
include("header.php");
include("sidebar.php");
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Categories</h4>
        <a href="category_add.php" class="btn btn-primary">Add Category</a>
    </div>

    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th width="180">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($conn,"SELECT * FROM categories");
            while($row = mysqli_fetch_assoc($q)){
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['category_name'] ?></td>
                <td>
                    <a href="category_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="category_delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<?php include("footer.php"); ?>