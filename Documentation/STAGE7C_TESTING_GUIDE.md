# STAGE 7-C TESTING GUIDE
**Keyboard Navigation & ARIA/Screen Reader Support**

---

## Testing Environment Setup

### Tools Needed

#### Free & Recommended
- **NVDA Screen Reader**: https://www.nvaccess.org/ (Windows - FREE)
- **Chrome DevTools**: Built-in (F12)
- **Firefox DevTools**: Built-in (F12)
- **Accessibility Inspector Extensions**:
  - axe DevTools (Chrome/Firefox)
  - WAVE (Firefox)
  - Lighthouse (Chrome)

#### Optional (Commercial)
- **JAWS Screen Reader**: https://www.freedomscientific.com/ (Windows - $$)
- **VoiceOver**: Built-in on macOS (Cmd+F5)
- **TalkBack**: Built-in on Android phones

### Browser Setup

```
Chrome/Chromium:
1. Open DevTools (F12)
2. Click "Elements" tab
3. Right-click any element → "Inspect"
4. Look for "Accessibility" tab in inspector

Firefox:
1. Open DevTools (F12)
2. Click "Inspector" tab
3. Look for "Accessibility" panel on right
```

---

## Test Checklist

### Test 1: Keyboard Navigation - Login Page

**URL**: http://localhost/public/login.php

**Steps**:
```
1. Press Tab key repeatedly
   ✓ Focus should move to: Skip Link → Logo → Email Input → Password Input → 
     Forgot Password Link → Sign In Button → Create Account Link

2. Press Shift+Tab to go backwards
   ✓ Should reverse the tab order

3. Focus on Email input, type email
   ✓ Should be able to type without mouse

4. Press Tab to Password field
   ✓ Should move to password field

5. Type password
   ✓ Should accept password input

6. Press Tab to Sign In button
   ✓ Should focus button

7. Press Enter on Sign In button
   ✓ Should submit form (or show error if invalid)

8. Tab to Forgot Password link
   ✓ Should be focusable

9. Press Enter on link
   ✓ Should navigate (if link is functional)
```

**Expected Results**:
- [ ] Can complete entire login without using mouse
- [ ] Focus indicator (blue outline) visible on all elements
- [ ] Tab order is logical (top-to-bottom)
- [ ] Can't tab outside the page
- [ ] Skip link visible when focused

---

### Test 2: Keyboard Navigation - Registration Page

**URL**: http://localhost/public/register.php

**Steps**:
```
1. Press Tab to reach radio buttons (Account Type)
   ✓ Should reach first radio button

2. Press Arrow Up/Down to switch between Buyer/Seller
   ✓ Should select different radio options

3. Press Tab to Username field
   ✓ Should move through form inputs in order

4. Type in each field (no mouse)
   ✓ Email field should work
   ✓ Password field should work
   ✓ Confirm Password field should work

5. Tab to Terms checkbox
   ✓ Should be focusable

6. Press Space on checkbox
   ✓ Should toggle checked state

7. Tab to Create Account button
   ✓ Should focus button

8. Press Enter on button
   ✓ Should submit form

9. If validation errors occur:
   ✓ Should see error message below field
   ✓ Focus should move to first invalid field
   ✓ Error should be announced to screen reader
```

**Expected Results**:
- [ ] Can complete registration with keyboard only
- [ ] Radio buttons navigable with Arrow keys
- [ ] Checkbox toggles with Space key
- [ ] Form submits with Enter key
- [ ] All fields have visible focus
- [ ] Error messages appear for invalid fields

---

### Test 3: Form Error Handling

**On Both Login & Registration Pages**:

```
1. Press Tab to Email field
2. Leave it empty and try to submit (if required)
   ✓ Should show error below field in red text
   ✓ Error should be announced: "Error: Email and password are required"

3. Enter invalid email "notanemail"
4. Try to submit
   ✓ Should show error: "Please enter a valid email address"

5. On password field, enter "weak"
6. Try to submit
   ✓ Should show error about password strength

7. Error messages should be:
   ✓ Visible in red
   ✓ Below the field
   ✓ Linked to field via aria-describedby
   ✓ Announced to screen readers

8. Fix the error and submit
   ✓ Error should disappear
   ✓ Form should submit successfully
```

