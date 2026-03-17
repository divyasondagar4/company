<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'astro_panchang');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Kolkata');

$conn->set_charset("utf8mb4");
$conn->query("SET NAMES utf8mb4");

require_once __DIR__ . '/config.php';

define('SITE_NAME', 'Astro Panchang');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

function isSubscribed($conn, $user_id) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT id FROM subscriptions WHERE user_id = ? AND status = 'active' AND start_date <= ? AND end_date >= ?");
    $stmt->bind_param("iss", $user_id, $today, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function sanitize($conn, $input) {
    return $conn->real_escape_string(trim($input));
}

function render_field($val, $isTime = false) {
    if ($val === null || (is_string($val) && trim($val) === '')) {
        return '<span class="text-muted">' . t('na') . '</span>';
    }

    $val = trim((string)$val);

    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $val)) {
        return date('d M Y, h:i A', strtotime($val));
    }

    if ($isTime) {
        $prefix_map = [
            'સવાર' => 'AM', 'બપોર' => 'PM',
            'સાંજ' => 'PM', 'રાત્રે' => 'PM',
            'મધ્યરાત્રિ' => 'AM'
        ];
        foreach ($prefix_map as $gu => $ap) {
            if (strpos($val, $gu) !== false) {
                $cleaned = trim(str_replace($gu, '', $val));
                if (!preg_match('/(AM|PM)/i', $cleaned)) {
                    $val = $cleaned . ' ' . $ap;
                } else {
                    $val = $cleaned;
                }
                break;
            }
        }
        $ts = strtotime($val);
        if ($ts === false) {
            $val_clean = preg_replace('/[^\x20-\x7E]/', '', $val);
            $ts = strtotime($val_clean);
        }
        if ($ts !== false) {
            return date('h:i A', $ts);
        }
    }

    return htmlspecialchars($val);
}

function render_translated_field($val) {
    if ($val === null || (is_string($val) && trim($val) === '')) {
        return '<span class="text-muted">' . t('na') . '</span>';
    }
    $raw = trim((string)$val);
    return htmlspecialchars(t($raw));
}
?>
