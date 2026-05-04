# Stage 7-B Architecture & Visual Guide

---

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                      PARTIDO PRODUCT MARKET HUB                     │
│                    Accessibility Toolbar - Stage 7-B                │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  BROWSER EXECUTION ENVIRONMENT                                      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │ HTML Document                                             │   │
│  │ ├─ <html>  (classes: dark, large-font, etc.)             │   │
│  │ ├─ <head>                                                 │   │
│  │ │  ├─ tokens.css       (Stage 7-A)                       │   │
│  │ │  ├─ helpers.css      (color mappings)                  │   │
│  │ │  ├─ main.css         (layout)                          │   │
│  │ │  └─ theme-switcher.js (early init)                    │   │
│  │ ├─ <body>                                                 │   │
│  │ │  ├─ [page content]                                     │   │
│  │ │  └─ [accessibility toolbar - from footer.php]          │   │
│  │ │     ├─ #a11y-toolbar                                   │   │
│  │ │     ├─ #a11y-toggle  (wheelchair button)              │   │
│  │ │     ├─ #a11y-panel   (hidden by default)              │   │
│  │ │     └─ #reading-guide-bar  (display: none)            │   │
│  │ │                                                         │   │
│  │ └─ [footer content]                                       │   │
│  │    ├─ accessibility-toolbar.php                          │   │
│  │    ├─ <link rel="stylesheet" href=".../accessibility.css">   │
│  │    └─ <script src=".../accessibility.js"></script>          │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │ JavaScript Execution                                       │   │
│  │                                                            │   │
│  │  1. IMMEDIATE (IIFE)                                      │   │
│  │     - Read localStorage                                   │   │
│  │     - Apply classes to <html>                            │   │
│  │     - Prevent flash                                       │   │
│  │                                                            │   │
│  │  2. ON DOMContentLoaded (A11y.init)                      │   │
│  │     - Load state from storage                             │   │
│  │     - Detect system preferences                           │   │
│  │     - Apply all modes                                     │   │
│  │     - Create live region                                  │   │
│  │     - Attach event listeners                              │   │
│  │                                                            │   │
│  │  3. ON USER INTERACTION (Event Listeners)                │   │
│  │     - Click toggle: open/close panel                      │   │
│  │     - Click switch: toggle mode                           │   │
│  │     - Click close: close panel                            │   │
│  │     - Click reset: clear all                              │   │
│  │     - Press Escape: close panel                           │   │
│  │     - Move mouse: update reading guide                    │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │ CSS Rendering                                              │   │
│  │                                                            │   │
│  │  tokens.css (Stage 7-A)                                   │   │
│  │  ├─ --color-primary, --color-surface, etc.               │   │
│  │  ├─ --shadow-md, --radius-lg, etc.                       │   │
│  │  └─ mode-specific overrides                               │   │
│  │                                                            │   │
│  │  accessibility.css (Stage 7-B)                            │   │
│  │  ├─ #a11y-toolbar (fixed positioning)                    │   │
│  │  ├─ #a11y-toggle (button styling)                        │   │
│  │  ├─ #a11y-panel (panel layout)                           │   │
│  │  ├─ .a11y-switch (toggle styling)                        │   │
│  │  ├─ #reading-guide-bar (visual aid)                      │   │
│  │  └─ Mode-specific rules (html.dark, html.large-font, etc.)  │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │ Browser Storage                                            │   │
│  │                                                            │   │
│  │  localStorage['partido_a11y']                             │   │
│  │  {                                                         │   │
│  │    "dark": true,                                          │   │
│  │    "high-contrast": false,                                │   │
│  │    "large-font": true,                                    │   │
│  │    "dyslexia-font": false,                                │   │
│  │    "reading-guide": false,                                │   │
│  │    "reduce-motion": false                                 │   │
│  │  }                                                         │   │
│  │                                                            │   │
│  │  sessionStorage (none - not needed)                       │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## User Interaction Flow Diagram

