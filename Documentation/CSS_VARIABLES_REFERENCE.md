# CSS CUSTOM PROPERTIES REFERENCE
## Complete Variable Documentation

**Project:** Partido Product Online Market Hub  
**Stage:** 7-A  
**Last Updated:** April 22, 2026

---

## COLOR VARIABLES

### Backgrounds

```css
--color-bg:              #F9FAFB    /* Main page background (light mode) */
--color-surface:         #FFFFFF    /* Secondary surface (cards, panels) */
--color-surface-2:       #F3F4F6    /* Tertiary surface (sections) */
--color-card:            #FFFFFF    /* Card/box background */
```

**Dark Mode Equivalents:**
```css
html.dark {
  --color-bg:            #0F172A    /* Navy background */
  --color-surface:       #1E293B    /* Dark slate cards */
  --color-surface-2:     #334155    /* Slightly lighter slate */
  --color-card:          #1E293B    /* Dark card background */
}
```

**High Contrast Equivalents:**
```css
html.high-contrast {
  --color-bg:            #000000    /* Pure black */
  --color-surface:       #000000    /* Pure black */
  --color-surface-2:     #1A1A1A    /* Near black */
  --color-card:          #000000    /* Pure black */
}
```

### Text Colors

```css
--color-text:            #111827    /* Primary text (dark) */
--color-text-muted:      #6B7280    /* Secondary text (lighter) */
--color-text-inverse:    #FFFFFF    /* Text on dark backgrounds */
```

**Dark Mode:**
```css
html.dark {
  --color-text:          #F1F5F9    /* Light gray text */
  --color-text-muted:    #94A3B8    /* Lighter muted text */
  --color-text-inverse:  #0F172A    /* Dark text on light bg (rare) */
}
```

**High Contrast:**
```css
html.high-contrast {
  --color-text:          #FFFFFF    /* Pure white */
  --color-text-muted:    #FFFF00    /* Yellow for secondary text */
  --color-text-inverse:  #000000    /* Black text on yellow */
}
```

### Brand / Primary Colors

```css
--color-primary:         #16A34A    /* Primary brand color (green) */
--color-primary-hover:   #15803D    /* Hover state (darker green) */
--color-primary-light:   #DCFCE7    /* Light variant (light green) */
```

**Dark Mode:**
```css
html.dark {
  --color-primary:       #22C55E    /* Brighter green */
  --color-primary-hover: #16A34A    /* Standard green */
  --color-primary-light: #14532D    /* Very dark green */
}
```

**High Contrast:**
```css
html.high-contrast {
  --color-primary:       #FFFF00    /* Pure yellow */
  --color-primary-hover: #FFD700    /* Gold */
  --color-primary-light: #333300    /* Olive */
}
```

### Status Colors

```css
--color-success:         #16A34A    /* Success/positive action (green) */
--color-warning:         #D97706    /* Warning/caution (amber) */
--color-error:           #DC2626    /* Error/danger (red) */
--color-info:            #2563EB    /* Information (blue) */
```

**Dark Mode:**
```css
html.dark {
  --color-success:       #10B981    /* Bright green */
  --color-warning:       #F59E0B    /* Bright amber */
  --color-error:         #EF4444    /* Bright red */
  --color-info:          #3B82F6    /* Bright blue */
}
```

**High Contrast:**
```css
html.high-contrast {
  --color-success:       #00FF00    /* Pure lime */
  --color-warning:       #FFAA00    /* Orange */
  --color-error:         #FF4444    /* Bright red */
  --color-info:          #00FFFF    /* Cyan */
}
```

### Borders

```css
--color-border:          #E5E7EB    /* Default border color */
--color-border-focus:    #16A34A    /* Focus indicator color */
```

**Dark Mode:**
```css
html.dark {
  --color-border:        #334155    /* Dark gray */
  --color-border-focus:  #22C55E    /* Bright green */
}
```

**High Contrast:**
```css
html.high-contrast {
  --color-border:        #FFFF00    /* Pure yellow */
  --color-border-focus:  #FFFFFF    /* Pure white */
}
```

### Gray Scale (Extended Palette)

```css
--color-gray-50:         #F9FAFB
--color-gray-100:        #F3F4F6
--color-gray-200:        #E5E7EB
--color-gray-300:        #D1D5DB
--color-gray-400:        #9CA3AF
--color-gray-500:        #6B7280
--color-gray-600:        #4B5563
--color-gray-700:        #374151
--color-gray-800:        #1F2937
--color-gray-900:        #111827
```

**Dark Mode:**
```css
html.dark {
  --color-gray-50:       #F8FAFC
  --color-gray-100:      #F1F5F9
  --color-gray-200:      #E2E8F0
  --color-gray-300:      #CBD5E1
  --color-gray-400:      #94A3B8
  --color-gray-500:      #64748B
  --color-gray-600:      #475569
  --color-gray-700:      #334155
  --color-gray-800:      #1E293B
  --color-gray-900:      #0F172A
}
```

---

## SHADOW VARIABLES

### Shadow Definitions

