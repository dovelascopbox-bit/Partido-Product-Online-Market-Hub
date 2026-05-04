# Stage 2 API Reference

## Product Class

**Location**: `/classes/Product.php`

### Constructor
```php
public function __construct($pdo)
```
- **Parameters**: PDO database connection object
- **Usage**: `$product = new Product($pdo);`

### Methods

#### `create($seller_id, $product_name, $product_description, $srp, $quantity, $image_path)`
Create a new product listing.

**Parameters:**
- `$seller_id` (int) - The seller's ID
- `$product_name` (string) - Product name
- `$product_description` (string) - Detailed description
- `$srp` (float) - Suggested Retail Price
- `$quantity` (int) - Available quantity
- `$image_path` (string) - Path to product image

**Returns:** 
- `int` - Product ID if successful
- `false` - If failed

**Example:**
```php
$product_id = $product_obj->create(
    $seller_id = 1,
    $product_name = "Fresh Mango",
    $product_description = "High quality mangoes",
    $srp = 150,
    $quantity = 20,
    $image_path = "seller_1_123456.jpg"
);
```

---

#### `getById($product_id)`
Retrieve a single product by ID.

**Parameters:**
- `$product_id` (int) - Product ID

**Returns:** 
- `array` - Product data including seller info
- `null` - If product not found

**Example:**
```php
$product = $product_obj->getById(5);
// Returns: [
//   'product_id' => 5,
//   'seller_id' => 1,
//   'product_name' => 'Fresh Mango',
//   'srp' => 150,
//   'image_path' => '...',
//   'shop_name' => 'Juan\'s Farm',
//   ...
// ]
```

---

#### `getBySellerID($seller_id, $limit = 50, $offset = 0)`
Get all products by a specific seller.

**Parameters:**
- `$seller_id` (int) - Seller ID
- `$limit` (int) - Results per page (default: 50)
- `$offset` (int) - Pagination offset (default: 0)

**Returns:** 
- `array` - Array of product arrays

**Example:**
```php
$seller_products = $product_obj->getBySellerID(1);
```

---

#### `getAvailableProducts($limit = 50, $offset = 0)`
Get all publicly available products (status = 'available', quantity > 0).

**Parameters:**
- `$limit` (int) - Results per page (default: 50)
- `$offset` (int) - Pagination offset (default: 0)

**Returns:** 
- `array` - Array of available product arrays including shop_name

**Example:**
```php
$marketplace_products = $product_obj->getAvailableProducts();
// Each product includes 'shop_name' and 'full_name' (seller name)
```

---

#### `update($product_id, $seller_id, $data)`
Update product information.

**Parameters:**
- `$product_id` (int) - Product ID to update
- `$seller_id` (int) - Seller ID (for ownership verification)
- `$data` (array) - Associative array of fields to update
  ```php
  [
      'product_name' => 'New Name',
      'product_description' => 'New description',
      'srp' => 200,
      'quantity' => 15,
      'image_path' => 'new_image.jpg' // optional
  ]
  ```

**Returns:** 
- `bool` - true if successful, false if failed or unauthorized

**Example:**
```php
$success = $product_obj->update(5, 1, [
    'product_name' => 'Premium Mango',
    'srp' => 180
]);
```

---

#### `delete($product_id, $seller_id)`
Delete a product (removes from database and image file from disk).

**Parameters:**
- `$product_id` (int) - Product ID
- `$seller_id` (int) - Seller ID (for ownership verification)

**Returns:** 
- `bool` - true if deleted, false if not found or unauthorized

**Example:**
```php
$deleted = $product_obj->delete(5, 1);
```

---

#### `toggleStatus($product_id, $seller_id, $new_status = null)`
Toggle product availability status.

**Parameters:**
- `$product_id` (int) - Product ID
- `$seller_id` (int) - Seller ID (for ownership verification)
- `$new_status` (string|null) - 'available' or 'unavailable', or null to toggle

**Returns:** 
- `array` - Status change info: `['success' => true, 'status' => 'unavailable']`
- `array` - Error info: `['success' => false, 'message' => '...']`

**Example (AJAX):**
```php
$result = $product_obj->toggleStatus(5, 1);
// Returns: ['success' => true, 'status' => 'unavailable']
// Output as JSON for JavaScript
```

---

#### `search($keyword, $limit = 50, $offset = 0)`
Search products by name or description (available products only).