```
START: User visits page
  │
  ├─► JavaScript IIFE runs (before DOMContentLoaded)
  │   ├─ Read localStorage
  │   └─ Apply classes to <html>
  │
  └─► DOMContentLoaded fires
      ├─ A11y.init() called
      ├─ Load state
      ├─ Detect system preferences
      ├─ Apply all modes
      ├─ Create live region
      └─ Attach event listeners

READY FOR INTERACTION
  │
  ├─► User clicks toggle button (♿)
  │   └─ togglePanel()
  │       ├─ panel.hidden = !panel.hidden
  │       ├─ Update aria-expanded
  │       └─ Auto-focus first switch
  │
  ├─► User clicks switch (e.g., Dark Mode)
  │   └─ toggleMode('dark')
  │       ├─ Check mutual exclusivity
  │       ├─ Update state object
  │       ├─ saveState() → localStorage
  │       ├─ applyAll()
  │       │  ├─ html.classList.toggle()
  │       │  └─ updateSwitch() UI
  │       └─ announceChange() → screen reader
  │
  ├─► User clicks close button (✕)
  │   └─ closePanel()
  │       ├─ panel.hidden = true
  │       ├─ Update aria-expanded
  │       └─ Return focus to toggle
  │
  ├─► User presses Escape key
  │   └─ closePanel() (same as above)
  │
  ├─► User clicks reset button
  │   └─ resetAll()
  │       ├─ state = {}
  │       ├─ saveState() → localStorage
  │       ├─ applyAll()
  │       └─ announce('Settings reset')
  │
  ├─► User moves mouse (reading guide enabled)
  │   └─ moveGuide(e)
  │       └─ guide.style.top = e.clientY
  │
  └─► Page refresh
      └─ Goes back to START (localStorage persists)
```

---

## State Management Diagram

```
┌──────────────────────────────────────────────────────────┐
│  IN-MEMORY STATE OBJECT (A11y.state)                     │
│                                                          │
│  {                                                       │
│    "dark": boolean,                                     │
│    "high-contrast": boolean,                            │
│    "large-font": boolean,                               │
│    "dyslexia-font": boolean,                            │
│    "reading-guide": boolean,                            │
│    "reduce-motion": boolean                             │
│  }                                                       │
└──────┬───────────────────────────────────────────────────┘
       │
       ├─ READ: loadState()
       │         ├─ localStorage → state
       │         └─ JSON.parse()
       │
       ├─ WRITE: saveState()
       │          ├─ state → JSON.stringify()
       │          └─ → localStorage
       │
       ├─ TOGGLE: toggleMode(mode)
       │           ├─ state[mode] = !state[mode]
       │           ├─ Check mutual exclusivity
       │           ├─ saveState()
       │           └─ applyAll()
       │
       └─ RESET: resetAll()
                  ├─ state = {}
                  ├─ saveState()
                  └─ applyAll()

┌──────────────────────────────────────────────────────────┐
│  VISUAL STATE (HTML Classes)                             │
│                                                          │
│  <html class="dark large-font dyslexia-font">           │
│                                                          │
│  One class added per enabled mode:                       │
│  • dark              → html.dark { }                     │
│  • high-contrast     → html.high-contrast { }           │
│  • large-font        → html.large-font { }              │
│  • dyslexia-font     → html.dyslexia-font { }           │
│  • reduce-motion     → html.reduce-motion { }           │
│  • reading-guide     → (special handling, no class)     │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│  UI STATE (Switch Elements)                              │
│                                                          │
│  <button class="a11y-switch is-on"                       │
│          aria-checked="true"                             │
│          data-mode="dark">                               │
│    <span class="a11y-switch-thumb"></span>              │
│  </button>                                               │
│                                                          │
│  Updated by updateSwitch(mode, isOn):                    │
│  • aria-checked = isOn ? 'true' : 'false'               │
│  • classList.toggle('is-on', isOn)                      │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│  PERSISTENT STATE (localStorage)                         │
│                                                          │
│  Key: "partido_a11y"                                     │
│  Value: JSON string                                      │
│                                                          │
│  {                                                       │
│    "dark": true,                                         │
│    "high-contrast": false,                               │
│    "large-font": true,                                   │
│    "dyslexia-font": false,                               │
│    "reading-guide": false,                               │
│    "reduce-motion": false                                │
│  }                                                       │
│                                                          │
│  Survives:                                               │
│  • Page refresh                                          │
│  • Tab close/reopen                                      │
│  • Browser close/reopen                                  │
│  • Days/weeks (until explicitly cleared)                 │
└──────────────────────────────────────────────────────────┘
```

---

## Event Listener Hierarchy

```
document
├─ DOMContentLoaded
│  └─► A11y.init()
│      ├─ initLiveRegion()
│      └─ initToolbar()
│
├─ click on #a11y-toggle
│  └─► togglePanel()
│
├─ click on #a11y-close
│  └─► closePanel()
│
├─ click on .a11y-switch (6 buttons)
│  └─► toggleMode(btn.dataset.mode)
│
├─ click on #a11y-reset
│  └─► resetAll()
│
├─ keydown (Escape)
│  └─► closePanel() if panel is open
│
└─ mousemove (when reading guide enabled)
   └─► moveGuide(e)
```

---

## CSS Cascade & Specificity

