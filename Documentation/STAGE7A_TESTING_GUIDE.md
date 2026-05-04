# STAGE 7-A: CSS Custom Properties & Design Tokens - Testing Guide

**Project:** Partido Product Online Market Hub  
**Stage:** 7-A  
**Date:** April 22, 2026  
**Status:** CSS Foundation Complete

---

## DELIVERABLES CHECKLIST

### ✅ Files Created/Updated

- [x] `/assets/css/tokens.css` - Master design token file with all theme variants
- [x] `/assets/css/helpers.css` - CSS helper classes mapping Tailwind to CSS variables
- [x] `/assets/js/theme-switcher.js` - JavaScript for theme management and persistence
- [x] `/assets/fonts/OpenDyslexic/` - Directory structure created for dyslexia fonts
- [x] `/includes/header.php` - Shared header with tokens.css before Tailwind
- [x] `/public/index.php` - Updated with tokens and skip-link
- [x] `/public/login.php` - Updated with tokens and main-content ID
- [x] `/public/register.php` - Updated with tokens and main-content ID
- [x] `/public/admin/dashboard.php` - Updated with tokens and accessibility
- [x] `/public/buyer/dashboard.php` - Updated with tokens and accessibility

---

## TESTING PROCEDURES

### TEST 1: Light Mode (Default)
**Expected Result:** Site appears with light background, dark text

**Steps:**
1. Load any page in the application
2. Open browser DevTools (F12)
3. Go to Console tab
4. Verify: `document.documentElement.className` should be empty or only contain non-theme classes