**Parameters:**
- `$keyword` (string) - Search term
- `$limit` (int) - Results per page (default: 50)
- `$offset` (int) - Pagination offset (default: 0)

**Returns:** 
- `array` - Array of matching product arrays

**Example:**
```php
$results = $product_obj->search('mango');
// Only returns products with status='available' and quantity > 0
```

---

#### `getSellerProductCount($seller_id)`
Get total product count for a seller.

**Parameters:**
- `$seller_id` (int) - Seller ID

**Returns:** 
- `int` - Total product count

**Example:**
```php
$total = $product_obj->getSellerProductCount(1); // Returns: 3
```

---

#### `getAvailableProductCount($seller_id)`
Get count of available products for a seller.

**Parameters:**
- `$seller_id` (int) - Seller ID

**Returns:** 
- `int` - Count of available products

**Example:**
```php
$available = $product_obj->getAvailableProductCount(1); // Returns: 2
```

---

---

## Deal Class

**Location**: `/classes/Deal.php`

### Constructor
```php
public function __construct($pdo)
```
- **Parameters**: PDO database connection object
- **Usage**: `$deal = new Deal($pdo);`

### Methods

#### `create($product_id, $buyer_id, $seller_id)`
Create a new deal (when buyer clicks "Deal" on a product).

**Parameters:**
- `$product_id` (int) - Product ID
- `$buyer_id` (int) - Buyer ID
- `$seller_id` (int) - Seller ID

**Returns:** 
- `int` - Deal ID if successful
- `false` - If failed or deal already exists

**Example:**
```php
$deal_id = $deal_obj->create(5, 2, 1);
```

---

#### `getById($deal_id)`
Get a single deal by ID.

**Parameters:**
- `$deal_id` (int) - Deal ID

**Returns:** 
- `array` - Deal data
- `null` - If not found

**Example:**
```php
$deal = $deal_obj->getById(10);
```

---

#### `getBySellerID($seller_id, $status = null)`
Get all deals for a seller (optionally filtered by status).

**Parameters:**
- `$seller_id` (int) - Seller ID
- `$status` (string|null) - Filter by status: 'ongoing', 'completed', 'cancelled', or null for all

**Returns:** 
- `array` - Array of deals including product_name, srp, buyer_name, shop_name

**Example:**
```php
// Get all ongoing deals
$ongoing = $deal_obj->getBySellerID(1, 'ongoing');

// Get all deals regardless of status
$all = $deal_obj->getBySellerID(1);

// Each deal includes:
// [
//   'deal_id' => 1,
//   'product_id' => 5,
//   'buyer_id' => 2,
//   'seller_id' => 1,
//   'product_name' => 'Fresh Mango',
//   'srp' => 150,
//   'buyer_name' => 'Maria',
//   'confirmed_by_seller' => false,
//   'confirmed_by_buyer' => false,
//   'status' => 'ongoing',
//   ...
// ]
```

---

#### `getByBuyerID($buyer_id, $status = null)`
Get all deals for a buyer (optionally filtered by status).

**Parameters:**
- `$buyer_id` (int) - Buyer ID
- `$status` (string|null) - Filter by status: 'ongoing', 'completed', 'cancelled', or null

**Returns:** 
- `array` - Array of deals including product and seller info

**Example:**
```php
$buyer_deals = $deal_obj->getByBuyerID(2, 'ongoing');
```

---

#### `getDealByProductAndBuyer($product_id, $buyer_id)`
Check if a deal already exists for a product and buyer.

**Parameters:**
- `$product_id` (int) - Product ID
- `$buyer_id` (int) - Buyer ID

**Returns:** 
- `array` - Deal data if exists
- `null` - If no deal exists

**Example:**
```php
$existing_deal = $deal_obj->getDealByProductAndBuyer(5, 2);
if ($existing_deal) {
    // Deal already initiated, prevent duplicate
}
```

---

#### `sellerConfirmDeal($deal_id, $seller_id)`
Seller confirms their part of the deal.

**Parameters:**
- `$deal_id` (int) - Deal ID
- `$seller_id` (int) - Seller ID (for verification)

**Returns:** 
- `bool` - true if confirmed, false if failed

**Note:** If both seller and buyer confirm, status automatically changes to 'completed'

**Example:**
```php
$confirmed = $deal_obj->sellerConfirmDeal(10, 1);
```

