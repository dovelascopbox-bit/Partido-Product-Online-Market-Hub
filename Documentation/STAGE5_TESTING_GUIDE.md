# Stage 5 - Deal Confirmation & Rating System Testing Guide

## Overview
This document provides comprehensive test scenarios for Stage 5 implementation. Stage 5 enables:
1. Deal confirmation flow (both parties must confirm)
2. Buyer rating seller after confirmation
3. Seller public profile with ratings/reviews
4. Notification system for deal events

---

## Pre-Testing Checklist

- [ ] Database updated: Run `partido_market.sql` to create `ratings` and `notifications` tables
- [ ] Classes loaded: Verify `Rating.php` and `Notification.php` in `/classes/`
- [ ] init.php updated: Classes auto-loaded with require_once
- [ ] CSRF token generation working in all pages
- [ ] Test data: Create at least 2 user accounts (buyer + seller)

---

## Test Scenarios

### Test 1: Deal Confirmation - Seller Marks Deal Done

**Objective**: Verify seller can confirm deal completion and buyer receives notification

**Setup**:
- Buyer account (buyer1@test.com)
- Seller account (seller1@test.com)
- Product created by seller1
- Deal initiated by buyer1 (status='ongoing')

**Steps**:
1. Login as seller1
2. Go to `/public/seller/deals.php`
3. Find ongoing deal from buyer1
4. Click "✓ Mark as Done" button
5. Confirm in dialog: "Mark this deal as done?"
6. Verify success message: "Deal confirmed! Waiting for buyer confirmation..."

**Expected Results**:
- ✅ Button changes to "✓ Confirmed" (disabled)
- ✅ Deal status REMAINS 'ongoing' (not both confirmed yet)
- ✅ `deals.confirmed_by_seller = 1`
- ✅ Buyer receives notification in notifications table
- ✅ Page reloads showing updated status

**Database Verification**:
```sql
SELECT deal_id, confirmed_by_seller, confirmed_by_buyer, status 
FROM deals 
WHERE deal_id = [TEST_DEAL_ID];
-- Expected: (deal_id, 1, 0, 'ongoing')

SELECT * FROM notifications 
WHERE type = 'deal_confirmed' 
ORDER BY created_at DESC LIMIT 1;
-- Expected: message contains "Seller confirmed your deal"
```

---

### Test 2: Deal Confirmation - Buyer Confirms & Redirected to Rating

**Objective**: Verify buyer can confirm deal and is redirected to rating page

**Setup**:
- Previous test deal with `confirmed_by_seller = 1`
- Buyer logged in

**Steps**:
1. Login as buyer1
2. Go to `/public/buyer/deals.php`
3. Find deal with "✓ Confirm Deal" button (seller confirmed)
4. Click "✓ Confirm Deal" button
5. Confirm in dialog: "Confirm this deal is complete?"

**Expected Results**:
- ✅ Deal status changes to 'completed'
- ✅ `confirmed_by_buyer = 1`
- ✅ `completed_at` timestamp set to NOW()
- ✅ Redirected to `/public/buyer/rate.php?deal_id=[id]`
- ✅ Seller receives notification: "Deal completed! Check your rating."

**Database Verification**:
```sql
SELECT deal_id, confirmed_by_seller, confirmed_by_buyer, status, completed_at 
FROM deals 
WHERE deal_id = [TEST_DEAL_ID];
-- Expected: (deal_id, 1, 1, 'completed', CURRENT_TIMESTAMP)

SELECT * FROM notifications 
WHERE type = 'deal_completed' 
ORDER BY created_at DESC LIMIT 1;
```

---

### Test 3: Rating Submission - Buyer Rates Seller

**Objective**: Verify buyer can submit rating with stars and optional review

**Setup**:
- Completed deal from Test 2
- Buyer on `/public/buyer/rate.php?deal_id=[id]`

**Steps**:
1. Review appears showing product name, seller name, date
2. Click on star #4 (should hover and show "Very Good (4/5)")
3. Enter review text: "Great seller, very responsive!"
4. Verify character count shows "33/500"
5. Click "Submit Rating" button

**Expected Results**:
- ✅ Rating saved with `stars = 4`
- ✅ Review saved as "Great seller, very responsive!"
- ✅ `created_at` timestamp recorded
- ✅ Redirected to buyer deals page with success message: "Rating submitted successfully!"
- ✅ Seller receives notification: "[buyer_name] rated you ★★★★☆ for [product]"

