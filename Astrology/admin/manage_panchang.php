<?php
require_once 'header.php';

$success = $error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM panchang WHERE id=$id")) {
        $success = "Panchang entry deleted.";
    } else {
        $error = "Delete failed: " . $conn->error;
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $date = $conn->real_escape_string($_POST['panchang_date'] ?? '');
    $day = $conn->real_escape_string($_POST['day_name'] ?? '');
    $vara_no = (int)($_POST['vara_no'] ?? 0);
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $sunrise = $conn->real_escape_string($_POST['sunrise'] ?? '');
    $sunset = $conn->real_escape_string($_POST['sunset'] ?? '');
    $ayan = $conn->real_escape_string($_POST['ayan'] ?? '');
    $ayan_no = (int)($_POST['ayan_no'] ?? 0);
    $gu_month = $conn->real_escape_string($_POST['gujarati_month'] ?? '');
    $gu_month_no = (int)($_POST['gujarati_month_no'] ?? 0);
    $sun_lon = $conn->real_escape_string($_POST['sun_lon'] ?? '');
    $moon_lon = $conn->real_escape_string($_POST['moon_lon'] ?? '');
    $tithi = $conn->real_escape_string($_POST['tithi'] ?? '');
    $tithi_no = (int)($_POST['tithi_no'] ?? 0);
    $tithi_end = $conn->real_escape_string($_POST['tithi_end'] ?? '');
    $nakshatra = $conn->real_escape_string($_POST['nakshatra'] ?? '');
    $nak_no = (int)($_POST['nak_no'] ?? 0);
    $nak_start = $conn->real_escape_string($_POST['nak_start'] ?? '');
    $nak_end = $conn->real_escape_string($_POST['nak_end'] ?? '');
    $vichudo = $conn->real_escape_string($_POST['vichudo'] ?? '');
    $vichudo_start = $conn->real_escape_string($_POST['vichudo_start'] ?? '');
    $vichudo_end = $conn->real_escape_string($_POST['vichudo_end'] ?? '');
    $yoga = $conn->real_escape_string($_POST['yoga'] ?? '');
    $yoga_no = (int)($_POST['yoga_no'] ?? 0);
    $yoga_end = $conn->real_escape_string($_POST['yoga_end'] ?? '');
    $karana = $conn->real_escape_string($_POST['karana'] ?? '');
    $karana_no = (int)($_POST['karana_no'] ?? 0);
    $karana_end = $conn->real_escape_string($_POST['karana_end'] ?? '');
    $rahu_start = $conn->real_escape_string($_POST['rahu_start'] ?? '');
    $rahu_end = $conn->real_escape_string($_POST['rahu_end'] ?? '');
    $gulika_start = $conn->real_escape_string($_POST['gulika_start'] ?? '');
    $gulika_end = $conn->real_escape_string($_POST['gulika_end'] ?? '');
    $yama_start = $conn->real_escape_string($_POST['yama_start'] ?? '');
    $yama_end = $conn->real_escape_string($_POST['yama_end'] ?? '');
    $vikram = $conn->real_escape_string($_POST['vikram_samvat'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $month = (int)($_POST['month'] ?? 0);
    $panchak_start = $conn->real_escape_string($_POST['panchak_start'] ?? '');
    $panchak_end = $conn->real_escape_string($_POST['panchak_end'] ?? '');
    $details = $conn->real_escape_string($_POST['details'] ?? '');

    // PDF upload
    $pdfFile = '';
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === 0) {
        $pdfName = 'panchang_' . $date . '_' . time() . '.pdf';
        move_uploaded_file($_FILES['pdf']['tmp_name'], __DIR__ . '/../uploads/pdf/' . $pdfName);
        $pdfFile = $pdfName;
    }

    $nullOrVal = function($v) { return $v ? "'$v'" : "NULL"; };

    if ($id > 0) {
        $pdfUpdate = $pdfFile ? ", pdf_file='$pdfFile'" : '';
        $sql = "UPDATE panchang SET panchang_date='$date', vara_no=$vara_no, day_name='$day', location='$location',
                sunrise={$nullOrVal($sunrise)}, sunset={$nullOrVal($sunset)},
                ayan_no=$ayan_no, ayan='$ayan', gujarati_month_no=$gu_month_no, gujarati_month='$gu_month', 
                sun_lon='$sun_lon', moon_lon='$moon_lon', tithi_no=$tithi_no, tithi='$tithi', tithi_end='$tithi_end', 
                nak_no=$nak_no, nakshatra='$nakshatra', nak_start='$nak_start', nak_end='$nak_end',
                vichudo='$vichudo', vichudo_start='$vichudo_start', vichudo_end='$vichudo_end',
                yoga_no=$yoga_no, yoga='$yoga', yoga_end='$yoga_end', karana_no=$karana_no, karana='$karana', karana_end='$karana_end',
                rahu_start={$nullOrVal($rahu_start)}, rahu_end={$nullOrVal($rahu_end)},
                gulika_start={$nullOrVal($gulika_start)}, gulika_end={$nullOrVal($gulika_end)},
                yama_start={$nullOrVal($yama_start)}, yama_end={$nullOrVal($yama_end)},
                vikram_samvat='$vikram', year=$year, month=$month, panchak_start='$panchak_start', panchak_end='$panchak_end',
                details='$details' $pdfUpdate WHERE id=$id";
        if ($conn->query($sql)) $success = "Panchang updated successfully.";
        else $error = "Update failed: " . $conn->error;
    } else {
        $sql = "INSERT INTO panchang (panchang_date, vara_no, day_name, location, sunrise, sunset, ayan_no, ayan, 
                gujarati_month_no, gujarati_month, sun_lon, moon_lon, tithi_no, tithi, tithi_end, 
                nak_no, nakshatra, nak_start, nak_end,
                vichudo, vichudo_start, vichudo_end, yoga_no, yoga, yoga_end, karana_no, karana, karana_end,
                rahu_start, rahu_end, gulika_start, gulika_end, yama_start, yama_end,
                vikram_samvat, year, month, panchak_start, panchak_end, details, pdf_file)
                VALUES ('$date', $vara_no, '$day', '$location', {$nullOrVal($sunrise)}, {$nullOrVal($sunset)}, 
                $ayan_no, '$ayan', $gu_month_no, '$gu_month', '$sun_lon', '$moon_lon', $tithi_no, '$tithi', '$tithi_end',
                $nak_no, '$nakshatra', '$nak_start', '$nak_end',
                '$vichudo', '$vichudo_start', '$vichudo_end', $yoga_no, '$yoga', '$yoga_end', $karana_no, '$karana', '$karana_end',
                {$nullOrVal($rahu_start)}, {$nullOrVal($rahu_end)},
                {$nullOrVal($gulika_start)}, {$nullOrVal($gulika_end)},
                {$nullOrVal($yama_start)}, {$nullOrVal($yama_end)},
                '$vikram', $year, $month, '$panchak_start', '$panchak_end', '$details', '$pdfFile')";
        if ($conn->query($sql)) $success = "Panchang added successfully.";
        else $error = "Failed to add: " . $conn->error;
    }
}

