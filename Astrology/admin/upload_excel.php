<?php
// Remove memory and time limits for massive file processing
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
set_time_limit(0);

// Check if disk space is dangerously low
$freeSpace = @disk_free_space(__DIR__);
if ($freeSpace !== false && $freeSpace < (10 * 1024 * 1024)) { // Less than 10MB
    $diskError = "Warning: Disk space is extremely low (" . round($freeSpace / (1024 * 1024), 2) . " MB). Uploads may fail.";
}

require_once 'header.php';
// Corrected vendor path
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
        $error = "File upload error. Code: " . $file['error'];
        if ($file['error'] == 1) $error .= " (Exceeds upload_max_filesize)";
        if ($file['error'] == 2) $error .= " (Exceeds MAX_FILE_SIZE)";
        if ($file['error'] == 3) $error .= " (Partial upload)";
        if ($file['error'] == 4) $error .= " (No file uploaded)";
        if ($file['error'] == 6) $error .= " (No temp folder)";
        if ($file['error'] == 7) $error .= " (Cannot write to disk)";
        if ($file['error'] == 8) $error .= " (Extension stopped upload)";
    } else {
        // Save uploaded file
        $filename = 'upload_' . date('YmdHis') . '.' . $ext;
        $filepath = __DIR__ . '/../uploads/excel/' . $filename;

        // Ensure upload directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $filepath);

        $allRows = [];

        if (in_array($ext, ['xlsx', 'xls'])) {
            if (!class_exists('ZipArchive')) {
                $error = "The PHP 'zip' extension is required to read Excel files but is not enabled on this server. Please upload your file as a .csv (Comma Delimited) file instead, or enable the zip extension in php.ini.";
            } else {
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
                } catch (\Throwable $e) {
                    $error = "Failed to read Excel file: " . $e->getMessage();
                }
            }
        } else {
            // Read CSV/TXT File
            $content = @file_get_contents($filepath);
            if ($content !== false) {
                $bom = pack('H*', 'EFBBBF');
                if (str_starts_with($content, $bom)) {
                    $content = substr($content, 3);
                }
                
                if (!mb_check_encoding($content, 'UTF-8')) {
                    $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
                }
                $content = str_replace(["\r\n", "\r"], "\n", $content);
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
            } else {
                $error = "Failed to read data file.";
            }
        }

        // PROCESS ROWS
        if (empty($error) && count($allRows) > 1) {
            array_shift($allRows); // Skip Header

            if ($uploadType === 'panchang') {
                $stmt = $conn->prepare("INSERT INTO panchang (
                    panchang_date, vara_no, day_name, location, sunrise, sunset, ayan_no, ayan, 
                    gujarati_month_no, gujarati_month, sun_lon, moon_lon, tithi_no, tithi, tithi_end, 
                    nak_no, nakshatra, nak_start, nak_end, yoga_no, yoga, yoga_end, karana_no, 
                    karana, karana_end, vikram_samvat, year, month, panchak_start, panchak_end, 
                    vichudo, vichudo_start, vichudo_end, rahu_start, rahu_end, gulika_start, 
                    gulika_end, yama_start, yama_end, details
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                day_name=VALUES(day_name), location=VALUES(location), sunrise=VALUES(sunrise), sunset=VALUES(sunset), ayan=VALUES(ayan), gujarati_month=VALUES(gujarati_month),
                sun_lon=VALUES(sun_lon), moon_lon=VALUES(moon_lon), tithi=VALUES(tithi), tithi_end=VALUES(tithi_end), nakshatra=VALUES(nakshatra), nak_start=VALUES(nak_start), nak_end=VALUES(nak_end),
                yoga=VALUES(yoga), yoga_end=VALUES(yoga_end), karana=VALUES(karana), karana_end=VALUES(karana_end), vikram_samvat=VALUES(vikram_samvat),
                panchak_start=VALUES(panchak_start), panchak_end=VALUES(panchak_end), vichudo=VALUES(vichudo), details=VALUES(details),
                vara_no=VALUES(vara_no), ayan_no=VALUES(ayan_no), gujarati_month_no=VALUES(gujarati_month_no), tithi_no=VALUES(tithi_no), nak_no=VALUES(nak_no), yoga_no=VALUES(yoga_no),
                karana_no=VALUES(karana_no), year=VALUES(year), month=VALUES(month), vichudo_start=VALUES(vichudo_start), vichudo_end=VALUES(vichudo_end),
                rahu_start=VALUES(rahu_start), rahu_end=VALUES(rahu_end), gulika_start=VALUES(gulika_start), gulika_end=VALUES(gulika_end), yama_start=VALUES(yama_start), yama_end=VALUES(yama_end)");

                if (!$stmt) {
                    $error = "Prepare failed: " . $conn->error;
                } else {
                    foreach ($allRows as $rowIdx => $row) {
                        if (empty($row[0])) continue;

                        // Map CSV columns to DB fields in EXACT order matching the SQL INSERT statement
                        // SQL order: panchang_date, vara_no, day_name, location, sunrise, sunset, ayan_no, ayan,
                        //   gujarati_month_no, gujarati_month, sun_lon, moon_lon, tithi_no, tithi, tithi_end,
                        //   nak_no, nakshatra, nak_start, nak_end, yoga_no, yoga, yoga_end, karana_no,
                        //   karana, karana_end, vikram_samvat, year, month, panchak_start, panchak_end,
                        //   vichudo, vichudo_start, vichudo_end, rahu_start, rahu_end, gulika_start,
                        //   gulika_end, yama_start, yama_end, details
                        //
                        // CSV (39 cols): date(0), vara_no(1), vara(2), location(3), sunrise(4), sunset(5),
                        //   ayan_no(6), ayan(7), gujarati_month_no(8), gujarati_month(9), sun_lon(10),
                        //   moon_lon(11), tithi_no(12), tithi_end(13), nak_no(14), nak_start(15),
                        //   nakshatra(16), nak_end(17), vichudo(18), vichudo_start(19), vichudo_end(20),
                        //   yoga_no(21), yoga(22), yoga_end(23), karana_no(24), karana(25), karana_end(26),
                        //   rahu_start(27), rahu_end(28), gulika_start(29), gulika_end(30),
                        //   yama_start(31), yama_end(32), vikram_samvat(33), Year(34), Month(35),
                        //   Panchak_Start(36), Panchak_End(37), Details(38)

                        $pDate = isset($row[0]) && $row[0] ? date('Y-m-d', strtotime($row[0])) : null;
                        if (!$pDate || $pDate === '1970-01-01') continue;

                        // Array order MUST match the 40 SQL INSERT placeholders exactly
                        $data = [
                            $pDate,                  // 1.  panchang_date
                            $row[1] ?? '',           // 2.  vara_no
                            $row[2] ?? '',           // 3.  day_name (vara in CSV)
                            $row[3] ?? '',           // 4.  location
                            $row[4] ?? '',           // 5.  sunrise
                            $row[5] ?? '',           // 6.  sunset
                            $row[6] ?? '',           // 7.  ayan_no
                            $row[7] ?? '',           // 8.  ayan
                            $row[8] ?? '',           // 9.  gujarati_month_no
                            $row[9] ?? '',           // 10. gujarati_month
                            $row[10] ?? '',          // 11. sun_lon
                            $row[11] ?? '',          // 12. moon_lon
                            $row[12] ?? '',          // 13. tithi_no
                            '',                      // 14. tithi (NOT in CSV — stays empty)
                            $row[13] ?? '',          // 15. tithi_end
                            $row[14] ?? '',          // 16. nak_no
                            $row[16] ?? '',          // 17. nakshatra  (CSV col 16)
                            $row[15] ?? '',          // 18. nak_start  (CSV col 15)
                            $row[17] ?? '',          // 19. nak_end
                            $row[21] ?? '',          // 20. yoga_no
                            $row[22] ?? '',          // 21. yoga
                            $row[23] ?? '',          // 22. yoga_end
                            $row[24] ?? '',          // 23. karana_no
                            $row[25] ?? '',          // 24. karana
                            $row[26] ?? '',          // 25. karana_end
                            $row[33] ?? '',          // 26. vikram_samvat
                            $row[34] ?? '',          // 27. year
                            $row[35] ?? '',          // 28. month
                            $row[36] ?? '',          // 29. panchak_start
                            $row[37] ?? '',          // 30. panchak_end
                            $row[18] ?? '',          // 31. vichudo
                            $row[19] ?? '',          // 32. vichudo_start
                            $row[20] ?? '',          // 33. vichudo_end
                            $row[27] ?? '',          // 34. rahu_start
                            $row[28] ?? '',          // 35. rahu_end
                            $row[29] ?? '',          // 36. gulika_start
                            $row[30] ?? '',          // 37. gulika_end
                            $row[31] ?? '',          // 38. yama_start
                            $row[32] ?? '',          // 39. yama_end
                            $row[38] ?? ''           // 40. details
                        ];

                        $types = str_repeat('s', 40);
                        $stmt->bind_param($types, ...$data);

                        if ($stmt->execute()) {
                            $importCount++;
                        }
                    }
                    $stmt->close();
                }
            } elseif ($uploadType === 'muhurat') {
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

                        $stmt->bind_param("ssssss", $title, $parsedDate, $start, $end, $type, $desc);

                        if ($stmt->execute()) {
                            $importCount++;
                        }
                    }
                    $stmt->close();
                }
            } elseif ($uploadType === 'festivals') {
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
                $success = "Successfully imported $importCount records.";
            } else {
                $error = "No records were imported. Please check your file format.";
            }
        } elseif (empty($error)) {
            $error = "No data rows found in the file.";
        }
    }
}
?>

