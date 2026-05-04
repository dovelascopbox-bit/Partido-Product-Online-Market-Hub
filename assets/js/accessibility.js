/**
 * ────────────────────────────────────────────────────────────
 * ACCESSIBILITY TOOLBAR CONTROLLER
 * Partido Product Online Market Hub - Stage 7-B
 * 
 * Manages all accessibility modes:
 * - Dark Mode
 * - High Contrast Mode
 * - Large Font Mode
 * - Dyslexia-Friendly Font
 * - Reading Guide (visual aid)
 * - Reduce Motion
 * 
 * Features:
 * - localStorage persistence (survives page reloads)
 * - System preference detection (prefers-color-scheme, prefers-reduced-motion)
 * - Mutually exclusive modes (dark ⊗ high-contrast)
 * - Screen reader announcements
 * - Zero accessibility issues in UI itself
 * ──────────────────────────────────────────────────────────── */

const A11y = (() => {

  // ── CONFIGURATION ──────────────────────────────────────────
  const MODES = ['dark', 'high-contrast', 'large-font', 
                 'dyslexia-font', 'reading-guide', 'reduce-motion'];
  const STORAGE_KEY = 'partido_a11y';
  const html = document.documentElement;

  // ── STATE ──────────────────────────────────────────────────
  let state = {};
  let liveRegion = null;

  /**
   * Load accessibility state from localStorage
   */
  function loadState() {
    try {
      state = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
    } catch (e) {
      console.warn('Could not load accessibility state:', e);
      state = {};
    }
  }

  /**
   * Save accessibility state to localStorage
   */
  function saveState() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch (e) {
      console.warn('Could not save accessibility state:', e);
    }
  }

  /**
   * Apply all modes to <html> element and update UI
   */
  function applyAll() {
    MODES.forEach(mode => {
      if (mode === 'reading-guide') {
        toggleReadingGuide(state[mode] || false);
      } else {
        html.classList.toggle(mode, state[mode] || false);
      }
      updateSwitch(mode, state[mode] || false);
    });
  }

  /**
   * Toggle a single mode on/off
   * Handles mutual exclusivity (dark vs high-contrast)
   */
  function toggleMode(mode) {
    state[mode] = !state[mode];

    // Dark and High Contrast are mutually exclusive
    if (mode === 'dark' && state[mode]) {
      state['high-contrast'] = false;
    }
    if (mode === 'high-contrast' && state[mode]) {
      state['dark'] = false;
    }

    saveState();
    applyAll();
    announceChange(mode, state[mode]);
  }

  /**
   * Reset all accessibility settings to defaults
   */
  function resetAll() {
    state = {};
    saveState();
    applyAll();
    announce('All accessibility settings have been reset.');
  }

  // ── UI UPDATES ─────────────────────────────────────────────

  /**
   * Update switch button appearance and aria-checked state
   */
  function updateSwitch(mode, isOn) {
    const btn = document.querySelector(`[data-mode="${mode}"]`);
    if (!btn) return;
    
    btn.setAttribute('aria-checked', isOn ? 'true' : 'false');
    btn.classList.toggle('is-on', isOn);
  }

  // ── READING GUIDE (VISUAL AID) ─────────────────────────────

  /**
   * Toggle reading guide bar (follows mouse)
   * Disabled on touch devices (coarse pointer)
   */
  function toggleReadingGuide(isOn) {
    const guide = document.getElementById('reading-guide-bar');
    if (!guide) return;

    const isTouchDevice = window.matchMedia('(pointer: coarse)').matches;

    if (isOn && !isTouchDevice) {
      guide.style.display = 'block';
      document.addEventListener('mousemove', moveGuide);
    } else {
      guide.style.display = 'none';
      document.removeEventListener('mousemove', moveGuide);
    }
  }

  /**
   * Move reading guide bar to follow mouse position
   */
  function moveGuide(e) {
    const guide = document.getElementById('reading-guide-bar');
    if (guide) {
      guide.style.top = (e.clientY - 20) + 'px';
    }
  }

  // ── SCREEN READER ANNOUNCEMENTS ────────────────────────────

  /**
   * Initialize live region for screen reader announcements
   * This is the accessible way to announce changes
   */
  function initLiveRegion() {
    liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    document.body.appendChild(liveRegion);
  }

  /**
   * Announce message to screen reader users
   */
  function announce(msg) {
    if (!liveRegion) return;
    
    // Clear and re-set to trigger announcement
    liveRegion.textContent = '';
    setTimeout(() => {
      liveRegion.textContent = msg;
    }, 50);
  }

  /**
   * Announce when a mode is toggled
   */
  function announceChange(mode, isOn) {
    const labels = {
      'dark': 'Dark mode',
      'high-contrast': 'High contrast mode',
      'large-font': 'Large font mode',
      'dyslexia-font': 'Dyslexia font mode',
      'reading-guide': 'Reading guide',
      'reduce-motion': 'Reduce motion mode'
    };
    
    const label = labels[mode] || mode;
    const status = isOn ? 'enabled' : 'disabled';
    announce(`${label} ${status}.`);
  }

  // ── SYSTEM PREFERENCE DETECTION ────────────────────────────

  /**
   * Auto-detect system preferences for dark mode and reduced motion
   * Only applies if user hasn't already set a preference
   */
  function detectSystemPreferences() {
    // Dark mode preference
    if (state['dark'] === undefined) {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (prefersDark) {
        state['dark'] = true;
        saveState();
      }
    }

    // Reduced motion preference
    if (state['reduce-motion'] === undefined) {
      const prefersReducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
      ).matches;
      if (prefersReducedMotion) {
        state['reduce-motion'] = true;
        saveState();
      }
    }
  }

  // ── TOOLBAR INITIALIZATION ─────────────────────────────────

  /**
   * Initialize toolbar button and panel interactions
   */
  function initToolbar() {
    const toggle = document.getElementById('a11y-toggle');
    const panel = document.getElementById('a11y-panel');
    const close = document.getElementById('a11y-close');
    const reset = document.getElementById('a11y-reset');

    // Toggle button opens/closes panel
    if (toggle && panel) {
      toggle.addEventListener('click', () => {
        const isOpen = !panel.hidden;
        panel.hidden = isOpen;
        toggle.setAttribute('aria-expanded', !isOpen);
        
        // Focus first switch when opening
        if (!isOpen) {
          const firstSwitch = panel.querySelector('.a11y-switch');
          if (firstSwitch) {
            setTimeout(() => firstSwitch.focus(), 100);
          }
        }
      });
    }

    // Close button closes panel
    if (close && panel && toggle) {
      close.addEventListener('click', () => {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      });
    }

    // Reset button
    if (reset) {
      reset.addEventListener('click', resetAll);
    }

    // Mode switch buttons
    document.querySelectorAll('.a11y-switch').forEach(btn => {
      btn.addEventListener('click', () => {
        const mode = btn.dataset.mode;
        if (mode) {
          toggleMode(mode);
        }
      });
    });

    // Escape key closes panel
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && panel && !panel.hidden) {
        panel.hidden = true;
        if (toggle) {
          toggle.setAttribute('aria-expanded', 'false');
          toggle.focus();
        }
      }
    });
  }

  // ── PUBLIC API ─────────────────────────────────────────────

  /**
   * Initialize accessibility system
   * Call once on DOMContentLoaded
   */
  function init() {
    loadState();
    detectSystemPreferences();
    applyAll();
    initLiveRegion();
    initToolbar();
  }

  return {
    init,
    // Expose for testing/advanced usage
    toggleMode,
    resetAll,
    getState: () => ({ ...state }),
    getMode: (mode) => state[mode] || false
  };

})();

/**
 * Apply classes IMMEDIATELY (before DOM ready) to prevent flash
 * This runs before DOMContentLoaded to avoid visual flicker
 */
(() => {
  try {
    const state = JSON.parse(localStorage.getItem('partido_a11y')) || {};
    const html = document.documentElement;
    
    ['dark', 'high-contrast', 'large-font', 'dyslexia-font', 'reduce-motion']
      .forEach(mode => {
        if (state[mode]) {
          html.classList.add(mode);
        }
      });
  } catch (e) {
    console.warn('Could not apply early accessibility classes:', e);
  }
})();

/**
 * Initialize when DOM is ready
 */
document.addEventListener('DOMContentLoaded', A11y.init);
