# Stage 2 Quick Reference Card

## 🚀 Quick Links

| Purpose | File/Command |
|---------|--------------|
| **View Project** | http://localhost/public/index.php |
| **Seller Dashboard** | /public/seller/dashboard.php |
| **Add Product** | /public/seller/products/add.php |
| **Product List** | /public/seller/products/list.php |
| **My Deals** | /public/seller/deals.php |
| **Buyer Dashboard** | /public/buyer/dashboard.php |
| **Marketplace** | /public/buyer/marketplace.php |
| **Database Schema** | partido_market.sql |
| **Testing Guide** | TESTING_GUIDE.md |

---

## 📚 Documentation Files

```
README_STAGE_2.md         ← START HERE: Project overview
├── STAGE_2_COMPLETION.md ← Feature summary
├── STAGE_2_CHECKLIST.md  ← Implementation details
├── TESTING_GUIDE.md      ← Test scenarios
├── API_REFERENCE.md      ← Code documentation
└── STAGE_2_VERIFICATION.md ← Pre-launch checklist
```

---

## 🔑 Key Classes

### Product.php
```php
$product = new Product($pdo);
$id = $product->create(...);      // Create product
$data = $product->getById($id);   // Get single
$all = $product->getAvailableProducts(); // Marketplace
$product->update($id, $seller_id, [...]);
$product->delete($id, $seller_id);
$results = $product->search($keyword);
$status = $product->toggleStatus($id, $seller_id);
```

### Deal.php
```php
$deal = new Deal($pdo);
$id = $deal->create($product_id, $buyer_id, $seller_id);
$ongoing = $deal->getBySellerID($seller_id, 'ongoing');
$deal->sellerConfirmDeal($deal_id, $seller_id);
$deal->buyerConfirmDeal($deal_id, $buyer_id);
```

---

## 🛡️ Security Essentials

### On Forms
```php
<?php $csrf_token = generateCSRFToken(); ?>
<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

// Verify on submission
if (!verifyCSRFToken($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

### Database Queries
```php
// ✅ CORRECT - Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = :seller_id");
$stmt->execute([':seller_id' => $seller_id]);

// ❌ WRONG - Never concatenate user input
$query = "SELECT * FROM products WHERE seller_id = $seller_id";
```

### Input Sanitization
```php
// Always sanitize user input
$safe_input = sanitizeInput($_POST['product_name']);
$safe_price = floatval($_POST['srp']);
$safe_quantity = intval($_POST['quantity']);
```

---

## 📋 Test Scenario (5 min)

1. **Add Product**
   - Login as seller
   - Dashboard → "Add New Product"
   - Fill form + upload image
   - Click "Add Product"

2. **Toggle Status**
   - Dashboard → "View My Products"
   - Click "Hide" button
   - Verify status changes instantly (AJAX)

3. **Verify Marketplace**
   - Logout
   - Login as buyer
   - Dashboard → "Browse Products"
   - Verify product NOT visible (it's hidden)

**Expected Result**: ✅ Only available products show in marketplace

---

## 🔧 Common Tasks

### Add New Seller Product
```php
$image_path = uploadProductImage($_FILES['product_image'], $seller_id);
$product_id = $product_obj->create(
    $seller_id, 'Product', 'Description', 150, 20, $image_path
);
```

### Get Marketplace Products
```php
$products = $product_obj->getAvailableProducts();
foreach ($products as $product) {
    echo $product['product_name'];  // Show available only
}
```

### Get Seller's Deals
```php
$ongoing = $deal_obj->getBySellerID($seller_id, 'ongoing');
$completed = $deal_obj->getBySellerID($seller_id, 'completed');
```

### Handle Product Edit
```php
if ($_FILES['new_image']['size'] > 0) {
    deleteProductImage($old_image);  // Delete old
    $image_path = uploadProductImage($_FILES['new_image'], $seller_id);
}
$product_obj->update($id, $seller_id, ['image_path' => $image_path]);
```

---

## 🐛 Quick Debugging

| Issue | Check |
|-------|-------|
| Product not in marketplace | Is status='available' AND quantity > 0? |
| Image not uploading | Is /assets/uploads/products/ writable? File < 2MB? |
| AJAX toggle not working | CSRF token in form? POST handler present? |
| Database error | Is MySQL running? Tables created? |
| Seller can't edit product | Is seller_id verified? Check ownership check. |
| Search returns nothing | Product must be status='available' |

---

## 📊 Database Queries

### Check Available Products
```sql
SELECT product_id, product_name, seller_id, status, quantity
FROM products
WHERE status = 'available' AND quantity > 0;
```

### Get Seller's Stats
```sql
SELECT COUNT(*) as total, SUM(CASE WHEN status='available' THEN 1 ELSE 0 END) as available
FROM products WHERE seller_id = 1;
```

### Get Deals by Status
```sql
SELECT * FROM deals WHERE seller_id = 1 AND status = 'ongoing';
```

---

## 🎨 File Upload Validation

```php
// In includes/functions.php
uploadProductImage($file_array, $seller_id)
  ├── Checks MIME type (image/jpeg, image/png, image/webp)
  ├── Checks file size (max 2MB)
  ├── Generates random filename (seller_1_hash.jpg)
  └── Stores in /assets/uploads/products/

