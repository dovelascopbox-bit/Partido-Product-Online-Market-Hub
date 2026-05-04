# STAGE 7-C IMPLEMENTATION GUIDE
**How to Apply Accessibility Fixes to All Pages**

---

## Overview

Stage 7-C makes the Partido platform fully accessible for keyboard and screen reader users. This guide explains what was done and how to apply it to remaining pages.

### What's Complete ✅
- `keyboard.js` - All accessibility utilities (500+ lines)
- `main.css` - Accessibility styles (250+ lines)  
- `footer.php` - Includes keyboard.js
- `login.php` - Full accessibility example
- `register.php` - Full accessibility example

### What Remains ⏳
- Apply same patterns to: market.php, product.php, messenger pages, dashboard pages, admin pages, seller pages

---

## Step-by-Step: Making a Page Accessible

### Step 1: Update HTML Structure

Replace generic page structure with landmarks:

```html
<!-- BEFORE -->
<body>
  <nav>Navigation</nav>
  <div class="content">Page content</div>
  <footer>Footer</footer>
</body>

<!-- AFTER -->
<body>
  <!-- Skip link -->
  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- Header landmark -->
  <header role="banner">
    <nav role="navigation" aria-label="Main navigation">
      <!-- Navigation content -->
    </nav>
  </header>

  <!-- Main content landmark with tabindex=-1 for focus -->
  <main id="main-content" role="main" tabindex="-1">
    <!-- Page content -->
  </main>

  <!-- Footer landmark -->
  <footer role="contentinfo">
    <!-- Footer content -->
  </footer>

  <!-- Include accessibility footer (keyboard.js + toolbar) -->
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
```

### Step 2: Fix All Forms

For every form on the page:

```html
<!-- BEFORE -->
<form>
  <input type="email" placeholder="Email">
  <input type="password" placeholder="Password">
  <button type="submit">Submit</button>
</form>

<!-- AFTER -->
<form id="my-form" novalidate>
  <div class="form-group">
    <label for="email" class="block mb-2">
      <span class="required-label">Email Address</span>
    </label>
    <input 
      type="email"
      id="email"
      name="email"
      required
      aria-required="true"
      aria-describedby="email-hint email-error"
      autocomplete="email"
      class="w-full px-4 py-2 border border-border rounded-lg"
    >
    <span id="email-hint" class="form-hint">Helper text here</span>
    <span id="email-error" class="error-msg" role="alert" hidden></span>
  </div>

  <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script>
  // Add validation
  document.getElementById('my-form').addEventListener('submit', function(e) {
    let isValid = true;

    // Clear errors
    this.querySelectorAll('input').forEach(field => clearFieldError(field));

    // Validate email
    const email = this.querySelector('#email');
    if (!email.value.includes('@')) {
      showFieldError(email, 'Please enter a valid email');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      focusFirstInvalidField(this);
    }
  });
</script>
```

### Step 3: Fix Images

Replace bare `<img>` tags with proper alt text:

```html
<!-- BEFORE (BAD) -->
<img src="product.jpg">
<img src="seller.jpg">
<img src="logo.png">

<!-- AFTER (GOOD) -->
<!-- Product image -->
<img src="product.jpg" alt="iPhone 15 Pro - product photo">

<!-- Seller profile -->
<img src="seller.jpg" alt="John Smith profile photo">

<!-- Logo -->
<img src="logo.png" alt="Partido Product Online Market Hub logo">

<!-- Decorative image -->
<img src="divider.png" alt="" aria-hidden="true">

<!-- Icon in button (aria-label on button, not image) -->
<button aria-label="Add to cart">
  <img src="cart-icon.png" alt="" aria-hidden="true">
</button>
```

### Step 4: Fix Modals & Dialogs

Convert click-only modals to accessible versions:

