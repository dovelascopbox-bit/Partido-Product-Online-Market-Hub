<?php
/**
 * Cookie Consent Banner Component
 * Partido Product Online Market Hub - GDPR Compliance
 * 
 * This component displays a cookie consent banner that:
 * - Appears on first visit
 * - Persists consent preference in localStorage
 * - Provides granular control over cookie types
 * - Is fully accessible (WCAG 2.1 AA)
 */

if (!defined('APP_NAME')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
}
?>
<!-- Cookie Consent Banner -->
<div id="cookie-consent-banner" role="dialog" aria-labelledby="cookie-consent-title" aria-describedby="cookie-consent-desc" hidden>
    <div class="cookie-consent-container">
        <div class="cookie-consent-header">
            <h2 id="cookie-consent-title">🍪 Cookie Preferences</h2>
            <p id="cookie-consent-desc">
                We use cookies to enhance your experience. Under GDPR, you can choose which cookies you want to accept.
            </p>
        </div>
        
        <div class="cookie-consent-options">
            <!-- Essential Cookies (Always On) -->
            <div class="cookie-option">
                <div class="cookie-option-info">
                    <label for="cookie-essential">Essential Cookies</label>
                    <span class="cookie-option-desc">Required for the site to function. Cannot be disabled.</span>
                </div>
                <input type="checkbox" id="cookie-essential" checked disabled aria-describedby="cookie-essential-desc">
            </div>
            
            <!-- Analytics Cookies -->
            <div class="cookie-option">
                <div class="cookie-option-info">
                    <label for="cookie-analytics">Analytics Cookies</label>
                    <span class="cookie-option-desc">Help us understand how visitors interact with our website.</span>
                </div>
                <button 
                    type="button" 
                    class="cookie-switch" 
                    role="switch" 
                    aria-checked="false"
                    id="cookie-analytics"
                    aria-describedby="cookie-analytics-desc">
                    <span class="cookie-switch-thumb"></span>
                </button>
            </div>
            
            <!-- Marketing Cookies -->
            <div class="cookie-option">
                <div class="cookie-option-info">
                    <label for="cookie-marketing">Marketing Cookies</label>
                    <span class="cookie-option-desc">Used to deliver relevant advertisements.</span>
                </div>
                <button 
                    type="button" 
                    class="cookie-switch" 
                    role="switch" 
                    aria-checked="false"
                    id="cookie-marketing"
                    aria-describedby="cookie-marketing-desc">
                    <span class="cookie-switch-thumb"></span>
                </button>
            </div>
        </div>
        
        <div class="cookie-consent-actions">
            <button type="button" class="cookie-btn cookie-btn-secondary" id="cookie-save-preferences">
                Save Preferences
            </button>
            <button type="button" class="cookie-btn cookie-btn-primary" id="cookie-accept-all">
                Accept All
            </button>
        </div>
        
        <div class="cookie-consent-footer">
            <a href="<?php echo BASE_URL; ?>/public/privacy.php" class="cookie-link">
                Read our Privacy Policy
            </a>
            <button type="button" class="cookie-link" id="cookie-manage-settings">
                Manage Cookie Settings
            </button>
        </div>
    </div>
</div>

<!-- Cookie Settings Modal (Hidden by default) -->
<div id="cookie-settings-modal" role="dialog" aria-labelledby="cookie-settings-title" hidden>
    <div class="cookie-modal-overlay" id="cookie-modal-close"></div>
    <div class="cookie-modal-content">
        <div class="cookie-modal-header">
            <h2 id="cookie-settings-title">Cookie Settings</h2>
            <button type="button" class="cookie-modal-close" id="cookie-modal-close-btn" aria-label="Close cookie settings">
                ✕
            </button>
        </div>
        <div class="cookie-modal-body">
            <p>Manage your cookie preferences. Toggle each category on or off.</p>
            
            <div class="cookie-option">
                <div class="cookie-option-info">
                    <label for="cookie-analytics-modal">Analytics</label>
                    <span class="cookie-option-desc">Google Analytics and similar tools</span>
                </div>
                <button type="button" class="cookie-switch" role="switch" aria-checked="false" id="cookie-analytics-modal">
                    <span class="cookie-switch-thumb"></span>
                </button>
            </div>
            
            <div class="cookie-option">
                <div class="cookie-option-info">
                    <label for="cookie-marketing-modal">Marketing</label>
                    <span class="cookie-option-desc">Personalized advertisements</span>
                </div>
                <button type="button" class="cookie-switch" role="switch" aria-checked="false" id="cookie-marketing-modal">
                    <span class="cookie-switch-thumb"></span>
                </button>
            </div>
        </div>
        <div class="cookie-modal-footer">
            <button type="button" class="cookie-btn cookie-btn-secondary" id="cookie-reject-all">
                Reject All
            </button>
            <button type="button" class="cookie-btn cookie-btn-primary" id="cookie-save-settings">
                Save Settings
            </button>
        </div>
    </div>
</div>
