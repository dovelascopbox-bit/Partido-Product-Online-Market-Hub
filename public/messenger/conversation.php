<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication
requireAuth();

// Get deal ID from URL
$deal_id = isset($_GET['deal_id']) ? intval($_GET['deal_id']) : 0;
if (!$deal_id) {
    setFlashMessage('Deal not found', 'error');
    secureRedirect(BASE_URL . '/public/index.php');
}

// Get messenger
$messenger = new Messenger($pdo);

// Get or create conversation
$stmt = $pdo->prepare("SELECT * FROM deals WHERE deal_id = :deal_id");
$stmt->execute([':deal_id' => $deal_id]);
$deal = $stmt->fetch();

if (!$deal) {
    setFlashMessage('Deal not found', 'error');
    secureRedirect(BASE_URL . '/public/index.php');
}

// Get current user's buyer/seller info
$buyer_id = null;
$seller_id = null;
$is_buyer = false;
$is_seller = false;

$stmt = $pdo->prepare("SELECT buyer_id FROM buyers WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$buyer = $stmt->fetch();
if ($buyer) {
    $buyer_id = $buyer['buyer_id'];
    $is_buyer = true;
}

$stmt = $pdo->prepare("SELECT seller_id FROM sellers WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$seller = $stmt->fetch();
if ($seller) {
    $seller_id = $seller['seller_id'];
    $is_seller = true;
}

// Check if user is part of this deal
$is_participant = ($buyer_id && $buyer_id == $deal['buyer_id']) || ($seller_id && $seller_id == $deal['seller_id']);

if (!$is_participant) {
    setFlashMessage('You do not have access to this conversation', 'error');
    secureRedirect(BASE_URL . '/public/index.php');
}

// Create or get conversation
$conversation_id = $messenger->createConversation($deal_id);
if (!$conversation_id) {
    setFlashMessage('Error creating conversation', 'error');
    secureRedirect(BASE_URL . '/public/index.php');
}

// Mark messages as read
$messenger->markAsRead($conversation_id, $_SESSION['user_id']);

// Get conversation details
$conv_details = $messenger->getConversationDetails($conversation_id);
$messages = $messenger->getMessages($conversation_id);

// Get other party name
$other_party_name = ($is_buyer) ? $conv_details['seller_name'] : $conv_details['buyer_name'];

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('partido_theme_preference');
                const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemDark ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-surface shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="<?php echo BASE_URL; ?>/public/index.php" class="flex items-center gap-2 text-lg font-bold text-blue-600">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" class="h-8 w-auto rounded">
                        <span>Partido Online Hub</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/index.php" class="text-text font-medium hover:text-primary transition">← Back to Conversations</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto h-[calc(100vh-64px)] flex flex-col bg-white">
        <!-- Product Context Header -->
        <div class="bg-blue-50 border-b border-blue-200 p-4 flex items-center gap-4">
            <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                <?php $img = getProductImageUrl($conv_details['image_path'] ?? ''); ?>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="product" class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-lg text-gray-900"><?php echo htmlspecialchars($conv_details['product_name']); ?></h3>
                <p class="text-blue-600 font-semibold"><?php echo formatCurrency($conv_details['srp']); ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Chatting with</p>
                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($other_party_name); ?></p>
            </div>
        </div>

        <!-- Deal Status Banner -->
        <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3 text-sm">
            <?php if ($conv_details['deal_status'] == 'ongoing'): ?>
                <span class="text-yellow-800 font-semibold">🤝 Deal Ongoing — Arrange your meetup here!</span>
            <?php elseif ($conv_details['deal_status'] == 'completed'): ?>
                <span class="text-green-800 font-semibold">✓ Deal Completed — This conversation is now read-only</span>
            <?php else: ?>
                <span class="text-red-800 font-semibold">✕ Deal Cancelled</span>
            <?php endif; ?>
        </div>

        <!-- Messages Area -->
        <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
            <?php foreach ($messages as $message): ?>
                <?php 
                $is_own_message = ($message['sender_id'] == $_SESSION['user_id']);
                $bubble_class = $is_own_message ? 'ml-auto bg-blue-600 text-white' : 'mr-auto bg-gray-200 text-gray-900';
                $align_class = $is_own_message ? 'flex-row-reverse' : 'flex-row';
                ?>
                <div class="flex <?php echo $align_class; ?> gap-2 max-w-md">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        <?php echo substr(htmlspecialchars($message['sender_name']), 0, 1); ?>
                    </div>
                    <div class="flex flex-col <?php echo $is_own_message ? 'items-end' : 'items-start'; ?>">
                        <p class="text-xs text-gray-600 mb-1">
                            <?php echo htmlspecialchars($message['sender_name']); ?> • 
                            <?php echo formatDate($message['sent_at'], 'g:i A'); ?>
                        </p>
                        <div class="<?php echo $bubble_class; ?> rounded-lg px-4 py-2 break-words">
                            <?php echo htmlspecialchars($message['message_text']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($messages)): ?>
                <div class="text-center text-gray-500 py-8">
                    <p>No messages yet. Start the conversation!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Message Input (disabled if deal completed) -->
        <?php $is_readonly = ($conv_details['deal_status'] != 'ongoing'); ?>
        <div class="border-t border-gray-200 p-4 bg-white">
            <?php if ($is_readonly): ?>
                <div class="text-center text-gray-500 py-3">
                    This conversation is read-only. Deal is <?php echo htmlspecialchars($conv_details['deal_status']); ?>.
                </div>
            <?php else: ?>
                <form id="message-form" class="flex gap-3">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                    <textarea 
                        id="message-input"
                        name="message_text" 
                        placeholder="Type your message..." 
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                        rows="3"
                        maxlength="1000"
                    ></textarea>
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium h-fit"
                    >
                        Send
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-2">Max 1000 characters • Messages are real-time</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const conversationId = <?php echo $conversation_id; ?>;
        const userId = <?php echo $_SESSION['user_id']; ?>;
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        let lastFetchTime = new Date().toISOString();

        // Auto-scroll to bottom
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Send message
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const messageText = messageInput.value.trim();
            if (!messageText) return;

            const formData = new FormData(messageForm);
            
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/public/messenger/send.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    messageInput.value = '';
                    messageInput.focus();
                    fetchNewMessages();
                } else {
                    alert('Error sending message: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error sending message');
            }
        });

        // Fetch new messages
        async function fetchNewMessages() {
            try {
                const response = await fetch(`<?php echo BASE_URL; ?>/public/messenger/fetch.php?conversation_id=${conversationId}`);
                const data = await response.json();
                
                if (data.success && data.messages.length > 0) {
                    // Append new messages
                    data.messages.forEach(msg => {
                        const isOwnMessage = msg.sender_id == userId;
                        const bubbleClass = isOwnMessage ? 'ml-auto bg-blue-600 text-white' : 'mr-auto bg-gray-200 text-gray-900';
                        const alignClass = isOwnMessage ? 'flex-row-reverse' : 'flex-row';
                        
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `flex ${alignClass} gap-2 max-w-md`;
                        messageDiv.innerHTML = `
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                ${msg.sender_name.substring(0, 1)}
                            </div>
                            <div class="flex flex-col ${isOwnMessage ? 'items-end' : 'items-start'}">
                                <p class="text-xs text-gray-600 mb-1">
                                    ${msg.sender_name} • ${new Date(msg.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                </p>
                                <div class="${bubbleClass} rounded-lg px-4 py-2 break-words">
                                    ${escapeHtml(msg.message_text)}
                                </div>
                            </div>
                        `;
                        messagesContainer.appendChild(messageDiv);
                    });
                    
                    scrollToBottom();
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Poll for new messages every 4 seconds
        setInterval(fetchNewMessages, 4000);

        // Fetch any unread messages immediately and scroll to bottom
        fetchNewMessages();
        // Initial scroll after messages appended
        scrollToBottom();
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