// Location and Date Range filtering
$selectedLocation = $_GET['filter_loc'] ?? '';
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

// Get available locations for dropdown
$locationsResult = $conn->query("SELECT DISTINCT location FROM panchang WHERE location IS NOT NULL AND location != '' ORDER BY location ASC");

$totalRows = 0;
$result = null;

if ($selectedLocation) {
    $loc = $conn->real_escape_string($selectedLocation);
    $where = "location = '$loc'";
    if ($fromDate && $toDate) {
        $fDate = $conn->real_escape_string($fromDate);
        $tDate = $conn->real_escape_string($toDate);
        $where .= " AND panchang_date BETWEEN '$fDate' AND '$tDate'";
    } elseif ($fromDate) {
        $fDate = $conn->real_escape_string($fromDate);
        $where .= " AND panchang_date >= '$fDate'";
    } elseif ($toDate) {
        $tDate = $conn->real_escape_string($toDate);
        $where .= " AND panchang_date <= '$tDate'";
    }
    
    $totalRows = $conn->query("SELECT COUNT(*) as c FROM panchang WHERE $where")->fetch_assoc()['c'];
    $result = $conn->query("SELECT * FROM panchang WHERE $where ORDER BY panchang_date ASC");
}
?>

<div class="text-center mb-4">
    <h2 style="font-family: 'Cinzel', serif; color: var(--sacred-maroon); border-bottom: 2px solid var(--chandan-gold); padding-bottom: 0.5rem; display: inline-block;">
        <i class="fas fa-sun me-2" style="color:var(--chandan-gold);"></i>Manage Panchang
    </h2>
