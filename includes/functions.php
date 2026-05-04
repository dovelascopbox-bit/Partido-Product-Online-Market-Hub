<?php
/**
 * Helper Functions & Security Utilities
 */

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * Requirements: min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
 */
function validatePasswordStrength($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return false;
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    if (!preg_match('/\d/', $password)) {
        return false;
    }
    return true;
}

/**
 * Rate limiting - check login attempts
 */
function checkRateLimit($email, $max_attempts = MAX_LOGIN_ATTEMPTS) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    if (!isset($_SESSION['login_attempts'][$email])) {
        $_SESSION['login_attempts'][$email] = ['count' => 0, 'first_attempt' => time()];
    }

    $attempt_data = $_SESSION['login_attempts'][$email];
    $time_diff = time() - $attempt_data['first_attempt'];

    // Reset if lock period expired
    if ($time_diff > ATTEMPT_LOCK_TIME) {
        $_SESSION['login_attempts'][$email] = ['count' => 0, 'first_attempt' => time()];
        return true;
    }

    // Check if max attempts reached
    if ($attempt_data['count'] >= $max_attempts) {
        return false;
    }

    return true;
}

/**
 * Increment login attempts
 */
function incrementLoginAttempts($email) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    if (!isset($_SESSION['login_attempts'][$email])) {
        $_SESSION['login_attempts'][$email] = ['count' => 0, 'first_attempt' => time()];
    }

    $_SESSION['login_attempts'][$email]['count']++;
}

/**
 * Reset login attempts
 */
function resetLoginAttempts($email) {
    if (isset($_SESSION['login_attempts'][$email])) {
        unset($_SESSION['login_attempts'][$email]);
    }
}

/**
 * Get remaining login attempts
 */
function getRemainingAttempts($email) {
    if (!isset($_SESSION['login_attempts'][$email])) {
        return MAX_LOGIN_ATTEMPTS;
    }

    $count = $_SESSION['login_attempts'][$email]['count'];
    return max(0, MAX_LOGIN_ATTEMPTS - $count);
}

/**
 * Secure redirect
 */
function secureRedirect($url) {
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header('Location: ' . $url);
        exit;
    }
    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}

/**
 * Set flash message
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Get flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Check authentication and redirect if needed
 */
function requireAuth($allowed_roles = []) {
    if (!Auth::isAuthenticated()) {
        setFlashMessage(MSG_SESSION_EXPIRED, 'error');
        secureRedirect(BASE_URL . '/public/login.php');
    }

    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        setFlashMessage(MSG_UNAUTHORIZED, 'error');
        secureRedirect(BASE_URL . '/public/index.php');
    }

    if (!Auth::checkSessionTimeout()) {
        setFlashMessage(MSG_SESSION_EXPIRED, 'error');
        secureRedirect(BASE_URL . '/public/login.php');
    }
}

/**
 * Check if already logged in
 */
function requireGuest() {
    if (Auth::isAuthenticated()) {
        secureRedirect(Auth::getDashboardUrl($_SESSION['role']));
    }
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2, '.', ',');
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $colors = [
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-100 text-gray-800',
        'suspended' => 'bg-red-100 text-red-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];

    $class = $colors[$status] ?? 'bg-gray-100 text-gray-800';
    return '<span class="px-3 py-1 rounded-full text-sm font-semibold ' . $class . '">' . ucfirst($status) . '</span>';
}

/**
 * Log security events
 */
function logSecurityEvent($event_type, $description, $user_id = null, $ip_address = null) {
    $ip = $ip_address ?? $_SERVER['REMOTE_ADDR'];
    $user = $user_id ?? ($_SESSION['user_id'] ?? null);
    
    error_log(
        "[" . date('Y-m-d H:i:s') . "] {$event_type} - User: {$user}, IP: {$ip}, Description: {$description}"
    );
}

/**
 * Handle product image upload
 * Allowed: jpg, png, webp
 * Max size: 2MB
 */