**Expected Results**:
- [ ] Validation errors show immediately below fields
- [ ] Errors are announced to screen readers
- [ ] Fields with errors have red border
- [ ] Focus moves to first error field on submit
- [ ] Can see all error messages

---

### Test 4: Screen Reader Testing - Login Page

**Tools**: NVDA (Windows) or VoiceOver (Mac)

#### NVDA on Windows:
```
1. Download & install NVDA from https://www.nvaccess.org/
2. Launch NVDA
3. Open Chrome/Firefox to http://localhost/public/login.php
4. Close any NVDA dialogs

5. Press Insert+H (or fn+Insert+H on laptop keyboard)
   ✓ Should hear list of all headings
   ✓ Should hear "Heading 1: Welcome Back"
   ✓ Should see outline structure

6. Close heading list (Escape)

7. Press Arrow Down multiple times
   ✓ Should hear: "Sign in to your account"
   ✓ Should hear: "Email address" (field label)
   ✓ Should hear: "Required" (from aria-required)
   ✓ Should hear: "We'll never share your email"
   ✓ Should hear: "Password" (field label)
   ✓ Should hear: "Forgot password" (link)

8. Tab through the page with Tab key
   ✓ Should hear each button and link
   ✓ Should hear field labels

9. Press Insert+F7
   ✓ Should get list of all form fields
   ✓ Should see: Email, Password fields

10. Try entering data
   ✓ Should hear field content as you type (in browse mode)
```

#### VoiceOver on Mac:
```
1. Enable VoiceOver: Cmd+F5
2. Open Safari to http://localhost/public/login.php
3. Press VO+Home to go to start of page

4. Press VO+U for rotor
   ✓ Should see categories: Headings, Links, Form Controls
   ✓ Click Headings to see "Welcome Back"

5. Press VO+Right Arrow to navigate
   ✓ Should hear: "Skip link"
   ✓ Should hear: "Welcome Back heading"
   ✓ Should hear: "Sign in to your account text"
   ✓ Should hear: "Email Address, editable text"

6. On form field, press VO+Down to read attributes
   ✓ Should hear: "Required" (if aria-required="true")
   ✓ Should hear: Hints linked via aria-describedby

7. Disable VoiceOver: Cmd+F5 to stop
```

**Expected Results**:
- [ ] Page title announced
- [ ] All headings listed and hierarchical
- [ ] Form labels announced with fields
- [ ] Required attributes announced
- [ ] Hints and descriptions read by screen reader
- [ ] All interactive elements announced
- [ ] Images have alt text or marked as decorative

---

### Test 5: Skip Link Functionality

**Steps**:
```
1. Load page: http://localhost/public/login.php
2. Press Tab once (before any focus)
   ✓ Should see blue "Skip to main content" link appear
   ✓ Should be visually prominent

3. Press Enter on Skip link
   ✓ Should jump to main content area (#main-content)
   ✓ Focus should be on <main> tag (visible outline)
   ✓ Page should scroll to show main content

4. Press Tab again
   ✓ Should continue from where skip took you
```

**Expected Results**:
- [ ] Skip link visible on Tab focus
- [ ] Skip link hidden when not focused
- [ ] Clicking/pressing Enter navigates to #main-content
- [ ] Focus visible on main after skip

---

### Test 6: Focus Indicators

**Steps**:
```
1. Load page with all interactive elements visible
2. Press Tab through entire page
   ✓ Every focusable element should have visible outline
   ✓ Outline should be at least 3px wide
   ✓ Outline should be bright color (blue)
   ✓ Outline should not be hard to see

3. Check outline on:
   [ ] Input fields - should have blue outline
   [ ] Buttons - should have blue outline
   [ ] Links - should have blue outline
   [ ] Checkboxes - should have blue outline
   [ ] Radio buttons - should have blue outline

4. Try using browser's default focus styling (shouldn't be removed)
   ✓ Focus should be visible at all times
```

