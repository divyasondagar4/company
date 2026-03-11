<?php
$pageTitle = 'Muhurat Calendar';
require_once 'header.php';

// Check subscription/login gating
$isSubscribed = isLoggedIn() && (isAdmin() || isSubscribed($conn, $_SESSION['user_id']));

// Navigation Logic
$calMonth = $_GET['cal_month'] ?? date('Y-m');
$calYear = (int)substr($calMonth, 0, 4);
$calMon = (int)substr($calMonth, 5, 2);
$calStart = "$calYear-" . str_pad($calMon, 2, '0', STR_PAD_LEFT) . "-01";
$calEnd = date('Y-m-t', strtotime($calStart));
$today = date('Y-m-d');

// 1. Fetch Muhurats for calendar
$muhuratResult = $conn->query("SELECT * FROM muhurat WHERE muhurat_date BETWEEN '$calStart' AND '$calEnd' ORDER BY muhurat_date ASC");
$muhuratData = [];
if ($muhuratResult) {
    while ($m = $muhuratResult->fetch_assoc()) {
        $muhuratData[$m['muhurat_date']][] = $m;
    }
}

// 2. Fetch Tithis for calendar
$tithiResult = $conn->query("SELECT panchang_date, tithi FROM panchang WHERE panchang_date BETWEEN '$calStart' AND '$calEnd' AND tithi IS NOT NULL AND tithi != ''");
$tithiData = [];
if ($tithiResult) {
    while ($t = $tithiResult->fetch_assoc()) {
        $tithiData[$t['panchang_date']] = $t['tithi'];
    }
}

// 3. Fetch Festivals for calendar
$festResult = $conn->query("SELECT festival_date, festival_name FROM festivals WHERE festival_date BETWEEN '$calStart' AND '$calEnd'");
$festData = [];
if ($festResult) {
    while ($f = $festResult->fetch_assoc()) {
        $festData[$f['festival_date']][] = $f['festival_name'];
    }
}

// For use in JS
$allData = [
    'muhurats' => $muhuratData,
    'tithis' => $tithiData,
    'festivals' => $festData
];

?>