</div>

<!-- Add New Button 
<div class="mb-4">
    <button type="button" class="btn-sacred w-100" style="padding:1rem; font-size:1.1rem; text-transform:uppercase; letter-spacing:1px;" onclick="document.getElementById('addPanchangFormContainer').style.display = document.getElementById('addPanchangFormContainer').style.display === 'none' ? 'block' : 'none';">
        <i class="fas fa-plus-circle me-2"></i> Add New Panchang Entry
    </button>
</div>
-->

<div class="sacred-card mb-4 shadow-lg p-0" style="border: 2px solid var(--chandan-gold); background: linear-gradient(135deg, #2C1810, #1A0F0A); overflow: hidden;">
  <div class="d-flex align-items-stretch">
    <!-- Filter Sidebar Decorator -->
    <div class="d-none d-md-flex align-items-center justify-content-center px-4" style="background: rgba(197,151,59,0.1); border-right: 1px solid rgba(197,151,59,0.3);">
      <div class="text-center">
         <i class="fas fa-filter fa-lg mb-2" style="color:var(--chandan-gold);"></i>
         <div style="font-family:'Cinzel', serif; color:var(--chandan-gold); font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">Filters</div>
      </div>
    </div>

    <!-- Main Filter Controls -->
    <div class="flex-grow-1 p-3">
      <form method="GET" id="filterForm" class="row g-3">
        <div class="col-md-4">
          <label class="form-label small text-uppercase fw-bold mb-1" style="color:rgba(197,151,59,0.8); font-size:0.65rem; letter-spacing:1px;">Location</label>
          <div class="input-group">
            <span class="input-group-text bg-transparent" style="border-right:none; border-color:rgba(197,151,59,0.3);"><i class="fas fa-map-marker-alt" style="color:var(--chandan-gold); font-size:0.8rem;"></i></span>
            <select name="filter_loc" class="form-select form-select-sacred" onchange="this.form.submit()" style="border-left:none;">
              <option value="">-- All Locations --</option>
              <?php if($locationsResult && $locationsResult->num_rows > 0): $locationsResult->data_seek(0); ?>
                <?php while($l = $locationsResult->fetch_assoc()): ?>
                  <option value="<?php echo htmlspecialchars($l['location']); ?>" <?php echo $selectedLocation === $l['location'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($l['location']); ?>
                  </option>
                <?php endwhile; ?>
              <?php endif; ?>
            </select>
          </div>
        </div>
        
        <div class="col-md-3">
          <label class="form-label small text-uppercase fw-bold mb-1" style="color:var(--chandan-gold); font-size:0.65rem; letter-spacing:1px;">From Date <span class="text-danger small">(REQ)</span></label>
          <div class="input-group cursor-pointer" onclick="this.querySelector('input').showPicker()">
            <span class="input-group-text bg-transparent" style="border-right:none; border-color:rgba(197,151,59,0.3);"><i class="fas fa-calendar-alt" style="color:var(--chandan-gold); font-size:0.8rem;"></i></span>
            <input type="date" name="from_date" class="form-control form-control-sacred" value="<?php echo $fromDate; ?>" onchange="this.form.submit()" style="border-left:none;">
          </div>
        </div>

        <div class="col-md-3">
          <label class="form-label small text-uppercase fw-bold mb-1" style="color:rgba(197,151,59,0.8); font-size:0.65rem; letter-spacing:1px;">To Date (Opt)</label>
          <div class="input-group cursor-pointer" onclick="this.querySelector('input').showPicker()">
            <span class="input-group-text bg-transparent" style="border-right:none; border-color:rgba(197,151,59,0.3);"><i class="fas fa-calendar-check" style="color:var(--chandan-gold); font-size:0.8rem;"></i></span>
            <input type="date" name="to_date" class="form-control form-control-sacred" value="<?php echo $toDate; ?>" onchange="this.form.submit()" style="border-left:none;">
          </div>
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <a href="manage_panchang" class="btn btn-sm w-100" style="border:1px solid rgba(197,151,59,0.3); color:rgba(197,151,59,0.6); font-size:0.75rem; text-transform:uppercase; letter-spacing:1px;">
             <i class="fas fa-undo me-1"></i>Reset
          </a>
        </div>
      </form>
    </div>

    <!-- Stat Display -->
    <div class="d-flex align-items-center bg-black bg-opacity-25 px-4" style="border-left: 1px solid rgba(197,151,59,0.3);">
      <div class="text-center">
        <div style="font-size:1.8rem; font-weight:800; color:var(--chandan-gold); line-height:1;"><?php echo $totalRows; ?></div>
        <div style="font-size:0.55rem; text-transform:uppercase; letter-spacing:1px; color:var(--chandan-light); opacity:0.6;">Total Found</div>
      </div>
    </div>
  </div>
</div>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success" data-redirect="manage_panchang.php"></div>
<?php endif; ?>
<?php if($error): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
<?php endif; ?>

<?php
// Edit mode
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editData = $conn->query("SELECT * FROM panchang WHERE id=$editId")->fetch_assoc();
}
// Show form automatically if we are editing
$displayForm = $editData ? 'block' : 'none';
?>