**Database Verification**:
```sql
SELECT * FROM ratings 
WHERE deal_id = [TEST_DEAL_ID];
-- Expected: (rating_id, deal_id, buyer_id, seller_id, 4, 'Great seller...', TIMESTAMP)

SELECT * FROM notifications 
WHERE type = 'rating_received' 
ORDER BY created_at DESC LIMIT 1;
```

---

### Test 4: Rating Submission - Prevent Duplicate Rating

**Objective**: Verify buyer cannot rate same deal twice

**Setup**:
- Completed deal with existing rating from Test 3
- Same buyer tries to access rate page again

**Steps**:
1. Login as buyer1
2. Manually navigate to `/public/buyer/rate.php?deal_id=[same_deal_id]`

**Expected Results**:
- ✅ Redirected to `/public/buyer/deals.php`
- ✅ Flash message: "Rating already submitted"
- ✅ No new rating created in database

**Database Verification**:
```sql
SELECT COUNT(*) as rating_count FROM ratings 
WHERE deal_id = [TEST_DEAL_ID];
-- Expected: 1 (not 2)
```

---

### Test 5: Seller Average Rating Calculation

**Objective**: Verify seller's average rating updates correctly

**Setup**:
- Seller with 3 completed deals
- Deal 1 rated 5 stars
- Deal 2 rated 4 stars
- Deal 3 rated 3 stars

**Steps**:
1. Complete Test 3 for all 3 deals
2. Visit `/public/seller/profile.php?id=[seller_id]`

**Expected Results**:
- ✅ Average rating displays as 4.0 (5+4+3)/3
- ✅ All 3 ratings listed in "Customer Reviews"
- ✅ Star display shows: ★★★★☆ 4.0 (3 reviews)
- ✅ "Completed Deals" count: 3

**Database Verification**:
```sql
SELECT AVG(stars) as avg, COUNT(*) as count FROM ratings 
WHERE seller_id = [SELLER_ID];
-- Expected: avg=4.0, count=3
```

---

### Test 6: Seller Profile Public Access

**Objective**: Verify seller profile is publicly accessible with correct data

**Setup**:
- Seller with completed deals and ratings (from Test 5)

**Steps**:
1. Logout (or use incognito window)
2. Navigate to `/public/seller/profile.php?id=[seller_id]`
3. Verify page loads without authentication

**Expected Results**:
- ✅ Page loads successfully (no auth required)
- ✅ Seller name, shop name, description displayed
- ✅ Member since date shown
- ✅ Location info displayed
- ✅ Average rating: 4.0 ★★★★☆
- ✅ Completed deals count: 3
- ✅ All 3 reviews with buyer names, stars, text, dates displayed
- ✅ Active products (status='available') listed in grid
- ✅ Product links clickable

---

### Test 7: Product Detail Page Shows Seller Rating

**Objective**: Verify seller's average rating displayed on product detail page

**Setup**:
- Product from seller with rating 4.0
- From Test 5 setup

**Steps**:
1. Navigate to product detail page
2. Locate seller information section

**Expected Results**:
- ✅ Seller name displayed
- ✅ Star rating badge shows: ★★★★☆ 4.0 (3)
- ✅ Clicking on seller name OR rating badge → redirects to seller profile
- ✅ Rating updates in real-time if new rating submitted

---

### Test 8: Market Hub Product Cards Show Rating Badge

**Objective**: Verify rating badge appears on product cards in market

**Setup**:
- Several sellers with different ratings
- Market page loaded

**Steps**:
1. Go to `/public/buyer/market.php`
2. Scroll through product cards

**Expected Results**:
- ✅ Each product card shows seller rating badge
- ✅ Badges show: ★★★★☆ 4.0 or "No ratings" if new seller
- ✅ Badge clickable → links to seller profile

---

### Test 9: Messenger Conversation Header Shows Seller Rating

**Objective**: Verify seller's rating displayed in messenger chat context

**Setup**:
- Ongoing deal with seller who has rating 4.0
- Buyer in messenger conversation

**Steps**:
1. Login as buyer
2. Open messenger conversation
3. Look at product header section