function uploadProductImage($file, $seller_id) {
    // Validate file
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }

    // Check file size (max 2MB pre-compression)
    $max_size = 2 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size must be less than 2MB.'];
    }

    // Check MIME type
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, and WEBP images are allowed.'];
    }

    // Get file extension
    $ext_map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];
    $extension = $ext_map[$file['type']];

    // Generate unique filename
    $filename = 'product_' . $seller_id . '_' . time() . '.jpg';
    $upload_dir = 'assets/uploads/products/';
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $upload_dir;

    // Ensure directory exists
    if (!is_dir($full_path)) {
        mkdir($full_path, 0755, true);
    }

    $file_path = $full_path . $filename;
    $relative_path = $upload_dir . $filename;

    // Stage 7: COMPRESS IMAGE using GD library (with fallback)
    if (extension_loaded('gd')) {
        $compressed_result = compressProductImage($file['tmp_name'], $file_path, $extension);
        
        if (!$compressed_result['success']) {
            return ['success' => false, 'message' => $compressed_result['message']];
        }

        logSecurityEvent('file_upload', 'Product image uploaded and compressed', $_SESSION['user_id']);
    } else {
        // Fallback: move uploaded file directly without compression
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return ['success' => false, 'message' => 'Failed to save uploaded image. Please try again.'];
        }

        logSecurityEvent('file_upload', 'Product image uploaded (GD not available, no compression)', $_SESSION['user_id']);
    }

    return ['success' => true, 'message' => 'Image uploaded successfully!', 'path' => $relative_path];
}

/**
 * STAGE 7: Compress product image using GD library
 * Resizes to 800px max width and compresses to 85% JPEG quality
 * 
 * @param string $source_path Temporary uploaded file path
 * @param string $dest_path Destination file path
 * @param string $format Original image format (jpg, png, webp)
 * @return array ['success' => bool, 'message' => string]
 */
function compressProductImage($source_path, $dest_path, $format = 'jpg') {
    // Check if GD extension is available
    if (!extension_loaded('gd')) {
        return ['success' => false, 'message' => 'GD library not available for image processing.'];
    }

    try {
        // Load image
        $image = null;
        switch ($format) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($source_path);
                break;
            case 'png':
                $image = imagecreatefrompng($source_path);
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($source_path);
                } else {
                    return ['success' => false, 'message' => 'WebP support not available.'];
                }
                break;
            default:
                return ['success' => false, 'message' => 'Unsupported image format.'];
        }

        if (!$image) {
            return ['success' => false, 'message' => 'Failed to load image.'];
        }

        // Get original dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Calculate new dimensions (max 800px width)
        $max_width = 800;
        if ($width > $max_width) {
            $new_width = $max_width;
            $new_height = ($height / $width) * $max_width;
        } else {
            $new_width = $width;
            $new_height = $height;
        }

        // Create resized image
        $resized = imagecreatetruecolor($new_width, $new_height);
        
        // Preserve transparency for PNG
        if ($format === 'png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $new_width, $new_height, $transparent);
        }

        // Resize
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Save as JPEG with 85% quality
        imagejpeg($resized, $dest_path, 85);

        // Free memory
        imagedestroy($image);
        imagedestroy($resized);

        return ['success' => true, 'message' => 'Image compressed successfully.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Image compression failed: ' . $e->getMessage()];
    }
}

/**
 * Delete product image
 */
function deleteProductImage($image_path) {
    if (empty($image_path)) {
        return true;
    }

    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image_path;
    
    if (file_exists($full_path)) {
        return unlink($full_path);
    }

    return true;
}

/**
 * Get image dimensions (for validation)
 */
function getImageDimensions($file_path) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file_path;
    
    if (!file_exists($full_path)) {
        return null;
    }

    return getimagesize($full_path);
}

/**
 * Get product image URL
 */
function getProductImageUrl($image_path) {
    if (empty($image_path)) {
        return BASE_URL . '/assets/images/placeholder-product.png';
    }

    return BASE_URL . '/' . $image_path;
}

/**
 * STAGE 7: PAGINATION HELPERS
 * ════════════════════════════════════════════════════════════
 */

/**
 * Calculate pagination offsets and limits
 * 
 * @param int $current_page Current page number (1-indexed)
 * @param int $items_per_page Items to display per page
 * @return array ['offset' => int, 'limit' => int, 'page' => int]
 */
function getPaginationParams($current_page = 1, $items_per_page = 12) {
    $current_page = max(1, (int)($current_page));
    $items_per_page = max(1, (int)($items_per_page));
    $offset = ($current_page - 1) * $items_per_page;
    
    return [
        'offset' => $offset,
        'limit' => $items_per_page,
        'page' => $current_page
    ];
}

/**
 * Get total pages for pagination
 * 
 * @param int $total_items Total number of items
 * @param int $items_per_page Items per page
 * @return int Total number of pages
 */
