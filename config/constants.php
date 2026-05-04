<?php
/**
 * Application Constants - InfinityFree Production
 * Partido Product Online Market Hub
 */

// Application
define('APP_NAME', 'Partido Product Online Market Hub');
define('APP_VERSION', '1.0.0');
define('STAGE', 'Production - InfinityFree');

// Paths
define('BASE_URL', 'https://partido-online-market-hub.page.gd');
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
// Set to FALSE for production - email verification required
define('SKIP_EMAIL_VERIFICATION', true);

// Environment
define('ENVIRONMENT', 'production');
?>
