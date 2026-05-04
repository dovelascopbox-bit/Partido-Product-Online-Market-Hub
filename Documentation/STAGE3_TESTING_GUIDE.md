# Stage 3 - Buyer Dashboard & Market Hub - Testing Guide

## Overview
This document provides comprehensive testing procedures for the Buyer Dashboard and Market Hub features of Partido Product Online Market Hub.

## Testing Environment Setup

1. **Database State**: Ensure you have test products in the `products` table with:
   - `status = 'available'`
   - `quantity > 0`
   - At least 15 products for pagination testing
   - Products linked to different sellers

2. **Seller Data**: Ensure sellers have:
   - `shop_name` (not null)
   - `rating` (float value)
   - `seller_name` in users table (full_name)
   - `completed_deals` count > 0 for trust signals

3. **Test User**: Create a buyer account or use existing buyer login

## Test Scenarios

### Test 1: Access Buyer Dashboard
**Objective**: Verify buyer can access dashboard and see all features
**Steps**:
1. Login as buyer
2. Navigate to `/public/buyer/dashboard.php`
3. Verify following visible:
   - Active Deals counter (should show 0 initially)
   - Total Purchases counter
   - Total Spent amount
   - Browse Market quick action card
   - My Deals quick action card
   - Profile section with email/name
   - Delivery address section

**Expected Result**: ✅ Dashboard loads without errors, all stats display correctly

---

### Test 2: Browse Market - Product Grid
**Objective**: Verify marketplace displays products in responsive grid with search
**Steps**:
1. Click "Browse Market" button from dashboard
2. Verify grid layout:
   - Desktop: 3 columns
   - Tablet: 2 columns
   - Mobile: 1 column
3. Verify each product card shows:
   - Product image
   - Product name
   - Seller name (shop_name)
   - Rating with ⭐ emoji
   - SRP (formatted currency)
   - Quantity/Stock count
   - Truncated description (80 chars max)
   - "✓ Available" badge
   - "View Details" link button
   - "Deal" action button

4. Verify pagination:
   - Shows first 12 products
   - "Previous" button disabled on first page
   - "Next" button enabled if more products
   - Page numbers clickable

**Expected Result**: ✅ Grid displays correctly, 12 products per page, responsive layout works

---

### Test 3: Search Market Products
**Objective**: Verify search functionality filters products correctly
**Steps**:
1. In market.php, use search bar
2. Search for: "test" (or any keyword matching products)
3. Verify:
   - Only products matching keyword in name or description appear
   - Results counter shows accurate count
   - Pagination resets to page 1
   - All matching products visible

4. Test edge cases:
   - Empty search (should show all available products)
   - Search with no results (should show "No products found")
   - Search with special characters (should handle safely)

**Expected Result**: ✅ Search filters correctly, no SQL errors, results accurate

---

### Test 4: View Product Detail
**Objective**: Verify product detail page shows full information and seller trust signals
**Steps**:
1. Click "View Details" on any product card
2. Verify URL changes to: `product.php?id=X` (where X is product_id)
3. Verify following displays:
   - Large product image (h-96 container)
   - Product title
   - Availability status (✓ Available or ✗ Out of Stock)
   - Stock quantity
   - Full product description (not truncated)
   - SRP in large blue text

4. Verify seller info box displays:
   - Shop name (blue background box)
   - Seller name (full_name from users table)
   - Rating display with ⭐
   - "Completed Deals: X" count
   - Seller credibility signals

5. Verify related products:
   - Shows 3 products from same seller
   - Products are different from current product
   - Same layout as marketplace grid

6. Verify action buttons:
   - "🤝 INITIATE DEAL" button visible (green, prominent)
   - Breadcrumb navigation at top
   - "Back to Market" link

**Expected Result**: ✅ Detail page displays correctly with all seller info, no DB errors

---

### Test 5: Initiate Deal - No Existing Deal
**Objective**: Verify deal can be created when no existing deal exists
**Steps**:
1. On product detail page, click "🤝 INITIATE DEAL" button
2. System should:
   - Verify CSRF token (should pass)
   - Check product is available (status='available' AND quantity>0)
   - Check no existing ongoing deal for this product
   - Create deal record in database
   - Set initial status = 'ongoing'
   - Set confirmed_by_buyer = 0
   - Set confirmed_by_seller = 0

3. After creation:
   - Redirect to `deals.php?deal_id=X`
   - Display success message: "Deal initiated! Awaiting seller confirmation"
   - Deal appears in "Active" tab

**Expected Result**: ✅ Deal created in DB, redirect works, success message shows