**Expected Results**:
- [ ] All focusable elements have visible focus indicator
- [ ] Focus outline is at least 3px thick
- [ ] Focus outline is high contrast (blue on white/light)
- [ ] Cannot accidentally hide focus styles

---

### Test 7: Modal Dialog (Simulated with Sample Modal)

**Setup** (add to any page for testing):
```html
<button id="open-modal-btn">Open Modal</button>

<div id="test-modal" role="dialog" aria-modal="true" 
     aria-labelledby="modal-title" hidden>
  <h2 id="modal-title">Confirm Action</h2>
  <p>Are you sure?</p>
  <button id="modal-confirm">Yes</button>
  <button id="modal-cancel">Cancel</button>
</div>

<script>
  document.getElementById('open-modal-btn').addEventListener('click', () => {
    openModal('test-modal', 'open-modal-btn');
  });
  
  document.getElementById('modal-cancel').addEventListener('click', () => {
    closeModal('test-modal');
  });
</script>
```

**Steps**:
```
1. Click "Open Modal" button
   ✓ Modal should appear
   ✓ Modal should be visible and centered
   ✓ Background should be semi-transparent

2. Press Tab inside modal
   ✓ Tab should cycle ONLY within modal
   ✓ Focus should never leave modal to background

3. Tab to "Cancel" button
4. Press Shift+Tab
   ✓ Should go to "Yes" button (previous)
   ✓ Should NOT go to background page

5. Press Escape key
   ✓ Modal should close
   ✓ Focus should return to "Open Modal" button

6. Click "Open Modal" again
7. Tab to "Yes" button
8. Press Tab from last button
   ✓ Should wrap to first button ("Yes")
   ✓ Should NOT leave modal

9. In screen reader (NVDA):
   ✓ Should announce "Dialog"
   ✓ Should announce title: "Confirm Action"
   ✓ Background elements should be hidden (aria-hidden="true")
```

**Expected Results**:
- [ ] Modal opens when button clicked
- [ ] Focus trapped inside modal (Tab cycles)
- [ ] Escape key closes modal
- [ ] Focus returns to trigger button
- [ ] Screen reader announces modal dialog
- [ ] Background marked aria-hidden while modal open

---

### Test 8: Image Alt Text

**Steps**:
```
1. Open DevTools (F12)
2. Go to Elements tab
3. Find and click on an <img> tag

4. Check the Accessibility panel
   ✓ Should show the alt text
   ✓ Should say "Accessible Name: [description]"

5. For product image:
   ✓ alt="iPhone 15 Pro - product photo" (GOOD)
   ✓ alt="image" (BAD)
   ✓ alt="" (GOOD if decorative)

6. For decorative images (dividers, spacers):
   ✓ Should have alt=""
   ✓ Should have aria-hidden="true"

7. In screen reader:
   ✓ Product images should announce full description
   ✓ Decorative images should be skipped
```

**Expected Results**:
- [ ] All product images have descriptive alt text
- [ ] Profile photos include person name
- [ ] Decorative images have alt="" and aria-hidden="true"
- [ ] No images with alt="image" or alt="picture"
- [ ] Screen reader reads alt text correctly

---

### Test 9: ARIA Labels and Roles

**Browser DevTools Method**:

```
1. Open DevTools Accessibility Inspector
2. Expand the Accessibility Tree

For forms:
   ✓ Input labeled with <label for="id">
   ✓ Input linked via aria-labelledby if needed
   ✓ Name and role shown in inspector

For buttons:
   ✓ Button text visible and announced
   ✓ Or has aria-label="description"
   ✓ Role shows as "button"

For landmarks:
   ✓ <header role="banner">
   ✓ <nav role="navigation" aria-label="...">
   ✓ <main id="main-content" role="main">
   ✓ <footer role="contentinfo">

For links:
   ✓ Link text makes sense out of context
   ✓ Not just "Click here" or "Read more"
   ✓ Use full page title if needed
```

**Using Accessibility Inspector**:
```
Chrome:
1. F12 → Elements
2. Right-click element → Inspect
3. Look for "Accessibility" sidebar
4. Expand tree to see names and roles

Firefox:
1. F12 → Inspector
2. Look for "Accessibility" tab
3. Select elements to see roles/names
```

