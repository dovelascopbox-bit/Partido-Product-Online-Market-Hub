<?php
/**
 * Messenger Class
 * Handles in-app messaging for deal negotiations
 * Stage 4: Messenger System
 */
class Messenger {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create conversation for a deal
     * @param int $deal_id - Deal ID
     * @return int|false - Conversation ID or false on error
     */
    public function createConversation($deal_id) {
        try {
            // Get deal info (buyer_id, seller_id)
            $stmt = $this->pdo->prepare("
                SELECT buyer_id, seller_id 
                FROM deals 
                WHERE deal_id = :deal_id
            ");
            $stmt->execute([':deal_id' => $deal_id]);
            $deal = $stmt->fetch();

            if (!$deal) {
                return false;
            }

            // Check if conversation already exists for this deal
            $stmt = $this->pdo->prepare("
                SELECT conversation_id 
                FROM conversations 
                WHERE deal_id = :deal_id
            ");
            $stmt->execute([':deal_id' => $deal_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                return $existing['conversation_id'];
            }

            // Create new conversation
            $stmt = $this->pdo->prepare("
                INSERT INTO conversations (deal_id, buyer_id, seller_id)
                VALUES (:deal_id, :buyer_id, :seller_id)
            ");
            $stmt->execute([
                ':deal_id' => $deal_id,
                ':buyer_id' => $deal['buyer_id'],
                ':seller_id' => $deal['seller_id']
            ]);

            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Messenger::createConversation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all conversations for a user
     * @param int $user_id - User ID
     * @return array - Array of conversations with last message preview
     */
    public function getConversations($user_id) {
        try {
            // Get buyer_id for this user
            $buyer_stmt = $this->pdo->prepare("SELECT buyer_id FROM buyers WHERE user_id = ?");
            $buyer_stmt->execute([$user_id]);
            $buyer_id = $buyer_stmt->fetchColumn();
            
            // Get seller_id for this user  
            $seller_stmt = $this->pdo->prepare("SELECT seller_id FROM sellers WHERE user_id = ?");
            $seller_stmt->execute([$user_id]);
            $seller_id = $seller_stmt->fetchColumn();
            
            $stmt = $this->pdo->prepare("
                SELECT 
                    c.conversation_id,
                    c.deal_id,
                    d.status as deal_status,
                    p.product_name,
                    p.image_path,
                    p.srp,
                    CASE 
                        WHEN c.buyer_id = :buyer_id THEN COALESCE(su.full_name, 'Seller')
                        ELSE COALESCE(bu.full_name, 'Buyer')
                    END as other_party_name,
                    (SELECT message_text FROM messages WHERE conversation_id = c.conversation_id ORDER BY sent_at DESC LIMIT 1) as last_message,
                    (SELECT sent_at FROM messages WHERE conversation_id = c.conversation_id ORDER BY sent_at DESC LIMIT 1) as last_message_time,
                    (SELECT COUNT(*) FROM messages WHERE conversation_id = c.conversation_id AND is_read = FALSE AND sender_id != :user_id) as unread_count,
                    c.created_at
                FROM conversations c
                JOIN deals d ON c.deal_id = d.deal_id
                JOIN products p ON d.product_id = p.product_id
                LEFT JOIN buyers b ON c.buyer_id = b.buyer_id
                LEFT JOIN users bu ON b.user_id = bu.user_id
                LEFT JOIN sellers s ON c.seller_id = s.seller_id
                LEFT JOIN users su ON s.user_id = su.user_id
                WHERE (c.buyer_id = :buyer_id OR c.seller_id = :seller_id)
                ORDER BY COALESCE((SELECT sent_at FROM messages WHERE conversation_id = c.conversation_id ORDER BY sent_at DESC LIMIT 1), c.created_at) DESC
                LIMIT 50
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':buyer_id' => $buyer_id ?: 0,
                ':seller_id' => $seller_id ?: 0
            ]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Messenger::getConversations error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get messages for a conversation
     * @param int $conversation_id - Conversation ID
     * @param int $limit - Number of messages to fetch
     * @return array - Array of messages
     */
    public function getMessages($conversation_id, $limit = 100) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    m.message_id,
                    m.sender_id,
                    u.full_name as sender_name,
                    m.message_text,
                    m.is_read,
                    m.sent_at
                FROM messages m
                JOIN users u ON m.sender_id = u.user_id
                WHERE m.conversation_id = :conversation_id
                ORDER BY m.sent_at ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Messenger::getMessages error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get new messages since a given timestamp
     * @param int $conversation_id - Conversation ID
     * @param int $user_id - User ID (to exclude own messages from new count)
     * @return array - Array of new messages
     */
    public function getNewMessages($conversation_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    m.message_id,
                    m.sender_id,
                    u.full_name as sender_name,
                    m.message_text,
                    m.is_read,
                    m.sent_at
                FROM messages m
                JOIN users u ON m.sender_id = u.user_id
                WHERE m.conversation_id = :conversation_id
                AND m.sender_id != :user_id
                AND m.is_read = FALSE
                ORDER BY m.sent_at ASC
            ");
            $stmt->execute([
                ':conversation_id' => $conversation_id,
                ':user_id' => $user_id
            ]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Messenger::getNewMessages error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Send a message
     * @param int $conversation_id - Conversation ID
     * @param int $sender_id - Sender user ID
     * @param string $message_text - Message content
     * @return int|false - Message ID or false on error
     */
    public function sendMessage($conversation_id, $sender_id, $message_text) {
        try {
            // Validate message length
            if (strlen($message_text) > 1000) {
                $message_text = substr($message_text, 0, 1000);
            }

            // Sanitize message
            $message_text = trim($message_text);
            if (empty($message_text)) {
                return false;
            }

            // Insert message
            $stmt = $this->pdo->prepare("
                INSERT INTO messages (conversation_id, sender_id, message_text, is_read)
                VALUES (:conversation_id, :sender_id, :message_text, FALSE)
            ");
            $stmt->execute([
                ':conversation_id' => $conversation_id,
                ':sender_id' => $sender_id,
                ':message_text' => $message_text
            ]);

            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Messenger::sendMessage error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark messages as read
     * @param int $conversation_id - Conversation ID
     * @param int $user_id - User ID (receiver, not sender)
     * @return bool - Success status
     */
    public function markAsRead($conversation_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE messages 
                SET is_read = TRUE
                WHERE conversation_id = :conversation_id
                AND sender_id != :user_id
                AND is_read = FALSE
            ");
            $stmt->execute([
                ':conversation_id' => $conversation_id,
                ':user_id' => $user_id
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Messenger::markAsRead error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has access to conversation
     * @param int $conversation_id - Conversation ID
     * @param int $user_id - User ID
     * @return bool - True if user is part of conversation
     */
    public function checkAccess($conversation_id, $user_id) {
        try {
            // Get buyer and seller IDs from conversation
            $stmt = $this->pdo->prepare("
                SELECT c.buyer_id, c.seller_id, b.user_id as buyer_user_id, s.user_id as seller_user_id
                FROM conversations c
                LEFT JOIN buyers b ON c.buyer_id = b.buyer_id
                LEFT JOIN sellers s ON c.seller_id = s.seller_id
                WHERE c.conversation_id = :conversation_id
            ");
            $stmt->execute([':conversation_id' => $conversation_id]);
            $conv = $stmt->fetch();

            if (!$conv) {
                return false;
            }

            // Check if user is buyer or seller
            return ($conv['buyer_user_id'] == $user_id || $conv['seller_user_id'] == $user_id);
        } catch (Exception $e) {
            error_log("Messenger::checkAccess error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get conversation with deal and product details
     * @param int $conversation_id - Conversation ID
     * @return array|false - Conversation details or false
     */
    public function getConversationDetails($conversation_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    c.conversation_id,
                    c.deal_id,
                    c.buyer_id,
                    c.seller_id,
                    d.status as deal_status,
                    d.confirmed_by_buyer,
                    d.confirmed_by_seller,
                    p.product_id,
                    p.product_name,
                    p.image_path,
                    p.srp,
                    ub.full_name as buyer_name,
                    us.full_name as seller_name,
                    c.created_at
                FROM conversations c
                JOIN deals d ON c.deal_id = d.deal_id
                JOIN products p ON d.product_id = p.product_id
                LEFT JOIN buyers b ON c.buyer_id = b.buyer_id
                LEFT JOIN users ub ON b.user_id = ub.user_id
                LEFT JOIN sellers s ON c.seller_id = s.seller_id
                LEFT JOIN users us ON s.user_id = us.user_id
                WHERE c.conversation_id = :conversation_id
            ");
            $stmt->execute([':conversation_id' => $conversation_id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Messenger::getConversationDetails error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread message count for user
     * @param int $user_id - User ID
     * @return int - Total unread count
     */
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(m.message_id) as unread_count
                FROM messages m
                JOIN conversations c ON m.conversation_id = c.conversation_id
                WHERE (c.buyer_id IN (SELECT buyer_id FROM buyers WHERE user_id = :user_id)
                       OR c.seller_id IN (SELECT seller_id FROM sellers WHERE user_id = :user_id))
                AND m.sender_id != :user_id
                AND m.is_read = FALSE
            ");
            $stmt->execute([':user_id' => $user_id]);
            $result = $stmt->fetch();
            return $result['unread_count'] ?? 0;
        } catch (Exception $e) {
            error_log("Messenger::getUnreadCount error: " . $e->getMessage());
            return 0;
        }
    }
}
?>
