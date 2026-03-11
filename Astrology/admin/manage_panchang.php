<?php
$adminTitle = 'Manage Panchang';
require_once 'header.php';

$success = $error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM panchang WHERE id=$id")) {
        $success = "Panchang entry deleted.";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $date = $conn->real_escape_string($_POST['panchang_date'] ?? '');
    $day = $conn->real_escape_string($_POST['day_name'] ?? '');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $sunrise = $conn->real_escape_string($_POST['sunrise'] ?? '');
    $sunset = $conn->real_escape_string($_POST['sunset'] ?? '');
    $ayan = $conn->real_escape_string($_POST['ayan'] ?? '');
    $gu_month = $conn->real_escape_string($_POST['gujarati_month'] ?? '');
    $sun_lon = $conn->real_escape_string($_POST['sun_lon'] ?? '');
    $moon_lon = $conn->real_escape_string($_POST['moon_lon'] ?? '');
    $tithi = $conn->real_escape_string($_POST['tithi'] ?? '');
    $tithi_end = $conn->real_escape_string($_POST['tithi_end'] ?? '');
    $nakshatra = $conn->real_escape_string($_POST['nakshatra'] ?? '');
    $nak_start = $conn->real_escape_string($_POST['nak_start'] ?? '');
    $nak_end = $conn->real_escape_string($_POST['nak_end'] ?? '');
    $vichudo = $conn->real_escape_string($_POST['vichudo'] ?? '');
    $vichudo_start = $conn->real_escape_string($_POST['vichudo_start'] ?? '');
    $vichudo_end = $conn->real_escape_string($_POST['vichudo_end'] ?? '');
    $yoga = $conn->real_escape_string($_POST['yoga'] ?? '');
    $yoga_end = $conn->real_escape_string($_POST['yoga_end'] ?? '');
    $karana = $conn->real_escape_string($_POST['karana'] ?? '');
    $karana_end = $conn->real_escape_string($_POST['karana_end'] ?? '');
    $rahu_start = $conn->real_escape_string($_POST['rahu_start'] ?? '');
    $rahu_end = $conn->real_escape_string($_POST['rahu_end'] ?? '');
    $gulika_start = $conn->real_escape_string($_POST['gulika_start'] ?? '');
    $gulika_end = $conn->real_escape_string($_POST['gulika_end'] ?? '');
    $yama_start = $conn->real_escape_string($_POST['yama_start'] ?? '');
    $yama_end = $conn->real_escape_string($_POST['yama_end'] ?? '');
    $vikram = $conn->real_escape_string($_POST['vikram_samvat'] ?? '');
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
        $sql = "UPDATE panchang SET panchang_date='$date', day_name='$day', location='$location',
                sunrise={$nullOrVal($sunrise)}, sunset={$nullOrVal($sunset)},
                ayan='$ayan', gujarati_month='$gu_month', sun_lon='$sun_lon', moon_lon='$moon_lon',
                tithi='$tithi', tithi_end='$tithi_end', nakshatra='$nakshatra',
                nak_start='$nak_start', nak_end='$nak_end',
                vichudo='$vichudo', vichudo_start='$vichudo_start', vichudo_end='$vichudo_end',
                yoga='$yoga', yoga_end='$yoga_end', karana='$karana', karana_end='$karana_end',
                rahu_start={$nullOrVal($rahu_start)}, rahu_end={$nullOrVal($rahu_end)},
                gulika_start={$nullOrVal($gulika_start)}, gulika_end={$nullOrVal($gulika_end)},
                yama_start={$nullOrVal($yama_start)}, yama_end={$nullOrVal($yama_end)},
                vikram_samvat='$vikram', panchak_start='$panchak_start', panchak_end='$panchak_end',
                details='$details' $pdfUpdate WHERE id=$id";
        if ($conn->query($sql)) $success = "Panchang updated successfully.";
        else $error = "Update failed: " . $conn->error;
    } else {
        $sql = "INSERT INTO panchang (panchang_date, day_name, location, sunrise, sunset, ayan, gujarati_month,
                sun_lon, moon_lon, tithi, tithi_end, nakshatra, nak_start, nak_end,
                vichudo, vichudo_start, vichudo_end, yoga, yoga_end, karana, karana_end,
                rahu_start, rahu_end, gulika_start, gulika_end, yama_start, yama_end,
                vikram_samvat, panchak_start, panchak_end, details, pdf_file)
                VALUES ('$date','$day','$location',{$nullOrVal($sunrise)},{$nullOrVal($sunset)},
                '$ayan','$gu_month','$sun_lon','$moon_lon',
                '$tithi','$tithi_end','$nakshatra','$nak_start','$nak_end',
                '$vichudo','$vichudo_start','$vichudo_end',
                '$yoga','$yoga_end','$karana','$karana_end',
                {$nullOrVal($rahu_start)},{$nullOrVal($rahu_end)},
                {$nullOrVal($gulika_start)},{$nullOrVal($gulika_end)},
                {$nullOrVal($yama_start)},{$nullOrVal($yama_end)},
                '$vikram','$panchak_start','$panchak_end','$details','$pdfFile')";
        if ($conn->query($sql)) $success = "Panchang added successfully.";
        else $error = "Failed to add: " . $conn->error;
    }
}

