<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require seller role
requireAuth(['seller']);

// Get product ID
$product_id = intval($_GET['id'] ?? 0);
$csrf_token = $_GET['csrf'] ?? '';

if (!$product_id) {
    setFlashMessage('Product not found.', 'error');
    secureRedirect(BASE_URL . '/public/seller/products/list.php');
}

// Verify CSRF token
if (!verifyCSRFToken($csrf_token)) {
    setFlashMessage('Security token validation failed.', 'error');
    logSecurityEvent('csrf_failure', 'Delete product CSRF token mismatch');
    secureRedirect(BASE_URL . '/public/seller/products/list.php');
}

// Get seller info
$seller_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM sellers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $seller_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading seller info.', 'error');
    secureRedirect(BASE_URL . '/public/seller/dashboard.php');
}

if (!$seller_info) {
    setFlashMessage('Seller account not found.', 'error');
    secureRedirect(BASE_URL . '/public/seller/dashboard.php');
}

// Delete product
$product_obj = new Product($pdo);
$result = $product_obj->delete($product_id, $seller_info['seller_id']);

if ($result['success']) {
    setFlashMessage($result['message'], 'success');
    logSecurityEvent('product_deleted', 'Product deleted: ID ' . $product_id, $_SESSION['user_id']);
} else {
    setFlashMessage($result['message'], 'error');
}

secureRedirect(BASE_URL . '/public/seller/products/list.php');
?>
