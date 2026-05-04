<?php
/**
 * Rating Management Class
 * Handles seller ratings and reviews after deal completion (Stage 5)
 */

class Rating {
    private $pdo;
    private $table = 'ratings';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new rating for a deal
     * Only one rating per deal (deal_id is UNIQUE)
     */
    public function create($deal_id, $buyer_id, $seller_id, $stars, $review_text = '') {
        try {
            // Verify deal exists and is completed
            $dealStmt = $this->pdo->prepare("SELECT * FROM deals WHERE deal_id = :deal_id 
                AND status = 'completed' 
                AND confirmed_by_buyer = TRUE 
                AND confirmed_by_seller = TRUE");
            $dealStmt->execute([':deal_id' => $deal_id]);
            $deal = $dealStmt->fetch();
            
            if (!$deal) {
                return ['success' => false, 'message' => 'Deal not found or not completed.'];
            }

            // Verify buyer is the one rating
            if ($deal['buyer_id'] != $buyer_id) {
                return ['success' => false, 'message' => 'Only the buyer can rate this deal.'];
            }

            // Verify buyer is not rating themselves
            if ($deal['seller_id'] == $buyer_id) {
                return ['success' => false, 'message' => 'Cannot rate yourself.'];
            }

            // Check if rating already exists for this deal
            if ($this->hasRating($deal_id)) {
                return ['success' => false, 'message' => 'Rating already exists for this deal.'];
            }

            // Validate stars (1-5)
            if ($stars < 1 || $stars > 5) {
                return ['success' => false, 'message' => 'Stars must be between 1 and 5.'];
            }

            // Sanitize and truncate review text
            $review_text = trim($review_text);
            if (strlen($review_text) > 500) {
                $review_text = substr($review_text, 0, 500);
            }
            if (strlen($review_text) == 0) {
                $review_text = null; // Allow null for no review text
            }

            // Insert rating
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} 
                (deal_id, buyer_id, seller_id, stars, review_text, created_at) 
                VALUES (:deal_id, :buyer_id, :seller_id, :stars, :review_text, NOW())");
            
            $stmt->execute([
                ':deal_id' => $deal_id,
                ':buyer_id' => $buyer_id,
                ':seller_id' => $seller_id,
                ':stars' => $stars,
                ':review_text' => $review_text
            ]);

            return [
                'success' => true, 
                'message' => 'Rating submitted successfully!',
                'rating_id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error creating rating: ' . $e->getMessage()];
        }
    }

    /**
     * Get rating by deal ID
     */
    public function getRatingByDeal($deal_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT r.*, u.full_name as buyer_name 
                FROM {$this->table} r
                JOIN buyers b ON r.buyer_id = b.buyer_id
                JOIN users u ON b.user_id = u.user_id
                WHERE r.deal_id = :deal_id");
            $stmt->execute([':deal_id' => $deal_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Check if rating exists for deal
     */
    public function hasRating($deal_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                WHERE deal_id = :deal_id");
            $stmt->execute([':deal_id' => $deal_id]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all ratings for a seller
     */
    public function getSellerRatings($seller_id, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT r.*, p.product_name, u.full_name as buyer_name,
                d.created_at as deal_date
                FROM {$this->table} r
                JOIN deals d ON r.deal_id = d.deal_id
                JOIN products p ON d.product_id = p.product_id
                JOIN buyers b ON r.buyer_id = b.buyer_id
                JOIN users u ON b.user_id = u.user_id
                WHERE r.seller_id = :seller_id
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset");
            
            $stmt->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get average rating for a seller
     * Returns array with 'average' and 'count'
     */
    public function getAverageRating($seller_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT 
                AVG(stars) as average,
                COUNT(*) as count
                FROM {$this->table}
                WHERE seller_id = :seller_id");
            $stmt->execute([':seller_id' => $seller_id]);
            $result = $stmt->fetch();
            
            return [
                'average' => $result['average'] ? round($result['average'], 1) : 0,
                'count' => $result['count'] ? $result['count'] : 0
            ];
        } catch (PDOException $e) {
            return ['average' => 0, 'count' => 0];
        }
    }

    /**
     * Get rating count for seller
     */
    public function getSellerRatingCount($seller_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                WHERE seller_id = :seller_id");
            $stmt->execute([':seller_id' => $seller_id]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Generate star display HTML (★ character)
     * e.g., "★★★★☆ 4.0 (15 reviews)"
     */
    public static function renderStars($average, $count = 0) {
        if ($average == 0 || $count == 0) {
            return '<span class="text-gray-400">No ratings yet</span>';
        }

        $full_stars = floor($average);
        $has_half = ($average - $full_stars) >= 0.5;
        $empty_stars = 5 - $full_stars - ($has_half ? 1 : 0);

        $stars = '';
        for ($i = 0; $i < $full_stars; $i++) {
            $stars .= '★';
        }
        if ($has_half) {
            $stars .= '½';
        }
        for ($i = 0; $i < $empty_stars; $i++) {
            $stars .= '☆';
        }

        return '<span class="text-yellow-500">' . htmlspecialchars($stars) . '</span> 
                <span class="text-gray-700 font-semibold">' . htmlspecialchars($average) . '</span>
                <span class="text-gray-500 text-sm">(' . htmlspecialchars($count) . ' reviews)</span>';
    }

    /**
     * Get ratings for a product (from all sellers)
     */
    public function getProductRatings($product_id, $limit = 5) {
        try {
            $stmt = $this->pdo->prepare("SELECT r.*, u.full_name as buyer_name,
                p.product_name
                FROM {$this->table} r
                JOIN deals d ON r.deal_id = d.deal_id
                JOIN products p ON d.product_id = p.product_id
                JOIN buyers b ON r.buyer_id = b.buyer_id
                JOIN users u ON b.user_id = u.user_id
                WHERE d.product_id = :product_id
                ORDER BY r.created_at DESC
                LIMIT :limit");
            
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
