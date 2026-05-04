<?php
/**
 * InfinityFree Application Constants
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to constants.php
 * 2. Update BASE_URL with your actual InfinityFree domain
 * 3. For production, set ENVIRONMENT to 'production'
 * 4. For production, set SKIP_EMAIL_VERIFICATION to false
 */

// ============================================================================
// UPDATE THESE VALUES FOR INFINITYFREE
// ============================================================================

// Application
define('APP_NAME', 'Partido Product Online Market Hub');
define('APP_VERSION', '1.0.0');
define('STAGE', 'Production - InfinityFree');

// IMPORTANT: Replace with your actual InfinityFree domain or subdomain
// Example: https://yourdomain.epizy.com/htdocs/ParProOMH or https://yourdomain.byethost.com/htdocs/ParProOMH
define('BASE_URL', 'https://your-domain.epizy.com');  // UPDATE THIS!

// Paths - Leave as is for InfinityFree (DOCUMENT_ROOT handles it)
define('PUBLIC_PATH', $_SERVER['DOCUMENT_ROOT'] . '/public');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

// Timezone - Philippine Time
define('APP_TIMEZONE', 'Asia/Manila');
define('APP_TIMEZONE_OFFSET', '+08:00');

// Security
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('ATTEMPT_LOCK_TIME', 900); // 15 minutes

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_SELLER', 'seller');
define('ROLE_BUYER', 'buyer');

// Messages
define('MSG_LOGIN_SUCCESS', 'Successfully logged in!');
define('MSG_LOGIN_FAILED', 'Invalid email or password.');
define('MSG_REGISTER_SUCCESS', 'Registration successful! Please log in.');
define('MSG_REGISTER_FAILED', 'Registration failed. Please try again.');
define('MSG_SESSION_EXPIRED', 'Your session has expired. Please log in again.');
define('MSG_UNAUTHORIZED', 'Unauthorized access. Please log in.');
define('MSG_EMAIL_NOT_VERIFIED', 'Please verify your email before logging in.');
define('MSG_VERIFICATION_SENT', 'Verification email sent! Check your inbox.');

// Email Verification Settings
// IMPORTANT: Set to FALSE for production deployment!
define('SKIP_EMAIL_VERIFICATION', false);  // Set to TRUE only if you can't send emails

// Environment - Set to 'production' for live site
define('ENVIRONMENT', 'production');  // 'development' or 'production'
?>
