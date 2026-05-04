# STAGE 7-C - Quick Reference Guide
**Keyboard Navigation & ARIA/Screen Reader Support**

---

## Quick Checklist for Page Audits

### Every Page Must Have (Copy-paste this into every page)

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - Partido</title>
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <!-- MUST HAVE: Skip link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- MUST HAVE: Header landmark -->
    <header role="banner">
        <nav role="navigation" aria-label="Main navigation">
            <!-- Navigation content -->
        </nav>
    </header>

    <!-- MUST HAVE: Main content landmark with tabindex="-1" -->
    <main id="main-content" role="main" tabindex="-1">
        <!-- Page content -->
    </main>

    <!-- MUST HAVE: Footer landmark -->
    <footer role="contentinfo">
        <!-- Footer content -->
    </footer>

    <!-- MUST HAVE: Include footer.php for accessibility toolbar & keyboard.js -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
```

---

## Forms - Complete Pattern

```html
<!-- Form with proper error handling -->
<form id="my-form" novalidate>
    <!-- Email Field -->
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
        <span id="email-hint" class="form-hint">We'll never share your email.</span>
        <span id="email-error" class="error-msg" role="alert" hidden></span>
    </div>

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="block mb-2">
            <span class="required-label">Password</span>
        </label>
        <input 
            type="password"
            id="password"
            name="password"
            required
            aria-required="true"
            autocomplete="current-password"
            class="w-full px-4 py-2 border border-border rounded-lg"
        >
    </div>

    <button type="submit" class="btn btn-primary">
        Submit Form
    </button>
</form>

<script>
    // Form validation with keyboard.js
    document.getElementById('my-form').addEventListener('submit', (e) => {
        const form = e.target;
        let isValid = true;

        // Clear all errors first
        form.querySelectorAll('input').forEach(input => {
            clearFieldError(input);
        });

        // Validate email
        const email = form.querySelector('#email');
        if (!email.value.includes('@')) {
            showFieldError(email, 'Please enter a valid email address');
            isValid = false;
        }

        // Validate password
        const password = form.querySelector('#password');
        if (password.value.length < 8) {
            showFieldError(password, 'Password must be at least 8 characters');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            focusFirstInvalidField(form);
        }
    });
</script>
```

---

## Images - Alt Text Rules

```html
<!-- Product image -->
<img src="product.jpg" alt="iPhone 15 Pro - product photo" />

<!-- Seller profile photo -->
<img src="seller.jpg" alt="John Smith profile photo" />

<!-- Logo -->
<img src="logo.png" alt="Partido Product Online Market Hub logo" />

<!-- Decorative image (empty alt) -->
<img src="divider.png" alt="" aria-hidden="true" />

<!-- Icon that is decorative -->
<button aria-label="Add to cart">
    <img src="cart-icon.png" alt="" aria-hidden="true" />
    Add to Cart
</button>

