<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Only GET allowed
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Require authentication
requireAuth();

// Return JSON
header('Content-Type: application/json; charset=utf-8');

// Get conversation ID
$conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;

if (!$conversation_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing conversation_id']);
    exit;
}

try {
    $messenger = new Messenger($pdo);

    // Check user access to conversation
    if (!$messenger->checkAccess($conversation_id, $_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    // Mark all unread messages from other users as read
    $messenger->markAsRead($conversation_id, $_SESSION['user_id']);

    // Get new messages (unread from other users)
    $messages = $messenger->getNewMessages($conversation_id, $_SESSION['user_id']);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'count' => count($messages)
    ]);

} catch (Exception $e) {
    error_log("Fetch messages error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