**Expected Results**:
- ✅ Product name, price shown
- ✅ Seller name with rating badge: ★★★★☆ 4.0
- ✅ Rating clickable → links to seller profile

---

### Test 10: Rating Display With Different Star Counts

**Objective**: Verify star rendering for different ratings

**Setup**:
- Sellers with ratings: 1.0, 2.5, 3.0, 4.5, 5.0

**Steps**:
1. Visit each seller profile
2. Verify star display

**Expected Results**:
- ✅ 1.0 → ★☆☆☆☆ 1.0 (1 review)
- ✅ 2.5 → ★★½☆☆ 2.5 (1 review)
- ✅ 3.0 → ★★★☆☆ 3.0 (1 review)
- ✅ 4.5 → ★★★★½ 4.5 (1 review)
- ✅ 5.0 → ★★★★★ 5.0 (1 review)

---

### Test 11: Rating Security - Buyer Cannot Rate Own Deal

**Objective**: Verify buyer cannot directly manipulate rating system

**Setup**:
- Seller trying to access rating page for a deal they are seller on

**Steps**:
1. Manually craft URL: `/public/buyer/rate.php?deal_id=[deal_where_user_is_seller]`
2. Attempt to submit rating

**Expected Results**:
- ✅ Page redirects to deals page (deal not found)
- ✅ No rating created
- ✅ Error message shown

---

### Test 12: Rating Page - Star Rating Interaction

**Objective**: Verify interactive star rating UI works correctly

**Setup**:
- Buyer on rating page

**Steps**:
1. Hover over each star (1-5)
2. Verify tooltip shows rating name:
   - Star 1: "Poor (1/5)"
   - Star 2: "Fair (2/5)"
   - Star 3: "Good (3/5)"
   - Star 4: "Very Good (4/5)"
   - Star 5: "Excellent (5/5)"
3. Click star 3
4. Move mouse away (mouseleave)
5. Verify selected stars remain highlighted

**Expected Results**:
- ✅ Hover effect shows tooltip
- ✅ Click selects star rating (color changes to gold)
- ✅ Selection persists after mouseleave
- ✅ Form shows "Good (3/5)"
- ✅ Submit button becomes ENABLED

---

### Test 13: Rating Page - Submit Button Disabled Without Star

**Objective**: Verify form validation prevents empty rating submission

**Setup**:
- Buyer on rating page
- No star selected

**Steps**:
1. Verify submit button appears DISABLED (grayed out)
2. Try to click it (should not work)
3. Enter review text only
4. Attempt submit
5. Select 1 star
6. Verify button becomes ENABLED

**Expected Results**:
- ✅ Button disabled initially
- ✅ Button remains disabled with review text only
- ✅ Button enables only after star selected
- ✅ Form prevents submission without stars

---

### Test 14: Rating Page - Review Text Character Limit

**Objective**: Verify review text cannot exceed 500 characters

**Setup**:
- Buyer on rating page with star selected

**Steps**:
1. Paste 600 characters of text into review field
2. Verify input stops at 500 characters
3. Verify character counter shows "500/500"
4. Try to add more characters
5. Observe counter stays at 500

**Expected Results**:
- ✅ Maxlength attribute limits input to 500
- ✅ Character counter updates live
- ✅ Cannot exceed 500 characters
- ✅ Can submit with exactly 500 characters

---

### Test 15: Notification Triggers - Complete Workflow

**Objective**: Verify all notification types trigger correctly

**Setup**:
- Fresh deal setup

**Steps**:
1. Seller confirms deal (should trigger: 'deal_confirmed')
2. Buyer confirms deal (should trigger: 'deal_completed')
3. Buyer rates seller (should trigger: 'rating_received')
4. Check notifications table for all 3 entries

**Expected Results**:
- ✅ Notification 1: type='deal_confirmed', buyer receives it
- ✅ Notification 2: type='deal_completed', seller receives it
- ✅ Notification 3: type='rating_received', seller receives it
- ✅ All notifications have is_read=FALSE initially

**Database Verification**:
```sql
SELECT user_id, type, message FROM notifications 
WHERE deal_id = [TEST_DEAL_ID]
ORDER BY created_at ASC;
-- Expected: 3 rows with correct types and recipients
```

---

### Test 16: CSRF Token Protection

