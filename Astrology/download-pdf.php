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
    header("Location: " . SITE_URL . "/login.php?redirect=download-pdf&id=$id");
    exit();
}

if (!isAdmin() && !isSubscribed($conn, $_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/subscribe.php?msg=subscription_required");
    exit();
}

// Get full panchang record
$stmt = $conn->prepare("SELECT * FROM panchang WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$panchang = $result->fetch_assoc();

if (!$panchang) {
    header("Location: " . SITE_URL . "/panchang.php?msg=no_data");
    exit();
}

// Generate HTML Content for PDF
$dateStr = t_date($panchang['panchang_date']);
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Astro Panchang - ' . $dateStr . '</title>
    <style>
        body { font-family: "Helvetica", "Arial", sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #C5973B; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #5B1A18; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { color: #8C6239; margin: 5px 0 0; font-size: 14px; }
        .section-title { background: #FEFCF4; color: #5B1A18; padding: 8px 12px; font-weight: bold; font-size: 16px; border-left: 4px solid #C5973B; margin-top: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #e0e0e0; padding: 10px 12px; font-size: 13px; }
        th { background-color: #f9f9f9; text-align: left; width: 35%; color: #555; }
        td { color: #222; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
        .flex-container { width: 100%; }
        .flex-half { width: 48%; display: inline-block; vertical-align: top; }
    </style>
</head>
<body>

    <div class="header">
        <h1>' . t('astro_panchang') . '</h1>
        <p>' . t('divine_guide') . '</p>
        <p><strong>' . $dateStr . '</strong></p>
    </div>
    
    <div class="section-title">' . t('daily_overview') . '</div>
    <table>
        <tr>
            <th>' . t('sunrise') . ' / ' . t('sunset') . '</th>
            <td>' . ($panchang['sunrise'] ? date('h:i A', strtotime($panchang['sunrise'])) : t('na')) . ' / ' . ($panchang['sunset'] ? date('h:i A', strtotime($panchang['sunset'])) : t('na')) . '</td>
        </tr>
        <tr>
            <th>' . t('location') . '</th><td>' . ($panchang['location'] ?: t('na')) . '</td>
        </tr>
        <tr>
            <th>' . t('vikram_samvat') . '</th><td>' . ($panchang['vikram_samvat'] ?: t('na')) . '</td>
        </tr>
        <tr>
            <th>' . t('gujarati_month') . '</th><td>' . ($panchang['gujarati_month'] ? t($panchang['gujarati_month']) : t('na')) . '</td>
        </tr>
        <tr>
            <th>' . t('ayan') . '</th><td>' . ($panchang['ayan'] ? t($panchang['ayan']) : t('na')) . '</td>
        </tr>
    </table>

    <div class="section-title">' . t('panchang_elements') . '</div>
    <table>
        <tr>
            <th>' . t('tithi') . '</th>
            <td>' . ($panchang['tithi'] ? t($panchang['tithi']) : t('na')) . ' (' . t('end') . ': ' . ($panchang['tithi_end'] ?: t('na')) . ')</td>
        </tr>
        <tr>
            <th>' . t('nakshatra') . '</th>
            <td>' . ($panchang['nakshatra'] ? t($panchang['nakshatra']) : t('na')) . 
            ' ( ' . t('start') . ': ' . ($panchang['nak_start'] ?: '---') . ' | ' . t('end') . ': ' . ($panchang['nak_end'] ?: '---') . ' )</td>
        </tr>
        <tr>
            <th>' . t('yoga') . '</th>
            <td>' . ($panchang['yoga'] ? t($panchang['yoga']) : t('na')) . ' (' . t('end') . ': ' . ($panchang['yoga_end'] ?: t('na')) . ')</td>
        </tr>
        <tr>
            <th>' . t('karana') . '</th>
            <td>' . ($panchang['karana'] ? t($panchang['karana']) : t('na')) . ' (' . t('end') . ': ' . ($panchang['karana_end'] ?: t('na')) . ')</td>
        </tr>
    </table>

    <div class="section-title">' . t('vichudo_panchak') . '</div>
    <table>
        <tr>
            <th>' . t('vichudo') . '</th>
            <td>' . ($panchang['vichudo'] ?: t('na')) . ' (' . t('start') . ': ' . ($panchang['vichudo_start'] ?: '---') . ' | ' . t('end') . ': ' . ($panchang['vichudo_end'] ?: '---') . ')</td>
        </tr>
        <tr>
            <th>' . t('panchak') . '</th>
            <td>' . t('start') . ': ' . ($panchang['panchak_start'] ?: '---') . ' | ' . t('end') . ': ' . ($panchang['panchak_end'] ?: '---') . '</td>
        </tr>
    </table>

    <div class="section-title">' . t('inauspicious_timings') . '</div>
    <table>
        <tr>
            <th style="color: #e74c3c;">' . t('rahu_kaal') . '</th>
            <td>' . ($panchang['rahu_start'] && $panchang['rahu_end'] ? date('h:i A', strtotime($panchang['rahu_start'])) . ' - ' . date('h:i A', strtotime($panchang['rahu_end'])) : t('na')) . '</td>
        </tr>
        <tr>
            <th style="color: #9b59b6;">' . t('gulika_kaal') . '</th>
            <td>' . ($panchang['gulika_start'] && $panchang['gulika_end'] ? date('h:i A', strtotime($panchang['gulika_start'])) . ' - ' . date('h:i A', strtotime($panchang['gulika_end'])) : t('na')) . '</td>
        </tr>
        <tr>
            <th style="color: #e67e22;">' . t('yama_gandam') . '</th>
            <td>' . ($panchang['yama_start'] && $panchang['yama_end'] ? date('h:i A', strtotime($panchang['yama_start'])) . ' - ' . date('h:i A', strtotime($panchang['yama_end'])) : t('na')) . '</td>
        </tr>
    </table>

    <div class="section-title">' . t('graha_position') . '</div>
    <table>
        <tr>
            <th>' . t('sun_longitude') . '</th><td>' . ($panchang['sun_lon'] ? $panchang['sun_lon'] . '°' : t('na')) . '</td>
        </tr>
        <tr>
            <th>' . t('moon_longitude') . '</th><td>' . ($panchang['moon_lon'] ? $panchang['moon_lon'] . '°' : t('na')) . '</td>
        </tr>
    </table>

    ' . ($panchang['details'] ? '<div class="section-title">' . t('details') . '</div><div style="padding:10px; border:1px solid #eee; font-size:13px; color:#444;">' . nl2br(htmlspecialchars($panchang['details'])) . '</div>' : '') . '

    <div class="footer">
        ' . t('generated_by') . ' ' . date("Y") . '<br>
        ' . t('pdf_footer_text') . '
    </div>

</body>
</html>';

// Initialize Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Load HTML
$dompdf->loadHtml($html);

// Set Paper Size
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output to Browser
$filename = "Astro_Panchang_" . date('Y_m_d', strtotime($panchang['panchang_date'])) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit();
?>
