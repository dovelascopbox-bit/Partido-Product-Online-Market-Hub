<?php
/**
 * Seller Confirm Deal Endpoint (AJAX)
 * POST-only endpoint for seller to confirm deal completion
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Check authentication
if (!Auth::isAuthenticated() || $_SESSION['role'] !== 'seller') {
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

// Get seller_id from session
$stmt = $pdo->prepare("SELECT seller_id FROM sellers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$seller_result = $stmt->fetch();
if (!$seller_result) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Seller not found']);
    exit;
}
$seller_id = $seller_result['seller_id'];

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
$result = $deal->sellerConfirmDeal($deal_id, $seller_id);

if (!$result['success']) {
    http_response_code(400);
    echo json_encode($result);
    exit;
}

// Get deal details for notification
$dealInfo = $deal->getById($deal_id);
if ($dealInfo) {
    // Get buyer's user_id
    $buyerStmt = $pdo->prepare("SELECT user_id FROM buyers WHERE buyer_id = :buyer_id");
    $buyerStmt->execute([':buyer_id' => $dealInfo['buyer_id']]);
    $buyer = $buyerStmt->fetch();

    // Get product name
    $productStmt = $pdo->prepare("SELECT product_name FROM products WHERE product_id = :product_id");
    $productStmt->execute([':product_id' => $dealInfo['product_id']]);
    $product = $productStmt->fetch();

    if ($buyer && $product) {
        // Notify buyer that seller confirmed
        Notification::notifyBuyerSellerConfirmed(
            $pdo, 
            $buyer['user_id'], 
            $product['product_name'],
            $deal_id
        );
    }
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Deal confirmed! Waiting for buyer confirmation...'
]);
exit;
?>