```css
--shadow-sm:             0 1px 2px rgba(0,0,0,0.05);
--shadow-md:             0 4px 6px rgba(0,0,0,0.07);
--shadow-lg:             0 10px 15px rgba(0,0,0,0.1);
--shadow-xl:             0 20px 25px rgba(0,0,0,0.15);
```

**Dark Mode:**
```css
html.dark {
  --shadow-sm:           0 1px 2px rgba(0,0,0,0.3);
  --shadow-md:           0 4px 6px rgba(0,0,0,0.4);
  --shadow-lg:           0 10px 15px rgba(0,0,0,0.5);
  --shadow-xl:           0 20px 25px rgba(0,0,0,0.6);
}
```

**High Contrast:**
```css
html.high-contrast {
  --shadow-sm:           0 1px 2px rgba(255,255,0,0.5);
  --shadow-md:           0 4px 6px rgba(255,255,0,0.5);
  --shadow-lg:           0 10px 15px rgba(255,255,0,0.5);
  --shadow-xl:           0 20px 25px rgba(255,255,0,0.5);
}
```

---

## TYPOGRAPHY VARIABLES

### Font Families

```css
--font-base:             'Inter', system-ui, -apple-system, sans-serif;
```

**Special Modes:**
```css
html.dyslexia-font * {
  font-family:           'OpenDyslexic', 'Comic Sans MS', sans-serif !important;
}
```

### Font Sizing

```css
--font-size-base:        1rem;        /* 16px base */
```

**Large Font Mode:**
```css
html.large-font {
  font-size:             120%;        /* 1.2rem = 19.2px */
}
```

### Line Heights

```css
--line-height-base:      1.6;         /* Normal reading */
```

**Modes:**
```css
html.large-font body {
  line-height:           1.8;         /* Extra space for readability */
}

html.dyslexia-font * {
  line-height:           1.9 !important;  /* Dyslexia-friendly */
}
```

### Letter & Word Spacing

```css
html.dyslexia-font * {
  letter-spacing:        0.05em !important;
  word-spacing:          0.1em !important;
}
```

---

## SPACING VARIABLES

### Border Radius

```css
--radius-sm:             0.375rem    /* 6px */
--radius-md:             0.5rem      /* 8px */
--radius-lg:             0.75rem     /* 12px */
--radius-xl:             1rem        /* 16px */
```

---

## TRANSITION/ANIMATION VARIABLES

### Timing Functions

```css
--transition-fast:       0.15s ease-in-out
--transition-base:       0.3s ease-in-out
--transition-slow:       0.5s ease-in-out
```

**Reduce Motion Mode:**
```css
html.reduce-motion * {
  animation-duration:    0.01ms !important;
  transition-duration:   0.01ms !important;
}
```

---

## Z-INDEX SCALE

```css
--z-dropdown:            1000
--z-sticky:              1020
--z-fixed:               1030
--z-modal:               1040
--z-tooltip:             1070
```

---

## USAGE EXAMPLES

### Example 1: Text Color

```html
<!-- Light mode: dark text -->
<h1 class="text-text">Heading</h1>

<!-- Dark mode: light text (automatic) -->
<!-- High contrast mode: white text (automatic) -->
```

```css
/* CSS Mapping */
.text-text {
  color: var(--color-text);  /* Switches with theme */
}
```

### Example 2: Card Background

```html
<div class="bg-card border border-border rounded-md shadow-md">
  <h2 class="text-text">Card Title</h2>
  <p class="text-text-muted">Card content</p>
</div>
```

```css
/* CSS Mapping */
.bg-card {
  background-color: var(--color-card);      /* #FFF, #1E293B, #000 */
}

.border-border {
  border-color: var(--color-border);        /* #E5E7EB, #334155, #FFFF00 */
}

.text-text {
  color: var(--color-text);                 /* #111827, #F1F5F9, #FFFFFF */
}

.text-text-muted {
  color: var(--color-text-muted);           /* #6B7280, #94A3B8, #FFFF00 */
}
```

### Example 3: Button Styling

```html
<button class="bg-primary text-white hover:bg-primary-hover rounded-md px-4 py-2">
  Action Button
</button>
```

```css
/* CSS Mapping */
.bg-primary {
  background-color: var(--color-primary);        /* #16A34A, #22C55E, #FFFF00 */
}

.hover:bg-primary-hover:hover {
  background-color: var(--color-primary-hover);  /* #15803D, #16A34A, #FFD700 */
}

.text-white {
  color: var(--color-text-inverse);              /* #FFFFFF, #0F172A, #000000 */
}
```

### Example 4: Alert Box

```html
<div class="bg-error bg-opacity-10 border border-error rounded-md p-4">
  <p class="text-error">Error message text</p>
</div>
```

```css
/* CSS Mapping */
.text-error {
  color: var(--color-error);                     /* #DC2626 (all modes) */
}

.bg-error {
  background-color: var(--color-error);
  opacity: 0.1;  /* 10% opacity */
}

.border-error {
  border-color: var(--color-error);
}
```

### Example 5: Focus Styles

```html
<input type="text" class="focus:ring-2 focus:ring-primary focus:border-transparent">
```

