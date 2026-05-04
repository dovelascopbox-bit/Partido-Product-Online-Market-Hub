# STAGE 7 TESTING GUIDE

**Partido Product Online Market Hub** - Comprehensive Testing
**Stage**: 7 (Accessibility, Responsiveness, Quality)
**Date**: April 2026

---

## 🎯 QUICK TEST (15 minutes)

Run this first to verify Stage 7 is working:

### ✅ Test 1: Accessibility Toolbar
1. Open any page
2. Look for ♿ icon in bottom-right corner
3. Click it → panel expands upward
4. Click "Dark Mode" toggle → page goes dark
5. Refresh page → Dark Mode still on  ✅ (settings persist)
6. Click accessibility icon again → panel closes
7. **Result**: Should be smooth, no errors

### ✅ Test 2: Error Pages
1. Visit: `http://localhost/nonexistent`
2. Should see 404 page with icon and helpful text
3. Visit: `http://localhost/classes/Auth.php`
4. Should see 403 Forbidden page  ✅
5. **Result**: Branded error pages appear

### ✅ Test 3: Keyboard Navigation
1. Load `http://localhost/public/index.php`
2. Press **Tab** repeatedly → see focus move through buttons
3. Focus should show on "Become a Seller" button (blue outline)
4. Press **Tab** until "Start Shopping" is focused
5. Press **Enter** → should navigate to register page  ✅
6. Press **Escape** → any open menu should close
7. **Result**: All navigation without mouse

### ✅ Test 4: Responsive Design
1. Open DevTools (F12)
2. Click mobile icon (toggle device toolbar)
3. Select **iPhone SE (375px)**:
   - "Become a Seller" button stacks below text ✅
   - Menu is mobile hamburger ✅
4. Increase to **iPad (768px)**:
   - Layout widens, 2-column grid appears ✅
5. Increase to **Desktop (1920px)**:
   - Full 4-column grid ✅
6. **Result**: No horizontal scrolling at any size

---

## 📋 COMPREHENSIVE TEST SUITE

Run this full test before releasing Stage 7.

### PART 1: ACCESSIBILITY FEATURES

#### Test 1.1: Dark Mode
```
Prerequisites: Fresh page load
Steps:
1. Click ♿ icon
2. Click "Dark Mode" toggle
Expected:
- Page background changes to dark gray
- Text changes to light color
- All colors inverted
Verify at breakpoints: 320px, 768px, 1920px
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.2: High Contrast Mode
```
Prerequisites: Dark Mode OFF (toggle off if needed)
Steps:
1. Click ♿ icon
2. Click "High Contrast" toggle
Expected:
- Pure black background
- Pure white text
- Yellow borders on buttons
- Links are underlined
- Dark Mode automatically turns OFF
Verify: Use WebAIM Contrast Checker to verify 7:1 ratio
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.3: Large Font Mode
```
Prerequisites: Fresh page
Steps:
1. Click ♿ icon
2. Click "Large Font" toggle
Expected:
- All text 20% bigger
- Buttons taller
- No text overflow on any page
Verify at these pages:
- [ ] /index.php (landing)
- [ ] /register.php
- [ ] /login.php
- [ ] /buyer/market.php
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.4: Dyslexia Font
```
Prerequisites: Fresh page
Steps:
1. Click ♿ icon
2. Click "Dyslexia Font" toggle
Expected:
- Font changes to OpenDyslexic
- Letter spacing increases
- Word spacing increases
- No justified text (all left-aligned)
Note: Combine with Large Font for best effect
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.5: Reading Guide
```
Prerequisites: Desktop only (disabled on touch)
Steps:
1. Click ♿ icon
2. Click "Reading Guide" toggle
3. Move mouse over text
Expected:
- Yellow horizontal bar follows cursor
- Bar positioned slightly above cursor (for reading)
- Clicking still works (guide doesn't block interactions)
- Reading guide appears ONLY on desktop
- Touch devices: guide disabled automatically
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.6: Reduce Motion
```
Prerequisites: Notice animations before test
Steps:
1. Click ♿ icon
2. Click "Reduce Motion" toggle
Expected:
- All page animations stop
- Slide-in animations disabled
- Loading spinners stop
- Transitions are instant
- Respects system prefers-reduced-motion
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.7: Settings Persistence
```
Prerequisites: Multiple accessibility modes enabled
Steps:
1. Enable: Dark Mode + Large Font + Dyslexia Font
2. Refresh page (F5)
Expected:
- All 3 modes still active
- Settings loaded from localStorage
3. Close browser window
4. Reopen Partido
Expected:
- All 3 modes still active
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 1.8: Reset All
```
Prerequisites: Multiple modes enabled
Steps:
1. Click ♿ icon
2. Scroll down to "Reset All" button
3. Click it
4. Confirm dialog: "Reset all accessibility settings?"
Expected:
- All modes turn OFF
- Page returns to normal colors/fonts/sizes
Result: ✅ PASS / ❌ FAIL _______
```

---

### PART 2: KEYBOARD NAVIGATION

#### Test 2.1: Tab Navigation
```
Prerequisites: /index.php open
Steps:
1. Press Tab
Expected:
- "Skip to main content" link highlights (usually hidden but appears on Tab)
2. Press Tab again, and again
Expected:
- Focus moves to "Become a Seller" button
- Then to "Start Shopping" button
- Then to other links in sequence
Verify: Focus ring always visible (blue outline 3px)
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 2.2: Shift+Tab (Reverse)
```
Prerequisites: Focused on "Start Shopping" button
Steps:
1. Press Shift + Tab
Expected:
- Focus moves backwards to "Become a Seller"
- Then to previous buttons
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 2.3: Enter Key
```
Prerequisites: /index.php open, focus on "Become a Seller" button
Steps:
1. Press Tab to focus "Become a Seller"
2. Press Enter
Expected:
- Navigate to register.php?role=seller
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 2.4: Escape Key
```
Prerequisites: Accessibility toolbar open
Steps:
1. Click ♿ icon (panel opens)
2. Press Escape
Expected:
- Panel closes
- Focus returns to ♿ icon
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 2.5: Full Page Navigation (No Mouse)
```
Prerequisites: /login.php open
Steps:
1. Unplug mouse or use keyboard-only mode
2. Press Tab to navigate through:
   - [ ] Email input
   - [ ] Password input
   - [ ] Login button
   - [ ] "Don't have account?" link
   - [ ] Accessibility toolbar
