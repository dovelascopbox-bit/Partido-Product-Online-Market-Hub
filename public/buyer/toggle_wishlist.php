<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require buyer role
requireAuth(['buyer']);

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'CSRF token invalid']);
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
if (!$product_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Get buyer info
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT buyer_id FROM buyers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

if (!$buyer_info) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Buyer not found']);
    exit;
}

$buyer_id = $buyer_info['buyer_id'];

// Initialize wishlist session if not exists
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$wishlist = &$_SESSION['wishlist'];

// Toggle: add if not present, remove if present
$found = false;
foreach ($wishlist as $key => $item) {
    if ($item['product_id'] == $product_id && $item['buyer_id'] == $buyer_id) {
        unset($wishlist[$key]);
        $found = true;
        $action = 'removed';
        break;
    }
}

if (!$found) {
    // Get product info
    try {
        $stmt = $pdo->prepare("
            SELECT p.product_id, p.product_name, p.srp, p.image_path, s.shop_name 
            FROM products p 
            JOIN sellers s ON p.seller_id = s.seller_id 
            WHERE p.product_id = :product_id AND p.status = 'available'
        ");
        $stmt->execute([':product_id' => $product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            $wishlist[] = [
                'product_id' => $product['product_id'],
                'buyer_id' => $buyer_id,
                'product_name' => $product['product_name'],
                'srp' => $product['srp'],
                'image_path' => $product['image_path'],
                'shop_name' => $product['shop_name'],
                'added_at' => date('Y-m-d H:i:s')
            ];
            $action = 'added';
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Product not available']);
            exit;
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }
}

// Re-index array
$_SESSION['wishlist'] = array_values($wishlist);

$count = count($_SESSION['wishlist']);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'action' => $action,
    'count' => $count,
    'message' => $action === 'added' ? 'Added to wishlist' : 'Removed from wishlist'
]);
exit;