<!-- Upload Loading Overlay -->
<div id="uploadOverlay" style="display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
    align-items:center; justify-content:center; flex-direction:column;">
  <div style="background:rgba(44,24,16,0.95); border:1px solid var(--chandan-gold);
    border-radius:20px; padding:3rem; text-align:center; box-shadow:0 0 50px rgba(197,151,59,0.4); min-width:400px;">
    <div class="upload-spinner" style="width:64px; height:64px; border:4px solid rgba(197,151,59,0.2);
      border-top:4px solid var(--chandan-gold); border-radius:50%;
      animation:uploadSpin 1s linear infinite; margin:0 auto 1.5rem;"></div>
    <h4 style="color:var(--chandan-gold); margin:0 0 0.8rem; font-size:1.4rem; font-family:'Cinzel', serif;">
      <i class="fas fa-file-upload me-2"></i><span id="uploadStatusText">Uploading...</span>
    </h4>
    <p style="color:var(--chandan-light); opacity:0.8; margin:0 0 1.5rem; font-size:1rem;">
      Bringing your sacred data into the system.
    </p>
    <div style="background:rgba(197,151,59,0.1); border-radius:10px; overflow:hidden; height:24px; position:relative; border:1px solid rgba(197,151,59,0.3);">
      <div id="uploadProgressBar" style="height:100%; width:0%; background:linear-gradient(90deg, #C5973B, #E6C17A);
        transition:width 0.4s ease; display:flex; align-items:center; justify-content:center;">
        <span id="uploadPercent" style="color:#2C1810; font-size:0.8rem; font-weight:800; position:absolute; width:100%; text-align:center;">0%</span>
      </div>
    </div>
    <p id="uploadFileInfo" style="color:var(--chandan-gold); margin:1rem 0 0; font-size:0.85rem; opacity:0.7;"></p>
  </div>