**Verify in Database**:
```sql
SELECT * FROM deals WHERE product_id = X ORDER BY deal_id DESC LIMIT 1;
-- Should show: status='ongoing', confirmed_by_buyer=0, confirmed_by_seller=0
```

---

### Test 6: Initiate Deal - Duplicate Prevention
**Objective**: Verify buyer cannot create multiple deals on same product
**Steps**:
1. Go back to same product detail page
2. Click "🤝 INITIATE DEAL" again
3. System should:
   - Detect existing ongoing deal
   - NOT create new deal
   - Show yellow warning: "You already have an ongoing deal for this product"
   - Redirect back to product.php with error message

**Expected Result**: ✅ Duplicate deal prevented, warning message displays

---

### Test 7: View Deals Page - Active Deals
**Objective**: Verify deals page shows buyer's active deals with correct info
**Steps**:
1. Navigate to `deals.php`
2. Verify page structure:
   - Navigation bar with logout
   - Breadcrumb: Dashboard / My Deals
   - Stats cards showing:
     - Total Deals count
     - Active (ongoing) count
     - Completed count
     - Cancelled count

3. Verify "Active" tab shows:
   - Table with columns: Product | Seller | Price | Your Status | Seller Status | Initiated | Actions
   - Deal row shows:
     - Product image (12x12 thumbnail)
     - Product name
     - Seller name
     - SRP (formatted currency)
     - "⏳ Pending" status (if not confirmed by buyer)
     - "⏳ Pending" status (if not confirmed by seller)
     - Initiated date/time
     - "✓ Confirm" button if not yet confirmed by buyer
     - "✕ Cancel" button

**Expected Result**: ✅ Active deals display correctly in table format

---

### Test 8: View Deals Page - Completed Deals
**Objective**: Verify completed deals tab works
**Steps**:
1. Click "Completed" tab
2. If no completed deals: show "No completed deals yet"
3. If completed deals exist:
   - Show table with columns: Product | Seller | Price | Completed
   - Each row displays deal info with completed_at timestamp

**Expected Result**: ✅ Completed tab works, proper empty state

---

### Test 9: View Deals Page - Cancelled Deals
**Objective**: Verify cancelled deals tab works
**Steps**:
1. Click "Cancelled" tab
2. If no cancelled deals: show "No cancelled deals yet"
3. If cancelled deals exist:
   - Show table with columns: Product | Seller | Price | Cancelled
   - Each row displays deal info with cancelled_at timestamp

**Expected Result**: ✅ Cancelled tab works, proper empty state

---

### Test 10: Tab Navigation
**Objective**: Verify tab switching works correctly
**Steps**:
1. Click on each tab (Active, Completed, Cancelled)
2. Verify:
   - Tab highlight changes (blue underline for active tab)
   - Content switches correctly
   - Tab button text shows count in parentheses

**Expected Result**: ✅ Tab switching works smoothly

---

### Test 11: Dashboard Active Deals Integration
**Objective**: Verify dashboard shows active deals count
**Steps**:
1. After creating a deal, go back to dashboard
2. Verify:
   - Active Deals card now shows "1" (or correct count)
   - "My Deals" quick action card shows active count
   - "View All Deals" link on dashboard goes to deals.php

**Expected Result**: ✅ Dashboard active deals count updates correctly

---

### Test 12: Responsive Design
**Objective**: Verify all pages respond correctly on different screen sizes
**Steps**:
1. Test on desktop (1920px width)
2. Test on tablet (768px width)
3. Test on mobile (375px width)

**Verify**:
- Grid changes from 3 columns (desktop) → 2 columns (tablet) → 1 column (mobile)
- Tables become scrollable on mobile (overflow-x-auto)
- Navigation remains accessible
- Buttons properly sized and touchable

**Expected Result**: ✅ All pages display correctly at all breakpoints

---

### Test 13: Security - CSRF Protection
**Objective**: Verify CSRF tokens prevent unauthorized requests
**Steps**:
1. Inspect product detail page HTML
2. Verify CSRF token hidden input exists in deal form
3. Try to bypass by modifying token value manually
4. Submit form
5. System should reject the request

**Expected Result**: ✅ Invalid CSRF token rejected, error message shown

---

### Test 14: Security - Buyer Access Only
**Objective**: Verify only buyers can access buyer pages
**Steps**:
1. Logout
2. Try to access `/public/buyer/market.php` directly
3. Should redirect to login
4. Login as seller
5. Try to access `/public/buyer/market.php`
6. Should redirect to seller dashboard or show access denied

**Expected Result**: ✅ Buyer-only middleware enforces access control

---

