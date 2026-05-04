/**
 * KEYBOARD.JS - Reusable keyboard & ARIA utilities for accessibility
 * 
 * Provides helper functions for:
 * - Modal focus trapping
 * - Keyboard event handling
 * - Screen reader announcements
 * - Interactive widget behavior
 */

// ── MODAL & FOCUS MANAGEMENT ────────────────────────────────────────

/**
 * Trap focus within a modal element
 * Prevents Tab/Shift+Tab from leaving the modal
 * @param {HTMLElement} modalEl - The modal container
 */
function trapFocus(modalEl) {
  if (!modalEl) return;
  
  const focusable = modalEl.querySelectorAll(
    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
  );
  
  if (focusable.length === 0) return;
  
  const first = focusable[0];
  const last = focusable[focusable.length - 1];
  
  const handleKeyDown = (e) => {
    if (e.key !== 'Tab') return;
    
    if (e.shiftKey) {
      // Shift + Tab on first element: focus last element
      if (document.activeElement === first) {
        e.preventDefault();
        last.focus();
      }
    } else {
      // Tab on last element: focus first element
      if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    }
  };
  
  modalEl.addEventListener('keydown', handleKeyDown);
  return () => modalEl.removeEventListener('keydown', handleKeyDown);
}

/**
 * Open a modal and set up focus trapping
 * @param {string} modalId - ID of the modal element
 * @param {string} triggerId - ID of the button that triggered the modal
 */
function openModal(modalId, triggerId) {
  const modal = document.getElementById(modalId);
  const trigger = document.getElementById(triggerId);
  
  if (!modal) {
    console.warn(`Modal with ID "${modalId}" not found`);
    return;
  }
  
  // Show modal
  modal.hidden = false;
  modal.setAttribute('aria-modal', 'true');
  
  // Hide background content from screen readers
  const main = document.querySelector('main');
  const nav = document.querySelector('nav');
  if (main) main.setAttribute('aria-hidden', 'true');
  if (nav) nav.setAttribute('aria-hidden', 'true');
  
  // Set up focus trap
  trapFocus(modal);
  
  // Focus first focusable element
  const firstFocusable = modal.querySelector('[autofocus], button, input');
  if (firstFocusable) {
    setTimeout(() => firstFocusable.focus(), 0);
  }
  
  // Store trigger for later focus return
  modal._trigger = trigger;
  
  // Announce modal to screen readers
  announceToScreenReader(`${modal.getAttribute('aria-labelledby') ? 'Dialog opened' : 'Modal opened'}`);
}

/**
 * Close a modal and restore focus
 * @param {string} modalId - ID of the modal element
 */
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  
  if (!modal) {
    console.warn(`Modal with ID "${modalId}" not found`);
    return;
  }
  
  // Hide modal
  modal.hidden = true;
  modal.removeAttribute('aria-modal');
  
  // Show background content
  const main = document.querySelector('main');
  const nav = document.querySelector('nav');
  if (main) main.removeAttribute('aria-hidden');
  if (nav) nav.removeAttribute('aria-hidden');
  
  // Return focus to trigger button
  if (modal._trigger) {
    modal._trigger.focus();
  }
  
  announceToScreenReader('Modal closed');
}

/**
 * Handle Escape key to close all open modals
 */
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const openModals = document.querySelectorAll('[role="dialog"]:not([hidden]), [aria-modal="true"]:not([hidden])');
    openModals.forEach(modal => {
      if (modal.id) closeModal(modal.id);
    });
  }
});

// ── SCREEN READER ANNOUNCEMENTS ────────────────────────────────────

let liveRegion = null;

/**
 * Initialize or get the screen reader live region
 * Creates it if it doesn't exist
 * @returns {HTMLElement} The live region element
 */
function getOrCreateLiveRegion() {
  if (liveRegion) return liveRegion;
  
  liveRegion = document.getElementById('sr-live-region');
  if (liveRegion) return liveRegion;
  
  liveRegion = document.createElement('div');
  liveRegion.id = 'sr-live-region';
  liveRegion.setAttribute('aria-live', 'polite');
  liveRegion.setAttribute('aria-atomic', 'true');
  liveRegion.className = 'sr-only';
  liveRegion.setAttribute('role', 'status');
  document.body.appendChild(liveRegion);
  
  return liveRegion;
}

/**
 * Announce a message to screen readers
 * @param {string} message - The message to announce
 */
function announceToScreenReader(message) {
  const region = getOrCreateLiveRegion();
  
  // Clear previous message
  region.textContent = '';
  
  // Set new message after a small delay to ensure announcement
  setTimeout(() => {
    region.textContent = message;
  }, 50);
}

/**
 * Announce an error to screen readers with high priority
 * @param {string} message - The error message
 */
function announceError(message) {
  const region = getOrCreateLiveRegion();
  region.setAttribute('role', 'alert');
  region.textContent = '';
  
  setTimeout(() => {
    region.textContent = `Error: ${message}`;
  }, 50);
  
  // Reset to polite after announcement
  setTimeout(() => {
    region.setAttribute('role', 'status');
  }, 2000);
}

// ── FORM ACCESSIBILITY HELPERS ─────────────────────────────────────