</div>

<style>
@keyframes uploadSpin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.sacred-upload-card {
    background: linear-gradient(145deg, #FFFDF9, #F5F1E9);
    border: 2px solid var(--chandan-gold);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 15px 35px rgba(44,24,16,0.08);
    position: relative;
    overflow: hidden;
}
.sacred-upload-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 6px;
    background: linear-gradient(90deg, #2C1810, #C5973B, #2C1810);
}
</style>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 3px solid var(--chandan-gold); padding-bottom: 0.8rem; display: inline-block;">
            <i class="fas fa-file-excel me-3" style="color:var(--chandan-gold);"></i>Excel / CSV Data Portal
        </h2>
    </div>

    <?php if($success): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success"></div>
    <?php endif; ?>
    <?php if($error): ?>
      <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
    <?php endif; ?>

    <div class="row g-5">
      <?php if(isset($diskError)): ?>
        <div class="col-12">
          <div class="alert alert-warning shadow-sm border-2">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $diskError; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="col-lg-7">
        <div class="sacred-upload-card h-100">
          <h4 style="font-family:'Cinzel', serif; color:var(--sacred-maroon);"><i class="fas fa-upload me-2" style="color:var(--chandan-gold);"></i>Synchronize Records</h4>
          <p class="text-muted mt-3">Upload your formatted Excel (.xlsx) or CSV files. Multilingual content in Gujarati, Hindi, and others is fully preserved during import.</p>

          <form action="<?php echo SITE_URL; ?>/admin/upload_excel" method="POST" enctype="multipart/form-data" class="form-sacred mt-4" id="uploadForm">
            <div class="mb-4">
              <label class="fw-bold mb-2">Target Data Source</label>
              <select name="upload_type" class="form-select border-2" id="uploadType" style="border-radius:10px;">
                <option value="panchang">Panchang Data (39 Columns)</option>
                <option value="muhurat">Muhurat Data</option>
                <option value="festivals">Festival Calendar</option>
              </select>
            </div>
            <div class="mb-5">
              <label class="fw-bold mb-2">Select Specification File</label>
              <input type="file" name="excel" class="form-control border-2" accept=".csv,.txt,.xlsx,.xls" required style="border-radius:10px; padding:0.6rem;">
              <div class="mt-2 text-muted" style="font-size:0.8rem;">
                <i class="fas fa-info-circle me-1"></i> Recommended format: .xlsx with UTF-8 encoding
              </div>
            </div>
            <button type="submit" class="btn-sacred w-100 py-3 shadow-lg" style="background: linear-gradient(to right, #5B1A18, #8C6239); border: 2px solid var(--chandan-gold); transform: translateY(0); transition: all 0.3s ease; border-radius: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
              <i class="fas fa-magic me-2"></i> Execute Smart Import
            </button>
            <style>
              .btn-sacred:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.3); border-color: #fff !important; }
            </style>
          </form>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="sacred-card h-100 border-2 shadow-sm" style="border-radius:20px; border-color:rgba(197,151,59,0.3);">
          <div id="panchangFormat">
            <h5 style="font-family:'Cinzel', serif; color:var(--sacred-maroon); border-bottom:1px solid #eee; padding-bottom:1rem;">
                <i class="fas fa-list-ol me-2" style="color:var(--chandan-gold);"></i>Panchang Column Order (39 Cols)
            </h5>
            <div class="mt-3" style="max-height:400px; overflow-y:auto; padding-right:10px;">
                <ol style="font-size:0.85rem; color:#444; line-height:2;">
                  <li><strong>date</strong> (YYYY-MM-DD)</li>
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
                  <li>Panchak Start</li>
                  <li>Panchak End</li>
                  <li>Details</li>
                </ol>
            </div>
          </div>

          <div id="muhuratFormat" style="display:none;">
            <h5 style="font-family:'Cinzel', serif; color:var(--sacred-maroon);"><i class="fas fa-clock me-2" style="color:var(--chandan-gold);"></i>Muhurat Schema</h5>
            <ol class="mt-4" style="line-height:2.5;">
              <li>Title</li>
              <li>Date (YYYY-MM-DD)</li>
              <li>Start Time</li>
              <li>End Time</li>
              <li>Type (e.g. Marriage)</li>
              <li>Description</li>
            </ol>
          </div>

          <div id="festivalFormat" style="display:none;">
            <h5 style="font-family:'Cinzel', serif; color:var(--sacred-maroon);"><i class="fas fa-om me-2" style="color:var(--chandan-gold);"></i>Festival Schema</h5>
            <ol class="mt-4" style="line-height:2.5;">
              <li>Festival Name</li>
              <li>Date (YYYY-MM-DD)</li>
              <li>Description</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