**Expected Results**:
- [ ] All form fields have labels
- [ ] All buttons have text or aria-label
- [ ] Page has proper landmarks (header, nav, main, footer)
- [ ] All images have alt text or aria-hidden
- [ ] No elements with missing accessible names

---

### Test 10: Color Contrast

**Using Accessibility Tools**:

```
1. Download axe DevTools browser extension
2. Open page in Chrome
3. Click axe icon in toolbar
4. Click "Scan ALL of my page"
5. Look for "Color contrast" issues

Issues show as:
   ✓ Green = Passes
   ✗ Red = Fails
   ⚠ Yellow = Needs review

For WCAG AA (minimum standard):
   - Normal text: 4.5:1 contrast ratio
   - Large text (18pt+): 3:1 contrast ratio
```

**Manual Check**:
```
1. Look at text on background
2. Can you read it easily?
3. Does it have good contrast?

Bad examples (avoid):
   - Light gray text on white background
   - Dark gray text on black background
   - Yellow text on white background

Good examples (use):
   - Dark text on white/light background
   - White/light text on dark background
   - High contrast combinations
```

**Expected Results**:
- [ ] All text has minimum 4.5:1 contrast ratio
- [ ] Large text has minimum 3:1 ratio
- [ ] No issues reported by accessibility checker
- [ ] Visual hierarchy clear without relying on color alone

---

### Test 11: Heading Hierarchy

**Steps**:
```
1. Open page in browser
2. Press Insert+H in NVDA (or use accessibility tree)
   ✓ Should see all headings listed

3. Heading structure should be:
   ✓ One <h1> per page (main title)
   ✓ <h2> sections under h1
   ✓ <h3> subsections under h2
   ✓ No skipped levels (not h1 → h3)

Example good structure:
   H1: Welcome Back
   H2: Login Form
   H3: Email Address

Example bad structure:
   H1: Welcome Back
   H3: Email Address (wrong - should be H2)

4. Look at page visually:
   ✓ Biggest text is H1
   ✓ Smaller text is H2, H3
   ✓ Hierarchy matches visual importance
```

**Expected Results**:
- [ ] Single H1 per page
- [ ] Headings numbered sequentially (1→2→3, not 1→3)
- [ ] Heading text matches content
- [ ] Heading hierarchy makes sense

---

## Automated Testing with axe DevTools

### Setup:
```
1. Go to Chrome Web Store
2. Search "axe DevTools"
3. Install free extension
4. Refresh your page
```

### Running Tests:
```
1. Click axe icon in toolbar
2. Select "Scan ALL of my page"
3. Wait for scan to complete
4. Review results:
   - Red = Failures (must fix)
   - Yellow = Warnings (should review)
   - Green = Passes

Common issues found:
   - Missing form labels
   - Low color contrast
   - Missing alt text
   - Keyboard traps
   - Missing ARIA attributes
```

---

## Lighthouse Accessibility Audit

### Steps:
```
1. Open DevTools (F12)
2. Go to "Lighthouse" tab (may need to install)
3. Select "Accessibility" category
4. Click "Analyze page load"
5. Wait for audit
6. Review score and issues

Score Guide:
   90-100 = Excellent
   50-89 = Needs improvement
   <50 = Critical issues
```

---

## Checklist for Page Completion

Use this for every page after making accessibility fixes:

```
LANDMARK ELEMENTS
[ ] <header role="banner"> on page
[ ] <nav role="navigation" aria-label="..."> for navigation
[ ] <main id="main-content" role="main" tabindex="-1"> for content
[ ] <footer role="contentinfo"> for footer
[ ] <a href="#main-content" class="skip-link"> at top of body

KEYBOARD NAVIGATION
[ ] Can Tab through all interactive elements
[ ] Tab order is logical (top-to-bottom, left-to-right)
[ ] Can submit forms with keyboard only
[ ] No keyboard traps (except intentional modals)
[ ] Focus indicator visible on all elements
[ ] Skip link works and jumps to main content

FORMS
[ ] All inputs have <label for="...">
[ ] Required fields marked * and aria-required="true"
[ ] Error messages shown in red below fields
[ ] aria-live="polite" regions for dynamic errors
[ ] Can complete form without mouse

SCREEN READER (Tested with NVDA/VoiceOver)
[ ] Page title announced correctly
[ ] All landmarks announced (header, nav, main, footer)
[ ] Form labels associated with fields
[ ] Error messages announced
[ ] All images have alt text
[ ] Button purposes clear
[ ] All content accessible by keyboard

IMAGES & ICONS
[ ] All <img> tags have alt attribute
[ ] Product images: alt="[name] - product photo"
[ ] Profile photos: alt="[name] profile photo"
[ ] Decorative images: alt="" and aria-hidden="true"
[ ] Icons in buttons: aria-label on button, alt="" on icon

FOCUS & CONTRAST
[ ] All focusable elements have visible focus indicator
[ ] Focus outline at least 3px thick
[ ] Text has minimum 4.5:1 contrast ratio
[ ] All accessibility checks pass (axe DevTools)

ARIA & SEMANTIC HTML
[ ] Using <button> not <div onclick>
[ ] Using <label> for form labels
[ ] Using <a href> for navigation links
[ ] Proper landmark roles on all pages
[ ] Proper ARIA attributes on interactive elements

TESTING COMPLETE
[ ] Keyboard navigation works
[ ] Screen reader announces all content correctly
[ ] No automated accessibility errors
[ ] Page is WCAG 2.1 Level AA compliant
```

---

## Troubleshooting Common Issues

### Issue: Focus Not Visible
**Solution**:
- Check for `outline: none` in CSS
- Add `:focus-visible { outline: 3px solid blue; }`
- Remove `* { outline: none; }`

### Issue: Tab Order Wrong
**Solution**:
- Remove or fix `tabindex` attributes
- Use semantic HTML (buttons, links, form controls)
- Order HTML logically in document

### Issue: Screen Reader Can't Read Content
**Solution**:
- Check for missing <label> on form fields
- Add aria-label to icons/buttons without text
- Check for aria-hidden="true" hiding content unintentionally
- Use semantic HTML <header>, <nav>, <main>, <footer>

### Issue: Modal Can't Trap Focus
**Solution**:
- Use openModal() and closeModal() from keyboard.js
- Add role="dialog" and aria-modal="true"
- Call trapFocus() manually if not using modal functions

### Issue: Images Confusing to Screen Reader
**Solution**:
- Use descriptive alt text (not just "image")
- For decorative images: alt="" and aria-hidden="true"
- For icons in buttons: aria-label on button, alt="" on icon

### Issue: Error Messages Not Announced
**Solution**:
- Use role="alert" on error container
- Use aria-live="polite" on messages
- Link field to error message via aria-describedby
- Use showFieldError() from keyboard.js

---

## Test Report Template

```
Testing Date: ______________
Page/Feature: ______________
Tester: ______________

RESULTS:
  Keyboard Navigation: [ ] Pass [ ] Fail [ ] Partial
  Screen Reader: [ ] Pass [ ] Fail [ ] Partial
  Form Validation: [ ] Pass [ ] Fail [ ] Partial
  Color Contrast: [ ] Pass [ ] Fail [ ] Partial
  Focus Indicators: [ ] Pass [ ] Fail [ ] Partial
  Heading Hierarchy: [ ] Pass [ ] Fail [ ] Partial

OVERALL: [ ] Pass [ ] Fail

ISSUES FOUND:
1. ________________
2. ________________
3. ________________

COMMENTS:
_________________________________
_________________________________

Retested: [ ] Pass [ ] Fail

Date Fixed: ______________
```

---

## Resources

- **WCAG 2.1**: https://www.w3.org/WAI/WCAG21/quickref/
- **NVDA Screen Reader**: https://www.nvaccess.org/
- **axe DevTools**: https://www.deque.com/axe/devtools/
- **WebAIM Contrast Checker**: https://webaim.org/resources/contrastchecker/
- **A11y Project**: https://www.a11yproject.com/
- **MDN ARIA Guide**: https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA

---

*Stage 7-C Testing Guide*  
*Updated: April 22, 2026*