3. For each element, press Enter/Space
Expected:
- All functionality works via keyboard
- Focus always visible
- No focus traps (can always Tab away)
Result: ✅ PASS / ❌ FAIL _______
```

---

### PART 3: RESPONSIVE DESIGN

#### Test 3.1: Breakpoint - 320px (iPhone SE)
```
Open DevTools, Device: iPhone SE (320x640)
Pages to test:
- [ ] /index.php → 1 column, mobile menu
- [ ] /register.php → Full-width form, no horizontal scroll
- [ ] /buyer/market.php → 1 column product cards
- [ ] /admin/dashboard.php → Tables converted to cards
Expected: ZERO horizontal scrolling at any page
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 3.2: Breakpoint - 375px (iPhone 12)
```
Open DevTools, Device: iPhone 12 (375x667)
Pages to test:
- [ ] Accessibility toolbar fits in corner
- [ ] Buttons are 44x44px minimum
- [ ] Touch targets have spacing
Expected: All buttons easily tappable
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 3.3: Breakpoint - 768px (iPad Portrait)
```
Open DevTools, Device: iPad (768x1024)
- [ ] Sidebar collapses to icons only
- [ ] Product grid: 2 columns
- [ ] Messenger: Split view begins
- [ ] Tables become scrollable (not cards)
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 3.4: Breakpoint - 1024px (iPad Landscape)
```
- [ ] Sidebar shows labels
- [ ] Product grid: 3 columns
- [ ] Messenger: Full split view
- [ ] All features visible
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 3.5: Breakpoint - 1920px (Desktop)
```
- [ ] Product grid: 4 columns
- [ ] Sidebar full width
- [ ] All features visible
- [ ] No empty space
Result: ✅ PASS / ❌ FAIL _______
```

---

### PART 4: ERROR HANDLING

#### Test 4.1: 404 Not Found
```
Steps:
1. Visit: /nonexistent
2. Visit: /buyer/invalid-page.php
Expected:
- See branded 404 page
- Icon (🔍)
- Error code "404"
- Button to go back
- Button to go to home
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 4.2: 403 Forbidden
```
Steps:
1. Try direct access: /classes/Auth.php
2. Try: /includes/functions.php
3. Try: /config/database.php
Expected:
- See branded 403 page
- Icon (🚫)
- Error code "403"
- Message: "You don't have permission"
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 4.3: 500 Server Error
```
Steps:
1. Simulate error (optional - skip if prod)
Expected:
- See branded 500 page
- Icon (⚠️)
- Error code "500"
- Error reference ID shown
- "Please contact support" message
Result: ✅ PASS / ❌ FAIL _______
```

---

### PART 5: PERFORMANCE

#### Test 5.1: Image Compression
```
Prerequisites: Seller account with product
Steps:
1. Log in as seller
2. Go to add product
3. Upload a large image (5MB+ JPEG)
Expected:
- File compresses to < 150KB
- Image quality remains good
- File stored as product_XXX.jpg (always JPEG)
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 5.2: Page Load Time
```
Prerequisites: Performance profiler open
Steps:
1. Open DevTools Network tab
2. Load /buyer/market.php
Expected:
- Total load < 3 seconds
- Images lazy-loaded (loading="lazy")
- CSS/JS gzipped (check in Network → Size)
Result Load time: _______ seconds
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 5.3: Pagination
```
Steps:
1. As admin, add 25+ products via seller account
2. Go to /buyer/market.php
Expected:
- Only 12-20 products show per page
- Pagination controls at bottom
- "Previous" button disabled on page 1
- Page numbers clickable
3. Click page 2
Expected:
- URL changes to ?page=2
- New products load
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 5.4: Lazy Loading
```
Steps:
1. Open DevTools Network tab
2. Load /buyer/market.php
Expected:
- Images not loaded until scrolled into view
- Scroll down
Expected:
- Images load as you scroll
Result: ✅ PASS / ❌ FAIL _______
```

