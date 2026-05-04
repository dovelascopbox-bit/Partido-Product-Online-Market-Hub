<!-- ════════════════════════════════════════════════════════════════
     FOOTER COMPONENT
     Partido Product Online Market Hub
     
     Included at the end of every page for:
     • Footer content (if needed)
     • Accessibility toolbar
     • Critical JavaScript files
     ═════════════════════════════════════════════════════════════════ -->

<!-- Accessibility Toolbar Component -->
<?php include 'accessibility-toolbar.php'; ?>

<!-- Cookie Consent Banner (GDPR) -->
<?php include 'cookie-consent.php'; ?>

<!-- Accessibility Toolbar Styles -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">

<!-- Keyboard Navigation & ARIA Utilities (Stage 7-C) -->
<script src="<?php echo BASE_URL; ?>/assets/js/keyboard.js"></script>

<!-- Accessibility Toolbar Controller -->
<script src="<?php echo BASE_URL; ?>/assets/js/accessibility.js"></script>

<!-- Responsive Layout System (Stage 7-D) -->
<script src="<?php echo BASE_URL; ?>/assets/js/layout.js"></script>

<!-- Theme Switcher (Professional Dark Mode - Stage 7-F) -->
<script src="<?php echo BASE_URL; ?>/assets/js/theme-switcher.js"></script>

<!-- Cookie Consent Manager (GDPR) -->
<script src="<?php echo BASE_URL; ?>/assets/js/cookie-consent.js"></script>

<!-- Close body and html tags are closed by the including page -->
