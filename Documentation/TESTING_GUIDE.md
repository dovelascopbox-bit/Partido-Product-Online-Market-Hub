# Stage 2 Testing Guide

## Quick Start

### Prerequisites
1. XAMPP or similar local server running
2. MySQL database with `partido_market` database created
3. Application at `http://localhost/`

### Database Setup
1. Import `partido_market.sql` into your MySQL database:
   ```sql
   MySQL prompt > source c:\Users\velas\Desktop\ParProOMH\partido_market.sql;
   ```

2. Verify tables created:
   ```sql
   SHOW TABLES;  -- Should show: users, admins, sellers, buyers, products, transactions, deals
   ```

---

## Test Case 1: Complete Product Lifecycle

### 1.1 Seller Login & Navigate
**Steps:**
1. Open browser to `http://localhost/public/index.php`
2. Click "Seller Login"
3. Enter seller credentials (from Stage 1 setup, e.g., email: seller@test.com)
4. Click "Login"

**Expected Result:**
- Redirected to `/public/seller/dashboard.php`
- Dashboard shows seller's shop name and stats
- See "My Products" stat showing 0 products initially

---

### 1.2 Add First Product
**Steps:**
1. On seller dashboard, click "Add New Product" button
2. Fill form:
   - **Product Name**: "Fresh Mango"
   - **Description**: "High-quality fresh mangoes from local farm, ripe and sweet. Perfect for eating fresh or making juice."
   - **Price (SRP)**: 150
   - **Quantity**: 20
   - **Image**: Upload jpg/png/webp file (max 2MB)
3. Click "Add Product" button

**Expected Result:**
- Product created successfully
- Redirected to products list page
- Flash message: "Product added successfully!"
- Product appears in table with status "available"

**Verification:**
- Check dashboard - "My Products" stat now shows 1
- Check available count shows 1

---

### 1.3 Add Second and Third Products
**Steps:**
1. Click "Add New Product" again
2. Add product:
   - **Name**: "Organic Rice"
   - **Description**: "Premium organic rice, 5kg bags"
   - **Price**: 350
   - **Quantity**: 50
   - **Image**: Upload different image

3. Repeat process for third product:
   - **Name**: "Coconut Oil"
   - **Description**: "Pure virgin coconut oil, cold-pressed"
   - **Price**: 280
   - **Quantity**: 30

**Expected Result:**
- All 3 products appear in list
- Dashboard shows "My Products: 3"
- All products have status "available"

---

### 1.4 Edit a Product
**Steps:**
1. In products list, click "Edit" button on "Fresh Mango"
2. Change:
   - **Price**: 160 (increase price)
   - **Quantity**: 15 (reduce quantity)
3. Leave image unchanged (click "Keep existing image")
4. Click "Update Product"

**Expected Result:**
- Product updated
- Flash message: "Product updated successfully!"
- Redirected to products list
- Price and quantity reflect changes
- Image remains the same

---

### 1.5 Toggle Product Status (Unavailable)
**Steps:**
1. In products list, locate "Fresh Mango"
2. Click "Hide" button (red button with "unavailable" icon)
3. **Important**: Wait for button to change to "Show" without page reload

**Expected Result:**
- Button changes from "Hide" to "Show" instantly (AJAX)
- Status changes to "unavailable"
- No page reload occurs
- Product stats update: "Available: 2" (was 3)

**Technical Verification (F12 Developer Tools):**
- Open Network tab
- Click "Hide" button
- Verify XHR request to `list.php` with POST action
- Response should contain JSON: `{"success": true, "status": "unavailable"}`

---

## Test Case 2: Buyer Marketplace Visibility

### 2.1 Logout and Login as Buyer
**Steps:**
1. Click "Logout" button (top right)
2. Confirm logout
3. Go to `http://localhost/public/index.php`
4. Click "Buyer Login"
5. Enter buyer credentials (email: buyer@test.com)
6. Click "Login"