<!-- Icon with text (icon doesn't need alt, button has aria-label) -->
<button aria-label="Search products">
    🔍
</button>

<!-- Empty state illustration -->
<img src="empty-state.png" alt="No products found illustration" />
```

---

## Modals & Dialogs

```html
<!-- Modal HTML -->
<div 
    id="confirm-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
    aria-describedby="modal-desc"
    hidden>
    <h2 id="modal-title">Confirm Deal Completion</h2>
    <p id="modal-desc">
        Are you sure? This cannot be undone.
    </p>
    <button id="modal-confirm-btn">Yes, Complete Deal</button>
    <button id="modal-cancel-btn">Cancel</button>
</div>

<script>
    // Open modal
    document.getElementById('open-modal-btn').addEventListener('click', () => {
        openModal('confirm-modal', 'open-modal-btn');
    });

    // Close modal
    document.getElementById('modal-cancel-btn').addEventListener('click', () => {
        closeModal('confirm-modal');
    });

    // Escape key automatically closes (keyboard.js handles this)
</script>
```

---

## Star Rating Widget

```html
<!-- Star Rating HTML -->
<fieldset>
    <legend>Rate your experience with this seller</legend>
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

---

## Dropdown Menu

```html
<!-- Dropdown Menu HTML -->
<button 
    id="profile-trigger"
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

<script>
    setupDropdownMenu('profile-trigger', 'profile-menu');
    // Handles: click, arrow keys, escape, focus management
</script>
```

---

## Status Badges - Never Color Alone

```html
<!-- ❌ BAD: Color only -->
<span class="badge badge-success">Available</span>

<!-- ✅ GOOD: Icon + Color + Text -->
<span class="badge badge-success">
    <span aria-hidden="true">✓</span>
    <span>Available</span>
</span>

<!-- Examples for other statuses -->
<span class="badge badge-warning">
    <span aria-hidden="true">⧖</span>
    <span>Pending</span>
</span>

<span class="badge badge-error">
    <span aria-hidden="true">✗</span>
    <span>Unavailable</span>
</span>
```

---

## Toast Notifications

```javascript
// Success notification
showToast('Product added to cart successfully!', 'success', 5000);

// Error notification  
showToast('Error: Unable to add product. Please try again.', 'error');

// Info notification
showToast('Your deal is pending confirmation.', 'info');

// Warning notification
showToast('You have 1 day left to confirm this deal.', 'warning');
```

---

## Accessibility Toolbar Integration

The accessibility toolbar is **already included** in footer.php. On every page, just:

1. Include landmarks (header, nav, main, footer)
2. Include footer.php at the end of body
3. The toolbar automatically appears with:
   - Dark Mode
   - High Contrast
   - Large Font
   - Dyslexia Font
   - Reading Guide
   - Reduce Motion
   - Reset All

---

## Focus Management Helpers

```javascript
// Get or create screen reader live region
const liveRegion = getOrCreateLiveRegion();

// Announce important information
announceToScreenReader('Your product was listed successfully');

// Announce errors
announceError('Email address is already registered');

// Focus first invalid field after form submission
focusFirstInvalidField(form);

// Show field-specific error
showFieldError('email', 'Please enter a valid email');

// Clear field error
clearFieldError('email');
```

---

## Keyboard Events Quick Reference

```javascript
// Listen to key events
document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        // Activate button/submit form
    }
    if (e.key === 'Escape') {
        // Close modal/dropdown
    }
    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
        // Navigate menu items
    }
    if (e.key === 'Tab') {
        // Focus management
    }
});
```

---

## Browser DevTools Accessibility Inspection

### Chrome/Edge DevTools
1. Open DevTools (F12)
2. Go to "Elements" tab
3. Right-click element → "Inspect"
4. Check "Accessibility" tree (shows ARIA roles, labels, etc.)
5. Look for warnings about missing alt text, labels, etc.

### Firefox DevTools
1. Open DevTools (F12)
2. Go to "Inspector" tab
3. Click "Accessibility" panel
4. See accessibility tree and check for issues

### Testing Checklist
- [ ] All focusable elements have visible focus indicator
- [ ] Tab order is logical (top to bottom, left to right)
- [ ] No keyboard traps (except intentional modals)
- [ ] All form fields have labels
- [ ] Error messages associated with fields
- [ ] Images have alt text
- [ ] Landmarks present (header, nav, main, footer)
- [ ] Proper heading hierarchy (h1 > h2 > h3)

---

## Screen Reader Testing Steps

### Using NVDA (Free, Windows)
1. Download NVDA: https://www.nvaccess.org/
2. Install and launch NVDA
3. Open your page in Firefox/Chrome
4. Press Insert+H for list of all headings
5. Navigate with Arrow keys
6. Tab through interactive elements
7. Check that all content is accessible

### Using VoiceOver (macOS/iOS)
1. Enable VoiceOver: Cmd+F5
2. Use VO+Arrow keys to navigate
3. VO+Space to activate buttons
4. VO+U for rotor (headings, links, etc.)

### What to Listen For
- [ ] Page title announced
- [ ] Heading hierarchy clear
- [ ] Form labels associated with inputs
- [ ] Error messages announced
- [ ] Alt text for images (or marked as decorative)
- [ ] Button purposes clear
- [ ] Link text makes sense out of context

---

## Common Mistakes to Avoid

```html
<!-- ❌ BAD: No label -->
<input type="email" placeholder="Email address">

<!-- ✅ GOOD: Has label -->
<label for="email">Email address</label>
<input type="email" id="email">

---

<!-- ❌ BAD: Color only -->
<span class="text-red-600">Error</span>

<!-- ✅ GOOD: Color + icon + text -->
<span class="text-red-600">❌ Error</span>

---

<!-- ❌ BAD: Click handler on div -->
<div onclick="handleDelete()" style="cursor: pointer;">Delete</div>

<!-- ✅ GOOD: Proper button element -->
<button onclick="handleDelete()">Delete</button>

---

<!-- ❌ BAD: Image without alt -->
<img src="product.jpg">

<!-- ✅ GOOD: Meaningful alt text -->
<img src="product.jpg" alt="iPhone 15 Pro - product photo">

---

<!-- ❌ BAD: Link with no href -->
<a onclick="openModal()">Open Dialog</a>

<!-- ✅ GOOD: Button for non-navigation actions -->
<button onclick="openModal()">Open Dialog</button>

---

<!-- ❌ BAD: Modal without focus trap -->
<div role="dialog" id="modal">Content</div>

<!-- ✅ GOOD: Modal with focus trap -->
<script>openModal('modal', 'trigger-button');</script>
```

---

## Testing Checklist Template

Use this for every page you audit:

```
Page: ________________
Date: ________________

KEYBOARD NAVIGATION
[ ] Can tab through all interactive elements
[ ] Tab order is logical (top-to-bottom, left-to-right)
[ ] Can submit forms with keyboard only
[ ] No keyboard traps (except modals)
[ ] Focus indicator visible on all focusable elements
[ ] Skip link works and jumps to main content

LANDMARKS
[ ] Page has <header role="banner">
[ ] Page has <nav role="navigation">
[ ] Page has <main id="main-content" role="main">
[ ] Page has <footer role="contentinfo">
[ ] Skip link present and functional

FORMS
[ ] All inputs have <label>
[ ] Required fields marked with * and aria-required="true"
[ ] Error messages shown clearly
[ ] Errors announced to screen readers (aria-live)
[ ] Focus moves to first invalid field on submit

IMAGES
[ ] Product images have descriptive alt text
[ ] Profile photos have person name in alt
[ ] Decorative images have alt=""
[ ] Icons have aria-label on parent or alt="" + aria-hidden

SCREEN READER (Test with NVDA/VoiceOver)
[ ] Page title announces correctly
[ ] Landmarks and regions announce
[ ] Heading hierarchy is correct
[ ] Form labels associated with inputs
[ ] Error messages announced
[ ] Button purposes clear
[ ] All content accessible via screen reader

STATUS/BADGES
[ ] Never use color alone for status
[ ] Always include icon + text + color
[ ] Example: ✓ Available (not just green color)

MODALS
[ ] Focus trapped inside modal
[ ] Escape key closes modal
[ ] Focus returns to trigger button
[ ] Modal has aria-modal="true"
[ ] Modal has aria-labelledby and aria-describedby

NOTES
_______________________________________________________________
_______________________________________________________________
```

---

## JavaScript Templates

### Form Validation Template
```javascript
const form = document.getElementById('my-form');

form.addEventListener('submit', (e) => {
    let isValid = true;

    // Clear all previous errors
    form.querySelectorAll('input').forEach(field => {
        clearFieldError(field);
    });

    // Validate each field
    const email = form.querySelector('[type="email"]');
    if (!validateEmail(email.value)) {
        showFieldError(email, 'Invalid email address');
        isValid = false;
    }

    // Stop submission if invalid
    if (!isValid) {
        e.preventDefault();
        focusFirstInvalidField(form);
        return;
    }

    // Form is valid, allow submission
});
```

### Modal Trigger Template
```javascript
const openBtn = document.getElementById('open-btn');
const modal = document.getElementById('my-modal');
const closeBtn = document.getElementById('close-btn');

openBtn.addEventListener('click', () => {
    openModal('my-modal', 'open-btn');
});

closeBtn.addEventListener('click', () => {
    closeModal('my-modal');
});

// Escape key handled automatically by keyboard.js
```

---

## Resources

- **WCAG 2.1 Guidelines**: https://www.w3.org/WAI/WCAG21/quickref/
- **MDN ARIA Guide**: https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA
- **WebAIM**: https://webaim.org/
- **A11y Project**: https://www.a11yproject.com/
- **NVDA Screen Reader**: https://www.nvaccess.org/

---

*Stage 7-C Quick Reference*  
*Updated: April 22, 2026*