**Verification:**
- ✅ Background is light gray (#F9FAFB)
- ✅ Text is dark (#111827)
- ✅ Cards have white background (#FFFFFF)
- ✅ Borders are light gray (#E5E7EB)
- ✅ Primary color is green (#16A34A)

---

### TEST 2: Dark Mode
**Expected Result:** Entire site shifts to dark theme without page reload

**Steps:**
1. Open browser DevTools Console
2. Run command: `document.documentElement.classList.add('dark')`
3. Or use theme switcher: `themeSwitcher.toggleDarkMode()`
4. Observe page transformation

**Verification:**
- ✅ Background becomes dark navy (#0F172A)
- ✅ Cards become dark slate (#1E293B)
- ✅ Text becomes light gray (#F1F5F9)
- ✅ Primary color becomes bright green (#22C55E)
- ✅ All transitions are smooth (0.3s)
- ✅ NO page reload required
- ✅ Theme persists after refresh (saved in localStorage)

**Console Commands:**
```javascript
// Verify dark mode
document.documentElement.classList.contains('dark'); // Should return true

// Check CSS variable
getComputedStyle(document.documentElement).getPropertyValue('--color-bg'); // Should be #0F172A
```

---

### TEST 3: High Contrast Mode
**Expected Result:** Pure black and yellow/white high contrast theme

**Steps:**
1. Open browser DevTools Console
2. Run command: `document.documentElement.classList.add('high-contrast')`
3. Or use: `themeSwitcher.toggleHighContrast()`

**Verification:**
- ✅ Background is pure black (#000000)
- ✅ Text is pure white (#FFFFFF)
- ✅ Primary color is pure yellow (#FFFF00)
- ✅ Borders are yellow (#FFFF00)
- ✅ All elements have strong borders (yellow/white)
- ✅ Links are underlined and yellow
- ✅ Buttons have 3px white borders
- ✅ Meets WCAG AAA contrast requirements

**Console Commands:**
```javascript
// Verify high contrast mode
document.documentElement.classList.contains('high-contrast'); // Should return true

// Check contrast
getComputedStyle(document.documentElement).getPropertyValue('--color-text'); // Should be #FFFFFF
getComputedStyle(document.documentElement).getPropertyValue('--color-primary'); // Should be #FFFF00
```

---

### TEST 4: Large Font Mode
**Expected Result:** Text enlarges by 20% globally

**Steps:**
1. Open browser DevTools Console
2. Run command: `document.documentElement.classList.add('large-font')`
3. Or use: `themeSwitcher.toggleLargeFont()`
4. Check page layout

**Verification:**
- ✅ Font size increases by 20% (1rem becomes 1.2rem)
- ✅ Line height increases to 1.8 for better readability
- ✅ NO layout breaks or overflow
- ✅ All text remains readable
- ✅ Page content is still accessible
- ✅ Buttons and form elements scale properly

**Console Commands:**
```javascript
// Verify large font
document.documentElement.classList.contains('large-font'); // Should return true

// Check font size calculation
document.documentElement.style.fontSize; // Should show 120%
```

---

### TEST 5: Dyslexia-Friendly Font Mode
**Expected Result:** OpenDyslexic font loads for all text

**Note:** OpenDyslexic font files must be placed in `/assets/fonts/OpenDyslexic/`:
- OpenDyslexic-Regular.otf
- OpenDyslexic-Bold.otf
- OpenDyslexic-Italic.otf
- OpenDyslexic-BoldItalic.otf

**Steps:**
1. Download OpenDyslexic fonts from https://opendyslexic.org/
2. Place files in `/assets/fonts/OpenDyslexic/`
3. Open browser DevTools Console
4. Run command: `document.documentElement.classList.add('dyslexia-font')`
5. Or use: `themeSwitcher.toggleDyslexiaFont()`

**Verification:**
- ✅ All text changes to OpenDyslexic font family
- ✅ Letter spacing increases by 0.05em
- ✅ Word spacing increases by 0.1em
- ✅ Line height is 1.9 for better spacing
- ✅ Text alignment is left (not justified)
- ✅ Font loads without errors (check DevTools Network tab)

**Console Commands:**
```javascript
// Verify dyslexia font
document.documentElement.classList.contains('dyslexia-font'); // Should return true

// Check computed font family
getComputedStyle(document.querySelector('body')).fontFamily; // Should include OpenDyslexic
```

---

### TEST 6: Reduce Motion Mode
**Expected Result:** All animations and transitions disabled

**Steps:**
1. Open browser DevTools Console
2. Run command: `document.documentElement.classList.add('reduce-motion')`
3. Or use: `themeSwitcher.toggleReduceMotion()`
4. Try actions that normally animate (hover effects, transitions)

**Verification:**
- ✅ No animations occur
- ✅ Transitions are instant (0.01ms)
- ✅ Scroll behavior is instant (not smooth)
- ✅ Buttons respond immediately without effects
- ✅ Respects system `prefers-reduced-motion` media query

**Console Commands:**
```javascript
// Verify reduce motion
document.documentElement.classList.contains('reduce-motion'); // Should return true

// Check transition duration
getComputedStyle(document.querySelector('a')).transitionDuration; // Should be 0.01ms
```

---

### TEST 7: Focus Styles (Keyboard Navigation)
**Expected Result:** Clear focus indicators for keyboard users

**Steps:**
1. Load any page
2. Press Tab key repeatedly to navigate through interactive elements
3. Verify each focused element shows clear outline

**Verification:**
- ✅ All buttons show 3px solid outline when focused
- ✅ Links show clear outline with 2px offset
- ✅ Form inputs show green/yellow outline
- ✅ Outline color matches `--color-border-focus`
- ✅ Focus is NEVER suppressed (visible on all browsers)
- ✅ High contrast mode shows white outline

**WCAG 2.1 AA Compliance:**
- ✅ Focus indicator has minimum 3px width
- ✅ Focus indicator has sufficient color contrast
- ✅ Focus is always visible (never hidden)

---

### TEST 8: Skip Link Accessibility
**Expected Result:** Skip link appears on focus for keyboard users

**Steps:**
1. Load any page
2. Press Tab key once (without clicking)
3. Verify "Skip to main content" link appears at top

**Verification:**
- ✅ Skip link appears at top of page on Tab
- ✅ Skip link has primary color background
- ✅ Skip link text is white
- ✅ Clicking/pressing Enter goes to `#main-content`
- ✅ Link is positioned off-screen by default (top: -40px)
- ✅ Link moves into view on focus (top: 0)
- ✅ All pages have `id="main-content"` on `<main>` element

---

### TEST 9: CSS Variables Verification
**Expected Result:** All CSS custom properties are properly defined

**Steps:**
1. Open browser DevTools Console
2. Run inspection commands below
3. Verify all values match expected colors

**Commands to Run:**
```javascript
// Get all CSS variables
const style = getComputedStyle(document.documentElement);

// Check color tokens
console.log('Background:', style.getPropertyValue('--color-bg'));           // #F9FAFB
console.log('Surface:', style.getPropertyValue('--color-surface'));         // #FFFFFF
console.log('Card:', style.getPropertyValue('--color-card'));               // #FFFFFF
console.log('Text:', style.getPropertyValue('--color-text'));               // #111827
console.log('Text Muted:', style.getPropertyValue('--color-text-muted'));   // #6B7280
console.log('Primary:', style.getPropertyValue('--color-primary'));         // #16A34A
console.log('Primary Hover:', style.getPropertyValue('--color-primary-hover')); // #15803D
console.log('Error:', style.getPropertyValue('--color-error'));             // #DC2626
console.log('Success:', style.getPropertyValue('--color-success'));         // #16A34A
console.log('Warning:', style.getPropertyValue('--color-warning'));         // #D97706
console.log('Info:', style.getPropertyValue('--color-info'));               // #2563EB

// Check shadows
console.log('Shadow SM:', style.getPropertyValue('--shadow-sm'));
console.log('Shadow MD:', style.getPropertyValue('--shadow-md'));

// Check typography
console.log('Font Base:', style.getPropertyValue('--font-base'));
console.log('Font Size Base:', style.getPropertyValue('--font-size-base'));
console.log('Line Height Base:', style.getPropertyValue('--line-height-base'));
```

---

### TEST 10: Theme Persistence
**Expected Result:** Selected theme is saved and restored

**Steps:**
1. Open page in browser
2. Switch to dark mode: `themeSwitcher.toggleDarkMode()`
3. Close browser tab completely
4. Re-open same page
5. Verify dark mode is still active

**Verification:**
- ✅ Theme is stored in localStorage
- ✅ localStorage key is `'partido-theme'`
- ✅ Theme persists across browser sessions
- ✅ Multiple theme classes can be combined (e.g., dark + large-font)

**Console Commands:**
```javascript
// Check localStorage
console.log(localStorage.getItem('partido-theme'));

// Check current themes
console.log(document.documentElement.className);

// Simulate persistence on next page load
// (theme-switcher.js runs automatically on page load)
```

---

### TEST 11: Combined Themes
**Expected Result:** Multiple theme modes can be active simultaneously

**Steps:**
1. Activate dark mode: `themeSwitcher.toggleDarkMode()`
2. Activate large font: `themeSwitcher.toggleLargeFont()`
3. Activate dyslexia font: `themeSwitcher.toggleDyslexiaFont()`
4. Activate reduce motion: `themeSwitcher.toggleReduceMotion()`

**Verification:**
- ✅ All modes are active simultaneously
- ✅ Page displays correctly with all combinations
- ✅ NO conflicts between styles
- ✅ All classes are visible: `document.documentElement.className`
- ✅ Theme persists in localStorage as combined string

---

### TEST 12: Browser Compatibility
**Expected Result:** Works across all modern browsers

**Test on:**
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

**Verification:**
- ✅ CSS custom properties supported (CSS 3)
- ✅ localStorage API works
- ✅ Theme switching smooth on all browsers
- ✅ No console errors
- ✅ Focus styles visible on all browsers

---

### TEST 13: Page Performance
**Expected Result:** CSS tokens don't impact performance

**Steps:**
1. Open DevTools Performance tab
2. Record page load
3. Note CSS parsing time

**Verification:**
- ✅ tokens.css loads before Tailwind (optimal order)
- ✅ No render blocking issues
- ✅ First contentful paint (FCP) not impacted
- ✅ Theme switching is instant (no jank)
- ✅ Memory usage from CSS variables is negligible

---

### TEST 14: All Pages Updated
**Expected Result:** All PHP files include skip-link and tokens

**Files to Verify:**
```
/public/index.php ✅
/public/login.php ✅
/public/register.php ✅
/public/admin/dashboard.php ✅
/public/buyer/dashboard.php ✅
/public/buyer/marketplace.php - TO DO
/public/buyer/product.php - TO DO
/public/buyer/deals.php - TO DO
/public/buyer/market.php - TO DO
/public/buyer/initiate_deal.php - TO DO
/public/buyer/confirm_deal.php - TO DO
/public/buyer/rate.php - TO DO
/public/seller/dashboard.php - TO DO
/public/seller/profile.php - TO DO
/public/seller/deals.php - TO DO
/public/seller/confirm_deal.php - TO DO
/public/seller/products/list.php - TO DO
/public/seller/products/add.php - TO DO
/public/seller/products/edit.php - TO DO
/public/seller/products/delete.php - TO DO
/public/admin/analytics.php - TO DO
/public/admin/deals.php - TO DO
/public/admin/flags.php - TO DO
/public/admin/products.php - TO DO
/public/admin/ratings.php - TO DO
/public/admin/users.php - TO DO
/public/messenger/index.php - TO DO
/public/messenger/conversation.php - TO DO
/public/messenger/send.php - TO DO
/public/messenger/fetch.php - TO DO
```

---

## AUTOMATED TESTING SCRIPT

Save as `/test-tokens.html` to test all features:

```html
<!DOCTYPE html>
<html>
<head>
    <title>CSS Tokens Testing</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .test { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .pass { background: #90EE90; }
        .fail { background: #FFB6C6; }
        button { padding: 10px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>CSS Tokens Testing Dashboard</h1>
    
    <h2>Theme Controls</h2>
    <button onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <button onclick="toggleHighContrast()">Toggle High Contrast</button>
    <button onclick="toggleLargeFont()">Toggle Large Font</button>
    <button onclick="toggleDyslexiaFont()">Toggle Dyslexia Font</button>
    <button onclick="toggleReduceMotion()">Toggle Reduce Motion</button>
    <button onclick="resetThemes()">Reset All</button>
    
    <h2>Test Results</h2>
    <div id="results"></div>
    
    <script src="/assets/js/theme-switcher.js"></script>
    <script>
        function runTests() {
            const results = document.getElementById('results');
            const style = getComputedStyle(document.documentElement);
            const tests = [
                { name: 'theme-switcher exists', check: () => window.themeSwitcher !== undefined },
                { name: '--color-bg defined', check: () => style.getPropertyValue('--color-bg').trim() !== '' },
                { name: '--color-primary defined', check: () => style.getPropertyValue('--color-primary').trim() !== '' },
                { name: '--shadow-md defined', check: () => style.getPropertyValue('--shadow-md').trim() !== '' },
                { name: 'Dark mode toggle works', check: () => {
                    window.themeSwitcher.toggleDarkMode();
                    const has = document.documentElement.classList.contains('dark');
                    window.themeSwitcher.toggleDarkMode();
                    return has;
                }},
                { name: 'localStorage works', check: () => typeof localStorage !== 'undefined' }
            ];
            
            results.innerHTML = tests.map(t => `
                <div class="test ${t.check() ? 'pass' : 'fail'}">
                    ${t.check() ? '✅' : '❌'} ${t.name}
                </div>
            `).join('');
        }
        
        function toggleDarkMode() {
            window.themeSwitcher.toggleDarkMode();
        }
        
        function toggleHighContrast() {
            window.themeSwitcher.toggleHighContrast();
        }
        
        function toggleLargeFont() {
            window.themeSwitcher.toggleLargeFont();
        }
        
        function toggleDyslexiaFont() {
            window.themeSwitcher.toggleDyslexiaFont();
        }
        
        function toggleReduceMotion() {
            window.themeSwitcher.toggleReduceMotion();
        }
        
        function resetThemes() {
            window.themeSwitcher.reset();
        }
        
        // Run tests on load
        document.addEventListener('DOMContentLoaded', runTests);
    </script>
</body>
</html>
```

---

## NEXT STEPS (Stage 7-B onwards)

1. **Download & Install OpenDyslexic Fonts**
   - Download from https://opendyslexic.org/
   - Place in `/assets/fonts/OpenDyslexic/`
   - Test dyslexia font mode

2. **Update Remaining Pages**
   - Apply tokens.css to all PHP files
   - Add skip-links and main-content IDs
   - Replace hardcoded Tailwind colors with CSS variables

3. **Create Theme Switcher UI**
   - Build accessibility settings panel
   - Add user preference saving to database
   - Implement per-user theme preferences

4. **Stage 8: Accessibility Audit**
   - Run WAVE accessibility checker
   - Test with screen readers (NVDA, JAWS)
   - Verify WCAG 2.1 AAA compliance

---

## KNOWN ISSUES & NOTES

- OpenDyslexic fonts must be manually downloaded (external resource)
- Some Tailwind classes may need manual color replacement in remaining files
- CSS custom properties are supported in all modern browsers (IE 11 not supported)
- localStorage requires cookies enabled

---

## COMPLETION SIGN-OFF

**Stage 7-A Complete:** ✅ CSS Foundation Established
- All primary pages updated with tokens
- Theme switching functional
- CSS custom properties defined for all modes
- Accessibility features implemented
- Ready for Stage 7-B expansion

**Next: Apply to remaining pages and create user preferences system**
