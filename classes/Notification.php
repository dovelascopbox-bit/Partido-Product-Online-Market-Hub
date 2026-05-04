<?php
/**
 * Notification Management Class
 * Handles event notifications for deal lifecycle (Stage 5)
 */

class Notification {
    private $pdo;
    private $table = 'notifications';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a notification
     */
    public function create($user_id, $type, $message, $deal_id = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} 
                (user_id, type, message, deal_id, is_read, created_at) 
                VALUES (:user_id, :type, :message, :deal_id, FALSE, NOW())");
            
            $stmt->execute([
                ':user_id' => $user_id,
                ':type' => $type,
                ':message' => $message,
                ':deal_id' => $deal_id
            ]);

            return [
                'success' => true,
                'notification_id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error creating notification: ' . $e->getMessage()];
        }
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnread($user_id, $limit = 10) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = FALSE
                ORDER BY created_at DESC
                LIMIT :limit");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all notifications for a user
     */
    public function getAll($user_id, $limit = 20, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} 
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET is_read = TRUE 
                WHERE notification_id = :notification_id AND user_id = :user_id");
            
            $stmt->execute([
                ':notification_id' => $notification_id,
                ':user_id' => $user_id
            ]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false];
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($user_id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET is_read = TRUE 
                WHERE user_id = :user_id AND is_read = FALSE");
            
            $stmt->execute([':user_id' => $user_id]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false];
        }
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = :user_id AND is_read = FALSE");
            $stmt->execute([':user_id' => $user_id]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Delete notification
     */
    public function delete($notification_id, $user_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} 
                WHERE notification_id = :notification_id AND user_id = :user_id");
            
            $stmt->execute([
                ':notification_id' => $notification_id,
                ':user_id' => $user_id
            ]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false];
        }
    }

    /**
     * Notify buyer that seller confirmed deal
     */
    public static function notifyBuyerSellerConfirmed($pdo, $buyer_user_id, $product_name, $deal_id) {
        $notification = new Notification($pdo);
        $message = "Seller confirmed your deal for \"$product_name\". Please confirm too!";
        return $notification->create($buyer_user_id, 'deal_confirmed', $message, $deal_id);
    }

    /**
     * Notify seller that deal is fully completed
     */
    public static function notifySellerDealCompleted($pdo, $seller_user_id, $product_name, $deal_id) {
        $notification = new Notification($pdo);
        $message = "Deal completed for \"$product_name\"! Check your rating.";
        return $notification->create($seller_user_id, 'deal_completed', $message, $deal_id);
    }

    /**
     * Notify seller that they received a rating
     */
    public static function notifySellerRatingReceived($pdo, $seller_user_id, $buyer_name, $stars, $product_name, $deal_id) {
        $notification = new Notification($pdo);
        $stars_display = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
        $message = "$buyer_name rated you $stars_display for \"$product_name\"";
        return $notification->create($seller_user_id, 'rating_received', $message, $deal_id);
    }

    /**
     * Notify buyer when deal is initiated
     */
    public static function notifyBuyerDealInitiated($pdo, $buyer_user_id, $product_name, $deal_id) {
        $notification = new Notification($pdo);
        $message = "Deal initiated for \"$product_name\". Open messenger to start negotiating!";
        return $notification->create($buyer_user_id, 'deal_initiated', $message, $deal_id);
    }
}
?>
