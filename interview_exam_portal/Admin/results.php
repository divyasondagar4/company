<?php
session_start();
include '../db.php';

$sql = "
SELECT 
    r.id, 
    s.username AS student_name, 
    c.category_name, 
    r.score AS correct_answers, 
    r.exam_date,
    (SELECT COUNT(*) FROM questions q WHERE q.category_id = r.category_id) AS total_questions
FROM res r
JOIN students s ON r.student_id = s.id
JOIN categories c ON r.category_id = c.id
ORDER BY r.exam_date DESC
";

$res = mysqli_query($conn, $sql);

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

<!-- PDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- jsPDF Table Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/admin.css/results.css" rel="stylesheet">
</head>

<body>
<div class="main-container">

    <div class="header-section">
        <h1><i class="fas fa-chart-line"></i> Quiz Results Dashboard</h1>
        <p class="header-subtitle">Monitor and analyze student performance</p>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h4><i class="fas fa-table"></i> Detailed Results</h4>

            <div class="table-controls">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchTable()">
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
                </div>
            </div>
        </div>

        <div class="table-responsive" id="tableWrapper">
            <table class="custom-table" id="resultsTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Category</th>
                    <th>Total</th>
                    <th>Right</th>
                    <th>Wrong</th>
                    <th>Score</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php mysqli_data_seek($res, 0); ?>
                <?php while($row = mysqli_fetch_assoc($res)): 
                    $total_q = intval($row['total_questions']);
                    $right = intval($row['correct_answers']);
                    $wrong = max(0, $total_q - $right);
                    $score_percent = $total_q > 0 ? round(($right / $total_q) * 100) : 0;
                ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td><?= $total_q ?></td>
                    <td><?= $right ?></td>
                    <td><?= $wrong ?></td>
                    <td><?= $score_percent ?>%</td>
                    <td><?= date('M d, Y', strtotime($row['exam_date'])) ?></td>
                    <td>
                        <a href="view_result.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Buttons -->
    <div class="action-buttons mt-4">
        <a href="admin_dashboard.php" class="btn btn-primary">Back</a>
        <button onclick="exportToCSV()" class="btn btn-success">CSV Download</button>
        <button onclick="downloadPDF()" class="btn btn-danger">PDF Download</button>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Search
function searchTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll("#resultsTable tbody tr");
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}

// Filter
function filterByCategory() {
    const filter = document.getElementById('categoryFilter').value.toLowerCase();
    const rows = document.querySelectorAll("#resultsTable tbody tr");
    rows.forEach(row => {
        const category = row.cells[2].innerText.toLowerCase();
        row.style.display = (!filter || category.includes(filter)) ? "" : "none";
    });
}

// CSV
function exportToCSV() {
    const rows = document.querySelectorAll("table tr");
    let csv = [];
    rows.forEach(row => {
        let cols = row.querySelectorAll("td, th");
        let rowData = [];
        cols.forEach(col => rowData.push('"' + col.innerText + '"'));
        csv.push(rowData.join(","));
    });
    const blob = new Blob([csv.join("\n")], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = "quiz_results.csv";
    a.click();
}

async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4'); 

    doc.setFontSize(18);
    doc.text("Quiz Results Full Report", 40, 40);

    // Get table data
    const table = document.getElementById("resultsTable");
    const rows = table.querySelectorAll("tbody tr");

    let body = [];

    rows.forEach(row => {
        if (row.style.display !== "none") {
            const cells = row.querySelectorAll("td");
            body.push([
                cells[0].innerText,
                cells[1].innerText,
                cells[2].innerText,
                cells[3].innerText,
                cells[4].innerText,
                cells[5].innerText,
                cells[6].innerText,
                cells[7].innerText
            ]);
        }
    });

    doc.autoTable({
        startY: 60,
        head: [["ID", "Student", "Category", "Total", "Right", "Wrong", "Score", "Date"]],
        body: body,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185] },
        theme: 'grid',
        margin: { left: 40, right: 40 }
    });

    doc.save("quiz_results_full_report.pdf");
}
</script>

</body>
</html>
