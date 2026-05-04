<?php
/**
 * Report Rating AJAX Endpoint
 * Allows admins to report inappropriate ratings (Stage 6)
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

$rating_id = intval($_POST['rating_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$rating_id || !$reason) {
    echo json_encode(['success' => false, 'message' => 'Rating ID and reason are required']);
    exit;
}

try {
    // Verify rating exists
    $stmt = $pdo->prepare("SELECT rating_id FROM ratings WHERE rating_id = :rating_id");
    $stmt->execute([':rating_id' => $rating_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Rating not found']);
        exit;
    }

    // Check if already reported
    $checkStmt = $pdo->prepare("SELECT report_id FROM flag_reports 
        WHERE reporter_id = :reporter_id AND item_type = 'rating' AND item_id = :rating_id");
    $checkStmt->execute([':reporter_id' => $_SESSION['user_id'], ':rating_id' => $rating_id]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already reported this rating']);
        exit;
    }

    // Create flag report
    $insertStmt = $pdo->prepare("INSERT INTO flag_reports
        (reporter_id, item_type, item_id, reason, notes, status, created_at)
        VALUES (:reporter_id, 'rating', :rating_id, :reason, :notes, 'pending', NOW())");
    
    $insertStmt->execute([
        ':reporter_id' => $_SESSION['user_id'],
        ':rating_id' => $rating_id,
        ':reason' => $reason,
        ':notes' => $notes
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Rating reported successfully. Our team will review it shortly.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
