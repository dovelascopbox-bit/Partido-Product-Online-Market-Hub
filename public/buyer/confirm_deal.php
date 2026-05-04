<?php
/**
 * Buyer Confirm Deal Endpoint (AJAX)
 * POST-only endpoint for buyer to confirm deal completion
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Check authentication
if (!Auth::isAuthenticated() || $_SESSION['role'] !== 'buyer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get buyer_id from session
$buyer_result = $pdo->query("SELECT buyer_id FROM buyers WHERE user_id = {$_SESSION['user_id']}")->fetch();
if (!$buyer_result) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Buyer not found']);
    exit;
}
$buyer_id = $buyer_result['buyer_id'];

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF token verification failed']);
    exit;
}

// Get deal_id from POST
$deal_id = isset($_POST['deal_id']) ? (int)$_POST['deal_id'] : 0;
if (!$deal_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Deal ID is required']);
    exit;
}

// Process confirmation
$deal = new Deal($pdo);
$result = $deal->buyerConfirmDeal($deal_id, $buyer_id);

if (!$result['success']) {
    http_response_code(400);
    echo json_encode($result);
    exit;
}

// Get deal details for notification
$dealInfo = $deal->getById($deal_id);
if ($dealInfo) {
    // Get seller's user_id
    $sellerStmt = $pdo->prepare("SELECT user_id FROM sellers WHERE seller_id = :seller_id");
    $sellerStmt->execute([':seller_id' => $dealInfo['seller_id']]);
    $seller = $sellerStmt->fetch();

    // Get product name
    $productStmt = $pdo->prepare("SELECT product_name FROM products WHERE product_id = :product_id");
    $productStmt->execute([':product_id' => $dealInfo['product_id']]);
    $product = $productStmt->fetch();

    if ($seller && $product && $dealInfo['confirmed_by_seller'] && $dealInfo['confirmed_by_buyer']) {
        // Deal is now completed, notify seller
        Notification::notifySellerDealCompleted(
            $pdo,
            $seller['user_id'],
            $product['product_name'],
            $deal_id
        );
    }
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Deal confirmed! Redirecting to rating...',
    'redirect' => BASE_URL . '/public/buyer/rate.php?deal_id=' . $deal_id
]);
exit;
?>
