<?php
/**
 * SHARED HEADER TEMPLATE - Stage 7-A CSS Tokens & Accessibility
 * 
 * This file provides the common <head> section with:
 * - CSS Custom Properties (tokens.css) before Tailwind
 * - Skip link accessibility helper
 * - Theme switching capabilities
 * 
 * Usage: require_once from any page that needs the header
 */

// Ensure init.php has been included for APP_NAME, BASE_URL constants
if (!defined('APP_NAME')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo APP_NAME; ?> - Empower Local Businesses Online">
    
    <!-- Design System (Order matters: tokens → main → dark mode) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/helpers.css">
    
<!-- Professional Dark Mode System (Stage 7-F) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <!-- Cookie Consent Banner (GDPR) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/cookie-consent.css">
    
    <!-- Theme Switcher Script (Must be early for flicker prevention) -->
    <script>
        // Prevent dark mode flash on page load
        (function() {
            try {
                const savedTheme = localStorage.getItem('partido_theme_preference');
                const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemDark ? 'dark' : 'light');
                
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {
                // localStorage might be disabled
            }
        })();
    </script>
    
    <!-- Tailwind CSS for utility classes -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Main stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    
    <!-- Theme Switcher Script (early load for smooth transitions) -->
    <script src="<?php echo BASE_URL; ?>/assets/js/theme-switcher.js"></script>
    
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . APP_NAME : APP_NAME; ?></title>
</head>
<body>
    <!-- Skip to main content link (WCAG 2.1 AA accessibility) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navigation & page content will be inserted here by each page -->
</body>
</html>
