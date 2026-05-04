<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require buyer role
requireAuth(['buyer']);

// Handle deal initiation only via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('Invalid request', 'error');
    secureRedirect(BASE_URL . '/public/buyer/market.php');
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlashMessage('Security token verification failed', 'error');
    secureRedirect(BASE_URL . '/public/buyer/market.php');
}

// Get POST parameters
$product_id = intval($_POST['product_id'] ?? 0);
$seller_id = intval($_POST['seller_id'] ?? 0);

if (!$product_id || !$seller_id) {
    setFlashMessage('Invalid product or seller', 'error');
    secureRedirect(BASE_URL . '/public/buyer/market.php');
}

// Get buyer info
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM buyers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading buyer info', 'error');
    secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
}

if (!$buyer_info) {
    setFlashMessage('Buyer account not found', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

// Verify product exists and is available
$market = new Market($pdo);
$product = $market->getProductById($product_id);

if (!$product) {
    setFlashMessage('Product not found', 'error');
    secureRedirect(BASE_URL . '/public/buyer/market.php');
}

// Check if product is still available for deals
if (!$market->isProductAvailable($product_id)) {
    setFlashMessage('Product is no longer available', 'error');
    secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
}

// Check if buyer already has ongoing deal for this product
try {
    $stmt = $pdo->prepare("SELECT deal_id FROM deals WHERE product_id = :product_id AND buyer_id = :buyer_id AND status = 'ongoing'");
    if (!isset($buyer_info['buyer_id']) || !is_scalar($buyer_info['buyer_id'])) {
        setFlashMessage('Invalid buyer ID', 'error');
        secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
    }
    $stmt->execute([':product_id' => $product_id, ':buyer_id' => $buyer_info['buyer_id']]);
    $existing_deal = $stmt->fetch();
    
    if ($existing_deal) {
        setFlashMessage('You already have an ongoing deal for this product', 'warning');
        secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
    }
} catch (Exception $e) {
    setFlashMessage('Database error', 'error');
    secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
}

// Create deal record
$deal_obj = new Deal($pdo);

try {
    if (!is_scalar($buyer_info['buyer_id'])) {
        setFlashMessage('Invalid buyer data', 'error');
        secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
    }
    $deal_id = $deal_obj->create($product_id, $buyer_info['buyer_id'], $seller_id);
    
    if ($deal_id) {
        setFlashMessage('Deal initiated! Redirecting to messenger...', 'success');
        // Redirect to messenger conversation
        header('Location: ' . BASE_URL . '/public/messenger/conversation.php?deal_id=' . $deal_id);
        exit;
    } else {
        setFlashMessage('Failed to create deal', 'error');
        secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
    }
} catch (Exception $e) {
    setFlashMessage('Error creating deal: ' . $e->getMessage(), 'error');
    secureRedirect(BASE_URL . '/public/buyer/product.php?id=' . $product_id);
}
