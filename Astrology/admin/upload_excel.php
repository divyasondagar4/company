<?php
// Increase memory and time limits for large file processing
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', '600');
set_time_limit(600);

$adminTitle = 'Upload Excel';
require_once 'header.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Chunk-based read filter — reads only a specific range of rows at a time
 * to avoid memory exhaustion on large files.
 */
class ChunkReadFilter implements IReadFilter {
    private $startRow = 0;
    private $endRow = 0;

    public function setRows($startRow, $chunkSize) {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($columnAddress, $row, $worksheetName = ''): bool {
        if ($row >= $this->startRow && $row < $this->endRow) {
            return true;
        }
        return false;
    }
}

$success = $error = '';
$importCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel'])) {
    $uploadType = $_POST['upload_type'] ?? 'panchang';
    $file = $_FILES['excel'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate file type — support Excel and CSV
    if (!in_array($ext, ['csv', 'txt', 'xlsx', 'xls'])) {
        $error = "Please upload an Excel (.xlsx, .xls) or CSV (.csv) file.";
    } elseif ($file['error'] !== 0) {
        $error = "File upload error. Please try again.";
    } else {
        // Save uploaded file
        $filename = 'upload_' . date('YmdHis') . '.' . $ext;
        $filepath = __DIR__ . '/../uploads/excel/' . $filename;

        // Ensure upload directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $filepath);

        // ========================================================
        // READ DATA: Use PhpSpreadsheet for xlsx/xls, fgetcsv for CSV
        // PhpSpreadsheet reads xlsx/xls as UTF-8 natively — all
        // languages (Gujarati, Hindi, Tamil, etc.) are preserved.
        // Uses chunk-based reading to handle large files without
        // running out of memory.
        // ========================================================
        $allRows = [];

        if (in_array($ext, ['xlsx', 'xls'])) {
            // --- Excel file: read via PhpSpreadsheet (chunked) ---
            try {
                $reader = IOFactory::createReaderForFile($filepath);
                $reader->setReadDataOnly(true); // Skip formatting — saves memory

                $chunkSize = 500;
                $chunkFilter = new ChunkReadFilter();
                $reader->setReadFilter($chunkFilter);

                $startRow = 1;
                $hasMoreRows = true;

                while ($hasMoreRows) {
                    $chunkFilter->setRows($startRow, $chunkSize);
                    $spreadsheet = $reader->load($filepath);
                    $worksheet = $spreadsheet->getActiveSheet();

                    $foundRows = false;
                    foreach ($worksheet->getRowIterator($startRow, $startRow + $chunkSize - 1) as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false); 

                        $rowData = [];
                        $hasData = false;

                        foreach ($cellIterator as $cell) {
                            $val = $cell->getValue();
                            $rowData[] = $val !== null ? (string)$val : '';
                            if ($val !== null && $val !== '') {
                                $hasData = true;
                            }
                        }

                        if ($hasData || ($startRow === 1 && $row->getRowIndex() === 1)) {
                            $allRows[] = $rowData;
                            $foundRows = true;
                        }
                    }

                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet);

                    if (!$foundRows) {
                        $hasMoreRows = false;
                    }
                    $startRow += $chunkSize;
                }
            } catch (Exception $e) {
                $error = "Failed to read Excel file: " . $e->getMessage();
            }
        } else {
            // --- Read CSV/TXT File natively (Very Fast) ---
            $content = file_get_contents($filepath);
            // Remove UTF-8 BOM if present
            $bom = pack('H*', 'EFBBBF');
            $content = preg_replace("/^$bom/", '', $content);
            
            // Detect and fix ANSI/Windows-1252 to UTF-8 for valid multilingual chars
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
            }
            file_put_contents($filepath, $content);

