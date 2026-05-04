<?php
/**
 * Public Logout Handler
 * Secure public endpoint for user logout
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Log security event if user was authenticated
if (Auth::isAuthenticated()) {
    logSecurityEvent('user_logout', 'User logged out successfully', $_SESSION['user_id']);
}

// Perform logout
Auth::logout();

// Redirect to home page with success message
header('Location: ' . BASE_URL . '/public/index.php?logout=1');
exit;
?>