```
Global Styles (main.css)
  ↓
Tailwind Utilities
  ↓
Design Tokens (tokens.css)
  ├─ CSS Variables
  └─ Mode Selectors (html.dark, html.high-contrast, etc.)
  ↓
Toolbar Styles (accessibility.css)
  ├─ Base styles
  ├─ Interactive states (:hover, :focus-visible)
  ├─ Mode-specific overrides (html.dark #a11y-toggle { })
  └─ Media queries (@media)

Specificity Order:
1. Element selectors (button, div)
2. Class selectors (.a11y-switch, .is-on)
3. Attribute selectors ([aria-checked])
4. Compound selectors (#a11y-toolbar button)
5. Mode-specific compound (html.dark .a11y-switch)
6. !important (only in @media print)
```

---

## CSS Variables Used

```
From tokens.css (Stage 7-A):

Color Variables:
├─ --color-primary       → Button background
├─ --color-surface       → Panel background
├─ --color-surface-2     → Panel header background
├─ --color-border        → Border color
├─ --color-text          → Text color
├─ --color-text-muted    → Muted text
└─ --color-focus         → Focus indicator

Layout Variables:
├─ --shadow-md           → Box shadow
├─ --radius-lg           → Large border radius
└─ --radius-sm           → Small border radius

Mode-Specific Overrides:
├─ html.dark --color-*   → Dark mode colors
├─ html.high-contrast --color-* → HC colors
└─ html.large-font --* → Larger dimensions
```

---

## File Dependency Graph

```
header.php
├─ tokens.css         ← CSS Design Tokens (Stage 7-A)
├─ helpers.css
└─ main.css

footer.php
├─ accessibility-toolbar.php  ← HTML Component
├─ accessibility.css          ← Toolbar Styles
│  └─ tokens.css              ← Design Tokens
└─ accessibility.js           ← Toolbar Controller
   └─ localStorage            ← Browser API

Page HTML
├─ HTML structure
├─ [header content]
├─ [page content]
└─ [footer content]

Browser Rendering:
tokens.css (first!) → helpers.css → main.css → accessibility.css
                                 ↓
                         JS initialization
                                 ↓
                         DOM + CSS applied
```

---

## Accessibility Tree

```
Accessibility Structure (as seen by screen readers):

document
└─ body
   └─ [complementary] #a11y-toolbar
      ├─ button#a11y-toggle
      │  ├─ aria-expanded: false/true
      │  ├─ aria-controls: a11y-panel
      │  └─ aria-label: "Open accessibility options"
      │
      ├─ [group] #a11y-panel (hidden: boolean)
      │  ├─ aria-label: "Accessibility settings"
      │  │
      │  ├─ div.a11y-panel-header
      │  │  └─ button#a11y-close
      │  │     └─ aria-label: "Close accessibility panel"
      │  │
      │  └─ div.a11y-panel-body
      │     ├─ div.a11y-option
      │     │  ├─ span.a11y-icon (aria-hidden: true)
      │     │  ├─ span.a11y-label "Dark Mode"
      │     │  └─ button.a11y-switch
      │     │     ├─ role: switch
      │     │     ├─ aria-checked: false/true
      │     │     ├─ data-mode: dark
      │     │     ├─ aria-label: "Toggle dark mode"
      │     │     └─ span.a11y-switch-thumb
      │     │
      │     ├─ [5 more option divs like above]
      │     │
      │     └─ button#a11y-reset
      │        └─ aria-label: "Reset all accessibility settings"
      │
      └─ div#reading-guide-bar
         └─ aria-hidden: true

Live Region (for announcements):
└─ div.sr-only (aria-live: polite, aria-atomic: true)
   └─ [content: "Dark mode enabled."]
```

---

## Time & Event Sequence

```
Timeline of Page Load & Interaction:

t=0ms
  ├─ HTML parsing begins
  ├─ tokens.css loaded
  ├─ helpers.css loaded
  ├─ main.css loaded
  └─ theme-switcher.js executes (early init)

t~100ms
  ├─ Tailwind CSS applies
  ├─ Page content renders
  └─ accessibility-toolbar.php HTML inserted

t~150ms
  ├─ accessibility.css loads and applies
  └─ <html> now has mode classes from IIFE

t~200ms
  ├─ DOMContentLoaded fires
  ├─ A11y.init() called
  ├─ Event listeners attached
  └─ Live region created

t~250ms
  └─ Page fully interactive
     └─ Ready for user interaction

User clicks switch at t=5000ms
  ├─ click event fires (~0ms)
  ├─ toggleMode() runs (~1ms)
  ├─ state updated (~0ms)
  ├─ saveState() → localStorage (~5ms)
  ├─ applyAll() → classList.toggle (~1ms)
  ├─ Browser paints (~10ms)
  ├─ announceChange() → live region (~2ms)
  └─ Total: ~19ms (imperceptible to user)
```