**Objective**: Verify CSRF token validation on rating submission

**Setup**:
- Rating page with valid CSRF token

**Steps**:
1. Get CSRF token from page
2. Submit rating form with valid token
3. Verify success
4. Get another rating URL
5. Try to submit with old/mismatched CSRF token
6. Observe error

**Expected Results**:
- ✅ Valid token → rating submitted successfully
- ✅ Invalid token → 403 error, "CSRF token verification failed"
- ✅ Empty token → 403 error
- ✅ Rating NOT created on failed attempt

---

### Test 17: Concurrent Confirmations - No Race Condition

**Objective**: Verify both parties confirming simultaneously doesn't cause issues

**Setup**:
- Deal with seller confirmed=0, buyer confirmed=0
- Two browser sessions (buyer + seller)

**Steps**:
1. Session 1 (Seller): Click "Mark as Done" (don't submit yet)
2. Session 2 (Buyer): Click "Confirm Deal" (don't submit yet)
3. Session 1: Submit confirmation
4. Session 2: Submit confirmation
5. Check final deal state

**Expected Results**:
- ✅ Deal state = 'completed' (both confirmed)
- ✅ No SQL error or race condition
- ✅ completed_at timestamp set correctly
- ✅ Both parties see updated status after page refresh

---

### Test 18: Access Control - Buyer Cannot Confirm Another's Deal

**Objective**: Verify authorization prevents unauthorized confirmations

**Setup**:
- Deal between seller1 and buyer1
- buyer2 account exists

**Steps**:
1. Login as buyer2
2. Manually submit POST to `/public/buyer/confirm_deal.php`
3. Include: deal_id (from deal 1), csrf_token

**Expected Results**:
- ✅ 403 Forbidden error
- ✅ Message: "Unauthorized action"
- ✅ Deal NOT modified
- ✅ No redirect to rating page

**Database Verification**:
```sql
SELECT * FROM deals WHERE deal_id = [TEST_DEAL_ID];
-- Status should remain unchanged
```

---

## Complete User Journey Test

### Scenario: Alice Buys from Bob, Leaves 5-Star Rating

**Setup**:
- Alice (buyer)
- Bob (seller with existing 4.0 rating from 3 deals)
- Product: "Vintage Camera" price: 2500

**Complete Flow**:

1. **Alice browses market**
   - Sees Bob's product
   - Product card shows: ★★★★☆ 4.0 (3 reviews)

2. **Alice views product detail**
   - Clicks on camera
   - Product detail shows: Bob's name with ★★★★☆ 4.0
   - Clicks "INITIATE DEAL"

3. **Alice opens messenger**
   - Redirected to conversation
   - Product header shows: "Vintage Camera 2500" and "Seller: Bob ★★★★☆ 4.0"
   - Sends: "Hi Bob, is this still available?"

4. **Bob receives notification**
   - Sees: "[buyer] rated you ★..." (from previous ratings)
   - Goes to deals page
   - Finds Alice's deal, status shows:
     - Your Status: ⏳ Pending
     - Buyer Status: ⏳ Pending

5. **Bob marks deal done**
   - Clicks "✓ Mark as Done"
   - Confirms: "Mark this deal as done?"
   - Success: "Deal confirmed!"
   - Deal shows:
     - Your Status: ✓ Confirmed
     - Buyer Status: ⏳ Pending
     - Receives notification: "Alice confirmed your deal for Vintage Camera. Please confirm too!"

6. **Alice gets notified**
   - Sees notification: "Seller confirmed your deal for Vintage Camera. Please confirm too!"
   - Goes to deals page
   - Finds deal with "✓ Confirm Deal" button (green, prominent)

7. **Alice confirms deal**
   - Clicks "✓ Confirm Deal"
   - Confirms dialog: "Confirm this deal is complete?"
   - Redirected to `/public/buyer/rate.php?deal_id=123`

8. **Alice rates Bob**
   - Sees: "Product: Vintage Camera, Seller: Bob, Date: Apr 20, 2026"
   - Hovers over stars, sees rating names
   - Clicks 5th star (Excellent)
   - Types review: "Amazing product! Bob is very professional. Highly recommended!"
   - Clicks "Submit Rating"
   - Redirected to deals page with: "Rating submitted successfully!"

9. **Bob receives rating notification**
   - Sees notification: "Alice rated you ★★★★★ for Vintage Camera"
   - Goes to seller profile
   - Rating updated: (4.0×3 + 5.0×1) / 4 = 4.25 average
   - Shows: ★★★★½ 4.25 (4 reviews)
   - Can see Alice's review with full text

10. **Alice verifies Bob's profile**
    - Visits `/public/seller/profile.php?id=bob_id`
    - Sees: Bob's shop info, member since, location
    - Rating shows: ★★★★½ 4.25 (4 reviews)
    - Sees all 4 reviews including her own
    - Sees Bob's active products

11. **Future buyers see updated rating**
    - Browse market
    - Bob's products now show: ★★★★½ 4.25 (4)
    - New customers see Alice's positive review on profile

**Success Criteria**:
- ✅ All 10 steps completed without errors
- ✅ Notifications triggered correctly
- ✅ Ratings saved and displayed accurately
- ✅ User experience is smooth and intuitive
- ✅ No SQL errors or conflicts
- ✅ Star rating calculation correct
- ✅ Public profile accessible to all

---

## Performance Considerations

### Query Optimization
```sql
-- Verify indexes exist for performance
SHOW INDEX FROM deals;
-- Expected: indexes on seller_id, buyer_id, status, created_at

SHOW INDEX FROM ratings;
-- Expected: indexes on seller_id, deal_id, created_at, stars

SHOW INDEX FROM notifications;
-- Expected: indexes on user_id, is_read, type, created_at
```

### Expected Response Times
- Seller confirm deal: <100ms
- Buyer confirm deal: <100ms
- Submit rating (1-5 stars + review): <150ms
- Load seller profile: <200ms
- Get average rating: <50ms

---

## Security Testing

### Test 19: XSS Prevention in Rating Display

**Objective**: Verify review text sanitized to prevent XSS

**Setup**:
- Manually insert rating with XSS attempt

**Steps**:
```sql
INSERT INTO ratings (deal_id, buyer_id, seller_id, stars, review_text) 
VALUES (999, 1, 1, 5, '<script>alert("XSS")</script>');
```

1. View seller profile
2. Look for that review

**Expected Results**:
- ✅ Script tags displayed as text, not executed
- ✅ Output: `&lt;script&gt;alert("XSS")&lt;/script&gt;`

### Test 20: SQL Injection Prevention

**Objective**: Verify PDO prepared statements prevent SQL injection

**Setup**:
- Rating form

**Steps**:
1. Enter review: `'); DROP TABLE ratings; --`
2. Submit form
3. Check if ratings table still exists

**Expected Results**:
- ✅ Rating created with text as-is (no SQL execution)
- ✅ Ratings table NOT dropped
- ✅ PDO prepared statements prevent injection

---

## Deployment Checklist

- [ ] Database schema updated (partido_market.sql applied)
- [ ] Rating.php in /classes/
- [ ] Notification.php in /classes/
- [ ] init.php includes both classes
- [ ] public/buyer/rate.php created
- [ ] public/seller/profile.php created
- [ ] public/buyer/confirm_deal.php created
- [ ] public/seller/confirm_deal.php created
- [ ] seller/deals.php updated with confirmation button
- [ ] buyer/deals.php updated with confirmation button
- [ ] CSRF token generation working
- [ ] All tests passing
- [ ] Performance acceptable (<200ms load time)
- [ ] Security verified (no XSS, SQL injection, etc.)

---

## Troubleshooting

### Issue: "Rating already exists" on first submission
**Solution**: Check `deals.deal_id` is UNIQUE in ratings table, not duplicate deals created

### Issue: Seller not receiving notification
**Solution**: Verify `Notification::notifySellerRatingReceived()` called with correct seller_id (from sellers table, not users table)

### Issue: Star rating not showing on product cards
**Solution**: Verify product detail queries joining with sellers and ratings tables correctly

### Issue: CSRF token mismatch error
**Solution**: Ensure `generateCSRFToken()` creates consistent token and `$_SESSION['csrf_token']` saved before form display

---

## Sign-Off

**Tester Name**: ______________
**Date**: ______________
**Status**: ✅ All tests passed / ⚠️ Tests passed with notes / ❌ Tests failed

**Notes**:
_____________________________________________________________

