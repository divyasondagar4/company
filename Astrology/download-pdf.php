<?php
session_start();
require_once 'db.php';
require_once 'lang.php';
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if user is logged in and subscribed
if (!isLoggedIn()) {
    header("Location: " . SITE_URL . "/login?redirect=download-pdf&id=$id");
    exit();
}

if (!isAdmin() && !isSubscribed($conn, $_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/subscribe?msg=subscription_required");
    exit();
}

// Get full panchang record
$stmt = $conn->prepare("SELECT * FROM panchang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$panchang = $result->fetch_assoc();

if (!$panchang) {
    header("Location: " . SITE_URL . "/panchang?msg=no_data");
    exit();
}


// PDF helper — safe value display
$pv = function($val, $translate = false) {
    if ($val === null || trim((string)$val) === '') return '---';
    $v = trim((string)$val);
    if ($translate) return htmlspecialchars(t($v));
    return htmlspecialchars($v);
};

// Time parser for PDF
$pt = function($val) {
    if ($val === null || trim((string)$val) === '') return '---';
    $val = trim((string)$val);
    $prefix_map = ['સવાર'=>'AM','બપોર'=>'PM','સાંજ'=>'PM','રાત્રે'=>'PM','મધ્યરાત્રિ'=>'AM'];
    foreach ($prefix_map as $gu => $ap) {
        if (strpos($val, $gu) !== false) {
            $cleaned = trim(str_replace($gu, '', $val));
            $val = !preg_match('/(AM|PM)/i', $cleaned) ? $cleaned . ' ' . $ap : $cleaned;
            break;
        }
    }
    $ts = strtotime($val);
    if ($ts === false) { $ts = strtotime(preg_replace('/[^\x20-\x7E]/', '', $val)); }
    return $ts ? date('h:i A', $ts) : htmlspecialchars($val);
};

$dateStr = t_date($panchang['panchang_date']);
$dateForFile = date('d_M_Y', strtotime($panchang['panchang_date']));

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . t('astro_panchang') . ' - ' . $dateStr . '</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&family=Noto+Sans+Gujarati:wght@400;600;700&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap");
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Noto Sans", "Noto Sans Gujarati", "Noto Sans Devanagari", sans-serif; 
            color: #333; line-height: 1.5; font-size: 12px; padding: 20px; 
        }
        
        .header { 
            text-align: center; padding: 18px 10px 14px; margin-bottom: 16px;
            background: #FDFBF0; border: 2px solid #5B1A18; border-radius: 8px;
        }
        .header h1 { color: #5B1A18; font-size: 22px; letter-spacing: 2px; margin-bottom: 4px; }
        .header .subtitle { color: #8C6239; font-size: 11px; margin-bottom: 6px; }
        .header .date-line { color: #5B1A18; font-size: 15px; font-weight: 700; }
        .header .meta { color: #8C6239; font-size: 9px; margin-top: 4px; opacity: 0.8; }
        
        .section { 
            margin-bottom: 12px; border: 1px solid #e5ddd0; border-radius: 6px; overflow: hidden; 
        }
        .section-title { 
            background: #FEFCF4; color: #5B1A18; padding: 7px 12px; font-weight: 700; 
            font-size: 13px; border-bottom: 2px solid #C5973B;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 10px; font-size: 11px; border-bottom: 1px solid #f0ebe3; }
        th { background: #faf7f0; text-align: left; width: 32%; color: #5B1A18; font-weight: 600; }
        td { color: #333; font-weight: 400; }
        tr:last-child th, tr:last-child td { border-bottom: none; }
        
        .two-col { width: 100%; }
        .two-col td { width: 50%; padding: 0; vertical-align: top; }
        .two-col .col-inner { margin: 0 4px; }
        
        .badge-yes { color: #c0392b; font-weight: 700; }
        .badge-no { color: #27ae60; }
        .time-danger { color: #c0392b; }
        
        .footer { 
            margin-top: 16px; text-align: center; font-size: 9px; color: #999; 
            border-top: 1px solid #eee; padding-top: 8px; 
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>☉ ' . t('astro_panchang') . '</h1>
        <div class="subtitle">' . t('divine_guide') . '</div>
        <div class="date-line">' . $dateStr . '</div>
        <div class="meta">' . t('location') . ': ' . $pv($panchang['location']) . ' | ID: ' . $panchang['id'] . '</div>
    </div>

    <!-- Daily Overview -->
    <div class="section">
        <div class="section-title">☀ ' . t('solar_lunar_details') . '</div>
        <table>
            <tr><th>' . t('day') . '</th><td>' . $pv($panchang['day_name'], true) . ' (' . $pv($panchang['vara_no']) . ')</td></tr>
            <tr><th>' . t('sunrise') . ' / ' . t('sunset') . '</th><td>' . $pt($panchang['sunrise']) . ' / ' . $pt($panchang['sunset']) . '</td></tr>
            <tr><th>' . t('ayan') . '</th><td>' . $pv($panchang['ayan'], true) . ' (' . $pv($panchang['ayan_no']) . ')</td></tr>
            <tr><th>' . t('gujarati_month') . '</th><td>' . $pv($panchang['gujarati_month'], true) . ' (' . $pv($panchang['gujarati_month_no']) . ')</td></tr>
            <tr><th>' . t('vikram_samvat') . '</th><td>' . $pv($panchang['vikram_samvat']) . ' (' . $pv($panchang['year']) . '-' . $pv($panchang['month']) . ')</td></tr>
        </table>
    </div>

    <!-- Panchang Elements -->
    <div class="section">
        <div class="section-title">🕉 ' . t('panchang_elements') . '</div>
        <table>
            <tr><th>' . t('tithi') . ' (' . $pv($panchang['tithi_no']) . ')</th><td>' . $pv($panchang['tithi'], true) . '<br><small>' . t('end') . ': ' . $pv($panchang['tithi_end']) . '</small></td></tr>
            <tr><th>' . t('nakshatra') . ' (' . $pv($panchang['nak_no']) . ')</th><td>' . $pv($panchang['nakshatra'], true) . '<br><small>' . t('start') . ': ' . $pv($panchang['nak_start']) . ' | ' . t('end') . ': ' . $pv($panchang['nak_end']) . '</small></td></tr>
            <tr><th>' . t('yoga') . ' (' . $pv($panchang['yoga_no']) . ')</th><td>' . $pv($panchang['yoga'], true) . '<br><small>' . t('end') . ': ' . $pv($panchang['yoga_end']) . '</small></td></tr>
            <tr><th>' . t('karana') . ' (' . $pv($panchang['karana_no']) . ')</th><td>' . $pv($panchang['karana'], true) . '<br><small>' . t('end') . ': ' . $pv($panchang['karana_end']) . '</small></td></tr>
        </table>
    </div>

    <!-- Inauspicious Timings -->
    <div class="section">
        <div class="section-title">⚠ ' . t('inauspicious_timings') . '</div>
        <table>
            <tr><th class="time-danger">' . t('rahu_kaal') . '</th><td>' . $pt($panchang['rahu_start']) . ' – ' . $pt($panchang['rahu_end']) . '</td></tr>
            <tr><th style="color:#9b59b6;">' . t('gulika_kaal') . '</th><td>' . $pt($panchang['gulika_start']) . ' – ' . $pt($panchang['gulika_end']) . '</td></tr>
            <tr><th style="color:#e67e22;">' . t('yama_gandam') . '</th><td>' . $pt($panchang['yama_start']) . ' – ' . $pt($panchang['yama_end']) . '</td></tr>
        </table>
    </div>

    <!-- Vichudo & Panchak -->
    <div class="section">
        <div class="section-title">⚡ ' . t('vichudo_panchak') . '</div>
        <table>
            <tr><th>' . t('vichudo') . '</th><td>' . ($panchang['vichudo'] === 'YES' ? '<span class="badge-yes">YES</span>' : '<span class="badge-no">' . $pv($panchang['vichudo']) . '</span>') . '
                ' . (($panchang['vichudo_start'] || $panchang['vichudo_end']) ? '<br><small>' . t('start') . ': ' . $pv($panchang['vichudo_start']) . ' | ' . t('end') . ': ' . $pv($panchang['vichudo_end']) . '</small>' : '') . '</td></tr>
            <tr><th>' . t('panchak') . '</th><td>' . t('start') . ': ' . $pv($panchang['panchak_start']) . ' | ' . t('end') . ': ' . $pv($panchang['panchak_end']) . '</td></tr>
        </table>
    </div>

    <!-- Graha Position -->
    <div class="section">
        <div class="section-title">🌍 ' . t('graha_position') . '</div>
        <table>
            <tr><th>' . t('sun_longitude') . '</th><td>' . $pv($panchang['sun_lon']) . '°</td></tr>
            <tr><th>' . t('moon_longitude') . '</th><td>' . $pv($panchang['moon_lon']) . '°</td></tr>
        </table>
    </div>

    ' . ($panchang['details'] ? '<div class="section"><div class="section-title">📋 ' . t('details') . '</div><div style="padding:8px 12px; font-size:11px; color:#555;">' . nl2br(htmlspecialchars($panchang['details'])) . '</div></div>' : '') . '

    <div class="footer">
        ' . t('generated_by') . ' ' . date("Y") . '<br>
        ' . t('pdf_footer_text') . '
    </div>

</body>
</html>';
// Initialize Download pdf with Unicode support
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Noto Sans');
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "Astro_Panchang_" . $dateForFile . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit();
?>
