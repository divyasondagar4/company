<?php
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    
    $_docRoot = str_replace('\\', '/', ltrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
    $_projectRoot = str_replace('\\', '/', ltrim(__DIR__, '/'));
    
    $_docRoot = preg_replace('/^[a-z]:/i', '', $_docRoot);
    $_projectRoot = preg_replace('/^[a-z]:/i', '', $_projectRoot);
    
    $_basePath = str_ireplace($_docRoot, '', $_projectRoot);
    $_basePath = '/' . trim($_basePath, '/');
    
    if ($_basePath === '/') {
        $_basePath = '';
    }
    
    return $protocol . "://" . $host . $_basePath;
}

if (!defined('SITE_URL')) {
    define('SITE_URL', getBaseUrl());
}
?>