// Month-based filtering — show only 1 month at a time
$selectedMonth = $_GET['month'] ?? date('Y-m');
$totalRows = $conn->query("SELECT COUNT(*) as c FROM panchang WHERE DATE_FORMAT(panchang_date, '%Y-%m') = '$selectedMonth'")->fetch_assoc()['c'];
$result = $conn->query("SELECT * FROM panchang WHERE DATE_FORMAT(panchang_date, '%Y-%m') = '$selectedMonth' ORDER BY panchang_date ASC");

// Get available months for dropdown
$monthsResult = $conn->query("SELECT DISTINCT DATE_FORMAT(panchang_date, '%Y-%m') as ym FROM panchang ORDER BY ym DESC");

// Edit mode
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editData = $conn->query("SELECT * FROM panchang WHERE id=$editId")->fetch_assoc();
}
?>

<div class="admin-header">
  <h2 style="margin:0;"><i class="fas fa-sun me-2" style="color:var(--chandan-gold);"></i>Manage Panchang</h2>
  <div class="d-flex align-items-center gap-3">
    <form method="GET" class="d-flex align-items-center gap-2">
      <label style="white-space:nowrap; font-weight:600; font-size:0.9rem;">Month:</label>
      <input type="month" name="month" class="form-control" value="<?php echo $selectedMonth; ?>" onchange="this.form.submit()" style="width:200px;">
    </form>
    <span class="text-muted"><?php echo $totalRows; ?> entries</span>
  </div>
</div>

