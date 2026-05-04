<?php
/**
 * InfinityFree Database Configuration
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to database.php
 * 2. Update the credentials below with your InfinityFree MySQL details
 *    (Found in InfinityFree Control Panel -> MySQL)
 * 
 * Example credentials:
 * - DB_HOST: usually sqlXXX.byetcluster.com (check your control panel)
 * - DB_USER: your InfinityFree username + underscore + database name prefix
 * - DB_PASS: your database password
 * - DB_NAME: your username_partido_market
 */

// ============================================================================
// UPDATE THESE VALUES WITH YOUR INFINITYFREE MYSQL CREDENTIALS
// ============================================================================

define('DB_HOST', 'sqlYYY.byetcluster.com');    // Check in InfinityFree Control Panel -> MySQL
define('DB_USER', 'yourusername_partido');      // Your InfinityFree username + database prefix
define('DB_PASS', 'your_database_password'); // Your database password
define('DB_NAME', 'yourusername_partido_market'); // Your database name

// ============================================================================

// Database connection (DO NOT EDIT BELOW)
$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    // Set MySQL timezone to Philippine Time (UTC+8)
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}
?>