```html
<!-- BEFORE (Not Accessible) -->
<div id="modal" style="display:none;">
  <div>Confirm action?</div>
  <button onclick="modal.style.display='none'">Yes</button>
</div>

<script>
  function showModal() {
    document.getElementById('modal').style.display = 'block';
  }
</script>

<!-- AFTER (Accessible) -->
<div 
  id="confirm-modal"
  role="dialog"
  aria-modal="true"
  aria-labelledby="modal-title"
  aria-describedby="modal-desc"
  hidden>
  <h2 id="modal-title">Confirm Action</h2>
  <p id="modal-desc">Are you sure? This cannot be undone.</p>
  <button id="modal-confirm">Yes</button>
  <button id="modal-cancel">Cancel</button>
</div>

<script>
  // Open modal
  document.getElementById('open-btn').addEventListener('click', () => {
    openModal('confirm-modal', 'open-btn');
  });

  // Close modal
  document.getElementById('modal-cancel').addEventListener('click', () => {
    closeModal('confirm-modal');
  });

  // Escape key handled automatically
</script>
```

### Step 5: Fix Buttons & Links

Ensure all interactive elements are proper semantic elements:

```html
<!-- BEFORE (NOT Accessible) -->
<div onclick="doSomething()" style="cursor: pointer;">Click me</div>
<span onclick="goHome()">Home</span>

<!-- AFTER (Accessible) -->
<!-- For actions (not navigation): use <button> -->
<button onclick="doSomething()">Click me</button>

<!-- For navigation: use <a href> -->
<a href="/home">Home</a>

<!-- Button with icon -->
<button aria-label="Delete item">
  <span aria-hidden="true">🗑️</span>
</button>

<!-- Link with tooltip -->
<a href="/profile" title="View your profile">
  Profile
</a>
```

### Step 6: Fix Status Badges

Never use color alone for status:

```html
<!-- BEFORE (BAD) -->
<span class="badge bg-green-100 text-green-800">Available</span>
<span class="badge bg-yellow-100 text-yellow-800">Pending</span>

<!-- AFTER (GOOD) -->
<span class="badge badge-success">
  <span aria-hidden="true">✓</span>
  <span>Available</span>
</span>

<span class="badge badge-warning">
  <span aria-hidden="true">⧖</span>
  <span>Pending</span>
</span>

<span class="badge badge-error">
  <span aria-hidden="true">✗</span>
  <span>Unavailable</span>
</span>
```

### Step 7: Add Screen Reader Announcements

For dynamic content updates:

```javascript
// When product is added to cart
showToast('Product added to cart!', 'success');

// When there's an error
showToast('Error: Unable to process payment', 'error');

// When page content updates
announceToScreenReader('5 new messages loaded');

// When user does something wrong
announceError('Please fill all required fields');
```

---

## Common Page Patterns

### Pattern 1: Product Card with "Buy" Button

```html
<!-- Product Card -->
<div class="product-card">
  <!-- Product Image -->
  <img 
    src="product.jpg" 
    alt="iPhone 15 Pro 256GB - product photo"
    class="product-image"
  >
  
  <!-- Product Details -->
  <h3>iPhone 15 Pro 256GB</h3>
  <p>Price: $999</p>
  <p>Seller: <a href="/seller/123">John's Electronics</a></p>
  
  <!-- Status Badge with Icon + Text (not just color) -->
  <div>
    <span class="badge badge-success">
      <span aria-hidden="true">✓</span>
      <span>Available</span>
    </span>
  </div>
  
  <!-- Buy Button -->
  <button 
    type="button"
    class="btn btn-primary"
    aria-label="Buy iPhone 15 Pro 256GB from John's Electronics"
    onclick="initiateDeal(123)">
    Buy Now
  </button>
</div>
```

### Pattern 2: Dropdown Menu

```html
<!-- Profile Dropdown -->
<div class="dropdown">
  <button 
    id="profile-btn"
    aria-expanded="false"
    aria-haspopup="true"
    aria-controls="profile-menu">
    Profile ▼
  </button>

  <div id="profile-menu" role="menu" hidden>
    <a href="/profile" role="menuitem">View Profile</a>
    <a href="/settings" role="menuitem">Settings</a>
    <a href="/logout" role="menuitem">Logout</a>
  </div>
</div>

<script>
  // Setup keyboard navigation
  setupDropdownMenu('profile-btn', 'profile-menu');
</script>
```

### Pattern 3: Star Rating Widget