---

#### `buyerConfirmDeal($deal_id, $buyer_id)`
Buyer confirms their part of the deal.

**Parameters:**
- `$deal_id` (int) - Deal ID
- `$buyer_id` (int) - Buyer ID (for verification)

**Returns:** 
- `bool` - true if confirmed, false if failed

**Note:** If both parties confirm, status automatically changes to 'completed'

**Example:**
```php
$confirmed = $deal_obj->buyerConfirmDeal(10, 2);
```

---

#### `checkDealCompletion($deal_id)`
Check if both parties have confirmed and auto-complete if so.

**Parameters:**
- `$deal_id` (int) - Deal ID

**Returns:** 
- `array` - Deal completion status
  ```php
  [
      'completed' => true,  // true if deal just completed
      'seller_confirmed' => true,
      'buyer_confirmed' => true,
      'status' => 'completed'
  ]
  ```

**Example:**
```php
$result = $deal_obj->checkDealCompletion(10);
if ($result['completed']) {
    // Deal just completed, can trigger notifications/rating
}
```

---

#### `cancel($deal_id, $user_id, $user_type)`
Cancel a deal (either party can cancel).

**Parameters:**
- `$deal_id` (int) - Deal ID
- `$user_id` (int) - User ID (seller or buyer)
- `$user_type` (string) - 'seller' or 'buyer'

**Returns:** 
- `bool` - true if cancelled, false if failed

**Example:**
```php
$cancelled = $deal_obj->cancel(10, 1, 'seller');
```

---

#### `getSellerDealCount($seller_id, $status = 'ongoing')`
Get count of deals for a seller by status.

**Parameters:**
- `$seller_id` (int) - Seller ID
- `$status` (string) - Deal status to count (default: 'ongoing')

**Returns:** 
- `int` - Count of deals

**Example:**
```php
$ongoing_count = $deal_obj->getSellerDealCount(1, 'ongoing'); // Returns: 3
$completed_count = $deal_obj->getSellerDealCount(1, 'completed'); // Returns: 5
```

---

---

## Helper Functions

**Location**: `/includes/functions.php`

### File Upload Functions

#### `uploadProductImage($file_array, $seller_id, $upload_dir = 'assets/uploads/products')`
Validate and upload a product image.

**Parameters:**
- `$file_array` (array) - File from `$_FILES['image_key']`
- `$seller_id` (int) - Seller ID (used in filename)
- `$upload_dir` (string) - Upload directory (default: 'assets/uploads/products')

**Returns:** 
- `string` - Path to uploaded file (e.g., 'seller_1_randomhash.jpg')
- `false` - If upload failed

**Validations:**
- MIME types: image/jpeg, image/png, image/webp only
- File size: Max 2MB
- File extension: .jpg, .jpeg, .png, .webp

**Example:**
```php
if ($_FILES['product_image']['size'] > 0) {
    $image_path = uploadProductImage($_FILES['product_image'], $seller_id);
    if (!$image_path) {
        $error = "Invalid image file";
    }
}
```

---

#### `deleteProductImage($image_path)`
Safely delete a product image file.

**Parameters:**
- `$image_path` (string) - Path to image (relative or absolute)

**Returns:** 
- `bool` - true if deleted or file not found, false if error

**Example:**
```php
if ($old_image_path) {
    deleteProductImage($old_image_path);
}
```

---

#### `getImageDimensions($image_path)`
Get width and height of an image.

**Parameters:**
- `$image_path` (string) - Path to image file

**Returns:** 
- `array` - `['width' => 800, 'height' => 600]`
- `false` - If file not found or error

**Example:**
```php
$dims = getImageDimensions('seller_1_abc123.jpg');
if ($dims) {
    echo "Image size: {$dims['width']}x{$dims['height']}";
}
```

---

#### `getProductImageUrl($image_path)`
Get full URL to product image with placeholder fallback.

**Parameters:**
- `$image_path` (string) - Relative image path

**Returns:** 
- `string` - Full URL to image or placeholder

**Example:**
```php
$url = getProductImageUrl($product['image_path']);
echo "<img src='{$url}' alt='Product'>";
// Output: <img src='http://localhost/assets/uploads/products/seller_1_abc.jpg' alt='Product'>
// Or if file not found: http://localhost/assets/images/placeholder.jpg
```

---

