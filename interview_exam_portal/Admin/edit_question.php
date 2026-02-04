<?php
include 'db.php';
include 'sidebar.php';

$id = $_GET['id'];
$q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM questions WHERE id=$id"));
$cats = mysqli_query($conn, "SELECT * FROM categories");

$updateSuccess = false;

if(isset($_POST['update'])){
    $cat = $_POST['category'];
    $question = $_POST['question'];
    $o1 = $_POST['opt1'];
    $o2 = $_POST['opt2'];
    $o3 = $_POST['opt3'];
    $o4 = $_POST['opt4'];
    $ans = $_POST['answer'];

    mysqli_query($conn, "UPDATE questions SET 
        category_id='$cat',
        question_text='$question',
        option1='$o1',
        option2='$o2',
        option3='$o3',
        option4='$o4',
        correct_answer='$ans'
        WHERE id=$id");

    $updateSuccess = true;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-5" style="margin-left:250px;">
    <div class="card mx-auto" style="max-width:600px;">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Question</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Category:</label>
                    <select name="category" class="form-select" required>
                        <?php while($c=mysqli_fetch_assoc($cats)) { ?>
                            <option value="<?= $c['id'] ?>" <?= ($c['id']==$q['category_id'])?'selected':'' ?>>
                                <?= $c['category_name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Question:</label>
                    <textarea name="question" class="form-control" required><?= $q['question'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Option 1:</label>
                    <input type="text" name="opt1" class="form-control" value="<?= $q['option1'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>Option 2:</label>
                    <input type="text" name="opt2" class="form-control" value="<?= $q['option2'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>Option 3:</label>
                    <input type="text" name="opt3" class="form-control" value="<?= $q['option3'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>Option 4:</label>
                    <input type="text" name="opt4" class="form-control" value="<?= $q['option4'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>Correct Answer:</label>
                    <select name="answer" class="form-select" required>
                        <option value="1" <?= $q['correct_answer']=='1'?'selected':'' ?>>1</option>
                        <option value="2" <?= $q['correct_answer']=='2'?'selected':'' ?>>2</option>
                        <option value="3" <?= $q['correct_answer']=='3'?'selected':'' ?>>3</option>
                        <option value="4" <?= $q['correct_answer']=='4'?'selected':'' ?>>4</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="manage_que.php" class="btn btn-secondary">Back</a>
                    <button type="submit" name="update" class="btn btn-success">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast -->
<?php if($updateSuccess): ?>
<div class="position-fixed top-50 start-50 translate-middle p-3" style="z-index: 11">
  <div id="updateToast" class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Question updated successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
  var toastEl = document.getElementById('updateToast');
  var toast = new bootstrap.Toast(toastEl, { delay: 2000 });
  toast.show();
</script>
<?php endif; ?>
