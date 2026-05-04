<?php

class Market {
    private $pdo;
    private $table = 'products';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get product by ID with seller info
     */
    public function getProductById($product_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, 
                COALESCE(s.seller_id, p.seller_id) as seller_id, 
                COALESCE(s.shop_name, 'Unknown Seller') as shop_name, 
                COALESCE(s.rating, 0) as rating,
                COALESCE(u.full_name, 'Unknown Seller') as seller_name
                FROM {$this->table} p
                LEFT JOIN sellers s ON p.seller_id = s.seller_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE p.product_id = :product_id");
            
            $stmt->execute([':product_id' => $product_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("getProductById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all available products with seller info (for marketplace)
     */
    public function getAvailableProducts($limit = 12, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, 
                COALESCE(s.shop_name, 'Unknown Seller') as shop_name, 
                COALESCE(s.rating, 0) as rating
                FROM {$this->table} p
                LEFT JOIN sellers s ON p.seller_id = s.seller_id
                WHERE p.status = 'available' AND p.quantity > 0
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAvailableProducts error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search products by name or description (available only)
     */
    public function searchProducts($keyword, $limit = 12, $offset = 0) {
        try {
            $search_term = '%' . $keyword . '%';
            // Strict available first
            $stmt = $this->pdo->prepare("SELECT p.*, 
                COALESCE(s.shop_name, 'Unknown Seller') as shop_name, 
                COALESCE(s.rating, 0) as rating
                FROM {$this->table} p
                LEFT JOIN sellers s ON p.seller_id = s.seller_id
                WHERE (p.product_name LIKE :name OR p.product_description LIKE :desc)
                AND p.status = 'available' AND p.quantity > 0
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':name', $search_term);
            $stmt->bindValue(':desc', $search_term);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            if (empty($results)) {
                $stmt = $this->pdo->prepare("SELECT p.*, 
                    COALESCE(s.shop_name, 'Unknown Seller') as shop_name, 
                    COALESCE(s.rating, 0) as rating
                    FROM {$this->table} p
                    LEFT JOIN sellers s ON p.seller_id = s.seller_id
                    WHERE (p.product_name LIKE :name OR p.product_description LIKE :desc)
                    ORDER BY p.created_at DESC
                    LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':name', $search_term);
                $stmt->bindValue(':desc', $search_term);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll();
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Market search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get featured products (most recent)
     */
    public function getFeaturedProducts($limit = 6) {
        return $this->getAvailableProducts($limit, 0);
    }

    /**
     * Get product count (for pagination)
     */
    public function getProductCount($keyword = null) {
        try {
            if ($keyword) {
                $search_term = '%' . $keyword . '%';
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table}
                    WHERE status = 'available' AND quantity > 0
                    AND (product_name LIKE :name OR product_description LIKE :desc)");
                $stmt->execute([':name' => $search_term, ':desc' => $search_term]);
                $result = $stmt->fetch();
                $count = $result['count'] ?? 0;
                
                if ($count === 0) {
                    $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table}
                        WHERE (product_name LIKE :name OR product_description LIKE :desc)");
                    $stmt->execute([':name' => $search_term, ':desc' => $search_term]);
                    $result = $stmt->fetch();
                    $count = $result['count'] ?? 0;
                }
                return $count;
            } else {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table}
                    WHERE status = 'available' AND quantity > 0");
                $stmt->execute();
                $result = $stmt->fetch();
                return $result['count'] ?? 0;
            }
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get products by seller
     */
    public function getProductsBySeller($seller_id, $limit = 50) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, 
                COALESCE(s.shop_name, 'Unknown Seller') as shop_name
                FROM {$this->table} p
                LEFT JOIN sellers s ON p.seller_id = s.seller_id
                WHERE p.seller_id = :seller_id AND p.status = 'available' AND p.quantity > 0
                ORDER BY p.created_at DESC
                LIMIT :limit");
            
            $stmt->bindValue(':seller_id', $seller_id);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Check if product is available
     */
    public function isProductAvailable($product_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT status, quantity FROM {$this->table} WHERE product_id = :product_id");
            $stmt->execute([':product_id' => $product_id]);
            $product = $stmt->fetch();
            
            return $product && $product['status'] === 'available' && $product['quantity'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get related products (same seller, different product)
     */
    public function getRelatedProducts($seller_id, $product_id, $limit = 3) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, 
                COALESCE(s.shop_name, 'Unknown Seller') as shop_name,
                COALESCE(u.full_name, 'Unknown Seller') as seller_name
                FROM {$this->table} p
                LEFT JOIN sellers s ON p.seller_id = s.seller_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE p.seller_id = :seller_id 
                AND p.product_id != :product_id
                AND p.status = 'available' 
                AND p.quantity > 0
                ORDER BY p.created_at DESC
                LIMIT :limit");
            
            $stmt->bindValue(':seller_id', $seller_id);
            $stmt->bindValue(':product_id', $product_id);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get trending products (by deal count)
     */
    public function getTrendingProducts($limit = 6) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, 
                COALESCE(s.shop_name, 'Unknown Seller') as shop_name, 
                COALESCE(s.rating, 0) as rating, 
                COUNT(d.deal_id) as deal_count
                FROM {$this->table} p
                LEFT JOIN deals d ON p.product_id = d.product_id
                LEFT JOIN sellers s ON p.seller_id = s.seller_id
                WHERE p.status = 'available' AND p.quantity > 0
                GROUP BY p.product_id
                ORDER BY deal_count DESC, p.created_at DESC
                LIMIT :limit");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Update product quantity after deal (for future use)
     */
    public function updateProductQuantity($product_id, $quantity) {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET quantity = quantity - :qty WHERE product_id = :product_id");
            $stmt->execute([':qty' => $quantity, ':product_id' => $product_id]);
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>