<!-- Add/Edit Form -->
<div id="addPanchangFormContainer" class="sacred-card mb-4" style="height:auto; display:<?php echo $displayForm; ?>;">
  <h4><i class="fas fa-<?php echo $editData ? 'edit' : 'plus'; ?> me-2" style="color:var(--chandan-gold);"></i><?php echo $editData ? 'Edit' : 'Add New'; ?> Panchang Entry</h4>
  <form method="POST" enctype="multipart/form-data" class="form-sacred mt-3">
    <?php if($editData): ?>
      <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
    <?php endif; ?>
    <div class="row g-3">
      <div class="col-md-2">
        <label>Date *</label>
        <input type="date" name="panchang_date" class="form-control" required value="<?php echo $editData['panchang_date'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Day Name</label>
        <div class="input-group input-group-sm">
          <input type="text" name="day_name" class="form-control" value="<?php echo htmlspecialchars($editData['day_name'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="vara_no" class="form-control" value="<?php echo htmlspecialchars($editData['vara_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Location</label>
        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($editData['location'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Sunrise</label>
        <input type="text" name="sunrise" class="form-control" value="<?php echo htmlspecialchars($editData['sunrise'] ?? ''); ?>" placeholder="HH:MM AM/PM">
      </div>
      <div class="col-md-2">
        <label>Sunset</label>
        <input type="text" name="sunset" class="form-control" value="<?php echo htmlspecialchars($editData['sunset'] ?? ''); ?>" placeholder="HH:MM AM/PM">
      </div>
      <div class="col-md-2">
        <label>Ayan</label>
        <div class="input-group input-group-sm">
          <input type="text" name="ayan" class="form-control" value="<?php echo htmlspecialchars($editData['ayan'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="ayan_no" class="form-control" value="<?php echo htmlspecialchars($editData['ayan_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Gujarati Month</label>
        <div class="input-group input-group-sm">
          <input type="text" name="gujarati_month" class="form-control" value="<?php echo htmlspecialchars($editData['gujarati_month'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="gujarati_month_no" class="form-control" value="<?php echo htmlspecialchars($editData['gujarati_month_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Sun Longitude</label>
        <input type="text" name="sun_lon" class="form-control" value="<?php echo htmlspecialchars($editData['sun_lon'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Moon Longitude</label>
        <input type="text" name="moon_lon" class="form-control" value="<?php echo htmlspecialchars($editData['moon_lon'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Tithi</label>
        <div class="input-group input-group-sm">
          <input type="text" name="tithi" class="form-control" value="<?php echo htmlspecialchars($editData['tithi'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="tithi_no" class="form-control" value="<?php echo htmlspecialchars($editData['tithi_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Tithi End</label>
        <input type="text" name="tithi_end" class="form-control" value="<?php echo htmlspecialchars($editData['tithi_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Nakshatra</label>
        <div class="input-group input-group-sm">
          <input type="text" name="nakshatra" class="form-control" value="<?php echo htmlspecialchars($editData['nakshatra'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="nak_no" class="form-control" value="<?php echo htmlspecialchars($editData['nak_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Nak Start</label>
        <input type="text" name="nak_start" class="form-control" value="<?php echo htmlspecialchars($editData['nak_start'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Nak End</label>
        <input type="text" name="nak_end" class="form-control" value="<?php echo htmlspecialchars($editData['nak_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Vichudo</label>
        <select name="vichudo" class="form-select">
          <option value="">-</option>
          <option value="YES" <?php echo ($editData['vichudo'] ?? '') === 'YES' ? 'selected' : ''; ?>>YES</option>
          <option value="NO" <?php echo ($editData['vichudo'] ?? '') === 'NO' ? 'selected' : ''; ?>>NO</option>
        </select>
      </div>
      <div class="col-md-2">
        <label>Vichudo Start</label>
        <input type="text" name="vichudo_start" class="form-control" value="<?php echo htmlspecialchars($editData['vichudo_start'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Vichudo End</label>
        <input type="text" name="vichudo_end" class="form-control" value="<?php echo htmlspecialchars($editData['vichudo_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Yoga</label>
        <div class="input-group input-group-sm">
          <input type="text" name="yoga" class="form-control" value="<?php echo htmlspecialchars($editData['yoga'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="yoga_no" class="form-control" value="<?php echo htmlspecialchars($editData['yoga_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Yoga End</label>
        <input type="text" name="yoga_end" class="form-control" value="<?php echo htmlspecialchars($editData['yoga_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Karana</label>
        <div class="input-group input-group-sm">
          <input type="text" name="karana" class="form-control" value="<?php echo htmlspecialchars($editData['karana'] ?? ''); ?>" style="width:60%;">
          <input type="number" name="karana_no" class="form-control" value="<?php echo htmlspecialchars($editData['karana_no'] ?? ''); ?>" placeholder="No" style="width:40%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Karana End</label>
        <input type="text" name="karana_end" class="form-control" value="<?php echo htmlspecialchars($editData['karana_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Rahu Start</label>
        <input type="text" name="rahu_start" class="form-control" value="<?php echo htmlspecialchars($editData['rahu_start'] ?? ''); ?>" placeholder="HH:MM">
      </div>
      <div class="col-md-2">
        <label>Rahu End</label>
        <input type="text" name="rahu_end" class="form-control" value="<?php echo htmlspecialchars($editData['rahu_end'] ?? ''); ?>" placeholder="HH:MM">
      </div>
      <div class="col-md-2">
        <label>Gulika Start</label>
        <input type="text" name="gulika_start" class="form-control" value="<?php echo htmlspecialchars($editData['gulika_start'] ?? ''); ?>" placeholder="HH:MM">
      </div>
      <div class="col-md-2">
        <label>Gulika End</label>
        <input type="text" name="gulika_end" class="form-control" value="<?php echo htmlspecialchars($editData['gulika_end'] ?? ''); ?>" placeholder="HH:MM">
      </div>
      <div class="col-md-2">
        <label>Yama Start</label>
        <input type="text" name="yama_start" class="form-control" value="<?php echo htmlspecialchars($editData['yama_start'] ?? ''); ?>" placeholder="HH:MM">
      </div>
      <div class="col-md-2">
        <label>Yama End</label>
        <input type="text" name="yama_end" class="form-control" value="<?php echo htmlspecialchars($editData['yama_end'] ?? ''); ?>" placeholder="HH:MM">
      </div>
      <div class="col-md-2">
        <label>Vikram Samvat</label>
        <div class="input-group input-group-sm">
          <input type="text" name="vikram_samvat" class="form-control" value="<?php echo htmlspecialchars($editData['vikram_samvat'] ?? ''); ?>" style="width:50%;">
          <input type="number" name="year" class="form-control" value="<?php echo htmlspecialchars($editData['year'] ?? ''); ?>" placeholder="Yr" style="width:25%;">
          <input type="number" name="month" class="form-control" value="<?php echo htmlspecialchars($editData['month'] ?? ''); ?>" placeholder="Mo" style="width:25%;">
        </div>
      </div>
      <div class="col-md-2">
        <label>Panchak Start</label>
        <input type="text" name="panchak_start" class="form-control" value="<?php echo htmlspecialchars($editData['panchak_start'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Panchak End</label>
        <input type="text" name="panchak_end" class="form-control" value="<?php echo htmlspecialchars($editData['panchak_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>PDF File</label>
        <input type="file" name="pdf" class="form-control" accept=".pdf">
        <?php if($editData && $editData['pdf_file']): ?>
          <small class="text-muted"><?php echo $editData['pdf_file']; ?></small>
        <?php endif; ?>
      </div>
      <div class="col-md-4">
        <label>Details</label>
        <textarea name="details" class="form-control" rows="2"><?php echo htmlspecialchars($editData['details'] ?? ''); ?></textarea>
      </div>
      <div class="col-12">
        <button type="submit" class="btn-sacred w-100 py-3 shadow-lg" style="background: linear-gradient(to right, #5B1A18, #8C6239); border: 2px solid var(--chandan-gold); transform: translateY(0); transition: all 0.3s ease; border-radius: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
          <i class="fas fa-save me-2"></i> <?php echo $editData ? 'Update' : 'Save'; ?> Panchang Record
        </button>
        <style>
          .btn-sacred:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.3); border-color: #fff !important; }
        </style>
        <?php if($editData): ?>
          <a href="manage_panchang" class="btn-sacred-outline ms-2">Cancel</a>
        <?php endif; ?>
      </div>
    </div>
  </form>
</div>

<!-- Data Table -->
<div class="table-responsive table-sacred" style="max-height:70vh; overflow-y:auto;">
  <table class="table table-sm mb-0" style="font-size:0.78rem; min-width:2400px;">
    <thead style="position:sticky; top:0; z-index:2;">
      <tr>
        <th style="position:sticky; left:0; z-index:4; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light);">ID</th>
        <th style="position:sticky; left:60px; z-index:3; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light);">Date</th>
        <th>Vara No</th>
        <th>Day</th>
        <th>Location</th>
        <th>Sunrise</th>
        <th>Sunset</th>
        <th>Ayan No</th>
        <th>Ayan</th>
        <th>Month No</th>
        <th>Guj. Month</th>
        <th>Sun Lon</th>
        <th>Moon Lon</th>
        <th>Tithi No</th>
        <th>Tithi</th>
        <th>Tithi End</th>
        <th>Nak No</th>
        <th>Nakshatra</th>
        <th>Nak Start</th>
        <th>Nak End</th>
        <th>Vichudo</th>
        <th>Vich. Start</th>
        <th>Vich. End</th>
        <th>Yoga No</th>
        <th>Yoga</th>
        <th>Yoga End</th>
        <th>Kar No</th>
        <th>Karana</th>
        <th>Karana End</th>
        <th>Rahu Kaal</th>
        <th>Gulika Kaal</th>
        <th>Yama Gandam</th>
        <th>Vikram Samvat</th>
        <th>Year</th>
        <th>Month</th>
        <th>Panchak Start</th>
        <th>Panchak End</th>
        <th style="color:#5B1A18;">PDF</th>
        <th>Details</th>
        <th>Created At</th>
        <th style="position:sticky; right:0; z-index:3; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light);">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!$selectedLocation): ?>
        <tr><td colspan="41" class="text-center py-5" style="background:rgba(197,151,59,0.03);">
          <div style="padding:2rem 1rem;">
            <i class="fas fa-map-marker-alt fa-3x mb-3" style="color:var(--chandan-gold); opacity:0.5;"></i>
            <h5 style="color:var(--sacred-maroon); margin-bottom:0.5rem;">Select a Location</h5>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Please select a location from the filter above to view Panchang entries</p>
          </div>
        </td></tr>
      <?php elseif($result && $result->num_rows > 0): ?>
        <?php while($r = $result->fetch_assoc()): ?>
        <tr>
          <td style="white-space:nowrap; position:sticky; left:0; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light); z-index:2; font-weight:600; text-align:center;"><?php echo $r['id']; ?></td>
          <td style="white-space:nowrap; position:sticky; left:60px; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light); z-index:1; font-weight:600;"><?php echo date('d M Y', strtotime($r['panchang_date'] ?? '')); ?></td>
          <td><?php echo render_field($r['vara_no'] ?? ''); ?></td>
          <td><?php echo render_field($r['day_name'] ?? ''); ?></td>
          <td><?php echo render_field($r['location'] ?? ''); ?></td>
          <td><?php echo render_field($r['sunrise'] ?? '', true); ?></td>
          <td><?php echo render_field($r['sunset'] ?? '', true); ?></td>
          <td><?php echo render_field($r['ayan_no'] ?? ''); ?></td>
          <td><?php echo render_translated_field($r['ayan'] ?? ''); ?></td>
          <td><?php echo render_field($r['gujarati_month_no'] ?? ''); ?></td>
          <td><?php echo render_translated_field($r['gujarati_month'] ?? ''); ?></td>
          <td><?php echo render_field($r['sun_lon'] ?? ''); ?></td>
          <td><?php echo render_field($r['moon_lon'] ?? ''); ?></td>
          <td><?php echo render_field($r['tithi_no'] ?? ''); ?></td>
          <td><?php echo render_translated_field($r['tithi'] ?? ''); ?></td>
          <td><?php echo render_field($r['tithi_end'] ?? ''); ?></td>
          <td><?php echo render_field($r['nak_no'] ?? ''); ?></td>
          <td><?php echo render_translated_field($r['nakshatra'] ?? ''); ?></td>
          <td><?php echo render_field($r['nak_start'] ?? ''); ?></td>
          <td><?php echo render_field($r['nak_end'] ?? ''); ?></td>
          <td>
            <?php if(($r['vichudo'] ?? '') === 'YES'): ?>
              <span style="color:#e74c3c; font-weight:600;"><i class="fas fa-exclamation-triangle"></i> YES</span>
            <?php else: ?>
              <?php echo render_field($r['vichudo'] ?? ''); ?>
            <?php endif; ?>
          </td>
          <td><?php echo render_field($r['vichudo_start'] ?? ''); ?></td>
          <td><?php echo render_field($r['vichudo_end'] ?? ''); ?></td>
          <td><?php echo render_field($r['yoga_no'] ?? ''); ?></td>
          <td><?php echo render_translated_field($r['yoga'] ?? ''); ?></td>
          <td><?php echo render_field($r['yoga_end'] ?? ''); ?></td>
          <td><?php echo render_field($r['karana_no'] ?? ''); ?></td>
          <td><?php echo render_translated_field($r['karana'] ?? ''); ?></td>
          <td><?php echo render_field($r['karana_end'] ?? ''); ?></td>
          <td style="white-space:nowrap;"><?php echo render_field($r['rahu_start'] ?? '', true) . ' - ' . render_field($r['rahu_end'] ?? '', true); ?></td>
          <td style="white-space:nowrap;"><?php echo render_field($r['gulika_start'] ?? '', true) . ' - ' . render_field($r['gulika_end'] ?? '', true); ?></td>
          <td style="white-space:nowrap;"><?php echo render_field($r['yama_start'] ?? '', true) . ' - ' . render_field($r['yama_end'] ?? '', true); ?></td>
          <td><?php echo render_field($r['vikram_samvat'] ?? ''); ?></td>
          <td><?php echo render_field($r['year'] ?? ''); ?></td>
          <td><?php echo render_field($r['month'] ?? ''); ?></td>
          <td><?php echo render_field($r['panchak_start'] ?? ''); ?></td>
          <td><?php echo render_field($r['panchak_end'] ?? ''); ?></td>
          <td><?php echo ($r['pdf_file'] ?? '') ? '<i class="fas fa-check-circle text-success"></i>' : render_field(''); ?></td>
          <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis;"><?php echo render_field(mb_substr($r['details'] ?? '', 0, 50)); ?></td>
          <td style="white-space:nowrap; font-size:0.7rem;"><?php echo render_field($r['created_at'] ?? ''); ?></td>
          <td style="white-space:nowrap; position:sticky; right:0; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); z-index:1;">
            <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.2rem 0.6rem; font-size:0.75rem; color:var(--chandan-light);"><i class="fas fa-edit"></i></a>
            <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.2rem 0.6rem; font-size:0.75rem; color:var(--chandan-light);" onclick="return confirm('Delete this entry?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="41" class="text-center py-4" style="background:rgba(197,151,59,0.03);">
          <div style="padding:1.5rem 1rem;">
            <i class="fas fa-inbox fa-2x mb-2" style="color:var(--chandan-gold); opacity:0.4;"></i>
            <p class="text-muted mb-0">No panchang entries found for the selected location and date range.</p>
          </div>
        </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