<?php if($success): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($success); ?>" data-type="success"></div>
<?php endif; ?>
<?php if($error): ?>
  <div id="toast-data" data-message="<?php echo htmlspecialchars($error); ?>" data-type="error"></div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div class="sacred-card mb-4" style="height:auto;">
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
        <input type="text" name="day_name" class="form-control" value="<?php echo htmlspecialchars($editData['day_name'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Location</label>
        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($editData['location'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Sunrise</label>
        <input type="time" name="sunrise" class="form-control" value="<?php echo $editData['sunrise'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Sunset</label>
        <input type="time" name="sunset" class="form-control" value="<?php echo $editData['sunset'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Ayan</label>
        <input type="text" name="ayan" class="form-control" value="<?php echo htmlspecialchars($editData['ayan'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Gujarati Month</label>
        <input type="text" name="gujarati_month" class="form-control" value="<?php echo htmlspecialchars($editData['gujarati_month'] ?? ''); ?>">
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
        <input type="text" name="tithi" class="form-control" value="<?php echo htmlspecialchars($editData['tithi'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Tithi End</label>
        <input type="text" name="tithi_end" class="form-control" value="<?php echo htmlspecialchars($editData['tithi_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Nakshatra</label>
        <input type="text" name="nakshatra" class="form-control" value="<?php echo htmlspecialchars($editData['nakshatra'] ?? ''); ?>">
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
        <input type="text" name="yoga" class="form-control" value="<?php echo htmlspecialchars($editData['yoga'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Yoga End</label>
        <input type="text" name="yoga_end" class="form-control" value="<?php echo htmlspecialchars($editData['yoga_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Karana</label>
        <input type="text" name="karana" class="form-control" value="<?php echo htmlspecialchars($editData['karana'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Karana End</label>
        <input type="text" name="karana_end" class="form-control" value="<?php echo htmlspecialchars($editData['karana_end'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>Rahu Start</label>
        <input type="time" name="rahu_start" class="form-control" value="<?php echo $editData['rahu_start'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Rahu End</label>
        <input type="time" name="rahu_end" class="form-control" value="<?php echo $editData['rahu_end'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Gulika Start</label>
        <input type="time" name="gulika_start" class="form-control" value="<?php echo $editData['gulika_start'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Gulika End</label>
        <input type="time" name="gulika_end" class="form-control" value="<?php echo $editData['gulika_end'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Yama Start</label>
        <input type="time" name="yama_start" class="form-control" value="<?php echo $editData['yama_start'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Yama End</label>
        <input type="time" name="yama_end" class="form-control" value="<?php echo $editData['yama_end'] ?? ''; ?>">
      </div>
      <div class="col-md-2">
        <label>Vikram Samvat</label>
        <input type="text" name="vikram_samvat" class="form-control" value="<?php echo htmlspecialchars($editData['vikram_samvat'] ?? ''); ?>">
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
        <button type="submit" class="btn-sacred"><i class="fas fa-save"></i> <?php echo $editData ? 'Update' : 'Add'; ?> Panchang</button>
        <?php if($editData): ?>
          <a href="manage_panchang.php" class="btn-sacred-outline ms-2">Cancel</a>
        <?php endif; ?>
      </div>
    </div>
  </form>
</div>

<?php
$pv = function($val) {
    if ($val === null || trim((string)$val) === '') return '<span class="text-muted">N/A</span>';
    return htmlspecialchars(trim((string)$val));
};
?>
<!-- Full Data Table — All Fields with Horizontal Scroll -->
<div class="table-responsive table-sacred" style="max-height:70vh; overflow-y:auto;">
  <table class="table table-sm mb-0" style="font-size:0.78rem; min-width:2400px;">
    <thead style="position:sticky; top:0; z-index:2;">
      <tr>
        <th style="position:sticky; left:0; z-index:3; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light);">Date</th>
        <th>Day</th>
        <th>Location</th>
        <th>Sunrise</th>
        <th>Sunset</th>
        <th>Ayan</th>
        <th>Guj. Month</th>
        <th>Sun Lon</th>
        <th>Moon Lon</th>
        <th>Tithi</th>
        <th>Tithi End</th>
        <th>Nakshatra</th>
        <th>Nak Start</th>
        <th>Nak End</th>
        <th>Vichudo</th>
        <th>Vich. Start</th>
        <th>Vich. End</th>
        <th>Yoga</th>
        <th>Yoga End</th>
        <th>Karana</th>
        <th>Karana End</th>
        <th>Rahu Kaal</th>
        <th>Gulika Kaal</th>
        <th>Yama Gandam</th>
        <th>Vikram Samvat</th>
        <th>Panchak Start</th>
        <th>Panchak End</th>
        <th>PDF</th>
        <th>Details</th>
        <th style="position:sticky; right:0; z-index:3; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light);">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($result && $result->num_rows > 0): ?>
        <?php while($r = $result->fetch_assoc()): ?>
        <tr>
          <td style="white-space:nowrap; position:sticky; left:0; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); color:var(--chandan-light); z-index:1; font-weight:600;"><?php echo date('d M Y', strtotime($r['panchang_date'])); ?></td>
          <td><?php echo $pv($r['day_name']); ?></td>
          <td><?php echo $pv($r['location']); ?></td>
          <td><?php echo $r['sunrise'] ? date('h:i A', strtotime($r['sunrise'])) : $pv(''); ?></td>
          <td><?php echo $r['sunset'] ? date('h:i A', strtotime($r['sunset'])) : $pv(''); ?></td>
          <td><?php echo $pv($r['ayan']); ?></td>
          <td><?php echo $pv($r['gujarati_month']); ?></td>
          <td><?php echo $pv($r['sun_lon']); ?></td>
          <td><?php echo $pv($r['moon_lon']); ?></td>
          <td><?php echo $pv($r['tithi']); ?></td>
          <td><?php echo $pv($r['tithi_end']); ?></td>
          <td><?php echo $pv($r['nakshatra']); ?></td>
          <td><?php echo $pv($r['nak_start']); ?></td>
          <td><?php echo $pv($r['nak_end']); ?></td>
          <td>
            <?php if(($r['vichudo'] ?? '') === 'YES'): ?>
              <span style="color:#e74c3c; font-weight:600;"><i class="fas fa-exclamation-triangle"></i> YES</span>
            <?php else: ?>
              <?php echo $pv($r['vichudo']); ?>
            <?php endif; ?>
          </td>
          <td><?php echo $pv($r['vichudo_start']); ?></td>
          <td><?php echo $pv($r['vichudo_end']); ?></td>
          <td><?php echo $pv($r['yoga']); ?></td>
          <td><?php echo $pv($r['yoga_end']); ?></td>
          <td><?php echo $pv($r['karana']); ?></td>
          <td><?php echo $pv($r['karana_end']); ?></td>
          <td style="white-space:nowrap;"><?php echo ($r['rahu_start'] && $r['rahu_end']) ? date('h:i', strtotime($r['rahu_start'])) . '-' . date('h:i', strtotime($r['rahu_end'])) : $pv(''); ?></td>
          <td style="white-space:nowrap;"><?php echo ($r['gulika_start'] && $r['gulika_end']) ? date('h:i', strtotime($r['gulika_start'])) . '-' . date('h:i', strtotime($r['gulika_end'])) : $pv(''); ?></td>
          <td style="white-space:nowrap;"><?php echo ($r['yama_start'] && $r['yama_end']) ? date('h:i', strtotime($r['yama_start'])) . '-' . date('h:i', strtotime($r['yama_end'])) : $pv(''); ?></td>
          <td><?php echo $pv($r['vikram_samvat']); ?></td>
          <td><?php echo $pv($r['panchak_start']); ?></td>
          <td><?php echo $pv($r['panchak_end']); ?></td>
          <td><?php echo $r['pdf_file'] ? '<i class="fas fa-check-circle text-success"></i>' : $pv(''); ?></td>
          <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis;"><?php echo $pv(mb_substr($r['details'] ?? '', 0, 50)); ?></td>
          <td style="white-space:nowrap; position:sticky; right:0; background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); z-index:1;">
            <a href="?edit=<?php echo $r['id']; ?>" class="btn-sacred" style="padding:0.2rem 0.6rem; font-size:0.75rem; color:var(--chandan-light);"><i class="fas fa-edit"></i></a>
            <a href="?delete=<?php echo $r['id']; ?>" class="btn-maroon" style="padding:0.2rem 0.6rem; font-size:0.75rem; color:var(--chandan-light);" onclick="return confirm('Delete this entry?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="30" class="text-center py-4">No panchang entries found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'footer.php'; ?>