// Fallback if file missing:
getProductImageUrl($path)  // Returns placeholder if file not found
```

---

## 🔄 Workflow Flows

### Seller: Add & Toggle Product
```
1. Add Product → Status: available
2. List Products → Verify in table
3. Click "Hide" (AJAX) → Status: unavailable
4. Marketplace: Product not visible
5. Click "Show" (AJAX) → Status: available
6. Marketplace: Product visible again
```

### Buyer: Discover Product
```
1. Browse Marketplace → See available products
2. Search by name → Filtered results
3. Sort by price → Reordered products
4. View product → [Stage 3] Detail page
5. Click Deal → [Stage 3] Initiate deal
```

### Deal: Confirmation Flow [Stage 2 Infrastructure]
```
1. Buyer initiates deal on product
2. Seller sees in /seller/deals.php (ongoing tab)
3. Seller clicks "Confirm"
4. Buyer confirms in /buyer/deals.php [Stage 3]
5. Both confirmed → Deal moves to completed
6. Trigger rating request [Stage 3]
```

---

## 📈 Performance Tips

| Action | Optimization |
|--------|--------------|
| Search | Uses `LIKE %keyword%` with indexed columns |
| Product list | LIMIT/OFFSET for pagination |
| Image display | Lazy loading + compression (2MB max) |
| Status toggle | AJAX avoids full page reload |
| Database | Indexes on seller_id, status, created_at |

---

## 🎯 Testing Checklist (5 items)

- [ ] Add 3 products with images
- [ ] Toggle one product unavailable
- [ ] Verify unavailable product hidden in marketplace
- [ ] Search for product by name
- [ ] Verify database contains all data

---

## 📞 Need Help?

1. **Code Issues**: Check API_REFERENCE.md
2. **Testing Issues**: Check TESTING_GUIDE.md
3. **Architecture**: Check STAGE_2_COMPLETION.md
4. **Verification**: Check STAGE_2_VERIFICATION.md
5. **Project Overview**: Check README_STAGE_2.md

---

## ⚡ Key Takeaways

✅ **Complete Product Lifecycle** - Add, edit, toggle, delete with images
✅ **Buyer Marketplace** - Search and sort available products
✅ **Deal Infrastructure** - Backend ready for Stage 3 messaging
✅ **Security** - CSRF, prepared statements, input validation
✅ **Performance** - AJAX updates, database optimization
✅ **Documentation** - 5 comprehensive guides included

---

## 🚀 Ready to Launch?

1. Import database: `mysql < partido_market.sql`
2. Run tests: Use TESTING_GUIDE.md
3. Verify: Check STAGE_2_VERIFICATION.md
4. Deploy: Project ready for production
5. Next: Plan Stage 3 features

---

**Quick Reference Version**: 1.0
**Last Updated**: 2024
**Status**: ✅ Complete