---

### PART 6: SECURITY HEADERS

#### Test 6.1: Check .htaccess Headers
```
Prerequisites: DevTools open
Steps:
1. Load any page
2. Open DevTools → Network
3. Click any page request
4. Scroll to Response Headers
Expected to see:
- [ ] X-Content-Type-Options: nosniff
- [ ] X-Frame-Options: SAMEORIGIN
- [ ] X-XSS-Protection: 1; mode=block
- [ ] Referrer-Policy: strict-origin-when-cross-origin
- [ ] Content-Security-Policy (permissive for Tailwind)
Result: ✅ PASS / ❌ FAIL _______
```

#### Test 6.2: CSRF Token Present
```
Steps:
1. Open /register.php
2. View page source (Ctrl+U)
3. Search for "csrf_token"
Expected:
- Hidden input with name="csrf_token" found
- Token is 64-character hex string
- Token is unique per page load
Result: ✅ PASS / ❌ FAIL _______
```

---

### PART 7: SCREEN READER (Optional - Advanced)

#### Test 7.1: NVDA (Windows Only)
```
Prerequisites: NVDA installed (free from nvaccess.org)
Steps:
1. Start NVDA
2. Open /index.php in browser
3. Listen to page read-out
Expected:
- Page title announced
- Headings announced with level (h1, h2, etc)
- Links announced with descriptive text
- Images announced with alt text (or "image")
- Form fields announced with labels
4. Press Tab, listen to element announcements
Expected:
- Button purposes announced
- Form field types announced
Result: ✅ PASS / ❌ FAIL _______
```

---

## 🐛 BUG REPORTING

If a test fails, report it:

```
Test Name: [Test 1.2: High Contrast Mode]
Status: ❌ FAIL
Browser: [Chrome 120, Firefox, Safari, Edge]
Device: [Desktop, iPhone 12, iPad]
OS: [Windows, macOS, Linux]

Steps to reproduce:
1. Click ♿ icon
2. Click High Contrast toggle
3. [specific action]

Expected:
[What should happen]

Actual:
[What actually happened]

Screenshot:
[Attach screenshot if possible]

Console Errors:
[Check DevTools console for errors]
```

---

## ✅ FINAL CHECKLIST

Before declaring Stage 7 complete, verify:

- [ ] All 8 accessibility tests pass
- [ ] All 5 keyboard navigation tests pass
- [ ] All 5 responsive design breakpoint tests pass
- [ ] All 3 error handling tests pass
- [ ] All 4 performance tests pass
- [ ] All 2 security tests pass
- [ ] Dark mode preference persists across page reloads
- [ ] Pagination works (20+ items)
- [ ] No errors in browser console (F12)
- [ ] No horizontal scrolling at any breakpoint
- [ ] Accessibility toolbar appears on all pages
- [ ] QUALITY.md and ACCESSIBILITY-GUIDE.md complete

---

## 📞 ESCALATION

If 5+ tests fail:
1. Check /logs/error.log for system errors
2. Verify database indexes applied: `STAGE7_INDEXES.sql`
3. Verify error handler included in init.php
4. Verify accessibility.js and accessibility.css loaded
5. Contact team

---

**Test Date**: _______________
**Tester**: _________________
**Result**: ✅ STAGE 7 COMPLETE / ❌ NEEDS FIXES
**Notes**: ___________________________________________________

---

**Version**: 7.0
**Last Updated**: April 23, 2026
