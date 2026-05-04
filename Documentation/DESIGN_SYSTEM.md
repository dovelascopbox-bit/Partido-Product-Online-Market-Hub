# Partido Market Hub - Professional Design System (Stage 7-F)

**Version**: 2.0 (Professional Edition)  
**Last Updated**: April 23, 2026  
**Status**: ✅ Production Ready

---

## 📋 Table of Contents

1. [Color Palette](#color-palette)
2. [Typography](#typography)
3. [Spacing System](#spacing-system)
4. [Components](#components)
5. [Usage Guidelines](#usage-guidelines)
6. [Accessibility](#accessibility)

---

## 🎨 Color Palette

### Primary Colors
- **Primary**: `#0f766e` (Teal) - Main brand color
- **Primary Light**: `#14b8a6` - Interactive hover states
- **Primary Lighter**: `#99f6e4` - Light backgrounds
- **Primary Dark**: `#0d4f46` - Darker emphasis
- **Primary Darkest**: `#134e4a` - Text on light backgrounds

### Secondary Colors
- **Secondary**: `#059669` (Emerald) - Accent color
- **Secondary Light**: `#10b981` - Secondary hover
- **Secondary Lighter**: `#d1fae5` - Light backgrounds

### Status Colors
- **Success**: `#16a34a` (Green) - Positive actions
- **Danger**: `#dc2626` (Red) - Destructive/errors
- **Warning**: `#ea580c` (Orange) - Caution/alerts
- **Info**: `#2563eb` (Blue) - Information

### Neutral Colors (Gray Scale)
- `--gray-50`: `#f9fafb` - Lightest background
- `--gray-100`: `#f3f4f6` - Light background
- `--gray-200`: `#e5e7eb` - Borders
- `--gray-300`: `#d1d5db` - Dividers
- `--gray-400`: `#9ca3af` - Icons/muted
- `--gray-500`: `#6b7280` - Secondary text
- `--gray-600`: `#4b5563` - Body text
- `--gray-700`: `#374151` - Emphasis
- `--gray-800`: `#1f2937` - Dark bgrd
- `--gray-900`: `#111827` - Darkest text

---

## 📝 Typography

### Headings
| Level | Size | Weight | Line Height | Usage |
|-------|------|--------|-------------|-------|
| H1 | 2.5rem | 700 | 1.1 | Page titles |
| H2 | 2rem | 700 | 1.2 | Section titles |
| H3 | 1.5rem | 700 | 1.2 | Subsections |
| H4 | 1.25rem | 600 | 1.3 | Small headings |
| H5 | 1.125rem | 600 | 1.3 | UI headings |
| H6 | 1rem | 600 | 1.3 | Caps/labels |

### Body Text
- **Regular (default)**: 1rem, weight 400, line-height 1.6
- **Muted**: 1rem, weight 400, color: `var(--gray-500)`
- **Large**: 1.125rem, weight 400, line-height 1.8
- **Small**: 0.875rem, weight 400

### Links
- Color: `var(--primary)` with hover: `var(--primary-light)`
- Underline on hover (optional)
- Focus outline 2px offset 2px

---

## 📏 Spacing System

All spacing follows a 4px base unit (0.25rem):

```
xs:   0.25rem (1x)
sm:   0.5rem  (2x)
md:   1rem    (4x)
lg:   1.5rem  (6x)
xl:   2rem    (8x)
2xl:  2.5rem  (10x)
3xl:  3rem    (12x)
```

### Application
- **Padding**: Use inside components
- **Margin**: Use between components
- **Gap**: Use in flex/grid layouts

**Best Practices**:
- Keep horizontal padding consistent (usually `--spacing-lg` or `--spacing-xl`)
- Vertical spacing larger than horizontal (visual hierarchy)
- Group related items with smaller spacing
- Separate sections with larger spacing

---

## 🧩 Components

### Buttons

#### Primary Button
```html
<button class="btn btn-primary">
    Action
</button>
```
- Background: `var(--primary)`
- Color: White
- Hover: Lighter color + shadow + slight lift
- Focus: 2px outline
- Disabled: Gray with `cursor: not-allowed`

#### Secondary Button
```html
<button class="btn btn-secondary">
    Alternative
</button>
```
- Background: `var(--gray-200)`
- Color: `var(--gray-900)`
- Hover: Darker gray

#### Outline Button
```html
<button class="btn btn-outline">
    Cancel
</button>
```
- Border: 2px `var(--primary)`
- Background: Transparent → light teal on hover
- Color: `var(--primary)`

#### Button Sizes
```html
<button class="btn btn-sm">Small</button>
<button class="btn">Regular</button>
<button class="btn btn-lg">Large</button>
```

### Cards
```html
<div class="card">
    <div class="card-header">
        <h3>Card Title</h3>
    </div>
    <div class="card-body">
        Content here
    </div>
    <div class="card-footer">
        Footer content
    </div>
</div>
```
- Background: White
- Border: 1px `var(--gray-200)`
- Padding: `var(--spacing-xl)`
- Shadow: Small shadow
- Hover: Medium shadow + lighter border

### Forms
```html
<label for="email">Email Address</label>
<input 
    type="email" 
    id="email" 
    placeholder="you@example.com"
    required
>
<span class="error-message">This field is required</span>
```

**Input States**:
- **Default**: Gray background, gray border
- **Hover**: White background, darker border
- **Focus**: White background, primary border, primary shadow
- **Disabled**: Light gray background, gray text
- **Error**: Red border, red shadow

**Min Heights**: 44px (touch-friendly)

### Alerts
```html
<div class="alert alert-success">
    ✓ Action completed successfully!
</div>

<div class="alert alert-danger">
    ✗ An error occurred. Please try again.
</div>

<div class="alert alert-warning">
    ⚠ This action cannot be undone.
</div>

<div class="alert alert-info">
    ℹ Here's some helpful information.
</div>
```

### Badges
```html
<span class="badge badge-primary">New</span>
<span class="badge badge-success">Active</span>
<span class="badge badge-danger">Inactive</span>
```

---

## 💡 Usage Guidelines

### Layout Structure
```html
<body>
    <div class="app-layout">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-logo">Partido</div>
            <div class="navbar-actions">
                <!-- Buttons/links -->
            </div>
        </nav>

        <div class="app-layout-with-sidebar">
            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Menu items -->
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                <div class="container">
                    <!-- Page content -->
                </div>
            </main>
        </div>

        <!-- Footer -->
        <footer>
            <!-- Footer content -->
        </footer>
    </div>
</body>
```

### Write Consistent HTML
1. Always use semantic HTML (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`)
2. Use proper heading hierarchy (no skipping h1 → h3)
3. Include alt text on images
4. Use `<label>` for form inputs
5. Use `<button>` for actions, `<a>` for navigation

### Apply Proper Spacing
```html
<!-- Section spacing -->
<section style="padding: var(--spacing-2xl) 0;">
    <!-- Content with xl margins between items -->
    <div style="margin-bottom: var(--spacing-lg);">Item</div>
    <div style="margin-bottom: var(--spacing-lg);">Item</div>
</section>
```

### Responsive Design
```css
/* Mobile first */
.card {
    padding: var(--spacing-lg);
}

@media (min-width: 768px) {
    .card {
        padding: var(--spacing-xl);
    }
}
```

---

## ♿ Accessibility

### Focus States
Every interactive element must have a visible focus state:
```css
button:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}
```

### Color Contrast
- **WCAG AA**: Minimum 4.5:1 for normal text, 3:1 for large text
- **WCAG AAA**: Minimum 7:1 for normal text
- Test at: https://www.tpgi.com/color-contrast-checker/

### Touch Targets
- **Minimum size**: 44×44px (recommended 48×48px)
- Buttons, links, inputs must meet this minimum

### Keyboard Navigation
- All interactive elements reachable via Tab key
- Focus order must be logical (left-to-right, top-to-bottom)
- Skip links to main content
- Escape key to close modals/menus

### ARIA Labels
```html
<!-- For icon-only buttons -->
<button aria-label="Close menu">×</button>

<!-- For forms with error -->
<input aria-invalid="true" aria-describedby="email-error">
<span id="email-error" class="error-message">Invalid email</span>

<!-- For icons -->
<svg aria-label="Success icon">...</svg>
```

### Screen Reader Text
```html
<!-- Hidden text for screen readers -->
<button>
    <span aria-hidden="true">🔔</span>
    <span class="sr-only">Notifications (3 unread)</span>
</button>
```

---

## 📱 Responsive Breakpoints

| Breakpoint | Screen Size | Usage |
|-----------|------------|-------|
| Mobile | < 640px | Single column, stacked |
| Tablet | 640–1024px | 2-column layouts |
| Desktop | > 1024px | Multi-column, sidebars |

### Responsive Classes
```html
<!-- Hide on mobile, show on tablet+ -->
<div class="hidden sm:block"></div>

<!-- Stack on mobile, side-by-side on tablet+ -->
<div class="flex flex-col sm:flex-row"></div>

<!-- Different padding per screen -->
<div class="p-4 sm:p-6 md:p-8"></div>
```

---

## 🚀 Implementation Checklist

- [ ] Replace old colors with new palette
- [ ] Apply spacing system to all layouts
- [ ] Update form styles globally
- [ ] Style all button variants
- [ ] Add shadows consistently
- [ ] Test responsive on mobile/tablet/desktop
- [ ] Verify WCAG AA contrast ratios
- [ ] Add focus states to all interactive elements
- [ ] Test keyboard navigation
- [ ] Test with screen reader
- [ ] Optimize images (max-width, compression)

---

## 📞 Support

For design questions or inconsistencies:
1. Check this guide first
2. Review CSS variables in `/assets/css/main.css`
3. Test in browser DevTools
4. Update guide if adding new patterns

---

**Last Review**: April 23, 2026  
**Next Review**: May 1, 2026
