<!-- ════════════════════════════════════════════════════════════════
     ACCESSIBILITY TOOLBAR COMPONENT
     Partido Product Online Market Hub - Stage 7-B
     
     This component provides user-facing controls for:
     • Dark/Light modes
     • High Contrast mode
     • Font size adjustment
     • Dyslexia-friendly font
     • Reading guide visual aid
     • Reduce motion for vestibular issues
     
     All changes are instant, persist across pages, and fully accessible.
     ═════════════════════════════════════════════════════════════════ -->

<div id="a11y-toolbar" role="complementary" aria-label="Accessibility options">

  <!-- Main toggle button (wheelchair symbol) -->
  <button 
    id="a11y-toggle" 
    aria-expanded="false"
    aria-controls="a11y-panel"
    aria-label="Open accessibility options"
    title="Accessibility Options">
    ♿
  </button>

  <!-- Accessibility settings panel (initially hidden) -->
  <div id="a11y-panel" hidden role="group" aria-label="Accessibility settings">
    
    <!-- Panel header with title and close button -->
    <div class="a11y-panel-header">
      <span>Accessibility</span>
      <button 
        id="a11y-close" 
        aria-label="Close accessibility panel"
        title="Close">
        ✕
      </button>
    </div>

    <!-- Panel body with all toggle options -->
    <div class="a11y-panel-body">

      <!-- Dark Mode Toggle -->
      <div class="a11y-option">
        <span class="a11y-icon" aria-hidden="true">🌙</span>
        <span class="a11y-label">Dark Mode</span>
        <button 
          class="a11y-switch" 
          role="switch" 
          aria-checked="false"
          data-mode="dark"
          aria-label="Toggle dark mode"
          title="Activates dark theme for reduced eye strain">
          <span class="a11y-switch-thumb"></span>
        </button>
      </div>

      <!-- High Contrast Mode Toggle -->
      <div class="a11y-option">
        <span class="a11y-icon" aria-hidden="true">◑</span>
        <span class="a11y-label">High Contrast</span>
        <button 
          class="a11y-switch" 
          role="switch" 
          aria-checked="false"
          data-mode="high-contrast"
          aria-label="Toggle high contrast mode"
          title="WCAG AAA compliance (21:1 contrast ratio)">
          <span class="a11y-switch-thumb"></span>
        </button>
      </div>

      <!-- Large Font Toggle -->
      <div class="a11y-option">
        <span class="a11y-icon" aria-hidden="true">A+</span>
        <span class="a11y-label">Large Font</span>
        <button 
          class="a11y-switch" 
          role="switch" 
          aria-checked="false"
          data-mode="large-font"
          aria-label="Toggle large font mode"
          title="Enlarges text by 20% for easier reading">
          <span class="a11y-switch-thumb"></span>
        </button>
      </div>

      <!-- Dyslexia-Friendly Font Toggle -->
      <div class="a11y-option">
        <span class="a11y-icon" aria-hidden="true">✦</span>
        <span class="a11y-label">Dyslexia Font</span>
        <button 
          class="a11y-switch" 
          role="switch" 
          aria-checked="false"
          data-mode="dyslexia-font"
          aria-label="Toggle dyslexia-friendly font"
          title="OpenDyslexic font with enhanced spacing">
          <span class="a11y-switch-thumb"></span>
        </button>
      </div>

      <!-- Reading Guide Toggle (visual guide that follows cursor) -->
      <div class="a11y-option">
        <span class="a11y-icon" aria-hidden="true">—</span>
        <span class="a11y-label">Reading Guide</span>
        <button 
          class="a11y-switch" 
          role="switch" 
          aria-checked="false"
          data-mode="reading-guide"
          aria-label="Toggle reading guide line"
          title="Shows visual guide line that follows your cursor">
          <span class="a11y-switch-thumb"></span>
        </button>
      </div>

      <!-- Reduce Motion Toggle (for users with vestibular disorders) -->
      <div class="a11y-option">
        <span class="a11y-icon" aria-hidden="true">⏸</span>
        <span class="a11y-label">Reduce Motion</span>
        <button 
          class="a11y-switch" 
          role="switch" 
          aria-checked="false"
          data-mode="reduce-motion"
          aria-label="Toggle reduce motion mode"
          title="Disables animations and transitions">
          <span class="a11y-switch-thumb"></span>
        </button>
      </div>

      <!-- Reset All Button -->
      <button 
        id="a11y-reset" 
        aria-label="Reset all accessibility settings to defaults"
        title="Clear all settings">
        ↺ Reset All
      </button>

    </div>

  </div>

</div>

<!-- Reading Guide Bar Element (appears when reading guide mode is on) -->
<div 
  id="reading-guide-bar" 
  aria-hidden="true" 
  style="display:none">
</div>