function getTotalPages($total_items, $items_per_page = 12) {
    return max(1, (int)ceil($total_items / $items_per_page));
}

/**
 * Generate query string for pagination links
 * Preserves existing query parameters
 * 
 * @param int $page Page number
 * @param array $preserve_params Parameters to preserve in query string
 * @return string Query string (without leading ?)
 */
function buildPaginationQueryString($page, $preserve_params = []) {
    $params = $_GET;
    $params['page'] = max(1, (int)$page);
    
    // Remove admin-only params for security
    unset($params['_token']);
    
    return http_build_query($params);
}

/**
 * Generate HTML pagination controls
 * 
 * @param int $current_page Current page number
 * @param int $total_pages Total number of pages
 * @param string $base_url Base URL for links
 * @param array $preserve_params Query params to preserve
 * @return string HTML pagination controls
 */
function renderPagination($current_page, $total_pages, $base_url = '', $preserve_params = []) {
    if ($total_pages <= 1) {
        return ''; // No pagination needed
    }

    if (empty($base_url)) {
        $base_url = $_SERVER['REQUEST_URI'];
        // Remove existing page param
        $base_url = preg_replace('/[&?]page=\d+/', '', $base_url);
    }

    $html = '<nav class="pagination" role="navigation" aria-label="Pagination">';
    $html .= '<ul class="flex gap-2 justify-center flex-wrap">';

    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $qs = buildPaginationQueryString($prev_page, $preserve_params);
        $url = $base_url . (strpos($base_url, '?') === false ? '?' : '&') . $qs;
        $html .= '<li><a href="' . htmlspecialchars($url) . '" class="pagination-link" aria-label="Previous page">← Previous</a></li>';
    }

    // Page number links (show max 5 pages at a time)
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);

    if ($start_page > 1) {
        $qs = buildPaginationQueryString(1, $preserve_params);
        $url = $base_url . (strpos($base_url, '?') === false ? '?' : '&') . $qs;
        $html .= '<li><a href="' . htmlspecialchars($url) . '" class="pagination-link">1</a></li>';
        if ($start_page > 2) {
            $html .= '<li><span class="pagination-ellipsis">…</span></li>';
        }
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $current_page) {
            $html .= '<li><span class="pagination-current" aria-current="page">' . $i . '</span></li>';
        } else {
            $qs = buildPaginationQueryString($i, $preserve_params);
            $url = $base_url . (strpos($base_url, '?') === false ? '?' : '&') . $qs;
            $html .= '<li><a href="' . htmlspecialchars($url) . '" class="pagination-link">' . $i . '</a></li>';
        }
    }

    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $html .= '<li><span class="pagination-ellipsis">…</span></li>';
        }
        $qs = buildPaginationQueryString($total_pages, $preserve_params);
        $url = $base_url . (strpos($base_url, '?') === false ? '?' : '&') . $qs;
        $html .= '<li><a href="' . htmlspecialchars($url) . '" class="pagination-link">' . $total_pages . '</a></li>';
    }

    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $qs = buildPaginationQueryString($next_page, $preserve_params);
        $url = $base_url . (strpos($base_url, '?') === false ? '?' : '&') . $qs;
        $html .= '<li><a href="' . htmlspecialchars($url) . '" class="pagination-link" aria-label="Next page">Next →</a></li>';
    }

    $html .= '</ul>';
    $html .= '</nav>';

    return $html;
}

/**
 * Add pagination CSS styles
 * Call this in <head> or include in main.css
 */
function getPaginationStyles() {
    return <<<CSS
.pagination {
    display: flex;
    justify-content: center;
    margin: 2rem 0;
}

.pagination ul {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.pagination-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
    padding: 0.5rem 1rem;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    text-decoration: none;
    color: var(--color-primary);
    background: var(--color-surface);
    font-weight: 500;
    transition: all 0.2s;
}

.pagination-link:hover {
    background: var(--color-surface-2);
    border-color: var(--color-primary);
}

.pagination-link:focus-visible {
    outline: 3px solid var(--color-focus);
    outline-offset: 2px;
}

.pagination-current {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
    padding: 0.5rem 1rem;
    background: var(--color-primary);
    color: white;
    font-weight: 600;
    border-radius: var(--radius-sm);
}

.pagination-ellipsis {
    padding: 0.5rem 0.5rem;
    color: var(--color-text-muted);
}
CSS;
}
?>