            $handle = fopen($filepath, 'r');
            if ($handle !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $hasData = false;
                    foreach ($row as $val) {
                        if (trim((string)$val) !== '') {
                            $hasData = true;
                            break;
                        }
                    }
                    if ($hasData || empty($allRows)) { // Keep Header
                        $allRows[] = $row;
                    }
                }
                fclose($handle);
            } else {
                $error = "Failed to open CSV file.";
            }
        }

        // ========================================================
        // PROCESS ROWS: Skip header, import data
        // ========================================================
        if (empty($error) && count($allRows) > 1) {
            // First row is header — skip it
            $headerRow = array_shift($allRows);

            // Ensure connection uses utf8mb4
            $conn->set_charset("utf8mb4");
            $conn->query("SET NAMES utf8mb4");
            $conn->query("SET CHARACTER SET utf8mb4");
            $conn->query("SET character_set_connection=utf8mb4");
            $conn->query("SET character_set_results=utf8mb4");
            $conn->query("SET character_set_client=utf8mb4");

            // Auto-fix table charset for multilingual support (Hindi, Gujarati, etc.)
            $conn->query("ALTER DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->query("ALTER TABLE panchang CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->query("ALTER TABLE muhurat CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            if ($conn->query("SHOW TABLES LIKE 'festivals'")->num_rows > 0) {
                $conn->query("ALTER TABLE festivals CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            if ($uploadType === 'panchang') {
                /**
                 * Excel columns (0-indexed):
                 * 0: date, 1: vara_no, 2: vara (day), 3: location,
                 * 4: sunrise, 5: sunset, 6: ayan_no, 7: ayan,
                 * 8: gujarati_month_no, 9: gujarati_month,
                 * 10: sun_lon, 11: moon_lon, 12: tithi_no, 13: tithi_end,
                 * 14: nak_no, 15: nak_start, 16: nakshatra, 17: nak_end,
                 * 18: vichudo, 19: vichudo_start, 20: vichudo_end,
                 * 21: yoga_no, 22: yoga, 23: yoga_end,
                 * 24: karana_no, 25: karana, 26: karana_end,
                 * 27: rahu_start, 28: rahu_end,
                 * 29: gulika_start, 30: gulika_end,
                 * 31: yama_start, 32: yama_end,
                 * 33: vikram_samvat,
                 * 34: Year, 35: Month, 36: Panchak Start Date, 37: Panchak End Date, 38: Details
                 */

                $stmt = $conn->prepare("INSERT INTO panchang (panchang_date, day_name, location, sunrise, sunset, ayan, gujarati_month,
                        sun_lon, moon_lon, tithi, tithi_end, nakshatra, nak_start, nak_end,
                        vichudo, vichudo_start, vichudo_end, yoga, yoga_end, karana, karana_end,
                        rahu_start, rahu_end, gulika_start, gulika_end, yama_start, yama_end,
                        vikram_samvat, panchak_start, panchak_end, details)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                        day_name=VALUES(day_name), location=VALUES(location),
                        sunrise=VALUES(sunrise), sunset=VALUES(sunset), ayan=VALUES(ayan), gujarati_month=VALUES(gujarati_month),
                        sun_lon=VALUES(sun_lon), moon_lon=VALUES(moon_lon), tithi=VALUES(tithi), tithi_end=VALUES(tithi_end),
                        nakshatra=VALUES(nakshatra), nak_start=VALUES(nak_start), nak_end=VALUES(nak_end),
                        vichudo=VALUES(vichudo), vichudo_start=VALUES(vichudo_start), vichudo_end=VALUES(vichudo_end),
                        yoga=VALUES(yoga), yoga_end=VALUES(yoga_end), karana=VALUES(karana), karana_end=VALUES(karana_end),
                        rahu_start=VALUES(rahu_start), rahu_end=VALUES(rahu_end),
                        gulika_start=VALUES(gulika_start), gulika_end=VALUES(gulika_end),
                        yama_start=VALUES(yama_start), yama_end=VALUES(yama_end),
                        vikram_samvat=VALUES(vikram_samvat), panchak_start=VALUES(panchak_start), panchak_end=VALUES(panchak_end), details=VALUES(details)");

                if (!$stmt) {
                    $error = "Prepare failed: " . $conn->error;
                } else {
                    foreach ($allRows as $row) {
                        if (empty($row[0])) continue;

                        $g = function($idx) use ($row) {
                            return trim($row[$idx] ?? '');
                        };

                        $date        = $g(0);
                        $day         = $g(2);
                        $location    = $g(3);
                        $sunrise     = $g(4);
                        $sunset      = $g(5);
                        $ayan        = $g(7);
                        $gu_month    = $g(9);
                        $sun_lon     = $g(10);
                        $moon_lon    = $g(11);
                        $tithi       = $g(12);
                        $tithi_end   = $g(13);
                        $nakshatra   = $g(16);
                        $nak_start   = $g(15);
                        $nak_end     = $g(17);
                        $vichudo     = $g(18);
                        $vichudo_s   = $g(19);
                        $vichudo_e   = $g(20);
                        $yoga        = $g(22);
                        $yoga_end    = $g(23);
                        $karana      = $g(25);
                        $karana_end  = $g(26);
                        $rahu_start  = $g(27);
                        $rahu_end    = $g(28);
                        $gulika_s    = $g(29);
                        $gulika_e    = $g(30);
                        $yama_s      = $g(31);
                        $yama_e      = $g(32);
                        $vikram      = $g(33);
                        $panchak_s   = $g(36);
                        $panchak_e   = $g(37);
                        $details     = $g(38);

                        // Parse date
                        $parsedDate = date('Y-m-d', strtotime($date));
                        if ($parsedDate === '1970-01-01') continue;

                        // Convert empty time strings to null for TIME columns
                        $nv_sunrise = $sunrise !== '' ? $sunrise : null;
                        $nv_sunset = $sunset !== '' ? $sunset : null;
                        $nv_rahu_start = $rahu_start !== '' ? $rahu_start : null;
                        $nv_rahu_end = $rahu_end !== '' ? $rahu_end : null;
                        $nv_gulika_s = $gulika_s !== '' ? $gulika_s : null;
                        $nv_gulika_e = $gulika_e !== '' ? $gulika_e : null;
                        $nv_yama_s = $yama_s !== '' ? $yama_s : null;
                        $nv_yama_e = $yama_e !== '' ? $yama_e : null;

                        $stmt->bind_param("sssssssssssssssssssssssssssssss",
                            $parsedDate, $day, $location,
                            $nv_sunrise, $nv_sunset,
                            $ayan, $gu_month, $sun_lon, $moon_lon,
                            $tithi, $tithi_end, $nakshatra, $nak_start, $nak_end,
                            $vichudo, $vichudo_s, $vichudo_e,
                            $yoga, $yoga_end, $karana, $karana_end,
                            $nv_rahu_start, $nv_rahu_end,
                            $nv_gulika_s, $nv_gulika_e,
                            $nv_yama_s, $nv_yama_e,
                            $vikram, $panchak_s, $panchak_e, $details
                        );

                        if ($stmt->execute()) {
                            $importCount++;
                        }
                    }
                    $stmt->close();
                }
            } elseif ($uploadType === 'muhurat') {
                // Expected columns: Title, Date, Start Time, End Time, Type, Description
                $stmt = $conn->prepare("INSERT INTO muhurat (title, muhurat_date, start_time, end_time, type, description)
                        VALUES (?, ?, ?, ?, ?, ?)");

                if (!$stmt) {
                    $error = "Prepare failed: " . $conn->error;
                } else {
                    foreach ($allRows as $row) {
                        if (empty($row[0])) continue;

                        $title = trim($row[0] ?? '');
                        $date = trim($row[1] ?? '');
                        $start = trim($row[2] ?? '');
                        $end = trim($row[3] ?? '');
                        $type = trim($row[4] ?? '');
                        $desc = trim($row[5] ?? '');

                        $parsedDate = date('Y-m-d', strtotime($date));
                        if ($parsedDate === '1970-01-01') continue;

                        $nv_start = $start !== '' ? $start : null;
                        $nv_end = $end !== '' ? $end : null;

                        $stmt->bind_param("ssssss", $title, $parsedDate, $nv_start, $nv_end, $type, $desc);

                        if ($stmt->execute()) {
                            $importCount++;
                        }
                    }
                    $stmt->close();
                }
            } elseif ($uploadType === 'festivals') {
                // Expected columns: Festival Name, Date, Description
                $stmt = $conn->prepare("INSERT INTO festivals (festival_name, festival_date, description)
                        VALUES (?, ?, ?)");

                if (!$stmt) {
                    $error = "Prepare failed: " . $conn->error;
                } else {
                    foreach ($allRows as $row) {
                        if (empty($row[0])) continue;

                        $name = trim($row[0] ?? '');
                        $date = trim($row[1] ?? '');
                        $desc = trim($row[2] ?? '');

                        $parsedDate = date('Y-m-d', strtotime($date));
                        if ($parsedDate === '1970-01-01') continue;

                        $stmt->bind_param("sss", $name, $parsedDate, $desc);

                        if ($stmt->execute()) {
                            $importCount++;
                        }
                    }
                    $stmt->close();
                }
            }

            if ($importCount > 0) {
                $success = "Successfully imported $importCount records from the uploaded file.";
            } else {
                $error = "No records were imported. Please check your file format.";
            }
        } elseif (empty($error)) {
            $error = "No data rows found in the file (only header or empty file).";
        }
    }
}
?>

<!-- Upload Loading Overlay -->
<div id="uploadOverlay" style="display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.65); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
    align-items:center; justify-content:center; flex-direction:column;">
  <div style="background:rgba(30,25,20,0.95); border:1px solid var(--chandan-gold);
    border-radius:16px; padding:2.5rem 3rem; text-align:center; box-shadow:0 0 40px rgba(197,151,59,0.3); min-width:380px;">
    <div class="upload-spinner" style="width:56px; height:56px; border:4px solid rgba(197,151,59,0.2);
      border-top:4px solid var(--chandan-gold); border-radius:50%;
      animation:uploadSpin 0.9s linear infinite; margin:0 auto 1.2rem;"></div>
    <h4 style="color:var(--chandan-gold); margin:0 0 0.5rem; font-size:1.2rem;">
      <i class="fas fa-file-upload me-2"></i><span id="uploadStatusText">Uploading File...</span>
    </h4>
    <p style="color:var(--text-secondary); margin:0 0 1rem; font-size:0.9rem;">
      Please wait while your data is being imported.
    </p>
    <!-- Progress Bar -->
    <div style="background:rgba(197,151,59,0.15); border-radius:8px; overflow:hidden; height:22px; position:relative;">
      <div id="uploadProgressBar" style="height:100%; width:0%; background:linear-gradient(135deg, var(--chandan-gold), #F4A83D);
        border-radius:8px; transition:width 0.3s ease; display:flex; align-items:center; justify-content:center;">
        <span id="uploadPercent" style="color:var(--sacred-maroon); font-size:0.75rem; font-weight:700; position:absolute; width:100%; text-align:center;">0%</span>
      </div>
    </div>
    <p id="uploadFileInfo" style="color:var(--soft-gray); margin:0.8rem 0 0; font-size:0.8rem;"></p>
  </div>
</div>

<style>
@keyframes uploadSpin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-file-excel me-2" style="color:var(--chandan-gold);"></i>Upload Excel / CSV Data</h2>
</div>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success"></div>
<?php endif; ?>
<?php if($error): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
<?php endif; ?>

<div class="row g-4">
  <!-- Upload Form -->
  <div class="col-lg-7">
    <div class="sacred-card">
      <h4><i class="fas fa-upload me-2" style="color:var(--chandan-gold);"></i>Upload Data File</h4>
      <p class="text-muted" style="font-size:0.9rem;">Upload an <strong>Excel (.xlsx, .xls)</strong> or <strong>CSV</strong> file with your Panchang or Muhurat data. All languages (Gujarati, Hindi, Tamil, etc.) are fully supported.</p>

      <form method="POST" enctype="multipart/form-data" class="form-sacred mt-4" id="uploadForm">
        <div class="mb-3">
          <label>Data Type</label>
          <select name="upload_type" class="form-select" id="uploadType">
            <option value="panchang">Panchang Data</option>
            <option value="muhurat">Muhurat Data</option>
            <option value="festivals">Festival Data</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Excel / CSV File</label>
          <input type="file" name="excel" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
          <small class="text-muted">Accepted: .xlsx, .xls, .csv — All languages supported (data is inserted exactly as in Excel)</small>
        </div>
        <button type="submit" class="btn-sacred">
          <i class="fas fa-upload"></i> Upload & Import
        </button>
      </form>
    </div>
  </div>

  <!-- Format Guide -->
  <div class="col-lg-5">
    <div class="sacred-card" id="panchangFormat">
      <h4><i class="fas fa-info-circle me-2" style="color:var(--chandan-gold);"></i>Panchang File Format</h4>
      <p class="text-muted" style="font-size:0.85rem;">Your Excel/CSV should have these columns in order (same as your Excel):</p>
      <ol style="font-size:0.8rem; color:var(--text-secondary); columns:2;">
        <li>date</li>
        <li>vara_no</li>
        <li>vara (day name)</li>
        <li>location</li>
        <li>sunrise</li>
        <li>sunset</li>
        <li>ayan_no</li>
        <li>ayan</li>
        <li>gujarati_month_no</li>
        <li>gujarati_month</li>
        <li>sun_lon</li>
        <li>moon_lon</li>
        <li>tithi_no</li>
        <li>tithi_end</li>
        <li>nak_no</li>
        <li>nak_start</li>
        <li>nakshatra</li>
        <li>nak_end</li>
        <li>vichudo</li>
        <li>vichudo_start</li>
        <li>vichudo_end</li>
        <li>yoga_no</li>
        <li>yoga</li>
        <li>yoga_end</li>
        <li>karana_no</li>
        <li>karana</li>
        <li>karana_end</li>
        <li>rahu_start</li>
        <li>rahu_end</li>
        <li>gulika_start</li>
        <li>gulika_end</li>
        <li>yama_start</li>
        <li>yama_end</li>
        <li>vikram_samvat</li>
        <li>Year</li>
        <li>Month</li>
        <li>Panchak Start Date</li>
        <li>Panchak End Date</li>
        <li>Details</li>
      </ol>
    </div>

    <div class="sacred-card mt-3" id="muhuratFormat" style="display:none;">
      <h4><i class="fas fa-info-circle me-2" style="color:var(--chandan-gold);"></i>Muhurat File Format</h4>
      <p class="text-muted" style="font-size:0.85rem;">Your Excel/CSV should have these columns in order:</p>
      <ol style="font-size:0.85rem; color:var(--text-secondary);">
        <li>Title</li>
        <li>Date (YYYY-MM-DD)</li>
        <li>Start Time (HH:MM)</li>
        <li>End Time (HH:MM)</li>
        <li>Type (Marriage/Griha Pravesh/Vastu/Temple Sthapna)</li>
        <li>Description</li>
      </ol>
    </div>

    <div class="sacred-card mt-3" id="festivalFormat" style="display:none;">
      <h4><i class="fas fa-info-circle me-2" style="color:var(--chandan-gold);"></i>Festival File Format</h4>
      <p class="text-muted" style="font-size:0.85rem;">Your Excel/CSV should have these columns in order:</p>
      <ol style="font-size:0.85rem; color:var(--text-secondary);">
        <li>Festival Name</li>
        <li>Date (YYYY-MM-DD)</li>
        <li>Description</li>
      </ol>
    </div>
  </div>
</div>

<script>
// Toggle format guide based on upload type
document.getElementById('uploadType').addEventListener('change', function() {
  document.getElementById('panchangFormat').style.display = this.value === 'panchang' ? 'block' : 'none';
  document.getElementById('muhuratFormat').style.display = this.value === 'muhurat' ? 'block' : 'none';
  document.getElementById('festivalFormat').style.display = this.value === 'festivals' ? 'block' : 'none';
});

// Upload with progress tracking via XMLHttpRequest
document.getElementById('uploadForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  var form = this;
  var formData = new FormData(form);
  var overlay = document.getElementById('uploadOverlay');
  var progressBar = document.getElementById('uploadProgressBar');
  var percentText = document.getElementById('uploadPercent');
  var statusText = document.getElementById('uploadStatusText');
  var fileInfo = document.getElementById('uploadFileInfo');
  
  // Get file name for display
  var fileInput = form.querySelector('input[type="file"]');
  if (fileInput.files.length > 0) {
    var file = fileInput.files[0];
    var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    fileInfo.textContent = file.name + ' (' + sizeMB + ' MB)';
  }
  
  // Show overlay
  overlay.style.display = 'flex';
  
  var xhr = new XMLHttpRequest();
  
  // Track upload progress
  xhr.upload.addEventListener('progress', function(e) {
    if (e.lengthComputable) {
      var pct = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = pct + '%';
      percentText.textContent = pct + '%';
      
      if (pct < 100) {
        statusText.textContent = 'Uploading File... ' + pct + '%';
      } else {
        statusText.textContent = 'Processing Data...';
        percentText.textContent = 'Processing...';
      }
    }
  });
  
  xhr.addEventListener('load', function() {
    // Upload and processing complete — reload page to show result
    statusText.textContent = 'Complete!';
    progressBar.style.width = '100%';
    percentText.textContent = '100%';
    
    // Create a temporary form to handle the response (reload page with result)
    setTimeout(function() {
      // Write the response to the page
      document.open();
      document.write(xhr.responseText);
      document.close();
    }, 500);
  });
  
  xhr.addEventListener('error', function() {
    overlay.style.display = 'none';
    alert('Upload failed. Please try again.');
  });
  
  xhr.open('POST', form.action || window.location.href, true);
  xhr.send(formData);
});
</script>

<?php require_once 'footer.php'; ?>
