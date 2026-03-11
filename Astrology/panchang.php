<?php
$pageTitle = 'Panchang';
require_once 'header.php';

// Search filters
$searchDate = $_GET['date'] ?? '';
$searchMonth = $_GET['month'] ?? '';

$where = '';
if ($searchDate) {
    $d = $conn->real_escape_string($searchDate);
    $where = "WHERE panchang_date = '$d'";
} elseif ($searchMonth) {
    $m = $conn->real_escape_string($searchMonth);
    $where = "WHERE DATE_FORMAT(panchang_date, '%Y-%m') = '$m'";
} else {
    // Default to current month
    $currentMonth = date('Y-m');
    $where = "WHERE DATE_FORMAT(panchang_date, '%Y-%m') = '$currentMonth'";
}

// Pagination — 100 per page
$perPage = 100;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$totalResult = $conn->query("SELECT COUNT(*) as c FROM panchang $where");
$totalRows = $totalResult ? $totalResult->fetch_assoc()['c'] : 0;
$totalPages = max(1, ceil($totalRows / $perPage));

$result = $conn->query("SELECT * FROM panchang $where ORDER BY panchang_date ASC LIMIT $perPage OFFSET $offset");

// Check subscription status for detail access
// Check subscription status for detail access
$canViewFull = isLoggedIn() && (isAdmin() || isSubscribed($conn, $_SESSION['user_id']));

// Monthly Panchang Calendar Data
$calMonth = $_GET['cal_month'] ?? date('Y-m');
$calYear = (int)substr($calMonth, 0, 4);
$calMon = (int)substr($calMonth, 5, 2);
$calStart = "$calYear-" . str_pad($calMon, 2, '0', STR_PAD_LEFT) . "-01";
$calEnd = date('Y-m-t', strtotime($calStart));
$today = date('Y-m-d');

// Fetch Panchang for calendar
$calResult = $conn->query("SELECT * FROM panchang WHERE panchang_date BETWEEN '$calStart' AND '$calEnd' ORDER BY panchang_date ASC");
$calData = [];
if ($calResult) {
    while ($cr = $calResult->fetch_assoc()) {
        $calData[$cr['panchang_date']] = $cr;
    }
}

// Fetch Festivals for calendar
$calFestResult = $conn->query("SELECT * FROM festivals WHERE festival_date BETWEEN '$calStart' AND '$calEnd'");
$calFestData = [];
if ($calFestResult) {
    while ($cf = $calFestResult->fetch_assoc()) {
        $calFestData[$cf['festival_date']][] = $cf['festival_name'];
    }
}

$pv = function($val) {
    if ($val === null || trim((string)$val) === '') return '<span class="text-muted">N/A</span>';
    return htmlspecialchars(trim((string)$val));
};
?>