**Expected Result:**
- Redirected to `/public/buyer/dashboard.php`
- Dashboard shows buyer stats
- See "Browse Products" button

---

### 2.2 View Marketplace - Only Available Products
**Steps:**
1. Click "Browse Products" button
2. Observe product grid

**Expected Result:**
- **Only 2 products visible**: "Organic Rice" and "Coconut Oil"
- **"Fresh Mango" NOT visible** (toggled to unavailable)
- Each product card shows:
  - Product image
  - Product name (bold)
  - Description (first 100 chars, truncated)
  - Seller shop name
  - Price (SRP) in blue, large font
  - Available quantity
  - "View" and "Deal" buttons

**Critical Verification**: Fresh Mango product should be completely hidden from buyer marketplace

---

### 2.3 Test Search Functionality
**Steps:**
1. In marketplace search bar, type: "rice"
2. Click "Search" button

**Expected Result:**
- Only "Organic Rice" product appears
- Page shows "Showing 1 product"

**Steps 2:**
1. Clear search by clicking "Clear Filters"
2. Search for: "coconut"

**Expected Result:**
- Only "Coconut Oil" product appears

**Steps 3:**
1. Clear filters
2. Search for: "mango"

**Expected Result:**
- **No results** (mango product is unavailable, should be hidden even in search)
- Message: "No products available"

---

### 2.4 Test Sort Functionality
**Steps:**
1. Clear all filters (click "Clear Filters")
2. Select sort: "Price: Low to High"
3. Click "Search"

**Expected Result:**
- Products sorted: Coconut Oil (280) → Organic Rice (350)

**Steps 2:**
1. Select sort: "Price: High to Low"
2. Click "Search"

**Expected Result:**
- Products sorted: Organic Rice (350) → Coconut Oil (280)

**Steps 3:**
1. Select sort: "Newest First"
2. Click "Search"

**Expected Result:**
- Products in creation order (Coconut Oil → Organic Rice if created in that order)

---

## Test Case 3: Product Details Verification

### 3.1 Check Product Card Information
**Steps:**
1. In marketplace, inspect any product card

**Expected Result:**
- Product name displays correctly
- Price shows as formatted currency (e.g., ₱350.00)
- Quantity shows accurately
- Seller shop name displays
- Image visible or placeholder shown
- "View" and "Deal" buttons present

**Note**: "View" and "Deal" buttons are placeholders for Stage 3

---

## Test Case 4: Data Persistence

### 4.1 Refresh Page
**Steps:**
1. In marketplace, refresh page (F5)

**Expected Result:**
- All 2 available products still visible
- Unavailable product still hidden
- Page loads correctly

### 4.2 Logout and Re-login
**Steps:**
1. Logout from buyer account
2. Login as seller
3. Go to products list
4. Verify all products still there with correct status

**Expected Result:**
- All 3 products visible to seller
- Fresh Mango shows as "unavailable"
- Edit/toggle still works

---

## Test Case 5: Delete Product

### 5.1 Delete a Product
**Steps:**
1. Login as seller (if not already)
2. Go to products list
3. Click "Delete" button on "Coconut Oil"
4. Confirm deletion in popup

**Expected Result:**
- Product deleted
- Removed from products list
- Flash message: "Product deleted successfully!"
- Seller dashboard shows "My Products: 2"
- Image file removed from `/assets/uploads/products/`

---

## Test Case 6: Marketplace After Delete

### 6.1 Verify Deleted Product Not in Marketplace
**Steps:**
1. Logout as seller
2. Login as buyer
3. View marketplace

**Expected Result:**
- Only "Organic Rice" visible
- "Coconut Oil" and "Fresh Mango" not visible
- Page shows "Showing 1 product"

---

## Test Case 7: AJAX Status Toggle Edge Cases

### 7.1 Toggle Multiple Times
**Steps:**
1. Login as seller
2. Go to products list
3. Click "Show" on Fresh Mango (toggle back to available)
4. Verify status changes to "available" without reload
5. Click "Hide" again
6. Verify status changes back to "unavailable"

