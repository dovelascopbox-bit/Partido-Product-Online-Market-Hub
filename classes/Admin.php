<?php
/**
 * Admin Management Class
 * Handles administrative operations for platform management (Stage 6)
 */

class Admin {
    private $pdo;
    private $table = 'admin_actions';
    private $flagTable = 'flag_reports';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get platform statistics for dashboard
     */
    public function getPlatformStats() {
        try {
            $stats = [];

            // Total users by role
            $userStmt = $this->pdo->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
            $userStmt->execute();
            $userStats = $userStmt->fetchAll(PDO::FETCH_KEY_PAIR);

            $stats['total_users'] = array_sum($userStats);
            $stats['total_sellers'] = $userStats['seller'] ?? 0;
            $stats['total_buyers'] = $userStats['buyer'] ?? 0;
            $stats['total_admins'] = $userStats['admin'] ?? 0;

            // Product statistics
            $productStmt = $this->pdo->prepare("SELECT
                COUNT(*) as total_products,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as active_products
                FROM products");
            $productStmt->execute();
            $productStats = $productStmt->fetch();
            $stats['total_products'] = $productStats['total_products'];
            $stats['active_products'] = $productStats['active_products'];

            // Deal statistics
            $dealStmt = $this->pdo->prepare("SELECT
                COUNT(*) as total_deals,
                SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing_deals,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_deals,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_deals
                FROM deals");
            $dealStmt->execute();
            $dealStats = $dealStmt->fetch();
            $stats['total_deals'] = $dealStats['total_deals'];
            $stats['ongoing_deals'] = $dealStats['ongoing_deals'];
            $stats['completed_deals'] = $dealStats['completed_deals'];
            $stats['cancelled_deals'] = $dealStats['cancelled_deals'];

            // Rating statistics
            $ratingStmt = $this->pdo->prepare("SELECT
                COUNT(*) as total_ratings,
                AVG(stars) as avg_rating
                FROM ratings");
            $ratingStmt->execute();
            $ratingStats = $ratingStmt->fetch();
            $stats['total_ratings'] = $ratingStats['total_ratings'];
            $stats['avg_rating'] = $ratingStats['avg_rating'] ? round($ratingStats['avg_rating'], 1) : 0;

            // Flag reports
            $flagStmt = $this->pdo->prepare("SELECT
                COUNT(*) as total_flags,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_flags
                FROM flag_reports");
            $flagStmt->execute();
            $flagStats = $flagStmt->fetch();
            $stats['total_flags'] = $flagStats['total_flags'];
            $stats['pending_flags'] = $flagStats['pending_flags'];

            return $stats;
        } catch (PDOException $e) {
            return [
                'total_users' => 0,
                'total_sellers' => 0,
                'total_buyers' => 0,
                'total_admins' => 0,
                'total_products' => 0,
                'active_products' => 0,
                'total_deals' => 0,
                'ongoing_deals' => 0,
                'completed_deals' => 0,
                'cancelled_deals' => 0,
                'total_ratings' => 0,
                'avg_rating' => 0,
                'total_flags' => 0,
                'pending_flags' => 0
            ];
        }
    }

    /**
     * Get all users with pagination and filtering
     */
    public function getUsers($role = null, $status = null, $search = '', $limit = 20, $offset = 0) {
        try {
            $where = [];
            $params = [];

            if ($role) {
                $where[] = "u.role = :role";
                $params[':role'] = $role;
            }

            if ($status) {
                $where[] = "u.status = :status";
                $params[':status'] = $status;
            }

            if ($search) {
                $where[] = "(u.username LIKE :search OR u.email LIKE :search OR u.full_name LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $this->pdo->prepare("SELECT
                u.user_id, u.username, u.email, u.full_name, u.role, u.status,
                u.created_at, u.last_login,
                CASE
                    WHEN u.role = 'seller' THEN s.total_products
                    WHEN u.role = 'buyer' THEN b.total_purchases
                    ELSE NULL
                END as activity_count
                FROM users u
                LEFT JOIN sellers s ON u.user_id = s.user_id AND u.role = 'seller'
                LEFT JOIN buyers b ON u.user_id = b.user_id AND u.role = 'buyer'
                $whereClause
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get user count for pagination
     */
    public function getUserCount($role = null, $status = null, $search = '') {
        try {
            $where = [];
            $params = [];

            if ($role) {
                $where[] = "role = :role";
                $params[':role'] = $role;
            }

            if ($status) {
                $where[] = "status = :status";
                $params[':status'] = $status;
            }

            if ($search) {
                $where[] = "(username LIKE :search OR email LIKE :search OR full_name LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users $whereClause");

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Suspend or reactivate user
     */
    public function toggleUserStatus($user_id, $admin_id) {
        try {
            // Get current status
            $stmt = $this->pdo->prepare("SELECT status FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }

            $newStatus = $user['status'] === 'active' ? 'suspended' : 'active';
            $action = $newStatus === 'suspended' ? 'suspended' : 'reactivated';

            // Update user status
            $updateStmt = $this->pdo->prepare("UPDATE users SET status = :status WHERE user_id = :user_id");
            $updateStmt->execute([':status' => $newStatus, ':user_id' => $user_id]);

            // Log admin action
            $this->logAction($admin_id, 'user_status_change', "User $user_id $action", $user_id);

            return [
                'success' => true,
                'message' => "User $action successfully",
                'new_status' => $newStatus
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating user status: ' . $e->getMessage()];
        }
    }

    /**
     * Get all products with pagination
     */
    public function getProducts($status = null, $search = '', $limit = 20, $offset = 0) {
        try {
            $where = [];
            $params = [];

            if ($status) {
                $where[] = "p.status = :status";
                $params[':status'] = $status;
            }

            if ($search) {
                $where[] = "(p.product_name LIKE :search OR u.full_name LIKE :search OR s.shop_name LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

            $stmt = $this->pdo->prepare("SELECT
                p.product_id, p.product_name, p.srp, p.quantity, p.status, p.created_at,
                u.full_name as seller_name, s.shop_name, u.user_id as seller_user_id
                FROM products p
                JOIN sellers s ON p.seller_id = s.seller_id
                JOIN users u ON s.user_id = u.user_id
                $whereClause
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Remove product (admin action)
     */
    public function removeProduct($product_id, $admin_id, $reason = '') {
        try {
            // Get product info for logging
            $stmt = $this->pdo->prepare("SELECT product_name FROM products WHERE product_id = :product_id");
            $stmt->execute([':product_id' => $product_id]);
            $product = $stmt->fetch();

            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }

            // Remove product (this will cascade to deals)
            $deleteStmt = $this->pdo->prepare("DELETE FROM products WHERE product_id = :product_id");
            $deleteStmt->execute([':product_id' => $product_id]);

            // Log admin action
            $this->logAction($admin_id, 'product_removal', "Removed product: {$product['product_name']}" . ($reason ? " - $reason" : ""), $product_id);

            return ['success' => true, 'message' => 'Product removed successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error removing product: ' . $e->getMessage()];
        }
    }

    /**
     * Get all deals with pagination
     */
    public function getDeals($status = null, $limit = 20, $offset = 0) {
        try {
            $where = $status ? "WHERE d.status = :status" : "";

            $stmt = $this->pdo->prepare("SELECT
                d.deal_id, d.status, d.created_at, d.completed_at,
                p.product_name, p.srp,
                bs.full_name as buyer_name, ss.full_name as seller_name,
                bs.user_id as buyer_user_id, ss.user_id as seller_user_id
                FROM deals d
                JOIN products p ON d.product_id = p.product_id
                JOIN buyers b ON d.buyer_id = b.buyer_id
                JOIN sellers s ON d.seller_id = s.seller_id
                JOIN users bs ON b.user_id = bs.user_id
                JOIN users ss ON s.user_id = ss.user_id
                $where
                ORDER BY d.created_at DESC
                LIMIT :limit OFFSET :offset");

            if ($status) {
                $stmt->bindValue(':status', $status);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get all ratings with pagination
     */
    public function getRatings($flagged_only = false, $limit = 20, $offset = 0) {
        try {
            $where = $flagged_only ? "WHERE r.flagged = 1" : "";

            $stmt = $this->pdo->prepare("SELECT
                r.rating_id, r.stars, r.review_text, r.created_at, r.flagged,
                p.product_name,
                bs.full_name as buyer_name, ss.full_name as seller_name
                FROM ratings r
                JOIN deals d ON r.deal_id = d.deal_id
                JOIN products p ON d.product_id = p.product_id
                JOIN buyers b ON r.buyer_id = b.buyer_id
                JOIN sellers s ON r.seller_id = s.seller_id
                JOIN users bs ON b.user_id = bs.user_id
                JOIN users ss ON s.user_id = ss.user_id
                $where
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Remove rating (admin action)
     */
    public function removeRating($rating_id, $admin_id, $reason = '') {
        try {
            // Get rating info for logging
            $stmt = $this->pdo->prepare("SELECT r.*, p.product_name, u.full_name as seller_name
                FROM ratings r
                JOIN deals d ON r.deal_id = d.deal_id
                JOIN products p ON d.product_id = p.product_id
                JOIN sellers s ON r.seller_id = s.seller_id
                JOIN users u ON s.user_id = u.user_id
                WHERE r.rating_id = :rating_id");
            $stmt->execute([':rating_id' => $rating_id]);
            $rating = $stmt->fetch();

            if (!$rating) {
                return ['success' => false, 'message' => 'Rating not found'];
            }

            // Remove rating
            $deleteStmt = $this->pdo->prepare("DELETE FROM ratings WHERE rating_id = :rating_id");
            $deleteStmt->execute([':rating_id' => $rating_id]);

            // Log admin action
            $this->logAction($admin_id, 'rating_removal', "Removed rating for {$rating['product_name']} by {$rating['seller_name']}" . ($reason ? " - $reason" : ""), $rating_id);

            return ['success' => true, 'message' => 'Rating removed successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error removing rating: ' . $e->getMessage()];
        }
    }

    /**
     * Flag rating for review
     */
    public function flagRating($rating_id, $admin_id) {
        try {
            $stmt = $this->pdo->prepare("UPDATE ratings SET flagged = 1 WHERE rating_id = :rating_id");
            $stmt->execute([':rating_id' => $rating_id]);

            $this->logAction($admin_id, 'rating_flagged', "Flagged rating $rating_id for review", $rating_id);

            return ['success' => true, 'message' => 'Rating flagged for review'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error flagging rating: ' . $e->getMessage()];
        }
    }

    /**
     * Get flagged items for review
     */
    public function getFlaggedItems($limit = 20, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT
                fr.report_id, fr.item_type, fr.item_id, fr.reason, fr.notes, fr.status, fr.created_at,
                u.full_name as reporter_name,
                CASE
                    WHEN fr.item_type = 'product' THEN p.product_name
                    WHEN fr.item_type = 'seller' THEN s.shop_name
                    WHEN fr.item_type = 'rating' THEN 'Rating'
                END as item_name
                FROM flag_reports fr
                JOIN users u ON fr.reporter_id = u.user_id
                LEFT JOIN products p ON fr.item_type = 'product' AND fr.item_id = p.product_id
                LEFT JOIN sellers s ON fr.item_type = 'seller' AND fr.item_id = s.seller_id
                ORDER BY fr.created_at DESC
                LIMIT :limit OFFSET :offset");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Resolve flag report
     */
    public function resolveFlag($report_id, $admin_id, $action, $notes = '') {
        try {
            // Get report details
            $stmt = $this->pdo->prepare("SELECT * FROM flag_reports WHERE report_id = :report_id");
            $stmt->execute([':report_id' => $report_id]);
            $report = $stmt->fetch();

            if (!$report) {
                return ['success' => false, 'message' => 'Report not found'];
            }

            // Update report status
            $updateStmt = $this->pdo->prepare("UPDATE flag_reports SET
                status = :status,
                resolved_at = NOW(),
                admin_notes = :notes
                WHERE report_id = :report_id");

            $status = $action === 'dismiss' ? 'dismissed' : 'resolved';
            $updateStmt->execute([
                ':status' => $status,
                ':notes' => $notes,
                ':report_id' => $report_id
            ]);

            // Take action if needed
            if ($action === 'remove') {
                if ($report['item_type'] === 'product') {
                    $this->removeProduct($report['item_id'], $admin_id, 'Flagged content');
                } elseif ($report['item_type'] === 'rating') {
                    $this->removeRating($report['item_id'], $admin_id, 'Flagged content');
                }
            }

            // Log admin action
            $this->logAction($admin_id, 'flag_resolved', "Resolved flag report $report_id with action: $action", $report_id);

            return ['success' => true, 'message' => 'Flag report resolved'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error resolving flag: ' . $e->getMessage()];
        }
    }

    /**
     * Log admin action
     */
    public function logAction($admin_id, $action_type, $description, $target_id = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO admin_actions
                (admin_id, action_type, description, target_id, created_at)
                VALUES (:admin_id, :action_type, :description, :target_id, NOW())");

            $stmt->execute([
                ':admin_id' => $admin_id,
                ':action_type' => $action_type,
                ':description' => $description,
                ':target_id' => $target_id
            ]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get admin action log
     */
    public function getActionLog($limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT
                aa.*, u.full_name as admin_name
                FROM admin_actions aa
                JOIN users u ON aa.admin_id = u.user_id
                ORDER BY aa.created_at DESC
                LIMIT :limit OFFSET :offset");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get analytics data for charts
     */
    public function getAnalyticsData() {
        try {
            $data = [];

            // User registrations by month (last 12 months)
            $userRegStmt = $this->pdo->prepare("SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month");
            $userRegStmt->execute();
            $data['user_registrations'] = $userRegStmt->fetchAll(PDO::FETCH_ASSOC);

            // Deals by month (last 12 months)
            $dealsStmt = $this->pdo->prepare("SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as total_deals,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_deals
                FROM deals
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month");
            $dealsStmt->execute();
            $data['deals_by_month'] = $dealsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Top 5 rated sellers
            $topSellersStmt = $this->pdo->prepare("SELECT
                u.full_name, s.shop_name,
                AVG(r.stars) as avg_rating,
                COUNT(r.rating_id) as rating_count
                FROM ratings r
                JOIN sellers s ON r.seller_id = s.seller_id
                JOIN users u ON s.user_id = u.user_id
                GROUP BY r.seller_id
                ORDER BY avg_rating DESC, rating_count DESC
                LIMIT 5");
            $topSellersStmt->execute();
            $data['top_sellers'] = $topSellersStmt->fetchAll(PDO::FETCH_ASSOC);

            // Deal completion rate
            $completionStmt = $this->pdo->prepare("SELECT
                COUNT(*) as total_deals,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_deals
                FROM deals");
            $completionStmt->execute();
            $completionData = $completionStmt->fetch();
            $data['completion_rate'] = $completionData['total_deals'] > 0
                ? round(($completionData['completed_deals'] / $completionData['total_deals']) * 100, 1)
                : 0;

            return $data;
        } catch (PDOException $e) {
            return [
                'user_registrations' => [],
                'deals_by_month' => [],
                'top_sellers' => [],
                'completion_rate' => 0
            ];
        }
    }
}
?>
