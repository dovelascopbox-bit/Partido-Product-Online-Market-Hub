<?php
/**
 * Application Initialization
 * Include this file at the top of every page
 */

// Set timezone to Philippine Time (UTC+8)
date_default_timezone_set('Asia/Manila');

// Load error handler FIRST (Stage 7 - ISO/IEC 25010)
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/error_handler.php';

// Configure session security
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    // Only set secure flag for HTTPS in production, not for local development
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $is_localhost = $_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === 0;
    ini_set('session.cookie_secure', ($is_https || !$is_localhost) ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Load configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/constants.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Deal.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Market.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Messenger.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Rating.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Notification.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Admin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Email.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Check session timeout if authenticated
if (Auth::isAuthenticated()) {
    if (!Auth::checkSessionTimeout()) {
        session_destroy();
        header('Location: ' . BASE_URL . '/public/login.php?timeout=1');
        exit;
    }
}
?>
