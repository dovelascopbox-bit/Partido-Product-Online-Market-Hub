/**
 * PARTIDO MARKET HUB - THEME SWITCHER
 * Stage 7-A: CSS Custom Properties & Design Token Foundation
 * 
 * This script manages theme switching across:
 * - Light mode (default)
 * - Dark mode
 * - High contrast mode
 * - Large font mode
 * - Dyslexia-friendly mode
 * - Reduce motion mode
 */

class ThemeSwitcher {
    constructor() {
        this.STORAGE_KEY = 'partido-theme';
        this.MODES = {
            LIGHT: 'light',
            DARK: 'dark',
            HIGH_CONTRAST: 'high-contrast',
            LARGE_FONT: 'large-font',
            DYSLEXIA_FONT: 'dyslexia-font',
            REDUCE_MOTION: 'reduce-motion'
        };
        this.init();
    }

    /**
     * Initialize theme switcher
     * Restore saved preferences and apply system preference if available
     */
    init() {
        const savedTheme = this.getSavedTheme();
        if (savedTheme) {
            this.applyTheme(savedTheme);
        } else if (this.prefersDarkMode()) {
            this.applyTheme(this.MODES.DARK);
        } else if (this.prefersReducedMotion()) {
            this.applyTheme(this.MODES.REDUCE_MOTION);
        }
    }

    /**
     * Get saved theme preference from localStorage
     */
    getSavedTheme() {
        return localStorage.getItem(this.STORAGE_KEY);
    }

    /**
     * Check if system prefers dark mode
     */
    prefersDarkMode() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    /**
     * Check if system prefers reduced motion
     */
    prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    /**
     * Apply theme to <html> element
     * @param {string} theme - Theme name
     */
    applyTheme(theme) {
        const html = document.documentElement;
        
        // Remove all theme classes
        Object.values(this.MODES).forEach(mode => {
            html.classList.remove(mode);
        });

        // Apply selected theme
        if (theme && theme !== this.MODES.LIGHT) {
            html.classList.add(theme);
        }

        // Save preference
        localStorage.setItem(this.STORAGE_KEY, theme || this.MODES.LIGHT);

        // Dispatch custom event for other scripts to listen
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    /**
     * Toggle dark mode
     */
    toggleDarkMode() {
        const isDark = document.documentElement.classList.contains(this.MODES.DARK);
        this.applyTheme(isDark ? this.MODES.LIGHT : this.MODES.DARK);
    }

    /**
     * Toggle high contrast mode
     */
    toggleHighContrast() {
        const isHighContrast = document.documentElement.classList.contains(this.MODES.HIGH_CONTRAST);
        this.applyTheme(isHighContrast ? this.MODES.LIGHT : this.MODES.HIGH_CONTRAST);
    }

    /**
     * Toggle large font mode
     */
    toggleLargeFont() {
        const hasLargeFont = document.documentElement.classList.contains(this.MODES.LARGE_FONT);
        const currentTheme = this.getCurrentTheme();
        
        if (hasLargeFont) {
            document.documentElement.classList.remove(this.MODES.LARGE_FONT);
        } else {
            document.documentElement.classList.add(this.MODES.LARGE_FONT);
        }

        // Save the combination
        const modes = this.getActiveThemes();
        localStorage.setItem(this.STORAGE_KEY, modes.join(' '));
    }

    /**
     * Toggle dyslexia-friendly font
     */
    toggleDyslexiaFont() {
        const hasDyslexiaFont = document.documentElement.classList.contains(this.MODES.DYSLEXIA_FONT);
        
        if (hasDyslexiaFont) {
            document.documentElement.classList.remove(this.MODES.DYSLEXIA_FONT);
        } else {
            document.documentElement.classList.add(this.MODES.DYSLEXIA_FONT);
        }

        // Save the combination
        const modes = this.getActiveThemes();
        localStorage.setItem(this.STORAGE_KEY, modes.join(' '));
    }

    /**
     * Toggle reduce motion preference
     */
    toggleReduceMotion() {
        const hasReduceMotion = document.documentElement.classList.contains(this.MODES.REDUCE_MOTION);
        
        if (hasReduceMotion) {
            document.documentElement.classList.remove(this.MODES.REDUCE_MOTION);
        } else {
            document.documentElement.classList.add(this.MODES.REDUCE_MOTION);
        }

        // Save the combination
        const modes = this.getActiveThemes();
        localStorage.setItem(this.STORAGE_KEY, modes.join(' '));
    }

    /**
     * Get current primary theme (light/dark/high-contrast)
     */
    getCurrentTheme() {
        const html = document.documentElement;
        if (html.classList.contains(this.MODES.HIGH_CONTRAST)) {
            return this.MODES.HIGH_CONTRAST;
        } else if (html.classList.contains(this.MODES.DARK)) {
            return this.MODES.DARK;
        }
        return this.MODES.LIGHT;
    }

    /**
     * Get all active theme classes
     */
    getActiveThemes() {
        const html = document.documentElement;
        return Object.values(this.MODES).filter(mode => html.classList.contains(mode));
    }

    /**
     * Reset to light mode (default)
     */
    reset() {
        this.applyTheme(this.MODES.LIGHT);
        Object.values(this.MODES).forEach(mode => {
            document.documentElement.classList.remove(mode);
        });
        localStorage.removeItem(this.STORAGE_KEY);
    }

    /**
     * Toggle theme (alias for toggleDarkMode for convenience)
     */
    toggleTheme() {
        this.toggleDarkMode();
    }
}

// Initialize theme switcher on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeSwitcher = new ThemeSwitcher();
    });
} else {
    window.themeSwitcher = new ThemeSwitcher();
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeSwitcher;
}
