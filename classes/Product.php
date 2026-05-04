<?php
/**
 * Product Management Class
 * Handles CRUD operations for products
 */

class Product {
    private $pdo;
    private $table = 'products';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new product
     */
    public function create($seller_id, $product_name, $product_description, $srp, $quantity, $image_path = null) {
        // Validate inputs
        if (empty($product_name) || empty($srp) || $srp <= 0) {
            return ['success' => false, 'message' => 'Invalid product data.'];
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} 
                (seller_id, product_name, product_description, srp, quantity, image_path, status) 
                VALUES (:seller_id, :product_name, :product_description, :srp, :quantity, :image_path, 'available')");
            
            $stmt->execute([
                ':seller_id' => $seller_id,
                ':product_name' => $product_name,
                ':product_description' => $product_description,
                ':srp' => $srp,
                ':quantity' => $quantity,
                ':image_path' => $image_path
            ]);

            $product_id = $this->pdo->lastInsertId();

            // Update seller's total_products count
            $this->updateSellerProductCount($seller_id);

            return ['success' => true, 'message' => 'Product created successfully!', 'product_id' => $product_id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error creating product: ' . $e->getMessage()];
        }
    }

    /**
     * Get product by ID
     */
    public function getById($product_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE product_id = :product_id");
            $stmt->execute([':product_id' => $product_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get all products by seller
     */
    public function getBySellerID($seller_id, $status = null) {
        try {
            if ($status) {
                $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} 
                    WHERE seller_id = :seller_id AND status = :status 
                    ORDER BY created_at DESC");
                $stmt->execute([':seller_id' => $seller_id, ':status' => $status]);
            } else {
                $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} 
                    WHERE seller_id = :seller_id 
                    ORDER BY created_at DESC");
                $stmt->execute([':seller_id' => $seller_id]);
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get available products (for marketplace)
     */
    public function getAvailableProducts($limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, s.shop_name, u.full_name 
                FROM {$this->table} p
                JOIN sellers s ON p.seller_id = s.seller_id
                JOIN users u ON s.user_id = u.user_id
                WHERE p.status = 'available' AND p.quantity > 0
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Update product
     */
    public function update($product_id, $product_name, $product_description, $srp, $quantity, $image_path = null) {
        try {
            if ($image_path) {
                $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                    SET product_name = :product_name, 
                        product_description = :product_description, 
                        srp = :srp, 
                        quantity = :quantity, 
                        image_path = :image_path,
                        updated_at = NOW()
                    WHERE product_id = :product_id");
                
                $stmt->execute([
                    ':product_name' => $product_name,
                    ':product_description' => $product_description,
                    ':srp' => $srp,
                    ':quantity' => $quantity,
                    ':image_path' => $image_path,
                    ':product_id' => $product_id
                ]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                    SET product_name = :product_name, 
                        product_description = :product_description, 
                        srp = :srp, 
                        quantity = :quantity,
                        updated_at = NOW()
                    WHERE product_id = :product_id");
                
                $stmt->execute([
                    ':product_name' => $product_name,
                    ':product_description' => $product_description,
                    ':srp' => $srp,
                    ':quantity' => $quantity,
                    ':product_id' => $product_id
                ]);
            }

            return ['success' => true, 'message' => 'Product updated successfully!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating product: ' . $e->getMessage()];
        }
    }

    /**
     * Delete product
     */
    public function delete($product_id, $seller_id) {
        try {
            // First get the product to verify ownership
            $product = $this->getById($product_id);
            if (!$product || $product['seller_id'] != $seller_id) {
                return ['success' => false, 'message' => 'Unauthorized: You cannot delete this product.'];
            }

            // Delete product image if exists
            if (!empty($product['image_path'])) {
                $image_full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $product['image_path'];
                if (file_exists($image_full_path)) {
                    unlink($image_full_path);
                }
            }

            // Delete product from database
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE product_id = :product_id");
            $stmt->execute([':product_id' => $product_id]);

            // Update seller's product count
            $this->updateSellerProductCount($seller_id);

            return ['success' => true, 'message' => 'Product deleted successfully!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()];
        }
    }

    /**
     * Toggle product status (available/unavailable)
     */
    public function toggleStatus($product_id, $seller_id) {
        try {
            // Verify ownership
            $product = $this->getById($product_id);
            if (!$product || $product['seller_id'] != $seller_id) {
                return ['success' => false, 'message' => 'Unauthorized action.'];
            }

            // Toggle status
            $new_status = $product['status'] === 'available' ? 'unavailable' : 'available';
            
            $stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET status = :status, updated_at = NOW() 
                WHERE product_id = :product_id");
            
            $stmt->execute([
                ':status' => $new_status,
                ':product_id' => $product_id
            ]);

            return ['success' => true, 'message' => 'Product status updated!', 'new_status' => $new_status];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()];
        }
    }

    /**
     * Get product count for seller
     */
    public function getSellerProductCount($seller_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE seller_id = :seller_id");
            $stmt->execute([':seller_id' => $seller_id]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Update seller's product count
     */
    private function updateSellerProductCount($seller_id) {
        try {
            $count = $this->getSellerProductCount($seller_id);
            $stmt = $this->pdo->prepare("UPDATE sellers SET total_products = :count WHERE seller_id = :seller_id");
            $stmt->execute([':count' => $count, ':seller_id' => $seller_id]);
        } catch (PDOException $e) {
            // Silently fail
        }
    }

    /**
     * Search products
     */
    public function search($keyword, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("SELECT p.*, s.shop_name, u.full_name 
                FROM {$this->table} p
                JOIN sellers s ON p.seller_id = s.seller_id
                JOIN users u ON s.user_id = u.user_id
                WHERE p.status = 'available' 
                AND p.quantity > 0
                AND (p.product_name LIKE :keyword OR p.product_description LIKE :keyword)
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset");
            
            $search_term = '%' . $keyword . '%';
            $stmt->bindValue(':keyword', $search_term);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get available product count for seller
     */
    public function getAvailableProductCount($seller_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                WHERE seller_id = :seller_id AND status = 'available'");
            $stmt->execute([':seller_id' => $seller_id]);
            $result = $stmt->fetch();
            return $result['count'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>
