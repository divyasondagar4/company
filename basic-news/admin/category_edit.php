<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./login.php");
    exit;
}

include "db.php";

$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id=$id"));

if (!$data) {
    header("Location: categories.php");
    exit;
}

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $status = intval($_POST['status']);

    if (!empty($name)) {
        mysqli_query($conn, "UPDATE categories 
                             SET category_name='$name', status=$status 
                             WHERE id=$id");

        header("Location: categories.php");
        exit;
    }
}

include "header.php";
include "sidebar.php";
?>

<div class="content">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Edit Category</h4>
            <a href="categories.php" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="post">

                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text"
                               name="category_name"
                               class="form-control"
                               value="<?= htmlspecialchars($data['category_name']) ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= $data['status']==1?'selected':'' ?>>
                                Active
                            </option>
                            <option value="0" <?= $data['status']==0?'selected':'' ?>>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <button type="submit" name="update" class="btn btn-success">
                        Update Category
                    </button>

                </form>

            </div>
        </div>

    </div>

</div>

<?php include "footer.php"; ?>