<?php
session_start();
include '../db.php';

// ===== Check student login =====
if(!isset($_SESSION['student']) || !is_array($_SESSION['student'])){
    header("Location: index.php");
    exit;
}

$student = $_SESSION['student'];

// ===== Fetch all categories =====
$categories = mysqli_query($conn,"SELECT * FROM categories ORDER BY category_name ASC");

// ===== Fetch student quiz results (only score) =====
$student_id = $student['id'];
$results_query = "SELECT 
    r.id,
    r.score,
    r.exam_date,
    c.category_name
FROM res r
LEFT JOIN categories c ON r.category_id = c.id
WHERE r.student_id = '$student_id'
ORDER BY r.exam_date DESC";

$results = mysqli_query($conn, $results_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student_dash.css">

    <title>Student Dashboard - Quiz Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php include "navbar.php"?>    

    <!-- Main Container -->
    <div class="main-container">
    <?php include "sidebar.php"?>    


    <!-- Main Content -->
        <main class="content">
            <div class="content-header">
                <h1 class="page-title">Dashboard</h1>
                <p class="subtitle">Track your progress and explore quiz categories</p>
            </div>


            <!-- Quiz Results Table -->
            <div class="results-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h2 class="section-title">Your Quiz Results</h2>
                </div>
                <div class="table-container">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Category</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($results) > 0): ?>
                                <?php while($result = mysqli_fetch_assoc($results)): 
                                    $date = date('M d, Y - h:i A', strtotime($result['exam_date']));
                                    $score = $result['score'];
                                    $scoreClass = $score >= 80 ? 'score-excellent' : ($score >= 60 ? 'score-good' : ($score >= 40 ? 'score-average' : 'score-poor'));
                                ?>
                                    <tr>
                                        <td>
                                            <div class="date-cell">
                                                <div class="date-icon">
                                                    <i class="fas fa-calendar"></i>
                                                </div>
                                                <?= $date ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-badge">
                                                <i class="fas fa-tag"></i>
                                                <?= htmlspecialchars($result['category_name']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="score-cell">
                                                <span class="score-badge <?= $scoreClass ?>">
                                                    <i class="fas fa-star"></i>
                                                    <?= $score ?>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="no-results">
                                        <i class="fas fa-inbox no-results-icon"></i>
                                        <div class="no-results-text">No quiz results yet</div>
                                        <div class="no-results-subtext">Start taking quizzes to see your scores here!</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
<?php  include "footer.php"?>
</body>
</html>