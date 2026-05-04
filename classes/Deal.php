<?php
/**
 * Deal Management Class
 * Handles deal creation and management (Stage 2+)
 */

class Deal {
    private $pdo;
    private $table = 'deals';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new deal
     */
    public function create($product_id, $buyer_id, $seller_id) {
        try {
            // Check if deal already exists
            $existing = $this->getDealByProductAndBuyer($product_id, $buyer_id);
            if ($existing && $existing['status'] === 'ongoing') {
                return ['success' => false, 'message' => 'Deal already exists for this product.'];
            }

            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} 
                (product_id, buyer_id, seller_id, status, confirmed_by_seller, confirmed_by_buyer) 
                VALUES (:product_id, :buyer_id, :seller_id, 'ongoing', FALSE, FALSE)");
            
            $stmt->execute([
                ':product_id' => $product_id,
                ':buyer_id' => $buyer_id,
                ':seller_id' => $seller_id
            ]);

            $deal_id = $this->pdo->lastInsertId();
            return ['success' => true, 'message' => 'Deal created!', 'deal_id' => $deal_id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error creating deal: ' . $e->getMessage()];
        }
    }

    /**
     * Get deal by ID
     */
    public function getById($deal_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE deal_id = :deal_id");
            $stmt->execute([':deal_id' => $deal_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get deals by seller
     */
    public function getBySellerID($seller_id, $status = null) {
        try {
            if ($status) {
                $stmt = $this->pdo->prepare("SELECT d.*, p.product_name, p.srp, 
                    u.full_name as buyer_name, s.shop_name
                    FROM {$this->table} d
                    JOIN products p ON d.product_id = p.product_id
                    JOIN buyers b ON d.buyer_id = b.buyer_id
                    JOIN users u ON b.user_id = u.user_id
                    JOIN sellers s ON d.seller_id = s.seller_id
                    WHERE d.seller_id = :seller_id AND d.status = :status
                    ORDER BY d.created_at DESC");
                $stmt->execute([':seller_id' => $seller_id, ':status' => $status]);
            } else {
                $stmt = $this->pdo->prepare("SELECT d.*, p.product_name, p.srp, 
                    u.full_name as buyer_name, s.shop_name
                    FROM {$this->table} d
                    JOIN products p ON d.product_id = p.product_id
                    JOIN buyers b ON d.buyer_id = b.buyer_id
                    JOIN users u ON b.user_id = u.user_id
                    JOIN sellers s ON d.seller_id = s.seller_id
                    WHERE d.seller_id = :seller_id
                    ORDER BY d.created_at DESC");
                $stmt->execute([':seller_id' => $seller_id]);
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get deals by buyer
     */
    public function getByBuyerID($buyer_id, $status = null) {
        try {
            if ($status) {
                $stmt = $this->pdo->prepare("SELECT d.*, p.product_name, p.srp, 
                    u.full_name as seller_name, s.shop_name
                    FROM {$this->table} d
                    JOIN products p ON d.product_id = p.product_id
                    JOIN sellers s ON d.seller_id = s.seller_id
                    JOIN users u ON s.user_id = u.user_id
                    WHERE d.buyer_id = :buyer_id AND d.status = :status
                    ORDER BY d.created_at DESC");
                $stmt->execute([':buyer_id' => $buyer_id, ':status' => $status]);
            } else {
                $stmt = $this->pdo->prepare("SELECT d.*, p.product_name, p.srp, 
                    u.full_name as seller_name, s.shop_name
                    FROM {$this->table} d
                    JOIN products p ON d.product_id = p.product_id
                    JOIN sellers s ON d.seller_id = s.seller_id
                    JOIN users u ON s.user_id = u.user_id
                    WHERE d.buyer_id = :buyer_id
                    ORDER BY d.created_at DESC");
                $stmt->execute([':buyer_id' => $buyer_id]);
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get deal by product and buyer
     */
    public function getDealByProductAndBuyer($product_id, $buyer_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} 
                WHERE product_id = :product_id AND buyer_id = :buyer_id 
                ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([':product_id' => $product_id, ':buyer_id' => $buyer_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Seller confirms deal completed
     */
    public function sellerConfirmDeal($deal_id, $seller_id) {
        try {
            $deal = $this->getById($deal_id);
            if (!$deal || $deal['seller_id'] != $seller_id) {
                return ['success' => false, 'message' => 'Unauthorized action.'];
            }

            $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET confirmed_by_seller = TRUE 
                WHERE deal_id = :deal_id");
            $stmt->execute([':deal_id' => $deal_id]);

            // Check if both confirmed
            $this->checkDealCompletion($deal_id);

            return ['success' => true, 'message' => 'Deal confirmed from your side!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error confirming deal: ' . $e->getMessage()];
        }
    }

    /**
     * Buyer confirms deal completed
     */
    public function buyerConfirmDeal($deal_id, $buyer_id) {
        try {
            $deal = $this->getById($deal_id);
            if (!$deal || $deal['buyer_id'] != $buyer_id) {
                return ['success' => false, 'message' => 'Unauthorized action.'];
            }

            $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET confirmed_by_buyer = TRUE 
                WHERE deal_id = :deal_id");
            $stmt->execute([':deal_id' => $deal_id]);

            // Check if both confirmed
            $this->checkDealCompletion($deal_id);

            return ['success' => true, 'message' => 'Deal confirmed from your side!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error confirming deal: ' . $e->getMessage()];
        }
    }

    /**
     * Check if both parties confirmed and mark as completed
     */
    private function checkDealCompletion($deal_id) {
        try {
            $deal = $this->getById($deal_id);
            if ($deal && $deal['confirmed_by_seller'] && $deal['confirmed_by_buyer']) {
                $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                    SET status = 'completed', completed_at = NOW() 
                    WHERE deal_id = :deal_id");
                $stmt->execute([':deal_id' => $deal_id]);
            }
        } catch (PDOException $e) {
            // Silently fail
        }
    }

    /**
     * Cancel deal
     */
    public function cancel($deal_id, $user_id, $user_type) {
        try {
            $deal = $this->getById($deal_id);
            if (!$deal) {
                return ['success' => false, 'message' => 'Deal not found.'];
            }

            // Verify user is part of this deal
            if ($user_type === 'seller' && $deal['seller_id'] != $user_id) {
                return ['success' => false, 'message' => 'Unauthorized action.'];
            }
            if ($user_type === 'buyer' && $deal['buyer_id'] != $user_id) {
                return ['success' => false, 'message' => 'Unauthorized action.'];
            }

            $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET status = 'cancelled' 
                WHERE deal_id = :deal_id");
            $stmt->execute([':deal_id' => $deal_id]);

            return ['success' => true, 'message' => 'Deal cancelled.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error cancelling deal: ' . $e->getMessage()];
        }
    }

    /**
     * Get deal count for seller
     */
    public function getSellerDealCount($seller_id, $status = 'ongoing') {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                WHERE seller_id = :seller_id AND status = :status");
            $stmt->execute([':seller_id' => $seller_id, ':status' => $status]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>