<!-- Page Header -->
<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-calendar me-2"></i><?php echo t('muhurat_calendar'); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/"><?php echo t('home'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/muhurat.php"><?php echo t('muhurat'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo t('calendar'); ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="panchang-calendar-section">
  <div class="container">
    
    <?php if(!$isSubscribed): ?>
    <div class="alert-sacred mb-4">
      <i class="fas fa-lock"></i>
      <span><?php echo t('muhurat_calendar_lock_desc'); ?></span>
      <a href="<?php echo SITE_URL; ?>/subscribe.php" class="btn-sacred ms-auto" style="font-size:0.85rem; padding:0.4rem 1rem;">
        <?php echo t('subscribe_now'); ?>
      </a>
    </div>
    <?php endif; ?>

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

        for ($e = 0; $e < $firstDayOfMonth; $e++) {
          echo "<div class='panchang-cal-cell empty'></div>";
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
          $dateStr = sprintf('%04d-%02d-%02d', $calYear, $calMon, $d);
          $isToday = ($dateStr === $today) ? ' today' : '';
          
          $tithi = $tithiData[$dateStr] ?? '';
          $festivals = $festData[$dateStr] ?? [];
          $muhurats = $muhuratData[$dateStr] ?? [];
          
          echo "<div class='panchang-cal-cell$isToday' data-date='$dateStr' onclick='handleCalClick(this)'>";
          echo "<div class='cal-day'>$d</div>";
          
          if ($tithi) echo "<div class='cal-tithi'>" . t($tithi) . "</div>";
          
          if (!empty($festivals)) {
              foreach($festivals as $fest) {
                  echo "<div class='cal-festival mt-1' style='font-size:0.6rem; color:var(--sacred-kumkum); background:rgba(192,57,43,0.1); border-radius:3px; padding:2px 4px; text-align:center;'>".t($fest)."</div>";
              }
          }

          if (!empty($muhurats)) {
              foreach($muhurats as $m) {
                  echo "<div class='cal-muhurat mt-1' style='font-size:0.6rem; color: #27ae60; background:rgba(39,174,96,0.1); border-radius:3px; padding:2px 4px; text-align:center;'>".t($m['title'])."</div>";
              }
          }

          echo "</div>";
        }
      ?>
    </div>

    <!-- Detail Panel -->
    <div id="calDetailPlaceholder" class="text-center py-5 mt-4" style="background:var(--chandan-cream); border-radius:8px; border:2px dashed var(--chandan-gold);">
        <i class="fas fa-hand-pointer fa-2x mb-3" style="color:var(--chandan-gold); opacity:0.6;"></i>
        <p class="text-muted mb-0 fw-semibold"><?php echo t('select_date_prompt');?></p>
    </div>
    <div id="calDetailPanel" style="display:none; margin-top:1.5rem;"></div>

  </div>
</section>

<!-- Detail Modal (for quick info if needed) -->
<div class="modal fade" id="muhuratModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border:2px solid var(--chandan-gold); border-radius:var(--radius-md);">
      <div class="modal-header" style="background:linear-gradient(135deg, var(--sacred-maroon), var(--dark-wood)); border:none;">
        <h5 class="modal-title" style="color:var(--chandan-gold); font-family:'Cinzel',serif;" id="modalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:2rem;">
        <div id="modalBodyContent"></div>
      </div>
    </div>
  </div>
</div>

<script>
var isSubscribed = <?php echo $isSubscribed ? 'true' : 'false'; ?>;
var calendarData = <?php echo json_encode($allData); ?>;
var selectedDates = [];

function handleCalClick(cell) {
    if (!isSubscribed) {
        window.location.href = 'subscribe.php';
        return;
    }

    var date = cell.getAttribute('data-date');
    var idx = selectedDates.indexOf(date);
    
    if (idx >= 0) {
        selectedDates.splice(idx, 1);
        cell.classList.remove('selected');
    } else {
        selectedDates.push(date);
        cell.classList.add('selected');
    }
    
    selectedDates.sort();
    updateDetailPanel();
}

function updateDetailPanel() {
    var panel = document.getElementById('calDetailPanel');
    var placeholder = document.getElementById('calDetailPlaceholder');
    
    if (selectedDates.length === 0) {
        panel.style.display = 'none';
        placeholder.style.display = 'block';
        return;
    }

    placeholder.style.display = 'none';
    var html = '<div class="panchang-detail-panel">';
    html += '<h4><i class="fas fa-calendar-check me-2" style="color:var(--chandan-gold);"></i>Selected Dates Detail</h4>';

    selectedDates.forEach(function(date) {
        var dayMuhurats = calendarData.muhurats[date] || [];
        var dayFestivals = calendarData.festivals[date] || [];
        var dayTithi = calendarData.tithis[date] || '';
        
        var d = new Date(date);
        var dateLabel = d.toLocaleDateString('en-IN', {weekday:'long', day:'numeric', month:'long', year:'numeric'});

        html += '<div style="background:var(--chandan-cream); padding:1.2rem; border-radius:8px; margin-bottom:1rem; border-left:4px solid var(--chandan-gold);">';
        html += '<div class="d-flex justify-content-between align-items-center mb-2">';
        html += '<strong style="color:var(--sacred-maroon); font-family:Cinzel,serif; font-size:1.1rem;">' + dateLabel + '</strong>';
        html += '<div class="d-flex align-items-center gap-2">';
        if (dayTithi) html += '<span class="badge" style="background:var(--chandan-gold); color:var(--dark-wood);">' + dayTithi + '</span>';
        if (isSubscribed) {
            html += '<a href="panchang-details.php?date=' + date + '" class="btn-sacred py-1 px-3" style="font-size:0.75rem;"><i class="fas fa-eye me-1"></i>Details</a>';
        } else {
            html += '<a href="subscribe.php" class="btn-sacred py-1 px-3" style="font-size:0.75rem;"><i class="fas fa-lock me-1"></i>Details</a>';
        }
        html += '</div></div>';

        if (dayFestivals.length > 0) {
            html += '<div class="mb-2"><span class="badge bg-danger me-1">' + dayFestivals.join(', ') + '</span></div>';
        }

        if (dayMuhurats.length > 0) {
            html += '<div class="row g-2">';
            dayMuhurats.forEach(function(m) {
                html += '<div class="col-md-6">';
                html += '<div style="background:rgba(255,255,255,0.7); border:1px solid #ddd; padding:0.8rem; border-radius:6px;">';
                html += '<div style="font-weight:700; color:var(--sacred-maroon);">' + (m.title || 'Muhurat') + '</div>';
                html += '<div style="font-size:0.85rem; color:#666;"><i class="fas fa-tag me-1"></i>' + (m.type || '') + '</div>';
                html += '<div style="font-size:0.85rem; color:#444; font-weight:600;"><i class="fas fa-clock me-1"></i>' + (m.start_time || '') + ' - ' + (m.end_time || '') + '</div>';
                if (m.description) html += '<div style="font-size:0.8rem; margin-top:5px; font-style:italic;">' + m.description + '</div>';
                html += '</div></div>';
            });
            html += '</div>';
        } else {
            html += '<p class="text-muted" style="font-size:0.9rem;">No specific muhurats found for this day.</p>';
        }
        
        html += '</div>';
    });

    html += '</div>';
    panel.innerHTML = html;
    panel.style.display = 'block';
    panel.scrollIntoView({behavior:'smooth', block:'nearest'});
}

document.addEventListener('DOMContentLoaded', function() {
    if (isSubscribed) {
        var todayStr = "<?php echo date('Y-m-d'); ?>";
        var todayCell = document.querySelector('.panchang-cal-cell[data-date="' + todayStr + '"]');
        if (todayCell) {
            handleCalClick(todayCell);
        }
    }
});
</script>

<?php require_once 'footer.php'; ?>
