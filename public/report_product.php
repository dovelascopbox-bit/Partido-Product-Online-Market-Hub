<?php
/**
 * Report Product AJAX Endpoint
 * Allows users to report inappropriate products (Stage 6)
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

header('Content-Type: application/json');

if (!Auth::isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$product_id || !$reason) {
    echo json_encode(['success' => false, 'message' => 'Product ID and reason are required']);
    exit;
}

try {
    // Verify product exists
    $stmt = $pdo->prepare("SELECT product_id FROM products WHERE product_id = :product_id");
    $stmt->execute([':product_id' => $product_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // Check if user already reported this product
    $checkStmt = $pdo->prepare("SELECT report_id FROM flag_reports 
        WHERE reporter_id = :reporter_id AND item_type = 'product' AND item_id = :product_id");
    $checkStmt->execute([':reporter_id' => $_SESSION['user_id'], ':product_id' => $product_id]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already reported this product']);
        exit;
    }

    // Create flag report
    $insertStmt = $pdo->prepare("INSERT INTO flag_reports
        (reporter_id, item_type, item_id, reason, notes, status, created_at)
        VALUES (:reporter_id, 'product', :product_id, :reason, :notes, 'pending', NOW())");
    
    $insertStmt->execute([
        ':reporter_id' => $_SESSION['user_id'],
        ':product_id' => $product_id,
        ':reason' => $reason,
        ':notes' => $notes
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Product reported successfully. Our team will review it shortly.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