/**
 * Show form field error and announce to screen readers
 * @param {string|HTMLElement} fieldOrId - Input field or its ID
 * @param {string} errorMessage - Error message to display
 */
function showFieldError(fieldOrId, errorMessage) {
  const field = typeof fieldOrId === 'string' 
    ? document.getElementById(fieldOrId) 
    : fieldOrId;
  
  if (!field) return;
  
  // Mark field as invalid
  field.setAttribute('aria-invalid', 'true');
  field.classList.add('field-error');
  
  // Find or create error message element
  let errorEl = document.getElementById(`${field.id}-error`);
  if (!errorEl) {
    errorEl = document.createElement('span');
    errorEl.id = `${field.id}-error`;
    errorEl.className = 'error-msg';
    errorEl.setAttribute('role', 'alert');
    field.parentNode.appendChild(errorEl);
  }
  
  errorEl.textContent = errorMessage;
  errorEl.hidden = false;
  
  // Link error to field
  const describedBy = field.getAttribute('aria-describedby') || '';
  const ids = describedBy.split(' ').filter(id => id !== `${field.id}-error`);
  ids.push(`${field.id}-error`);
  field.setAttribute('aria-describedby', ids.join(' '));
  
  // Announce error
  announceError(errorMessage);
}

/**
 * Clear form field error
 * @param {string|HTMLElement} fieldOrId - Input field or its ID
 */
function clearFieldError(fieldOrId) {
  const field = typeof fieldOrId === 'string' 
    ? document.getElementById(fieldOrId) 
    : fieldOrId;
  
  if (!field) return;
  
  field.removeAttribute('aria-invalid');
  field.classList.remove('field-error');
  
  const errorEl = document.getElementById(`${field.id}-error`);
  if (errorEl) {
    errorEl.textContent = '';
    errorEl.hidden = true;
  }
}

/**
 * Focus first invalid field in a form
 * @param {HTMLFormElement} form - The form element
 * @returns {HTMLElement|null} The first invalid field, or null if none found
 */
function focusFirstInvalidField(form) {
  const invalidFields = form.querySelectorAll('[aria-invalid="true"]');
  if (invalidFields.length > 0) {
    invalidFields[0].focus();
    return invalidFields[0];
  }
  return null;
}

// ── DROPDOWN & MENU BEHAVIORS ──────────────────────────────────────

/**
 * Set up a dropdown menu with keyboard support
 * @param {string} triggerId - ID of the button that triggers the dropdown
 * @param {string} menuId - ID of the menu container
 */
function setupDropdownMenu(triggerId, menuId) {
  const trigger = document.getElementById(triggerId);
  const menu = document.getElementById(menuId);
  
  if (!trigger || !menu) return;
  
  // Menu items must have role="menuitem"
  const items = menu.querySelectorAll('[role="menuitem"], li > a, li > button');
  
  trigger.addEventListener('click', () => {
    const isOpen = menu.hidden === false;
    menu.hidden = isOpen;
    trigger.setAttribute('aria-expanded', !isOpen);
    
    if (!isOpen) {
      // Focus first item when opening
      const firstItem = items[0];
      if (firstItem) {
        setTimeout(() => firstItem.focus(), 0);
      }
      announceToScreenReader('Menu opened');
    }
  });
  
  // Arrow key navigation
  trigger.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      menu.hidden = false;
      trigger.setAttribute('aria-expanded', 'true');
      const firstItem = items[0];
      if (firstItem) firstItem.focus();
    }
  });
  
  // Menu item navigation
  items.forEach((item, index) => {
    item.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextItem = items[index + 1];
        if (nextItem) nextItem.focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevItem = items[index - 1];
        if (prevItem) {
          prevItem.focus();
        } else {
          // Focus trigger when at first item and pressing up
          trigger.focus();
        }
      } else if (e.key === 'Escape') {
        e.preventDefault();
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        trigger.focus();
      }
    });
    
    // Close menu on selection
    item.addEventListener('click', () => {
      menu.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
    });
  });
  
  // Close menu when clicking outside
  document.addEventListener('click', (e) => {
    if (!trigger.contains(e.target) && !menu.contains(e.target)) {
      menu.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
    }
  });
}

// ── STAR RATING WIDGET ─────────────────────────────────────────────

/**
 * Set up keyboard-accessible star rating widget
 * @param {string} containerSelector - CSS selector for the star rating container
 * @param {string} inputId - ID of the hidden input to store the rating
 */
function setupStarRating(containerSelector, inputId) {
  const container = document.querySelector(containerSelector);
  const input = document.getElementById(inputId);
  const buttons = container?.querySelectorAll('[role="button"], button');
  const liveRegion = document.getElementById(`${inputId}-announce`);
  
  if (!container || !buttons.length) return;
  
  buttons.forEach((btn, index) => {
    const rating = index + 1;
    
    // Click handler
    btn.addEventListener('click', () => {
      selectRating(rating, buttons, input, liveRegion);
    });
    
    // Keyboard handler
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        selectRating(rating, buttons, input, liveRegion);
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        const nextBtn = buttons[Math.min(index + 1, buttons.length - 1)];
        if (nextBtn) nextBtn.focus();
      } else if (e.key === 'ArrowLeft') {
        e.preventDefault();
        const prevBtn = buttons[Math.max(index - 1, 0)];
        if (prevBtn) prevBtn.focus();
      }
    });
  });
}