```css
/* CSS Focus Override */
:focus-visible {
  outline: 3px solid var(--color-border-focus);
  outline-offset: 2px;
}

html.high-contrast :focus-visible {
  outline: 3px solid #FFFFFF !important;
}
```

---

## IMPLEMENTATION CHECKLIST

### When Adding New Pages

- [ ] Link `tokens.css` FIRST (before Tailwind)
- [ ] Link `helpers.css` (after tokens.css)
- [ ] Include `theme-switcher.js` in `<head>`
- [ ] Replace hardcoded colors with CSS variables
- [ ] Add skip-link as first `<body>` element
- [ ] Add `id="main-content"` to `<main>`
- [ ] Test all theme modes

### Color Replacement Guide

| Old Class | New Variable | Usage |
|-----------|--------------|-------|
| `bg-white` | `var(--color-card)` | Card/surface backgrounds |
| `bg-gray-50` | `var(--color-surface-2)` | Section backgrounds |
| `text-blue-600` | `var(--color-primary)` | Primary action text |
| `text-gray-700` | `var(--color-text)` | Primary text |
| `text-gray-600` | `var(--color-text-muted)` | Secondary text |
| `border-gray-300` | `var(--color-border)` | Default borders |
| `bg-red-50` | `rgba(220,38,38,0.1)` | Error background |
| `text-red-600` | `var(--color-error)` | Error text |
| `bg-green-50` | `rgba(22,163,74,0.1)` | Success background |
| `text-green-600` | `var(--color-success)` | Success text |
| `hover:bg-blue-700` | `hover:bg-primary-hover` | Button hover state |
| `shadow-md` | `var(--shadow-md)` | Drop shadows |

---

## ACCESSIBILITY NOTES

### High Contrast Mode Considerations

- Uses maximum contrast ratios (21:1 for black/white on yellow)
- Meets WCAG AAA standards
- All borders become yellow/white
- No subtle color distinctions
- Useful for color-blind users and low-vision users

### Large Font Mode

- 20% enlagement (1rem → 1.2rem)
- Increases line-height to 1.8
- Useful for users with vision impairments
- Maintains layout integrity

### Dyslexia Font Mode

- OpenDyslexic font has weighted letter-shapes
- Increased letter and word spacing
- Higher line-height (1.9) for letter discrimination
- Left-aligned text (no justification)

### Reduce Motion Mode

- Respects system preference
- User can override system setting
- Useful for motion sensitivity/vestibular disorders
- All interactions still functional

---

## DEBUGGING & TESTING

### Verify Variables in Console

```javascript
// Get all computed variables
const style = getComputedStyle(document.documentElement);
const vars = [
  '--color-bg',
  '--color-text',
  '--color-primary',
  '--shadow-md'
];
vars.forEach(v => {
  console.log(`${v}: ${style.getPropertyValue(v)}`);
});
```

### Test Theme Switching

```javascript
// Apply dark mode
document.documentElement.classList.add('dark');

// Verify variable changed
const style = getComputedStyle(document.documentElement);
console.log('Background in dark mode:', style.getPropertyValue('--color-bg'));
// Expected: #0F172A
```

### Check for Overrides

```javascript
// Find all elements with inline styles that might override variables
document.querySelectorAll('[style*="background"],\
                           [style*="color"],\
                           [style*="border"]').forEach(el => {
  console.warn('Inline style found:', el, el.getAttribute('style'));
});
```

---

## COMMON ISSUES & SOLUTIONS

### Issue: Theme doesn't apply
- **Cause:** tokens.css not linked before Tailwind
- **Solution:** Check link order in `<head>`. tokens.css MUST come first

### Issue: Color looks wrong in dark mode
- **Cause:** Tailwind class being used directly without mapping
- **Solution:** Replace with CSS variable alternative from helpers.css

### Issue: Focus outline disappears
- **Cause:** CSS selector specificity overriding `:focus-visible`
- **Solution:** Ensure `:focus-visible` styles have high enough specificity

### Issue: Dyslexia font not loading
- **Cause:** Font files not in `/assets/fonts/OpenDyslexic/`
- **Solution:** Download and place OpenDyslexic font files in correct directory

### Issue: Theme resets on page reload
- **Cause:** JavaScript not loading/executing
- **Solution:** Ensure `theme-switcher.js` is properly linked and executes early

---

## PERFORMANCE OPTIMIZATION

### CSS Variable Performance

- ✅ No JavaScript execution for color changes
- ✅ Native browser support (no polyfills)
- ✅ Minimal memory overhead
- ✅ Fast cascade recalculation
- ✅ No layout thrashing

### File Size Impact

- `tokens.css`: ~18KB (gzipped ~4KB)
- `helpers.css`: ~8KB (gzipped ~2KB)
- `theme-switcher.js`: ~8KB (gzipped ~3KB)
- **Total:** ~34KB (gzipped ~9KB)

### Optimization Tips

- Defer non-critical CSS
- Load theme-switcher.js in `<head>` for instant application
- Use CSS custom properties for repetitive values
- Avoid deep nesting in color selectors

---

**Last Updated:** April 22, 2026  
**Stage:** 7-A Complete  
**Next Review:** Stage 7-B Implementation