<!-- Subscription Required Modal -->
<?php if(!$canViewFull): ?>
<div class="modal fade" id="subscribeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:var(--dark-wood); border:1px solid var(--chandan-gold); border-radius:12px;">
      <div class="modal-header" style="border-bottom:1px solid rgba(197,151,59,0.2);">
        <h5 class="modal-title" style="color:var(--chandan-gold);"><i class="fas fa-crown me-2"></i><?php echo t('subscribe_premium'); ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <div style="font-size:3rem; color:var(--chandan-gold); margin-bottom:15px;"><i class="fas fa-lock"></i></div>
        <h5 style="color:var(--chandan-light);"><?php echo t('subscribe_premium'); ?></h5>
        <p style="color:var(--soft-gray); font-size:0.9rem;">
          <?php echo t('subscribe_premium_desc'); ?>
        </p>
        <div class="mt-3">
          <?php if(!isLoggedIn()): ?>
            <a href="<?php echo SITE_URL; ?>/login.php" class="btn-sacred me-2"><i class="fas fa-sign-in-alt"></i> <?php echo t('login'); ?></a>
          <?php endif; ?>
          <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred"><i class="fas fa-crown"></i> <?php echo t('subscribe'); ?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-sun me-2"></i><?php echo t('panchang'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('panchang'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<!-- Monthly Panchang Calendar Section -->
<section class="panchang-calendar-section">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2><i class="fas fa-calendar-alt me-2" style="color:var(--chandan-gold);"></i><?php echo t('monthly_calendar'); ?></h2>
      <div class="header-line"></div>
      <p><?php echo t('select_dates_desc'); ?></p>
    </div>

    <!-- Month Navigation -->
    <div class="cal-month-nav">
      <?php
        $prevMonth = date('Y-m', strtotime("$calStart -1 month"));
        $nextMonth = date('Y-m', strtotime("$calStart +1 month"));
        $monthLabel = t(strtolower(date('F', strtotime($calStart)))) . ' ' . date('Y', strtotime($calStart));
      ?>
      <a href="?cal_month=<?php echo $prevMonth; ?>" class="btn-sacred-outline" style="padding:0.4rem 1rem; font-size:0.9rem;">
        <i class="fas fa-chevron-left"></i>
      </a>
      <span class="month-label"><?php echo $monthLabel; ?></span>
      <a href="?cal_month=<?php echo $nextMonth; ?>" class="btn-sacred-outline" style="padding:0.4rem 1rem; font-size:0.9rem;">
        <i class="fas fa-chevron-right"></i>
      </a>
    </div>

    <!-- Calendar Grid -->
    <div class="panchang-cal-grid">
      <?php
        $dayNames = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
        foreach ($dayNames as $dn) {
          $label = mb_substr(t($dn),0,3);
          echo "<div class='panchang-cal-header'>$label</div>";
        }

        $firstDayOfMonth = date('w', strtotime($calStart));
        $daysInMonth = date('t', strtotime($calStart));

        // Empty cells before month starts
        for ($e = 0; $e < $firstDayOfMonth; $e++) {
          echo "<div class='panchang-cal-cell empty'></div>";
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
          $dateStr = sprintf('%04d-%02d-%02d', $calYear, $calMon, $d);
          $isToday = ($dateStr === $today) ? ' today' : '';
          $p = $calData[$dateStr] ?? null;
          $tithi = $p ? htmlspecialchars($p['tithi'] ?? '') : '';
          $festivals = $calFestData[$dateStr] ?? [];
          
          echo "<div class='panchang-cal-cell$isToday' data-date='$dateStr' onclick='toggleCalDate(this)'>";
          echo "<div class='cal-day'>$d</div>";
          if ($tithi) echo "<div class='cal-tithi'>".t($tithi)."</div>";
          if (!empty($festivals)) {
              foreach($festivals as $fest) {
                  echo "<div class='cal-festival mt-1' style='font-size:0.6rem; color:var(--sacred-kumkum); background:rgba(192,57,43,0.1); border-radius:3px; padding:2px 4px; text-align:center;'>".t($fest)."</div>";
              }
          }
          echo "</div>";
        }
      ?>
    </div>

    <!-- Detail Panel for Selected Dates -->
    <div id="calDetailPlaceholder" class="text-center py-5 mt-4" style="background:var(--chandan-cream); border-radius:8px; border:2px dashed var(--chandan-gold);">
        <i class="fas fa-hand-pointer fa-2x mb-3" style="color:var(--chandan-gold); opacity:0.6;"></i>
        <p class="text-muted mb-0 fw-semibold"><?php echo t('select_date_prompt'); ?></p>
    </div>
    <div id="calDetailPanel" style="display:none; margin-top:1.5rem;"></div>
  </div>
</section>

<section class="section-sacred" style="padding:40px 0;">
  <div class="container">

    <!-- Search Filters -->
    <div class="sacred-card mb-4" style="height:auto;">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <form method="GET" class="form-sacred">
            <label><?php echo t('search_by_date'); ?></label>
            <div class="input-group">
              <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($searchDate); ?>">
              <button type="submit" class="btn-sacred"><?php echo t('search'); ?></button>
            </div>
          </form>
        </div>
        <div class="col-md-4">
          <form method="GET" class="form-sacred">
            <label><?php echo t('search_by_month'); ?></label>
            <div class="input-group">
              <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($searchMonth); ?>">
              <button type="submit" class="btn-sacred"><?php echo t('search'); ?></button>
            </div>
          </form>
        </div>
        <div class="col-md-4 text-end">
          <?php if($searchDate || $searchMonth): ?>
            <a href="panchang.php" class="btn-sacred-outline"><i class="fas fa-times"></i> <?php echo t('all_types'); ?></a>
          <?php endif; ?>
          <span class="text-muted ms-2" style="font-size:0.85rem;">
            <?php echo t('showing'); ?> <?php echo min($totalRows, $perPage); ?> <?php echo t('of'); ?> <?php echo $totalRows; ?> <?php echo t('records'); ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Panchang Table -->
    <div class="table-responsive table-sacred">
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th><?php echo t('date'); ?></th>
            <th><?php echo t('day'); ?></th>
            <th><?php echo t('sunrise'); ?></th>
            <th><?php echo t('sunset'); ?></th>
            <th><?php echo t('tithi'); ?></th>
            <th><?php echo t('nakshatra'); ?></th>
            <th><?php echo t('actions'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if($result && $result->num_rows > 0): ?>
            <?php while($r = $result->fetch_assoc()): ?>
            <tr>
              <td style="white-space:nowrap;"><?php echo t_date($r['panchang_date']); ?></td>
              <td><?php echo t($pv($r['day_name'])); ?></td>
              <td><?php echo $pv($r['sunrise']); ?></td>
              <td><?php echo $pv($r['sunset']); ?></td>
              <td><?php echo t($pv($r['tithi'])); ?></td>
              <td><?php echo t($pv($r['nakshatra'])); ?></td>
              <td>
                <?php if($canViewFull): ?>
                  <a href="panchang-details.php?date=<?php echo $r['panchang_date']; ?>" class="btn-sacred" style="padding:0.3rem 0.8rem; font-size:0.78rem;">
                    <i class="fas fa-eye me-1"></i><?php echo t('panchang_details'); ?>
                  </a>
                <?php else: ?>
                  <button type="button" class="btn-sacred" style="padding:0.3rem 0.8rem; font-size:0.78rem;" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                    <i class="fas fa-lock me-1"></i><?php echo t('panchang_details'); ?>
                  </button>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center py-4"><?php echo t('no_data'); ?></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Prev / Next Pagination -->
    <?php if($totalPages > 1): ?>
    <div class="d-flex justify-content-between align-items-center mt-4">
      <div>
        <?php if($page > 1): ?>
          <a href="?page=<?php echo $page-1; ?><?php echo $searchDate ? '&date='.$searchDate : ''; ?><?php echo $searchMonth ? '&month='.$searchMonth : ''; ?>" class="btn-sacred-outline">
            <i class="fas fa-arrow-left"></i> <?php echo t('previous'); ?>
          </a>
        <?php endif; ?>
      </div>
      <div class="text-center" style="color:var(--text-secondary); font-size:0.9rem;">
        <?php echo t('page'); ?> <strong><?php echo $page; ?></strong> <?php echo t('of'); ?> <strong><?php echo $totalPages; ?></strong>
      </div>
      <div>
        <?php if($page < $totalPages): ?>
          <a href="?page=<?php echo $page+1; ?><?php echo $searchDate ? '&date='.$searchDate : ''; ?><?php echo $searchMonth ? '&month='.$searchMonth : ''; ?>" class="btn-sacred">
            <?php echo t('next'); ?> <i class="fas fa-arrow-right"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- Calendar JavaScript for Multi-Date Selection -->
<script>
var canViewFull = <?php echo $canViewFull ? 'true' : 'false'; ?>;
var calPanchangData = <?php echo json_encode($calData, JSON_HEX_TAG | JSON_HEX_APOS); ?>;
var selectedDates = [];

function toggleCalDate(cell) {
  var date = cell.getAttribute('data-date');
  if (!date || !calPanchangData[date]) return;

  var idx = selectedDates.indexOf(date);
  if (idx >= 0) {
    selectedDates.splice(idx, 1);
    cell.classList.remove('selected');
  } else {
    selectedDates.push(date);
    cell.classList.add('selected');
  }

  selectedDates.sort();
  showSelectedDetails();
}

function showSelectedDetails() {
  var panel = document.getElementById('calDetailPanel');
  var placeholder = document.getElementById('calDetailPlaceholder');
  var mainTable = document.querySelector('.table-responsive');
  var pagination = document.querySelector('.d-flex.justify-content-between.align-items-center.mt-4');
  var searchFilters = document.querySelector('.sacred-card.mb-4');
  
  if (selectedDates.length === 0) {
    panel.style.display = 'none';
    placeholder.style.display = 'block';
    
    // Show main table and filters again
    if(mainTable) mainTable.style.display = 'block';
    if(pagination) pagination.style.display = 'flex';
    if(searchFilters) searchFilters.style.display = 'block';
    return;
  }

  // Hide main table and filters when custom dates are selected
  if(mainTable) mainTable.style.display = 'none';
  if(pagination) pagination.style.display = 'none';
  if(searchFilters) searchFilters.style.display = 'none';

  placeholder.style.display = 'none';
  var html = '<div class="panchang-detail-panel">';
  html += '<h4><i class="fas fa-sun me-2" style="color:var(--chandan-gold);"></i><?php echo t('panchang_details'); ?> — ' + selectedDates.length + ' <?php echo t('dates_selected'); ?></h4>';

  selectedDates.forEach(function(date) {
    var p = calPanchangData[date];
    if (!p) return;
    var d = new Date(date);
    var dateLabel = d.toLocaleDateString('en-IN', {weekday:'long', day:'numeric', month:'long', year:'numeric'});

    html += '<div style="background:var(--chandan-cream); padding:1rem; border-radius:8px; margin-bottom:0.8rem; border-left:3px solid var(--chandan-gold);">';
    html += '<strong style="color:var(--sacred-maroon); font-family:Cinzel,serif;">' + dateLabel + '</strong>';
    html += '<div class="row mt-2" style="font-size:0.85rem;">';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('tithi'); ?>:</span> <strong>' + (p.tithi || 'n/a') + '</strong></div>';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('nakshatra'); ?>:</span> <strong>' + (p.nakshatra || 'n/a') + '</strong></div>';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('yoga'); ?>:</span> <strong>' + (p.yoga || 'n/a') + '</strong></div>';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('karana'); ?>:</span> <strong>' + (p.karana || 'n/a') + '</strong></div>';
    html += '</div>';
    html += '<div class="row mt-1" style="font-size:0.85rem;">';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('sunrise'); ?>:</span> ' + (p.sunrise || 'n/a') + '</div>';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('sunset'); ?>:</span> ' + (p.sunset || 'n/a') + '</div>';
    html += '<div class="col-md-3"><span style="color:var(--text-secondary);"><?php echo t('gujarati_month'); ?>:</span> ' + (p.gujarati_month || 'n/a') + '</div>';
    html += '<div class="col-md-3 text-end">';
    if (canViewFull) {
        html += '<a href="panchang-details.php?date=' + date + '" class="btn-sacred py-1 px-3" style="font-size:0.75rem;"><i class="fas fa-eye me-1"></i><?php echo t('view_full_details'); ?></a>';
    } else {
        html += '<button type="button" class="btn-sacred py-1 px-3" style="font-size:0.75rem;" data-bs-toggle="modal" data-bs-target="#subscribeModal"><i class="fas fa-lock me-1"></i><?php echo t('view_full_details'); ?></button>';
    }
    html += '</div></div></div>';
  });

  html += '</div>';
  panel.innerHTML = html;
  panel.style.display = 'block';
  panel.scrollIntoView({behavior:'smooth', block:'nearest'});
}

document.addEventListener('DOMContentLoaded', function() {
    var todayStr = "<?php echo date('Y-m-d'); ?>";
    var todayCell = document.querySelector('.panchang-cal-cell[data-date="' + todayStr + '"]');
    if (todayCell) {
        toggleCalDate(todayCell);
    }
});
</script>

<?php if(!$canViewFull && isset($_GET['subscribe'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var modal = new bootstrap.Modal(document.getElementById('subscribeModal'));
  modal.show();
});
</script>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
