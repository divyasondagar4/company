<?php
session_start();
include '../db.php';

/* ===== LOGIN CHECK ===== */
if(!isset($_SESSION['student']) || !is_array($_SESSION['student'])){
    header("Location: ../index.php");
    exit;
}

$student = $_SESSION['student']; 

/* ===== CATEGORY CHECK ===== */
if(!isset($_GET['cat'])){
    header("Location:student_dashboard.php");
    exit;
}
$cat_id = intval($_GET['cat']);

/* ===== FETCH CATEGORY ===== */
$cat_res = mysqli_query($conn,"SELECT * FROM categories WHERE id='$cat_id'");
if(mysqli_num_rows($cat_res) == 0){
    die("Invalid Category!");
}
$category = mysqli_fetch_assoc($cat_res);

/* ===== FETCH QUESTIONS ===== */
$questions_res = mysqli_query($conn,"SELECT * FROM questions WHERE category_id='$cat_id'");
$total_questions = mysqli_num_rows($questions_res);

if($total_questions == 0){
    die("No questions found!");
}

/* ===== QUIZ SUBMIT ===== */
if(isset($_POST['submit_quiz'])){
    $score = 0;
    $wrong = 0;
    
    mysqli_data_seek($questions_res, 0);
    
    while($q = mysqli_fetch_assoc($questions_res)){
        $qid = $q['id'];
        $correct_option = $q['correct_answer'];
        $student_answer = isset($_POST['answer'][$qid]) ? $_POST['answer'][$qid] : 0;
    
        if($student_answer == $correct_option){
            $score++;
        } else {
            $wrong++;
        }
    }
    
    $total_questions = $score + $wrong;
    $percentage = ($score / $total_questions) * 100;
    
    $student_id = $student['id']; 
    $category_id = $cat_id;
    $exam_date = date('Y-m-d H:i:s');
    
    $insert = mysqli_query($conn,"INSERT INTO res 
    (student_id, category_id, score, total_question, correct_answer, wrong_answer, exam_date)
    VALUES 
    ('$student_id', '$category_id', '$percentage', '$total_questions', '$score', '$wrong', '$exam_date')");
    
    if(!$insert){
        die("Error saving result: ".mysqli_error($conn));
    }
    
    if(!$insert){
        die("Error saving result: ".mysqli_error($conn));
    }

    // Calculate percentage
    $percentage = ($score / $total_questions) * 100;
    $passed = $percentage >= 50;
    
    // Display result
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz Result</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../assets/css/start_quiz.css">

    </head>
    <body>
        <div class="result-container">
            <div class="result-icon <?= $passed ? 'pass' : 'fail' ?>">
                <i class="fas <?= $passed ? 'fa-trophy' : 'fa-times-circle' ?>"></i>
            </div>

            <h1 class="result-title"><?= $passed ? 'Congratulations!' : 'Keep Trying!' ?></h1>
            <p class="result-subtitle"><?= htmlspecialchars($category['category_name']) ?> Quiz</p>

            <div class="score-display">
                <div class="score-number"><?= $score ?>/<?= $total_questions ?></div>
                <div class="score-text">Questions Answered Correctly</div>
            </div>

            
            <div class="percentage-bar">
                <div class="percentage-fill <?= $passed ? 'pass' : 'fail' ?>" style="width: <?= $percentage ?>%"></div>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= $total_questions ?></div>
                    <div class="stat-label">Total Questions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $score ?></div>
                    <div class="stat-label">Correct</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($percentage, 1) ?>%</div>
                    <div class="stat-label">Percentage</div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="student_dash.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <a href="start_quiz.php?cat=<?= $cat_id ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Retry Quiz
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['category_name']) ?> Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/start_quiz2.css">
    
</head>
<body>

<div class="quiz-header">
    <h1 class="quiz-title">
        <i class="fas fa-clipboard-list"></i> <?= htmlspecialchars($category['category_name']) ?> Quiz
    </h1>
    <div class="quiz-info">
        <div class="info-item">
            <i class="fas fa-user"></i>
            <span><?= htmlspecialchars($student['username']) ?></span>
        </div>
        <div class="info-item">
            <i class="fas fa-question-circle"></i>
            <span><?= $total_questions ?> Questions</span>
        </div>
        <div class="info-item">
            <i class="fas fa-clock"></i>
            <span>No Time Limit</span>
        </div>
    </div>
    <div class="progress-bar">
        <div class="progress-fill" id="progressBar" style="width: 0%"></div>
    </div>
</div>

<div class="container">
    <form method="post" id="quizForm">
        <?php 
        mysqli_data_seek($questions_res, 0);
        $i = 1;
        while($q = mysqli_fetch_assoc($questions_res)){ 
        ?>
            <div class="question-card" data-question="<?= $i ?>">
                <div class="question-header">
                    <div class="question-number"><?= $i ?></div>
                    <div class="question-text"><?= htmlspecialchars($q['question']) ?></div>
                </div>
                
                <div class="options-container">
                    <label class="option-label">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="1" class="option-radio" onchange="updateProgress()">
                        <span class="option-text"><?= htmlspecialchars($q['option1']) ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="2" class="option-radio" onchange="updateProgress()">
                        <span class="option-text"><?= htmlspecialchars($q['option2']) ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="3" class="option-radio" onchange="updateProgress()">
                        <span class="option-text"><?= htmlspecialchars($q['option3']) ?></span>
                    </label>
                    
                    <label class="option-label">
                        <input type="radio" name="answer[<?= $q['id'] ?>]" value="4" class="option-radio" onchange="updateProgress()">
                        <span class="option-text"><?= htmlspecialchars($q['option4']) ?></span>
                    </label>
                </div>
            </div>
        <?php 
        $i++; 
        } 
        ?>

        <div class="submit-container">
            <button type="submit" name="submit_quiz" class="submit-btn" onclick="return confirmSubmit()">
                <i class="fas fa-check-circle"></i> Submit Quiz
            </button>
            <br>
            <a href="student_dash.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('quizForm').addEventListener('submit', function(e) {
    const cards = document.querySelectorAll('.question-card');
    for (let card of cards) {
        if (!card.querySelector('input:checked')) {
            e.preventDefault();
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert("Please answer all questions before submitting.");
            return false;
        }
    }
});
</script>

</body>
</html>