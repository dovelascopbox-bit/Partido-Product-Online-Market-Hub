# ACCESSIBILITY FEATURES GUIDE

**Partido Product Online Market Hub** - Stage 7 Accessibility
**Standard**: WCAG 2.1 AA
**Updated**: April 2026

---

## 🎯 QUICK START

The **Accessibility Toolbar** is in the bottom-right corner of every page (♿ icon).

Click the icon to see 6 accessibility modes you can toggle on/off:

1. **🌙 Dark Mode** - Easy on the eyes at night
2. **◑ High Contrast** - For low vision users
3. **A+ Large Font** - 20% bigger text
4. **✦ Dyslexia Font** - OpenDyslexic font with extra spacing
5. **— Reading Guide** - Horizontal line follows your cursor
6. **← Reduce Motion** - Disables animations

All settings save automatically and work across all pages.

---

## 🌙 DARK MODE

**For**: Night reading, light sensitivity, eye strain

**What it does**:
- Background changes to dark gray (#1f2937)
- Text changes to light colors
- All colors inverted for readability
- Links remain highlighted

**How to use**:
1. Click the ♿ icon (bottom right)
2. Click the toggle next to "Dark Mode"
3. Page instantly switches to dark theme
4. Setting saves automatically

**Browser Support**: All modern browsers
**Note**: Respects system preference (macOS Dark Mode, Windows Dark Mode)

---

## ◑ HIGH CONTRAST MODE

**For**: Low vision, color blindness, computer vision syndrome

**What it does**:
- Background becomes pure black (#000000)
- Text becomes pure white (#FFFFFF)
- Removes all shadows and gradients
- Adds bright yellow (#FFFF00) borders to buttons
- All text links are underlined
- Tab focus indicator is 4px thick yellow outline

**WCAG AAA Compliance**: 7:1 contrast ratio (highest accessibility level)

**How to use**:
1. Click the ♿ icon
2. Click the toggle next to "High Contrast"
3. All colors change to maximum contrast
4. Note: This is **mutually exclusive** with Dark Mode (turning one off turns off the other)

**Why no gradients?**
- Gradients can be hard to read for some users
- Pure colors with high contrast are clearer

**Touch Targets**: All buttons expand to 48x48px minimum in this mode

---

## A+ LARGE FONT (120%)

**For**: Presbyopia, low vision, elderly users, people with weak eyesight

**What it does**:
- Increases all text size by 20%
- Buttons and spacing adjust proportionally
- Line height increases to 1.8 for better readability
- No text overflow on any page (responsive adjustments)

**How to use**:
1. Click the ♿ icon
2. Click the toggle next to "Large Font"
3. Text on page immediately grows
4. Try browsing the full site at this size

**Testing Guide**:
- ✅ Product cards should stack nicely
- ✅ Forms should not have text overflow
- ✅ Mobile navigation should remain functional
- ✅ Buttons should remain clickable (44px+ tall)

---

## ✦ DYSLEXIA-FRIENDLY FONT

**For**: Dyslexia, aphasia, visual processing disorders

**What it does**:
- Changes font to "OpenDyslexic" (open source, free)
- Adds extra spacing between letters (0.05em)
- Adds extra spacing between words (0.1em)
- Increases line height to 1.9
- Disables text justification (left-aligned only)
- Works with any other mode (combine with Large Font for best result)

**OpenDyslexic Benefits**:
- Heavy baseline makes letters distinguishable
- Unique character shapes prevent confusion (b/d, p/q)
- Designed by dyslexic designers
- Used in 100+ schools and organizations

**How to use**:
1. Click the ♿ icon
2. Click the toggle next to "Dyslexia Font"
3. Font changes across entire page
4. Combine with Large Font for maximum benefit

**Font Source**: https://opendyslexic.org (open source)

---

## — READING GUIDE (Visual Aid)

**For**: ADHD, dyslexia, concentration issues, tracking difficulties

**What it does**:
- A semi-transparent yellow horizontal bar appears
- Bar follows your mouse cursor position
- Helps focus on one line at a time
- Useful for maintaining reading flow
- Does NOT interfere with clicking buttons

**How to use**:
1. Click the ♿ icon
2. Click the toggle next to "Reading Guide"
3. Move your mouse over text — a yellow line follows it
4. Use it to track the line you're reading
5. Click normally — the guide doesn't block interactions

**Technical Details**:
- Line appears 20px above cursor (for easy reading)
- 2.5em height (covers a full line plus spacing)
- Semi-transparent yellow (rgba color)
- Automatically disabled on touch devices (phones/tablets — no cursor)

**Why it helps**:
- Keeps eye focus on current line
- Reduces visual clutter
- Helps with line-skipping when reading

---

## ← REDUCE MOTION

**For**: Vestibular disorders, motion sickness, seizure disorders

**What it does**:
- Removes ALL animations from page
- Removes ALL CSS transitions
- Disables slide-in effects
- Disables loading shimmer animations
- Makes notifications appear instantly
- Respects `prefers-reduced-motion` system setting

**How to use**:
1. Click the ♿ icon
2. Click the toggle next to "Reduce Motion"
3. All movements stop
4. Pages appear instantly instead of sliding

**Browser Setting Alternative**:
- **Mac**: System Preferences → Accessibility → Display → Reduce Motion
- **Windows 11**: Settings → Ease of Access → Display → Show animations
- **iOS**: Settings → Accessibility → Motion → Reduce Motion
- **Android**: Settings → Accessibility → Remove animations

**When to use**:
- While using other motion-heavy apps
- During migraine
- If you have ADHD with sensory issues
- If animations cause disorientation

---

## ⌨️ KEYBOARD NAVIGATION

**For**: Motor disabilities, keyboard-only users

**Complete keyboard access to every feature**:

### Global Shortcuts
| Key | Action |
|-----|--------|
| **Tab** | Move to next button/link/input |
| **Shift + Tab** | Move to previous button/link/input |
| **Enter** | Activate button/link or submit form |
| **Space** | Toggle checkbox or toggle button |
| **Escape** | Close menu/modal/accessibility panel |
| **↑ ↓** | Navigate dropdown menus |

### Page Structure (All pages have)
1. **Skip to Main Content** link (press Tab once on page load)
2. **Navbar** with role-based links
3. **Main Content** area with skip-to link destination
4. **Footer** at bottom

### Form Navigation
- **Tab** through all fields
- **Enter** submits form
- **Tab** reveals error messages (red border + text below)
- All buttons have 44px+ height (WCAG touch target)

### Testing: Full Keyboard Navigation
1. Load any page
2. Press Tab repeatedly — every interactive element gets focus
3. Press Escape to close modals
4. Press Enter on buttons to activate
5. No mouse required

---

## 🔊 SCREEN READER SUPPORT

**For**: Blind and low-vision users

Partido is compatible with:
- **NVDA** (Windows, free)
- **JAWS** (Windows, commercial)
- **VoiceOver** (Mac, iOS, built-in)
- **TalkBack** (Android, built-in)

### What Works
- [x] All images have descriptive alt text
- [x] Form labels read with inputs
- [x] Error messages announced
- [x] Buttons read with `aria-label`
- [x] Landmarks announc page areas (Header, Nav, Main, Footer)
- [x] Modal dialogs announced as dialogs
- [x] Loading states announced

### Testing with NVDA (Free)
1. **Download**: https://www.nvaccess.org/ (Windows only)
2. **Install** and **start NVDA**
3. **Press Insert + N** to open NVDA menu
4. Open Partido website
5. Use arrow keys to read page
6. Press Tab to jump between links/buttons
7. Modifications clearly announced

### Tested Pages
- ✅ Landing page (index.php)
- ✅ Login/Register forms
- ✅ Marketplace hub
- ✅ Seller dashboard
- ✅ Buyer dashboard
- ✅ Messenger conversations
- ✅ Product detail page

---

## 🎨 COMBINING MODES

All modes can be **combined** for personalized accessibility:

### Common Combinations

| Use Case | Modes | Why |
|----------|-------|-----|
| Dyslexia + low vision | Large Font + Dyslexia Font | Larger, easier shapes |
| Night reading | Dark Mode + Large Font | Eyes less strained |
| Photophobia | High Contrast + Reduce Motion | No bright flashes or animations |
| ADHD reading | Dark Mode + Reading Guide | Focus + reduced fatigue |
| Low vision | High Contrast + Large Font | Maximum visibility |
| Screen reader | Only keyboard nav needed | All other modes optional |

*Note*: High Contrast and Dark Mode are mutually exclusive (turning one on turns the other off)

---

## 🧪 TESTING YOUR SETUP

### Quick Test (5 min)
1. Click ♿ icon → enable Dark Mode → page goes dark ✅
2. Click ♿ icon → enable Large Font → text grows ✅
3. Press **Tab** 5 times → every element highlighted ✅
4. Press **Escape** → accessibility panel closes ✅
5. Refresh page → settings still on ✅

### Comprehensive Test (20 min)
- [ ] Enable each mode individually, verify it works
- [ ] Combine modes from table above
- [ ] Test keyboard-only (unplug mouse)
- [ ] Download NVDA, test reading
- [ ] Test High Contrast mode on dark background
- [ ] Test Reading Guide by moving cursor over product cards
- [ ] Resize browser to 320px, verify accessibility toolbar still works

### Browser DevTools Accessibility Audit
1. **Chrome**: DevTools → Lighthouse → Accessibility
2. **Firefox**: DevTools → Inspector → Accessibility
3. **Safari**: Develop → Check Accessibility
4. All should show ✅ green checks

---

## 📱 MOBILE ACCESSIBILITY

### Touch Accessibility
- All buttons are **minimum 44x44px** (easy to tap)
- Tap targets have 8px spacing (no accidental clicks)
- Double-tap zoom works on all pages
- Pinch-zoom works for magnification

### Screen Reader on Mobile
**iOS (VoiceOver - built-in)**:
1. Settings → Accessibility → VoiceOver → ON
2. Swipe 2-fingers twice to start
3. Use 1-finger swipe right/left to navigate
4. Double-tap to activate

**Android (TalkBack - built-in)**:
1. Settings → Accessibility → TalkBack → ON
2. Use 2-finger swipe right/left to navigate
3. Double-tap to activate

---

## 🌐 BROWSER DARK MODE AUTO-DETECTION

**Automatic**: If your **system** has dark mode on:
- Windows 11: Settings → Personalization → Colors → Dark
- macOS: System Preferences → General → Appearance → Dark
- iOS: Settings → Display & Brightness → Dark
- Android: Settings → Display → Dark Theme

Partido will **automatically enable** dark mode when you first visit.

**Manual Override**: Click accessibility toolbar to change regardless of system setting.

---

## 🚀 ACCESSIBILITY IMPROVEMENTS (STAGE 8+)

Future enhancements planned:

- [ ] **Translation**: Multi-language support (Spanish, Tagalog)
- [ ] **Captions**: Video transcripts for tutorials
- [ ] **Voice Control**: Amazon Alexa/Google Voice commands
- [ ] **Braille**: Braille pattern mapping for screen readers
- [ ] **Eye Tracking**: Alternative input for severe motor disabilities
- [ ] **Live Chat**: Accessible customer support widget
- [ ] **Autism-Friendly Mode**: Simplified UI, reduced complexity

---

## ✅ WCAG 2.1 AA CHECKLIST

| Criterion | Status | Notes |
|-----------|--------|-------|
| 1.1.1 Non-text Content | ✅ | All meaningful images have alt text |
| 1.3.1 Info & Relationships | ✅ | Semantic HTML (heading hierarchy, labels) |
| 1.4.3 Contrast (Minimum) | ✅ | 4.5:1 normal text, 3:1 large text |
| 2.1.1 Keyboard | ✅ | All functionality via keyboard |
| 2.1.2 No Keyboard Trap | ✅ | Can always Tab away |
| 2.4.3 Focus Order | ✅ | Logical left-to-right, top-to-bottom |
| 2.4.7 Focus Visible | ✅ | 3px outline always visible |
| 3.1.1 Language of Page | ✅ | `<html lang="en">` |
| 3.3.1 Error Identification | ✅ | Errors described clearly |
| 3.3.4 Error Prevention | ✅ | Confirmation on destructive actions |
| 4.1.3 Status Messages | ✅ | aria-live announcements |

---

## 📞 ACCESSIBILITY SUPPORT

**Report Issues**: Open GitHub issue with tag `[ACCESSIBILITY]`

**Quick Links**:
- QUALITY.md — Full quality standards
- WCAG 2.1 — https://www.w3.org/WAI/WCAG21/quickref/
- OpenDyslexic — https://opendyslexic.org
- NVDA (free screen reader) — https://www.nvaccess.org

**Questions?**
Email: accessibility@partidomarket.local
Response time: 24 hours

---

**Last Updated**: April 23, 2026
**Version**: 7.0 GA
**Status**: ✅ WCAG 2.1 AA Compliant