```html
<!-- Star Rating -->
<fieldset>
  <legend>Rate this seller</legend>
  <div class="star-rating" role="group">
    <button type="button" class="star-btn" data-value="1" aria-label="1 out of 5 stars" aria-pressed="false">★</button>
    <button type="button" class="star-btn" data-value="2" aria-label="2 out of 5 stars" aria-pressed="false">★</button>
    <button type="button" class="star-btn" data-value="3" aria-label="3 out of 5 stars" aria-pressed="false">★</button>
    <button type="button" class="star-btn" data-value="4" aria-label="4 out of 5 stars" aria-pressed="false">★</button>
    <button type="button" class="star-btn" data-value="5" aria-label="5 out of 5 stars" aria-pressed="false">★</button>
  </div>
  <input type="hidden" name="rating" id="rating-value">
  <p id="rating-announce" class="sr-only" aria-live="polite"></p>
</fieldset>

<script>
  setupStarRating('.star-rating', 'rating-value');
</script>
```

### Pattern 4: Message/Chat Interface

```html
<!-- Message List -->
<div 
  role="log" 
  aria-label="Conversation messages"
  aria-live="polite"
  class="message-list">
  
  <!-- Individual Message -->
  <div class="message">
    <img 
      src="avatar.jpg" 
      alt="John Smith profile photo"
      class="avatar"
    >
    <div>
      <strong>John Smith</strong>
      <p>Is this item still available?</p>
      <time>2:45 PM</time>
    </div>
  </div>
</div>

<!-- Message Input Form -->
<form id="message-form" novalidate>
  <label for="message-input" class="sr-only">Your message</label>
  <input 
    type="text"
    id="message-input"
    name="message"
    placeholder="Type your message..."
    required
    aria-required="true"
  >
  <button type="submit" aria-label="Send message">Send</button>
</form>

<script>
  document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const message = document.getElementById('message-input').value;
    
    // Send message via API
    fetch('/send-message', {
      method: 'POST',
      body: JSON.stringify({message}),
      headers: {'Content-Type': 'application/json'}
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        this.reset();
        showToast('Message sent!', 'success');
        announceToScreenReader(`Message sent: ${message}`);
      }
    });
  });
</script>
```

---

## Checklist for Each Page

Use this for every page you fix:

```
Landmarks
[ ] Has <header role="banner">
[ ] Has <nav role="navigation" aria-label="...">
[ ] Has <main id="main-content" role="main" tabindex="-1">
[ ] Has <footer role="contentinfo">
[ ] Has skip link <a href="#main-content" class="skip-link">

Forms
[ ] Every <input> has <label for="...">
[ ] Required fields have aria-required="true"
[ ] Error messages use role="alert"
[ ] Error messages linked via aria-describedby
[ ] Form submits with keyboard only
[ ] First invalid field gets focus on error

Images
[ ] All <img> have alt attribute
[ ] Decorative images have alt="" and aria-hidden="true"
[ ] Product/profile images have descriptive alt text

Interactive Elements
[ ] All buttons use <button> element
[ ] All links use <a href> element
[ ] Buttons without text have aria-label
[ ] Modals use role="dialog" and aria-modal="true"
[ ] Dropdowns use role="menu" and keyboard navigation

Content
[ ] No status indicated by color alone
[ ] All badges have icon + color + text
[ ] Messages use aria-live regions
[ ] Dynamic content announced

Testing
[ ] Tab through entire page - no mouse needed
[ ] Focus indicator visible on all elements
[ ] NVDA/screen reader reads content correctly
[ ] axe DevTools shows no errors
```

---

## Files Modified in Stage 7-C

### New Files Created
- `/assets/js/keyboard.js` - 500+ lines, all utilities
- `/STAGE7C_COMPLETION_SUMMARY.md` - Complete reference
- `/STAGE7C_QUICK_REFERENCE.md` - Developer quick reference
- `/STAGE7C_TESTING_GUIDE.md` - Testing procedures

### Files Updated
- `/assets/css/main.css` - Added 250+ lines of accessibility styles
- `/includes/footer.php` - Added keyboard.js include
- `/public/login.php` - Full accessibility implementation
- `/public/register.php` - Full accessibility implementation

### Available for Use
- `keyboard.js` functions in any PHP page via `/assets/js/keyboard.js`
- Accessibility CSS classes via `/assets/css/main.css`
- Form validation helpers already included in footer.php

---

## Before & After Comparison

