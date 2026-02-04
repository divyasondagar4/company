<?php
include '../db.php';
include 'sidebar.php';

$successMsg = "";

/* ================= CSV IMPORT ================= */

if(isset($_POST['import_csv'])){

    if(isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0){

        $fileName = $_FILES['csv_file']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

      
        if($fileExt != 'csv'){
            $errorMsg = "Please upload only CSV file!";
        } else {

            $file = fopen($_FILES['csv_file']['tmp_name'], "r");

            if($file === FALSE){
                $errorMsg = "File could not be opened!";
            } else {

                fgetcsv($file); 
                $count = 0;

                while(($row = fgetcsv($file, 1000, ",")) !== FALSE){

                 
                    if(count($row) < 7){
                        continue; 
                    }

                    $cat = mysqli_real_escape_string($conn, $row[0]);
                    $question = mysqli_real_escape_string($conn, $row[1]);
                    $o1 = mysqli_real_escape_string($conn, $row[2]);
                    $o2 = mysqli_real_escape_string($conn, $row[3]);
                    $o3 = mysqli_real_escape_string($conn, $row[4]);
                    $o4 = mysqli_real_escape_string($conn, $row[5]);
                    $ans = mysqli_real_escape_string($conn, $row[6]);

                    $insert = mysqli_query($conn,"INSERT INTO questions
                    (category_id, question, option1, option2, option3, option4, correct_answer)
                    VALUES ('$cat','$question','$o1','$o2','$o3','$o4','$ans')");

                    if($insert){
                        $count++;
                    }
                }

                fclose($file);
                $successMsg = "$count Questions Imported Successfully!";
            }
        }

    } else {
        $errorMsg = "Please select a file!";
    }
}

/* ================= MANUAL ADD ================= */
if(isset($_POST['add_question'])){
    $cat = $_POST['category'];
    $question = $_POST['question'];
    $o1 = $_POST['opt1'];
    $o2 = $_POST['opt2'];
    $o3 = $_POST['opt3'];
    $o4 = $_POST['opt4'];
    $ans = $_POST['answer'];

    mysqli_query($conn,"INSERT INTO questions
    (category_id, question, option1, option2, option3, option4, correct_answer)
    VALUES ('$cat','$question','$o1','$o2','$o3','$o4','$ans')");

    $successMsg = "Question Added Successfully!";
}

$cats = mysqli_query($conn,"SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets//admin.css//add_que.css" rel="stylesheet">

    <style>
    </style>
</head>
<body>

<div class="main-container">
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-question-circle"></i> Add Questions</h2>
        <p>Import questions via CSV or add them manually to your quiz database</p>
    </div>

    <!-- Toast Messages -->
    <?php if(isset($successMsg) && $successMsg != ""): ?>
    <div class="toast-container-custom">
        <div class="toast-custom success">
            <div class="toast-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="toast-content">
                <strong>Success!</strong>
                <span><?= $successMsg ?></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast-custom').style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => {
                document.querySelector('.toast-container-custom').remove();
            }, 300);
        }, 3000);
    </script>
    <?php endif; ?>

    <?php if(isset($errorMsg) && $errorMsg != ""): ?>
    <div class="toast-container-custom">
        <div class="toast-custom error">
            <div class="toast-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="toast-content">
                <strong>Error!</strong>
                <span><?= $errorMsg ?></span>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.toast-custom').style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => {
                document.querySelector('.toast-container-custom').remove();
            }, 300);
        }, 4000);
    </script>
    <?php endif; ?>

    <!-- CSV Import Section -->
    <div class="content-card">
        <div class="csv-section">
            <div class="section-title">
                <i class="fas fa-file-csv"></i>
                Import Questions from CSV
                <span class="badge-info ms-auto">Bulk Import</span>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <label class="form-label">
                        <i class="fas fa-upload me-1"></i> Select CSV File
                    </label>
                    <div class="file-input-wrapper">
                        <input type="file" name="csv_file" id="csv_file" required accept=".csv">
                        <label for="csv_file" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="file-name">Choose a CSV file or drag it here</span>
                        </label>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> Format: category_id, question, option1, option2, option3, option4, correct_answer
                    </small>
                </div>
                
                <button type="submit" name="import_csv" class="btn btn-primary">
                    <i class="fas fa-file-import me-2"></i>Import Questions
                </button>
            </form>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Manual Add Section -->
    <div class="content-card">
        <div class="manual-section">
            <div class="section-title">
                <i class="fas fa-pencil-alt"></i>
                Add Question Manually
                <span class="badge-info ms-auto">Single Entry</span>
            </div>

            <form method="post">
                <div class="form-row">
                    <label class="form-label">
                        <i class="fas fa-folder me-1"></i> Category
                    </label>
                    <select name="category" class="form-select" required>
                        <option value="" disabled selected>Select a category</option>
                        <?php 
                        mysqli_data_seek($cats, 0);
                        while($c = mysqli_fetch_assoc($cats)){ 
                        ?>
                        <option value="<?= $c['id'] ?>"><?= $c['category_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-row">
                    <label class="form-label">
                        <i class="fas fa-question me-1"></i> Question
                    </label>
                    <textarea name="question" class="form-control" placeholder="Enter your question here..." required></textarea>
                </div>

                <div class="option-group">
                    <div class="form-row">
                        <label class="form-label">
                            <i class="fas fa-circle me-1"></i> Option 1
                        </label>
                        <input type="text" name="opt1" class="form-control" placeholder="First option" required>
                    </div>

                    <div class="form-row">
                        <label class="form-label">
                            <i class="fas fa-circle me-1"></i> Option 2
                        </label>
                        <input type="text" name="opt2" class="form-control" placeholder="Second option" required>
                    </div>

                    <div class="form-row">
                        <label class="form-label">
                            <i class="fas fa-circle me-1"></i> Option 3
                        </label>
                        <input type="text" name="opt3" class="form-control" placeholder="Third option" required>
                    </div>

                    <div class="form-row">
                        <label class="form-label">
                            <i class="fas fa-circle me-1"></i> Option 4
                        </label>
                        <input type="text" name="opt4" class="form-control" placeholder="Fourth option" required>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-label">
                        <i class="fas fa-check-circle me-1"></i> Correct Answer
                    </label>
                    <select name="answer" class="form-select" required>
                        <option value="" disabled selected>Select the correct option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>

                <button type="submit" name="add_question" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i>Add Question
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('csv_file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose a CSV file or drag it here';
        document.getElementById('file-name').textContent = fileName;
    });
</script>

</body>
</html>