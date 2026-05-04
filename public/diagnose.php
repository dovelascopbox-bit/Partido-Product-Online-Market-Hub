<?php
/**
 * Diagnostic Page - Test InfinityFree Setup
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Partido - Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; }
        .test { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ccc; }
        .pass { border-left-color: green; }
        .fail { border-left-color: red; }
        h1 { color: #0f766e; }
        h2 { font-size: 1.1em; margin: 10px 0 5px 0; }
        .status { font-weight: bold; }
        code { background: #eee; padding: 2px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Partido - Server Diagnostic</h1>
        <p>Last checked: <?php echo date('Y-m-d H:i:s'); ?></p>

        <?php
        // Test 1: PHP Version
        $php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
        ?>
        <div class="test <?php echo $php_ok ? 'pass' : 'fail'; ?>">
            <h2>PHP Version</h2>
            <p><span class="status"><?php echo $php_ok ? '✓ PASS' : '✗ FAIL'; ?></span> - PHP <?php echo PHP_VERSION; ?> (required: 7.4+)</p>
        </div>

        <?php
        // Test 2: Required Extensions
        $extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
        $all_ext_ok = true;
        foreach ($extensions as $ext) {
            if (!extension_loaded($ext)) {
                $all_ext_ok = false;
                break;
            }
        }
        ?>
        <div class="test <?php echo $all_ext_ok ? 'pass' : 'fail'; ?>">
            <h2>Required PHP Extensions</h2>
            <p><span class="status"><?php echo $all_ext_ok ? '✓ PASS' : '✗ FAIL'; ?></span></p>
            <ul>
                <?php foreach ($extensions as $ext): ?>
                    <li><?php echo extension_loaded($ext) ? "✓ $ext" : "✗ $ext (MISSING)"; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
        // Test 3: Config Files
        $config_ok = file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/constants.php') && 
                     file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/database.php');
        ?>
        <div class="test <?php echo $config_ok ? 'pass' : 'fail'; ?>">
            <h2>Configuration Files</h2>
            <p><span class="status"><?php echo $config_ok ? '✓ PASS' : '✗ FAIL'; ?></span></p>
            <ul>
                <li><?php echo file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/constants.php') ? '✓' : '✗'; ?> config/constants.php</li>
                <li><?php echo file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/database.php') ? '✓' : '✗'; ?> config/database.php</li>
            </ul>
        </div>

        <?php
        // Test 4: Database Connection
        if ($config_ok) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';
            $db_ok = isset($pdo) && $pdo !== null;
        } else {
            $db_ok = false;
        }
        ?>
        <div class="test <?php echo $db_ok ? 'pass' : 'fail'; ?>">
            <h2>Database Connection</h2>
            <p><span class="status"><?php echo $db_ok ? '✓ PASS' : '✗ FAIL'; ?></span></p>
            <?php if ($db_ok): ?>
                <p>Successfully connected to database</p>
            <?php else: ?>
                <p style="color: red;">Failed to connect to database. Check config/database.php</p>
            <?php endif; ?>
        </div>

        <?php
        // Test 5: Directory Permissions
        $dirs = [
            '/logs' => is_writable($_SERVER['DOCUMENT_ROOT'] . '/logs'),
            '/assets/uploads' => is_writable($_SERVER['DOCUMENT_ROOT'] . '/assets/uploads'),
        ];
        $perms_ok = !in_array(false, $dirs, true);
        ?>
        <div class="test <?php echo $perms_ok ? 'pass' : 'fail'; ?>">
            <h2>Directory Permissions</h2>
            <p><span class="status"><?php echo $perms_ok ? '✓ PASS' : '✗ FAIL'; ?></span> - Writable directories needed</p>
            <ul>
                <?php foreach ($dirs as $dir => $writable): ?>
                    <li><?php echo $writable ? "✓ $dir (writable)" : "✗ $dir (not writable)"; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
        // Test 6: Classes
        $classes_ok = true;
        $classes = ['Auth', 'Market', 'Product', 'Deal'];
        foreach ($classes as $class) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/classes/$class.php")) {
                $classes_ok = false;
                break;
            }
        }
        ?>
        <div class="test <?php echo $classes_ok ? 'pass' : 'fail'; ?>">
            <h2>Required Classes</h2>
            <p><span class="status"><?php echo $classes_ok ? '✓ PASS' : '✗ FAIL'; ?></span></p>
            <ul>
                <?php foreach ($classes as $class): ?>
                    <li><?php echo file_exists($_SERVER['DOCUMENT_ROOT'] . "/classes/$class.php") ? "✓ $class.php" : "✗ $class.php (MISSING)"; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
        // Test 7: Summary
        $all_ok = $php_ok && $all_ext_ok && $config_ok && $db_ok && $perms_ok && $classes_ok;
        ?>
        <div class="test <?php echo $all_ok ? 'pass' : 'fail'; ?>" style="margin-top: 30px;">
            <h2>Overall Status</h2>
            <p><strong><span class="status" style="font-size: 1.5em;"><?php echo $all_ok ? '✓ ALL SYSTEMS OK' : '✗ ISSUES DETECTED'; ?></span></strong></p>
            <?php if (!$all_ok): ?>
                <p style="color: red;">Please fix the failing tests above before accessing the application.</p>
            <?php else: ?>
                <p style="color: green;"><a href="index.php" style="color: green; text-decoration: none; font-weight: bold;">→ Go to Homepage</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
