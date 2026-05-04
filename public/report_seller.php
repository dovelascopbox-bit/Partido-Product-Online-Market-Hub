<?php
/**
 * Report Seller AJAX Endpoint
 * Allows users to report sellers with issues (Stage 6)
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

$seller_id = intval($_POST['seller_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$seller_id || !$reason) {
    echo json_encode(['success' => false, 'message' => 'Seller ID and reason are required']);
    exit;
}

try {
    // Verify seller exists
    $stmt = $pdo->prepare("SELECT seller_id FROM sellers WHERE seller_id = :seller_id");
    $stmt->execute([':seller_id' => $seller_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Seller not found']);
        exit;
    }

    // Check if user already reported this seller
    $checkStmt = $pdo->prepare("SELECT report_id FROM flag_reports 
        WHERE reporter_id = :reporter_id AND item_type = 'seller' AND item_id = :seller_id");
    $checkStmt->execute([':reporter_id' => $_SESSION['user_id'], ':seller_id' => $seller_id]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already reported this seller']);
        exit;
    }

    // Create flag report
    $insertStmt = $pdo->prepare("INSERT INTO flag_reports
        (reporter_id, item_type, item_id, reason, notes, status, created_at)
        VALUES (:reporter_id, 'seller', :seller_id, :reason, :notes, 'pending', NOW())");
    
    $insertStmt->execute([
        ':reporter_id' => $_SESSION['user_id'],
        ':seller_id' => $seller_id,
        ':reason' => $reason,
        ':notes' => $notes
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Seller reported successfully. Our team will review it shortly.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
