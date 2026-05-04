<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Require authentication
requireAuth();

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'CSRF token invalid']);
    exit;
}

// Get parameters
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : 0;
$message_text = isset($_POST['message_text']) ? sanitizeInput($_POST['message_text']) : '';

if (!$conversation_id || !$message_text) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
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

    // Get conversation details to check if deal is still ongoing
    $stmt = $pdo->prepare("
        SELECT d.status FROM conversations c
        JOIN deals d ON c.deal_id = d.deal_id
        WHERE c.conversation_id = :conversation_id
    ");
    $stmt->execute([':conversation_id' => $conversation_id]);
    $conv = $stmt->fetch();

    if (!$conv || $conv['status'] != 'ongoing') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Deal is not ongoing']);
        exit;
    }

    // Send message
    $message_id = $messenger->sendMessage($conversation_id, $_SESSION['user_id'], $message_text);

    if (!$message_id) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error sending message']);
        exit;
    }

    // Get the message we just sent
    $stmt = $pdo->prepare("
        SELECT 
            m.message_id,
            m.sender_id,
            u.full_name as sender_name,
            m.message_text,
            m.is_read,
            m.sent_at
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.message_id = :message_id
    ");
    $stmt->execute([':message_id' => $message_id]);
    $message = $stmt->fetch();

    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Message sent',
        'message_data' => $message
    ]);

} catch (Exception $e) {
    error_log("Send message error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>
