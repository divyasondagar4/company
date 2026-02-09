<?php
session_start();
include 'db.php';

// Fetch all results with student name and category
$sql = "
SELECT r.id, s.username AS student_name, c.category_name, r.score, r.exam_date 
FROM res r
JOIN students s ON r.student_id = s.id
JOIN categories c ON r.category_id = c.id
ORDER BY r.exam_date DESC
";

$res = mysqli_query($conn, $sql);

// Calculate statistics
$total_exams = mysqli_num_rows($res);
$stats_sql = "SELECT AVG(score) as avg_score, MAX(score) as max_score, MIN(score) as min_score FROM res";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

include "sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets//admin.css//manage_student.css" rel="stylesheet">

    <style>
   
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1>
                <i class="fas fa-chart-line"></i>
                Quiz Results Dashboard
            </h1>
            <p class="header-subtitle">Monitor and analyze student performance across all quizzes</p>
        </div>

        <!-- Statistics Section -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3><?= $total_exams ?></h3>
                <p>Total Exams Completed</p>
            </div>

            <div class="stat-card average">
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3><?= $stats['avg_score'] ? number_format($stats['avg_score'], 1) : '0' ?>%</h3>
                <p>Average Score</p>
            </div>

            <div class="stat-card highest">
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3><?= $stats['max_score'] ?? '0' ?>%</h3>
                <p>Highest Score</p>
            </div>

            <div class="stat-card lowest">
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <h3><?= $stats['min_score'] ?? '0' ?>%</h3>
                <p>Lowest Score</p>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-header">
                <h4>
                    <i class="fas fa-table"></i>
                    Detailed Results
                </h4>
                <div class="table-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by student or category..." onkeyup="searchTable()">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="filter-dropdown">
                        <select id="categoryFilter" onchange="filterByCategory()">
                            <option value="">All Categories</option>
                            <?php
                            $categories_sql = "SELECT DISTINCT category_name FROM categories ORDER BY category_name";
                            $categories_result = mysqli_query($conn, $categories_sql);
                            while($cat = mysqli_fetch_assoc($categories_result)):
                            ?>
                            <option value="<?= htmlspecialchars($cat['category_name']) ?>">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                        <i class="fas fa-filter"></i>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table" id="resultsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user-graduate"></i> Student Name</th>
                            <th><i class="fas fa-tags"></i> Category</th>
                            <th><i class="fas fa-percentage"></i> Score</th>
                            <th><i class="fas fa-calendar-alt"></i> Exam Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    // Re-fetch results for display
                    mysqli_data_seek($res, 0);
                    if(mysqli_num_rows($res) > 0): 
                    ?>
                        <?php while($row = mysqli_fetch_assoc($res)): 
                            // Determine score badge class
                            $score = $row['score'];
                            if($score >= 80) {
                                $score_class = 'score-excellent';
                            } elseif($score >= 60) {
                                $score_class = 'score-good';
                            } elseif($score >= 40) {
                                $score_class = 'score-average';
                            } else {
                                $score_class = 'score-poor';
                            }
                        ?>
                        <tr>
                            <td>
                                <span class="id-badge">#<?= $row['id'] ?></span>
                            </td>
                            <td>
                                <span class="student-name"><?= htmlspecialchars($row['student_name']) ?></span>
                            </td>
                            <td>
                                <span class="category-badge"><?= htmlspecialchars($row['category_name']) ?></span>
                            </td>
                            <td>
                                <span class="score-badge <?= $score_class ?>">
                                    <?= $row['score'] ?>%
                                </span>
                            </td>
                            <td>
                                <span class="date-text">
                                    <i class="far fa-calendar"></i>
                                    <?= date('M d, Y', strtotime($row['exam_date'])) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>No Results Found</h5>
                                    <p>There are no quiz results available yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                    </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="admin_dashboard.php" class="btn-custom btn-primary-custom">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <button onclick="exportToCSV()" class="btn-custom btn-success-custom">
                <i class="fas fa-download"></i>
                Export to CSV
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('resultsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 1; j < cells.length - 1; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }

        // Filter by category
        function filterByCategory() {
            const select = document.getElementById('categoryFilter');
            const filter = select.value.toLowerCase();
            const table = document.getElementById('resultsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const categoryCell = row.getElementsByTagName('td')[2];
                
                if (categoryCell) {
                    const categoryText = categoryCell.textContent || categoryCell.innerText;
                    if (filter === '' || categoryText.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        }

        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('resultsTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                if (row.style.display !== 'none') {
                    const cols = row.querySelectorAll('td, th');
                    let csvRow = [];
                    
                    for (let j = 0; j < cols.length; j++) {
                        let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                        data = data.replace(/"/g, '""');
                        csvRow.push('"' + data + '"');
                    }
                    
                    csv.push(csvRow.join(','));
                }
            }

            const csvString = csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'quiz_results_' + new Date().getTime() + '.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
</body>
</html>