/**
 * Helper to select a rating and update UI
 * @private
 */
function selectRating(rating, buttons, input, liveRegion) {
  // Update input
  if (input) {
    input.value = rating;
  }
  
  // Update button states
  buttons.forEach((btn, index) => {
    const isSelected = (index + 1) <= rating;
    btn.setAttribute('aria-pressed', isSelected);
    btn.classList.toggle('selected', isSelected);
  });
  
  // Announce to screen reader
  if (liveRegion) {
    liveRegion.textContent = `You selected ${rating} out of ${buttons.length} stars`;
  } else {
    announceToScreenReader(`You selected ${rating} out of ${buttons.length} stars`);
  }
}

// ── TOAST NOTIFICATIONS ────────────────────────────────────────────

/**
 * Show a toast notification with screen reader announcement
 * @param {string} message - The message to display
 * @param {string} type - Type: 'success', 'error', 'info', 'warning'
 * @param {number} duration - How long to show (ms), default 5000
 */
function showToast(message, type = 'info', duration = 5000) {
  let container = document.getElementById('toast-container');
  
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  
  // Create toast element
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
  
  // Add content
  const content = document.createElement('span');
  content.textContent = message;
  toast.appendChild(content);
  
  // Add close button
  const closeBtn = document.createElement('button');
  closeBtn.className = 'toast-close';
  closeBtn.setAttribute('aria-label', 'Dismiss notification');
  closeBtn.innerHTML = '&times;';
  closeBtn.addEventListener('click', () => {
    toast.remove();
  });
  toast.appendChild(closeBtn);
  
  container.appendChild(toast);
  
  // Auto-dismiss
  if (duration > 0) {
    setTimeout(() => {
      toast.remove();
    }, duration);
  }
  
  // Also announce to screen reader
  announceToScreenReader(message);
}

// ── TAB PANEL KEYBOARD SUPPORT ─────────────────────────────────────

/**
 * Set up keyboard navigation for tabbed interfaces
 * @param {string} tabListSelector - CSS selector for the tab list
 * @param {string} panelSelector - CSS selector for panels
 */
function setupTabPanel(tabListSelector, panelSelector) {
  const tabList = document.querySelector(tabListSelector);
  const tabs = tabList?.querySelectorAll('[role="tab"]');
  const panels = document.querySelectorAll(panelSelector);
  
  if (!tabs || !panels) return;
  
  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
      selectTab(tabs, panels, index);
    });
    
    tab.addEventListener('keydown', (e) => {
      let targetIndex = index;
      
      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
        e.preventDefault();
        targetIndex = (index + 1) % tabs.length;
      } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
        e.preventDefault();
        targetIndex = (index - 1 + tabs.length) % tabs.length;
      } else if (e.key === 'Home') {
        e.preventDefault();
        targetIndex = 0;
      } else if (e.key === 'End') {
        e.preventDefault();
        targetIndex = tabs.length - 1;
      }
      
      if (targetIndex !== index) {
        selectTab(tabs, panels, targetIndex);
      }
    });
  });
}

/**
 * Helper to select a tab
 * @private
 */
function selectTab(tabs, panels, index) {
  // Deselect all tabs
  tabs.forEach(tab => {
    tab.setAttribute('aria-selected', 'false');
    tab.tabIndex = -1;
  });
  
  // Hide all panels
  panels.forEach(panel => {
    panel.hidden = true;
  });
  
  // Select current tab
  tabs[index].setAttribute('aria-selected', 'true');
  tabs[index].tabIndex = 0;
  tabs[index].focus();
  
  // Show current panel
  const panelId = tabs[index].getAttribute('aria-controls');
  if (panelId) {
    const panel = document.getElementById(panelId);
    if (panel) {
      panel.hidden = false;
    }
  }
}

// ── SKIP LINK ENHANCEMENT ──────────────────────────────────────────

/**
 * Enhance skip link to focus and scroll smoothly
 */
document.addEventListener('DOMContentLoaded', () => {
  const skipLinks = document.querySelectorAll('a.skip-link');
  
  skipLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      const targetId = link.getAttribute('href')?.substring(1);
      if (targetId) {
        e.preventDefault();
        const target = document.getElementById(targetId);
        if (target) {
          target.setAttribute('tabindex', '-1');
          target.focus();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          
          // Remove tabindex after focus to restore normal tab flow
          setTimeout(() => {
            target.removeAttribute('tabindex');
          }, 100);
        }
      }
    });
  });
});

// ── EXPORT FOR USE IN OTHER MODULES ────────────────────────────────

if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    trapFocus,
    openModal,
    closeModal,
    getOrCreateLiveRegion,
    announceToScreenReader,
    announceError,
    showFieldError,
    clearFieldError,
    focusFirstInvalidField,
    setupDropdownMenu,
    setupStarRating,
    showToast,
    setupTabPanel
  };
}