---

## Memory Layout

```
JavaScript Memory (A11y module):

┌─ IIFE Scope ─────────────────────────────────┐
│                                              │
│  Constants:                                  │
│  ├─ MODES: Array[6]        (~200 bytes)    │
│  ├─ STORAGE_KEY: string    (~20 bytes)     │
│  └─ html: HTMLElement ref (~8 bytes)       │
│                                              │
│  Variables:                                  │
│  ├─ state: Object          (~100 bytes)    │
│  ├─ liveRegion: Element    (~8 bytes)      │
│  └─ [nested functions]     (~500 bytes)    │
│                                              │
│  Total: ~1 KB per page load                 │
│                                              │
└──────────────────────────────────────────────┘

DOM Memory:

┌─ HTML Elements ───────────────────────────────┐
│                                              │
│  #a11y-toolbar           (~2 KB)            │
│  ├─ #a11y-toggle         (~500 bytes)       │
│  ├─ #a11y-panel          (~3 KB)            │
│  │  ├─ .a11y-option × 6  (~200 × 6)        │
│  │  └─ #a11y-reset       (~200 bytes)      │
│  └─ #reading-guide-bar   (~200 bytes)      │
│                                              │
│  Live region (sr-only)   (~200 bytes)      │
│                                              │
│  Total: ~7 KB                                │
│                                              │
└──────────────────────────────────────────────┘

localStorage Memory:

┌─ Browser Storage ─────────────────────────────┐
│                                              │
│  Key: "partido_a11y"     (~15 bytes)        │
│  Value: JSON string      (~100 bytes)       │
│                                              │
│  Total: ~115 bytes                           │
│                                              │
└──────────────────────────────────────────────┘

Total Impact: ~8 KB JavaScript + DOM
```

---

## CSS Specificity Examples

```
Element selector (lowest):
button { }                              /* 0-0-1 */

Class selector:
.a11y-switch { }                        /* 0-1-0 */

Multiple classes:
.a11y-switch.is-on { }                  /* 0-2-0 */

ID selector:
#a11y-toggle { }                        /* 1-0-0 */

Attribute selector:
[aria-checked="true"] { }               /* 0-1-1 */

Pseudo-class:
.a11y-switch:focus-visible { }          /* 0-2-1 */

Compound selector:
html.dark #a11y-panel { }               /* 1-1-0 */

High specificity (mode-specific):
html.dark .a11y-switch.is-on { }        /* 0-3-0 (with context) */

!important (breaks cascade, use only for print):
display: none !important;               /* Overrides all */
```

---

## Browser API Usage

```
Window APIs:
├─ localStorage
│  ├─ getItem(key)       → Get string value
│  ├─ setItem(key, val)  → Set string value
│  └─ clear()            → Clear all
│
├─ matchMedia(query)     → Media query
│  ├─ (pointer: coarse)  → Touch device detection
│  └─ (prefers-*)        → User preferences
│
└─ setTimeout(fn, ms)    → Delayed execution

Document APIs:
├─ querySelector()       → Find element
├─ addEventListener()    → Attach listener
└─ documentElement       → <html> element

Element APIs:
├─ classList.toggle()    → Add/remove class
├─ setAttribute()        → Set attribute
├─ addEventListener()    → Attach listener
└─ focus()              → Set focus

Event APIs:
├─ event.key            → Key pressed
├─ event.clientY        → Mouse Y position
└─ event.preventDefault() → Cancel default

No external APIs (no fetch, no tracking)
```

---

## Mode Interaction Matrix

```
Can Enable Together:

         dark  HC   LF   DF   RG   RM
dark     ✓     ✗    ✓    ✓    ✓    ✓
HC       ✗     ✓    ✓    ✓    ✓    ✓
LF       ✓     ✓    ✓    ✓    ✓    ✓
DF       ✓     ✓    ✓    ✓    ✓    ✓
RG       ✓     ✓    ✓    ✓    ✓    ✓
RM       ✓     ✓    ✓    ✓    ✓    ✓

Legend:
✓ = Can both be enabled
✗ = Mutually exclusive (only one can be true)

dark  = Dark Mode
HC    = High Contrast
LF    = Large Font
DF    = Dyslexia Font
RG    = Reading Guide
RM    = Reduce Motion
```

---

**Architecture Document Complete**  
**Stage 7-B Production Ready**  
**April 22, 2026**