### Test 15: Security - Ownership Verification
**Objective**: Verify seller cannot manipulate products/deals
**Steps**:
1. As buyer, create a deal for product_id = 5
2. Try to manually access product detail with different product_id
3. Verify seller_id is fetched from database, not from POST/GET
4. Try to cancel someone else's deal (as different buyer)
5. System should reject or show "Access Denied"

**Expected Result**: ✅ Ownership verification prevents unauthorized access

---

## Complete User Journey Test

**Scenario**: New buyer discovers product and initiates deal

**Complete Flow**:
1. ✅ Login as buyer → Buyer Dashboard
2. ✅ Click "Browse Market" → Market page with 3-column grid
3. ✅ Search for product → Results filtered correctly
4. ✅ Click "View Details" → Product detail page
5. ✅ See seller info (shop name, rating, completed deals)
6. ✅ Click "🤝 INITIATE DEAL" → Deal created, redirect to deals.php
7. ✅ See deal in "Active" tab with pending status
8. ✅ Go back to dashboard → Active deals count shows "1"
9. ✅ Try same product again → Warning shows duplicate deal message
10. ✅ Test duplicate prevention → Error shown correctly

**Expected Result**: ✅ Complete journey flows smoothly, all DB records correct

---

## Database Verification Queries

### Check Market Products
```sql
SELECT p.product_id, p.product_name, p.srp, p.quantity, s.shop_name, s.rating
FROM products p
JOIN sellers s ON p.seller_id = s.seller_id
WHERE p.status = 'available' AND p.quantity > 0
ORDER BY p.created_at DESC
LIMIT 20;
```

### Check Buyer Deals
```sql
SELECT d.deal_id, d.product_id, p.product_name, d.buyer_id, d.seller_id, 
       d.status, d.confirmed_by_buyer, d.confirmed_by_seller, d.created_at
FROM deals d
JOIN products p ON d.product_id = p.product_id
WHERE d.buyer_id = X
ORDER BY d.created_at DESC;
```

### Check No Duplicate Ongoing Deals
```sql
SELECT COUNT(*) as ongoing_count
FROM deals
WHERE product_id = X AND buyer_id = Y AND status = 'ongoing';
-- Should return 0 or 1 only
```

---

## Known Limitations & Future Enhancements

### Current Stage 3 Scope:
- ✅ Browse market with search/pagination
- ✅ View product details with seller info
- ✅ Initiate deals with duplicate prevention
- ✅ View deals by status
- ✅ Dashboard integration

### Deferred to Stage 4:
- ⏳ Rating seller after deal completion
- ⏳ Messenger integration (show in-app chat link)
- ⏳ Deal confirmation flow
- ⏳ Deal cancellation workflow
- ⏳ Wishlist functionality
- ⏳ Order history/receipts

---

## Troubleshooting

### Market.php Class Not Found
- ✅ Check `classes/Market.php` exists
- ✅ Verify `init.php` includes Market class
- ✅ Check autoloading in init.php

### Seller Info Not Displaying
- ✅ Verify `sellers` table has data
- ✅ Check seller_id in products table is valid
- ✅ Verify `shop_name` and `rating` fields are populated
- ✅ Check SQL query JOINs correctly

### Deal Not Creating
- ✅ Check Deal class exists at `classes/Deal.php`
- ✅ Verify PDO connection working
- ✅ Check CSRF token is being verified correctly
- ✅ Verify product availability check passes
- ✅ Check duplicate deal prevention logic

### Pagination Not Working
- ✅ Verify page parameter is being passed correctly
- ✅ Check Market::getProductCount() returns correct count
- ✅ Verify LIMIT/OFFSET calculations
- ✅ Check Market::getAvailableProducts() accepts limit/offset

---

## Sign-Off Checklist

- [ ] All 7 tasks completed (Market.php, market.php, product.php, initiate_deal.php, deals.php, dashboard.php, testing)
- [ ] Market page displays 3-column grid on desktop
- [ ] Search works and filters products
- [ ] Product detail shows seller info with rating/completed deals
- [ ] Deal button creates deal record in database
- [ ] Duplicate deal prevention working
- [ ] Deals page shows active/completed/cancelled tabs
- [ ] Dashboard shows active deals count
- [ ] All pages respond correctly on mobile/tablet/desktop
- [ ] CSRF protection active on all forms
- [ ] Buyer-only access enforced
- [ ] Ownership verification prevents unauthorized access
- [ ] No SQL errors in logs
- [ ] No JavaScript errors in console
- [ ] Database records created correctly

**Status**: Ready for Stage 4 (Messenger & Rating System)