document.getElementById('uploadType').addEventListener('change', function() {
  document.getElementById('panchangFormat').style.display = this.value === 'panchang' ? 'block' : 'none';
  document.getElementById('muhuratFormat').style.display = this.value === 'muhurat' ? 'block' : 'none';
  document.getElementById('festivalFormat').style.display = this.value === 'festivals' ? 'block' : 'none';
});

document.getElementById('uploadForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  var form = this;
  var formData = new FormData(form);
  var overlay = document.getElementById('uploadOverlay');
  var progressBar = document.getElementById('uploadProgressBar');
  var percentText = document.getElementById('uploadPercent');
  var statusText = document.getElementById('uploadStatusText');
  var fileInfo = document.getElementById('uploadFileInfo');
  
  var fileInput = form.querySelector('input[type="file"]');
  if (fileInput.files.length > 0) {
    var file = fileInput.files[0];
    var sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    fileInfo.textContent = file.name + ' (' + sizeMB + ' MB)';
  }
  
  overlay.style.display = 'flex';
  
  var xhr = new XMLHttpRequest();
  
  xhr.upload.addEventListener('progress', function(e) {
    if (e.lengthComputable) {
      var pct = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = pct + '%';
      percentText.textContent = pct + '%';
      
      if (pct < 100) {
        statusText.textContent = 'Uploading... ' + pct + '%';
      } else {
        statusText.textContent = 'Processing...';
        percentText.textContent = 'Working';
      }
    }
  });
  
  xhr.addEventListener('load', function() {
    statusText.textContent = 'Success!';
    progressBar.style.width = '100%';
    percentText.textContent = '100%';
    
    setTimeout(function() {
      document.open();
      document.write(xhr.responseText);
      document.close();
    }, 600);
  });
  
  xhr.addEventListener('error', function() {
    overlay.style.display = 'none';
    alert('Upload failed. Connection error.');
  });
  
  xhr.open('POST', form.action, true);
  xhr.send(formData);
});
</script>

<?php require_once 'footer.php'; ?>