### Before Stage 7-C
```
❌ No keyboard navigation
❌ Screen readers can't read content
❌ Focus not visible
❌ Forms have no error handling
❌ Images lack alt text
❌ Color-only status indicators
❌ No landmarks
```

### After Stage 7-C
```
✅ Full keyboard navigation (Tab, Arrow, Enter, Escape)
✅ Screen readers announce all content
✅ Focus always visible (3px blue outline)
✅ Form errors announced and focused
✅ All images have descriptive alt text
✅ Status has icon + color + text
✅ Proper page landmarks (header, nav, main, footer)
```

---

## Accessibility Utilities Reference

All these functions are automatically available on every page:

```javascript
// Form validation
showFieldError(fieldId, 'Error message')     // Show field error
clearFieldError(fieldId)                      // Clear field error
focusFirstInvalidField(form)                  // Focus first invalid

// Screen reader announcements
announceToScreenReader('Message here')        // Polite announcement
announceError('Error message')                // Urgent error

// Modals & dialogs
openModal(modalId, triggerId)                 // Open with focus trap
closeModal(modalId)                           // Close, restore focus
trapFocus(element)                            // Manual focus trap

// Interactive widgets
setupDropdownMenu(triggerId, menuId)          // Dropdown with keyboard nav
setupStarRating(selector, inputId)            // Star rating widget
setupTabPanel(tabSelector, panelSelector)    // Tab navigation
showToast(message, type, duration)            // Notification toast
```

---

## Troubleshooting

### Problem: Page isn't keyboard navigable
**Solution**: 
- Check that all interactive elements are semantic (`<button>`, `<a>`, `<input>`)
- Check there are no elements with `tabindex="0"` or `tabindex="-1"` (except main)
- Check that form submit works with Enter key
- Use setupDropdownMenu() for custom dropdowns

### Problem: Screen reader doesn't read content
**Solution**:
- Check that all form labels use `<label for="id">`
- Check that buttons have text or `aria-label`
- Check that images have `alt` text
- Check that landmarks are properly tagged

### Problem: Focus not visible
**Solution**:
- Check that CSS doesn't have `outline: none`
- Check that `:focus { outline: 3px solid blue; }` exists
- Ensure all focusable elements have visible focus indicator

### Problem: Form errors not announced
**Solution**:
- Use showFieldError() function from keyboard.js
- Check that error has `role="alert"` and `aria-live="assertive"`
- Check that field is linked to error via `aria-describedby`

---

## Quality Assurance Checklist

Before considering a page "accessible", verify:

```
WCAG 2.1 Level AA Compliance

Keyboard:
  [ ] All functionality available via keyboard
  [ ] No keyboard traps
  [ ] Tab order is logical
  [ ] Focus indicator visible

Visual:
  [ ] 4.5:1 minimum color contrast
  [ ] Focus indicators clear
  [ ] Text is readable
  [ ] No information by color alone

Screen Reader:
  [ ] Page structure announced (landmarks)
  [ ] Form labels announced
  [ ] Errors announced
  [ ] All images announced
  [ ] Interactive elements labeled

Content:
  [ ] Headings have proper hierarchy
  [ ] Lists use <ul>/<ol>/<li>
  [ ] Tables have <th> headers
  [ ] Video has captions (Phase 2)

Status:
  [ ] axe DevTools shows no errors
  [ ] Lighthouse accessibility score 90+
  [ ] NVDA can navigate full page
  [ ] All WCAG criteria met
```

---

## Next Steps

1. **Login & Register** ✅ Already done - use as examples
2. **Market/Product Pages** - Apply same patterns
3. **Messenger Pages** - Use aria-live for messages
4. **Dashboard Pages** - Ensure all tables and charts accessible
5. **Admin Pages** - Make sure data tables are navigable
6. **Seller Pages** - Apply forms accessibility to product editors
7. **Test Everything** - Run through testing guide on each page

---

## Additional Resources

- **WCAG 2.1 Checklist**: https://www.w3.org/WAI/WCAG21/Techniques/
- **WebAIM Articles**: https://webaim.org/articles/
- **A11y Project**: https://www.a11yproject.com/
- **MDN Web Docs**: https://developer.mozilla.org/en-US/docs/Web/Accessibility
- **Deque University**: https://dequeuniversity.com/

---

*Stage 7-C Implementation Guide*  
*Updated: April 22, 2026*
