<?php
/**
 * DARK MODE TOGGLE BUTTON
 * Stage 7-F: Professional Theme Switcher Component
 * 
 * Displays a theme toggle button with smooth transitions
 * Persists user preference to localStorage
 */

// Get current theme from cookie or default to 'light'
$current_theme = $_COOKIE['partido_theme'] ?? 'light';
?>

<!-- Dark Mode Toggle Button -->
<button 
    id="theme-toggle-btn"
    class="navbar-action-btn"
    aria-label="Toggle dark mode"
    title="Toggle dark mode (Ctrl+Shift+D)"
    style="
        cursor: pointer;
        font-size: 1.25rem;
        opacity: 0.7;
        transition: opacity var(--transition-fast);
    "
    onclick="window.themeSwitcher && window.themeSwitcher.toggleTheme()"
>
    <span id="theme-icon" style="display: inline-block; transition: transform 0.3s ease;">
        ☀️
    </span>
</button>

<style>
    #theme-toggle-btn:hover {
        opacity: 1;
    }

    #theme-toggle-btn:focus {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
    }

    /* Rotate icon when switching themes */
    html.dark #theme-icon {
        content: '🌙';
        transform: rotate(180deg);
    }

    html.dark #theme-toggle-btn {
        color: var(--gray-400);
    }
</style>

<script>
    // Update theme toggle icon
    function updateThemeIcon() {
        const icon = document.getElementById('theme-icon');
        const isDark = document.documentElement.classList.contains('dark');
        
        if (isDark) {
            icon.textContent = '🌙';
            icon.style.transform = 'rotate(0deg)';
        } else {
            icon.textContent = '☀️';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Listen for theme changes
    window.addEventListener('themechange', updateThemeIcon);

    // Initialize icon on page load
    document.addEventListener('DOMContentLoaded', updateThemeIcon);

    // Keyboard shortcut: Ctrl+Shift+D to toggle theme
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.code === 'KeyD') {
            e.preventDefault();
            if (window.themeSwitcher) {
                window.themeSwitcher.toggleTheme();
            }
        }
    });
</script>
