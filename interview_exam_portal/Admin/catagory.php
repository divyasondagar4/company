<?php
session_start();
include "../db.php";

if(!isset($_SESSION['admin'])){
    header("Location: index.php");
}

$toast = "";

// ADD CATEGORY
if(isset($_POST['add'])){
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    mysqli_query($conn,"INSERT INTO categories (category_name) VALUES ('$cat')");
    $toast = "added";
}

// DELETE CATEGORY
if(isset($_GET['del'])){
    $id = intval($_GET['del']);

    // Check if questions exist in this category
    $check = mysqli_query($conn,"SELECT * FROM questions WHERE category_id='$id'");
    if(mysqli_num_rows($check) > 0){
        $toast = "cannot_delete";
    } else {
        mysqli_query($conn,"DELETE FROM categories WHERE id='$id'");
        $toast = "deleted";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets//admin.css//catagory.css" rel="stylesheet">

    <style>
    </style>
</head>
<body>

<?php include "sidebar.php"; ?>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <h2>
            <i class="fas fa-folder"></i>
            Manage Categories
        </h2>
        <div class="stats-badge">
            <i class="fas fa-list"></i>
            <span><?php echo mysqli_num_rows(mysqli_query($conn,"SELECT * FROM categories")); ?> Categories</span>
        </div>
    </div>

    <!-- Add Category Section -->
    <div class="add-category-section">
        <h3>
            <i class="fas fa-plus-circle"></i>
            Add New Category
        </h3>
        
        <form method="POST">
            <div class="form-group">
                <label>Category Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-folder"></i>
                    <input type="text" name="category" placeholder="Enter category name" required>
                </div>
            </div>
            
            <button type="submit" name="add" class="btn-add">
                <i class="fas fa-plus"></i>
                Add Category
            </button>
        </form>
    </div>

    <!-- Categories List Section -->
    <div class="categories-section">
        <h3>
            <i class="fas fa-list"></i>
            All Categories
        </h3>

        <div class="table-container">
            <?php
            $res = mysqli_query($conn,"SELECT * FROM categories ORDER BY id DESC");
            if(mysqli_num_rows($res) > 0){
            ?>
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-folder"></i> Category Name</th>
                            <th style="text-align: center;"><i class="fas fa-cog"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($row = mysqli_fetch_assoc($res)){
                            echo "<tr>
                                <td class='category-id'>#{$row['id']}</td>
                                <td class='category-name'>{$row['category_name']}</td>
                                <td style='text-align: center;'>
                                    <a class='btn-delete' href='?del={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this category?\")'>
                                        <i class='fas fa-trash'></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } else {
            ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h4>No Categories Yet</h4>
                    <p>Add your first category using the form above.</p>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script>
var toast = document.getElementById("toast");

<?php if($toast=="added"){ ?>
    toast.innerHTML = '<i class="fas fa-check-circle"></i><span>Category added successfully!</span>';
    toast.classList.add("success", "show");
<?php } elseif($toast=="deleted"){ ?>
    toast.innerHTML = '<i class="fas fa-check-circle"></i><span>Category deleted successfully!</span>';
    toast.classList.add("success", "show");
<?php } elseif($toast=="cannot_delete"){ ?>
    toast.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Cannot delete! Questions exist in this category</span>';
    toast.classList.add("error", "show");
<?php } ?>

setTimeout(() => {
    toast.classList.remove("show");
}, 3000);
</script>

</body>
</html>