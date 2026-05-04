/**
 * ════════════════════════════════════════════════════════════════════
 * COOKIE CONSENT MANAGER
 * Partido Product Online Market Hub - GDPR Compliance
 * 
 * Handles:
 * - Cookie consent banner display
 * - Granular cookie preferences
 * - localStorage persistence
 * - Accessibiliity features
 * ════════════════════════════════════════════════════════════════════
 */

const CookieConsent = (() => {
  // ── CONFIGURATION ──────────────────────────────────────────────
  const STORAGE_KEY = 'partido_cookie_consent';
  const COOKIE_CATEGORIES = ['essential', 'analytics', 'marketing'];
  const CONSENT_EXPIRY_DAYS = 365;
  
  // DOM Elements
  let banner = null;
  let modal = null;
  let switches = {};
  let buttons = {};
  
  // ── STATE MANAGEMENT ───────────────────────────────────────
  
  /**
   * Get consent state from localStorage
   */
  function getConsent() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY)) || null;
    } catch (e) {
      return null;
    }
  }
  
  /**
   * Save consent state to localStorage
   */
  function saveConsent(consent) {
    try {
      consent.timestamp = new Date().toISOString();
      localStorage.setItem(STORAGE_KEY, JSON.stringify(consent));
      
      // Trigger custom event for other scripts to react
      window.dispatchEvent(new CustomEvent('cookieConsentUpdated', { detail: consent }));
    } catch (e) {
      console.warn('Could not save cookie consent:', e);
    }
  }
  
  /**
   * Check if consent has been given
   */
  function hasConsent() {
    const consent = getConsent();
    return consent !== null && consent.essential === true;
  }
  
  /**
   * Get timestamp of when consent was given
   */
  function getConsentTimestamp() {
    const consent = getConsent();
    return consent ? consent.timestamp : null;
  }
  
  // ── UI MANAGEMENT ───────────────────────────────────────────────
  
  /**
   * Initialize DOM elements
   */
  function initElements() {
    banner = document.getElementById('cookie-consent-banner');
    modal = document.getElementById('cookie-settings-modal');
    
    // Switches in banner
    switches = {
      analytics: document.getElementById('cookie-analytics'),
      marketing: document.getElementById('cookie-marketing')
    };
    
    // Switches in modal
    switches.analyticsModal = document.getElementById('cookie-analytics-modal');
    switches.marketingModal = document.getElementById('cookie-marketing-modal');
    
    // Buttons
    buttons = {
      acceptAll: document.getElementById('cookie-accept-all'),
      savePreferences: document.getElementById('cookie-save-preferences'),
      manageSettings: document.getElementById('cookie-manage-settings'),
      close: document.getElementById('cookie-modal-close'),
      closeBtn: document.getElementById('cookie-modal-close-btn'),
      rejectAll: document.getElementById('cookie-reject-all'),
      saveSettings: document.getElementById('cookie-save-settings')
    };
  }
  
  /**
   * Show the consent banner
   */
  function showBanner() {
    if (banner) {
      banner.hidden = false;
      
      // Focus for accessibility
      const firstFocusable = banner.querySelector('button, input');
      if (firstFocusable) {
        setTimeout(() => firstFocusable.focus(), 100);
      }
      
      // Announce to screen reader
      announce('Cookie consent banner opened. Please review and accept or customize your cookie preferences.');
    }
  }
  
  /**
   * Hide the consent banner
   */
  function hideBanner() {
    if (banner) {
      banner.hidden = true;
    }
  }
  
  /**
   * Show the settings modal
   */
  function showModal() {
    if (modal) {
      modal.hidden = false;
      
      // Focus first element
      const firstSwitch = modal.querySelector('.cookie-switch');
      if (firstSwitch) {
        setTimeout(() => firstSwitch.focus(), 100);
      }
      
      announce('Cookie settings modal opened.');
    }
  }
  
  /**
   * Hide the settings modal
   */
  function hideModal() {
    if (modal) {
      modal.hidden = true;
    }
  }
  
  /**
   * Update switch UI state
   */
  function updateSwitch(switchEl, isOn) {
    if (!switchEl) return;
    
    switchEl.setAttribute('aria-checked', isOn ? 'true' : 'false');
    switchEl.classList.toggle('is-on', isOn);
  }
  
  /**
   * Update all switches based on saved consent
   */
  function updateAllSwitches(consent) {
    // Banner switches
    if (switches.analytics) {
      updateSwitch(switches.analytics, consent.analytics === true);
    }
    if (switches.marketing) {
      updateSwitch(switches.marketing, consent.marketing === true);
    }
    
    // Modal switches
    if (switches.analyticsModal) {
      updateSwitch(switches.analyticsModal, consent.analytics === true);
    }
    if (switches.marketingModal) {
      updateSwitch(switches.marketingModal, consent.marketing === true);
    }
  }
  
  // ── EVENT HANDLERS ────────────────────────────────────────
  
  /**
   * Toggle switch on click
   */
  function handleSwitchClick(switchEl) {
    if (!switchEl) return;
    
    const currentState = switchEl.getAttribute('aria-checked') === 'true';
    const newState = !currentState;
    
    updateSwitch(switchEl, newState);
    announce(`${switchEl.id} ${newState ? 'enabled' : 'disabled'}.`);
  }
  
  /**
   * Accept all cookies
   */
  function handleAcceptAll() {
    const consent = {
      essential: true,
      analytics: true,
      marketing: true
    };
    
    saveConsent(consent);
    hideBanner();
    updateAllSwitches(consent);
    
    // Enable Google Analytics and other tracking scripts
    enableCookies(consent);
    
    announce('All cookies accepted. Your preferences have been saved.');
  }
  
  /**
   * Reject all optional cookies
   */
  function handleRejectAll() {
    const consent = {
      essential: true,
      analytics: false,
      marketing: false
    };
    
    saveConsent(consent);
    hideModal();
    updateAllSwitches(consent);
    
    // Disable tracking
    disableCookies();
    
    announce('Optional cookies rejected. Only essential cookies are enabled.');
  }
  
  /**
   * Save current preferences
   */
  function handleSavePreferences() {
    const consent = {
      essential: true,
      analytics: switches.analytics ? switches.analytics.getAttribute('aria-checked') === 'true' : false,
      marketing: switches.marketing ? switches.marketing.getAttribute('aria-checked') === 'true' : false
    };
    
    saveConsent(consent);
    hideBanner();
    
    // Enable/disable based on preferences
    if (consent.analytics || consent.marketing) {
      enableCookies(consent);
    } else {
      disableCookies();
    }
    
    announce('Cookie preferences saved.');
  }
  
  /**
   * Save settings from modal
   */
  function handleSaveSettings() {
    const consent = {
      essential: true,
      analytics: switches.analyticsModal ? switches.analyticsModal.getAttribute('aria-checked') === 'true' : false,
      marketing: switches.marketingModal ? switches.marketingModal.getAttribute('aria-checked') === 'true' : false
    };
    
    saveConsent(consent);
    hideModal();
    hideBanner();
    
    // Enable/disable based on preferences
    if (consent.analytics || consent.marketing) {
      enableCookies(consent);
    } else {
      disableCookies();
    }
    
    announce('Cookie settings saved.');
  }
  
  /**
   * Open settings modal
   */
  function handleManageSettings() {
    // Sync banner switches to modal switches
    if (switches.analytics && switches.analyticsModal) {
      const analyticsState = switches.analytics.getAttribute('aria-checked') === 'true';
      updateSwitch(switches.analyticsModal, analyticsState);
    }
    if (switches.marketing && switches.marketingModal) {
      const marketingState = switches.marketing.getAttribute('aria-checked') === 'true';
      updateSwitch(switches.marketingModal, marketingState);
    }
    
    showModal();
  }
  
  /**
   * Close modal
   */
  function handleCloseModal() {
    hideModal();
    announce('Cookie settings closed.');
  }
  
  // ── COOKIE ENABLE/DISABLE ───────────────────────────────────
  
  /**
   * Enable cookies/scripts based on consent
   */
  function enableCookies(consent) {
    // This function can be extended to load external scripts
    // Currently, this is where you would:
    // - Load Google Analytics
    // - Load Facebook Pixel
    // - Load other tracking scripts
    
    if (consent.analytics) {
      console.log('Analytics cookies enabled');
      // window.dataLayer = window.dataLayer || [];
      // window.dataLayer.push({'event': 'cookie_consent_analytics'});
    }
    
    if (consent.marketing) {
      console.log('Marketing cookies enabled');
      // Load marketing pixels here
    }
    
    // Dispatch event for external scripts
    window.dispatchEvent(new CustomEvent('cookiesEnabled', { detail: consent }));
  }
  
  /**
   * Disable all optional cookies
   */
  function disableCookies() {
    console.log('Optional cookies disabled');
    window.dispatchEvent(new CustomEvent('cookiesDisabled'));
  }
  
  // ── SCREEN READER ANNOUNCEMENTS ────────────────────────
  
  let liveRegion = null;
  
  /**
   * Initialize live region for screen reader announcements
   */
  function initLiveRegion() {
    liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.className = 'sr-only';
    document.body.appendChild(liveRegion);
  }
  
  /**
   * Announce message to screen readers
   */
  function announce(message) {
    if (!liveRegion) return;
    
    liveRegion.textContent = '';
    setTimeout(() => {
      liveRegion.textContent = message;
    }, 50);
  }
  
  // ── INITIALIZATION ──────────────────────────────────────────────
  
  /**
   * Initialize cookie consent system
   */
  function init() {
    // Initialize DOM elements
    initElements();
    
    // Initialize screen reader support
    initLiveRegion();
    
    // Check if user has already consented
    const savedConsent = getConsent();
    
    if (savedConsent) {
      // Sync saved consent with switch states
      updateAllSwitches(savedConsent);
      
      // Apply saved preferences
      if (savedConsent.analytics || savedConsent.marketing) {
        enableCookies(savedConsent);
      }
    } else {
      // Show banner on first visit
      // Use setTimeout to allow page to load first
      setTimeout(showBanner, 1000);
    }
    
    // Attach event listeners
    attachEventListeners();
    
    console.log('Cookie Consent initialized');
  }
  
  /**
   * Attach all event listeners
   */
  function attachEventListeners() {
    // Switch toggles in banner
    if (switches.analytics) {
      switches.analytics.addEventListener('click', () => handleSwitchClick(switches.analytics));
    }
    if (switches.marketing) {
      switches.marketing.addEventListener('click', () => handleSwitchClick(switches.marketing));
    }
    
    // Switch toggles in modal
    if (switches.analyticsModal) {
      switches.analyticsModal.addEventListener('click', () => handleSwitchClick(switches.analyticsModal));
    }
    if (switches.marketingModal) {
      switches.marketingModal.addEventListener('click', () => handleSwitchClick(switches.marketingModal));
    }
    
    // Action buttons
    if (buttons.acceptAll) {
      buttons.acceptAll.addEventListener('click', handleAcceptAll);
    }
    if (buttons.savePreferences) {
      buttons.savePreferences.addEventListener('click', handleSavePreferences);
    }
    if (buttons.manageSettings) {
      buttons.manageSettings.addEventListener('click', handleManageSettings);
    }
    if (buttons.close) {
      buttons.close.addEventListener('click', handleCloseModal);
    }
    if (buttons.closeBtn) {
      buttons.closeBtn.addEventListener('click', handleCloseModal);
    }
    if (buttons.rejectAll) {
      buttons.rejectAll.addEventListener('click', handleRejectAll);
    }
    if (buttons.saveSettings) {
      buttons.saveSettings.addEventListener('click', handleSaveSettings);
    }
    
    // Escape key closes modal
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal && !modal.hidden) {
        handleCloseModal();
      }
    });
    
    // Keyboard navigation for switches
    document.querySelectorAll('.cookie-switch').forEach(switchEl => {
      switchEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          switchEl.click();
        }
      });
    });
  }
  
  // ── PUBLIC API ────────────────────────────────────────
  
  return {
    init,
    hasConsent,
    getConsent,
    getConsentTimestamp,
    showBanner,
    hideBanner,
    showModal,
    hideModal,
    acceptAll: handleAcceptAll,
    rejectAll: handleRejectAll,
    savePreferences: handleSavePreferences,
    saveSettings: handleSaveSettings,
    isEnabled: (category) => {
      const consent = getConsent();
      return consent ? consent[category] === true : false;
    }
  };
  
})();

// ──────────────────────────────────────────────────────────────
// AUTO-INITIALIZE WHEN DOM IS READY
// ──────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', CookieConsent.init);

// Apply saved preferences immediately (before DOM ready)
// This prevents flash of unstyled content
(() => {
  try {
    const consent = JSON.parse(localStorage.getItem('partido_cookie_consent'));
    if (consent) {
      console.log('Cookie consent found:', consent);
      // You can dispatch event here to enable scripts before page loads
    }
  } catch (e) {
    // No consent yet
  }
})();