**Expected Result:**
- Each toggle completes without page reload
- Status updates accurately each time
- Available product count updates

### 7.2 Browser Back Button After Toggle
**Steps:**
1. Toggle a product status
2. Press browser back button
3. Press forward button

**Expected Result:**
- Marketplace reflects current database state
- No data inconsistency

---

## Test Case 8: File Upload Validation

### 8.1 Upload Invalid File Type
**Steps:**
1. Go to "Add New Product"
2. Try to upload a .txt, .pdf, or non-image file
3. Observe error

**Expected Result:**
- Error message: "Invalid file type. Allowed: jpg, png, webp"
- File not uploaded
- Form still visible for retry

### 8.2 Upload File Too Large
**Steps:**
1. Go to "Add New Product"
2. Try to upload image > 2MB
3. Observe error

**Expected Result:**
- Error message: "File size exceeds 2MB limit"
- File not uploaded
- Form still visible for retry

---

## Performance & Edge Cases

### 8.1 Large Product List
**Steps:**
1. Add 20+ products
2. View products list
3. Perform status toggles

**Expected Result:**
- Page loads smoothly
- AJAX toggles remain responsive
- No significant lag

### 8.2 Search with Special Characters
**Steps:**
1. Search for: "% &lt; &gt;"
2. Observe handling

**Expected Result:**
- No errors
- Safe handling of special characters
- Either no results or safe display

---

## Database Verification Queries

Run these to verify data integrity:

```sql
-- Check all products by status
SELECT product_id, product_name, status, quantity, seller_id, created_at 
FROM products 
ORDER BY created_at DESC;

-- Check available products only (what buyer sees)
SELECT product_id, product_name, srp, quantity, image_path, seller_id
FROM products 
WHERE status = 'available' AND quantity > 0;

-- Check products with seller details
SELECT p.product_id, p.product_name, p.status, s.shop_name, u.email
FROM products p
JOIN sellers s ON p.seller_id = s.seller_id
JOIN users u ON s.user_id = u.user_id
ORDER BY p.created_at DESC;

-- Check image files uploaded
SELECT product_id, product_name, image_path FROM products WHERE image_path IS NOT NULL;
```

---

## Troubleshooting

### Issue: Product appears in buyer marketplace when it should be hidden
**Solution:**
- Verify product status is 'unavailable' in database
- Refresh browser cache (Ctrl+Shift+Delete)
- Check SQL: `SELECT * FROM products WHERE product_id = X;`

### Issue: AJAX status toggle not working
**Solution:**
- Check browser console (F12) for JavaScript errors
- Verify CSRF token is present in form
- Check Network tab for failed requests
- Verify `list.php` handles POST requests

### Issue: Image upload not working
**Solution:**
- Verify `/assets/uploads/products/` directory exists and is writable
- Check file permissions: `chmod 755 /assets/uploads/products/`
- Verify MIME types supported in `functions.php`
- Check PHP upload limits in php.ini

### Issue: Product not appearing after creation
**Solution:**
- Check browser console for JavaScript errors
- Verify product status set to 'available' by default
- Check seller_id matches authenticated seller
- Verify database INSERT was successful

---

## Final Checklist

- [ ] 3 products created successfully
- [ ] Product edit functionality working
- [ ] Status toggle working without page reload
- [ ] Unavailable products hidden from buyer marketplace
- [ ] Available products visible in marketplace
- [ ] Search filtering unavailable products
- [ ] Sort functionality working
- [ ] Delete product removes image and updates count
- [ ] Product information displays correctly in marketplace
- [ ] All data persists after refresh/logout-login
- [ ] No JavaScript errors in console
- [ ] Mobile responsive (test on mobile or resize browser)
- [ ] Flash messages display properly
- [ ] CSRF tokens present on all forms
- [ ] Database queries execute without errors

---

**Status: ✅ Ready for Stage 2 Testing**

After completing these tests successfully, Stage 2 is complete and system is ready for Stage 3 (deal management and messaging system).
