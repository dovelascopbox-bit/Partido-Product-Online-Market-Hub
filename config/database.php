<?php
/**
 * Database Configuration - InfinityFree Production
 * Partido Product Online Market Hub
 */

define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_41733042');
define('DB_PASS', '0319Lovee'); // REPLACE with your InfinityFree MySQL password
define('DB_NAME', 'if0_41733042_partido_market');

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}
?>