### Security Functions (Existing from Stage 1)

#### `sanitizeInput($input)`
Prevent XSS attacks.
```php
$safe = sanitizeInput($user_input);
```

#### `generateCSRFToken()`
Create CSRF token for forms.
```php
$token = generateCSRFToken();
```

#### `verifyCSRFToken($token)`
Verify CSRF token on form submission.
```php
if (!verifyCSRFToken($_POST['csrf_token'])) {
    die('CSRF token invalid');
}
```

---

### Formatting Functions

#### `formatCurrency($amount, $currency = '₱')`
Format number as currency.

**Parameters:**
- `$amount` (float) - Amount to format
- `$currency` (string) - Currency symbol (default: '₱')

**Returns:** 
- `string` - Formatted currency string

**Example:**
```php
echo formatCurrency(150); // Output: ₱150.00
echo formatCurrency(150.50); // Output: ₱150.50
```

---

#### `formatDate($date, $format = 'M d, Y')`
Format date string.

**Parameters:**
- `$date` (string) - Date string
- `$format` (string) - PHP date format (default: 'M d, Y')

**Returns:** 
- `string` - Formatted date

**Example:**
```php
echo formatDate('2024-01-15 10:30:00'); // Output: Jan 15, 2024
echo formatDate('2024-01-15 10:30:00', 'M d, Y H:i'); // Output: Jan 15, 2024 10:30
```

---

---

## Error Handling Patterns

### Product Class Errors
```php
try {
    $product_id = $product_obj->create($seller_id, $name, $desc, $price, $qty, $img);
    if (!$product_id) {
        setFlashMessage('Failed to create product', 'error');
    }
} catch (Exception $e) {
    setFlashMessage('Database error: ' . $e->getMessage(), 'error');
}
```

### Deal Class Errors
```php
try {
    $deal_id = $deal_obj->create($product_id, $buyer_id, $seller_id);
    if ($deal_id) {
        // Deal created successfully
    }
} catch (Exception $e) {
    // Handle error
}
```

---

## Usage Examples

### Complete Product Creation Flow
```php
// Step 1: Validate and upload image
$image_path = uploadProductImage($_FILES['product_image'], $seller_id);
if (!$image_path) {
    setFlashMessage('Image upload failed', 'error');
    // redirect back
}

// Step 2: Create product
$product_obj = new Product($pdo);
$product_id = $product_obj->create(
    $seller_id,
    sanitizeInput($_POST['product_name']),
    sanitizeInput($_POST['product_description']),
    floatval($_POST['srp']),
    intval($_POST['quantity']),
    $image_path
);

if ($product_id) {
    setFlashMessage('Product created successfully!', 'success');
    secureRedirect(BASE_URL . '/public/seller/products/list.php');
} else {
    deleteProductImage($image_path); // cleanup on failure
    setFlashMessage('Failed to create product', 'error');
}
```

### Seller Deal Management
```php
$deal_obj = new Deal($pdo);

// Get ongoing deals
$ongoing_deals = $deal_obj->getBySellerID($seller_id, 'ongoing');

// Get completed deals
$completed_deals = $deal_obj->getBySellerID($seller_id, 'completed');

// Handle confirmation
if ($_POST['action'] === 'confirm_deal') {
    $deal_obj->sellerConfirmDeal($_POST['deal_id'], $seller_id);
    $result = $deal_obj->checkDealCompletion($_POST['deal_id']);
    
    if ($result['completed']) {
        // Can now trigger rating request, etc.
    }
}
```

---

## Database Schema References

### Products Table
```sql
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_description TEXT,
    srp DECIMAL(12, 2) NOT NULL,
    quantity INT DEFAULT 0,
    image_path VARCHAR(255),
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(seller_id) ON DELETE CASCADE,
    INDEX idx_seller_id (seller_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### Deals Table
```sql
CREATE TABLE deals (
    deal_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    status ENUM('ongoing', 'completed', 'cancelled') DEFAULT 'ongoing',
    confirmed_by_seller BOOLEAN DEFAULT FALSE,
    confirmed_by_buyer BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES buyers(buyer_id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES sellers(seller_id) ON DELETE CASCADE,
    INDEX idx_seller_id (seller_id),
    INDEX idx_buyer_id (buyer_id),
    INDEX idx_status (status)
};
```

---

**API Reference Complete - Stage 2 Ready**
