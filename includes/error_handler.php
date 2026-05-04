<?php
/**
 * GLOBAL ERROR HANDLER
 * Partido Product Online Market Hub - Stage 7
 * 
 * Handles all uncaught exceptions and errors
 * Routes to branded error pages instead of exposing system details
 * Logs errors securely for debugging
 */

// Create logs directory if it doesn't exist
$log_dir = $_SERVER['DOCUMENT_ROOT'] . '/logs';
if (!file_exists($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Set error log file
$error_log = $log_dir . '/error.log';
ini_set('error_log', $error_log);

/**
 * Custom Exception Handler
 */
function handleException($exception) {
    error_log("[" . date('Y-m-d H:i:s') . "] Exception: " . $exception->getMessage() . "\n" .
              "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n" .
              "Trace: " . $exception->getTraceAsString() . "\n");
    
    // Determine which error page to show
    if (strpos($exception->getMessage(), '404') !== false) {
        http_response_code(404);
        $error_page = $_SERVER['DOCUMENT_ROOT'] . '/public/404.php';
    } elseif (strpos($exception->getMessage(), 'Unauthorized') !== false ||
              strpos($exception->getMessage(), '403') !== false) {
        http_response_code(403);
        $error_page = $_SERVER['DOCUMENT_ROOT'] . '/public/403.php';
    } else {
        http_response_code(500);
        $error_page = $_SERVER['DOCUMENT_ROOT'] . '/public/500.php';
    }
    
    // Only include error page if it exists
    if (file_exists($error_page)) {
        include $error_page;
    } else {
        // Fallback to simple error message
        echo "An error occurred. Please contact support.";
    }
    exit;
}

/**
 * Custom Error Handler
 */
function handleError($errno, $errstr, $errfile, $errline) {
    // Don't catch suppressed errors (@)
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Log all errors
    error_log("[" . date('Y-m-d H:i:s') . "] PHP Error [$errno]: $errstr in $errfile:$errline\n");

    // Fatal errors: show error page
    if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        $error_page = $_SERVER['DOCUMENT_ROOT'] . '/public/500.php';
        if (file_exists($error_page)) {
            include $error_page;
        } else {
            echo "A fatal error occurred. Please try again.";
        }
        exit;
    }

    // Return false to continue with PHP's default error handling
    return false;
}

/**
 * Custom Shutdown Handler (for fatal errors)
 */
function handleShutdown() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("[" . date('Y-m-d H:i:s') . "] Fatal Error [{$error['type']}]: " . 
                  $error['message'] . " in " . $error['file'] . ":" . $error['line'] . "\n");
        
        http_response_code(500);
        $error_page = $_SERVER['DOCUMENT_ROOT'] . '/public/500.php';
        if (file_exists($error_page)) {
            include $error_page;
        }
    }
}

// Register error handlers
set_exception_handler('handleException');
set_error_handler('handleError');
register_shutdown_function('handleShutdown');

// Set default error reporting
ini_set('display_errors', 0); // Don't display errors to users
error_reporting(E_ALL); // Report all errors
?>

