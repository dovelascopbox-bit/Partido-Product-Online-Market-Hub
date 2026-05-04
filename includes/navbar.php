<!-- Responsive Navigation Bar (Stage 7-D)
     
     Features:
     - Hamburger toggle on mobile (< 768px)
     - Logo + nav links on desktop
     - Sticky positioning with z-index management
     - ARIA attributes for accessibility
     -->

<nav class="navbar" role="navigation" aria-label="Main navigation">
    <!-- Hamburger Toggle Button (mobile only) -->
    <button 
        id="sidebar-toggle" 
        class="hamburger show-mobile"
        aria-label="Toggle navigation menu" 
        aria-expanded="false"
        aria-controls="sidebar"
        title="Toggle menu (T)"
    >
        ☰
    </button>

    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>/public/index.php" class="navbar-logo">
        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" height="64" width="auto">Partido Online Hub
    </a>

    <!-- Page Title (mobile only) -->
    <h1 class="navbar-title show-mobile">
        <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Partido'; ?>
    </h1>

    <!-- Navigation Actions (messages, notifications, user menu) -->
    <div class="navbar-actions">
        <?php
        // Show messages link if user is logged in
        if (Auth::isAuthenticated()) {
            $unread_count = 0; // TODO: Fetch actual unread message count
        ?>
            <a 
                href="<?php echo BASE_URL; ?>/public/messenger/" 
                class="navbar-action-link"
                aria-label="Messages<?php echo $unread_count > 0 ? ', ' . $unread_count . ' unread' : ''; ?>"
                title="Messages (M)"
            >
                💬
                <?php if ($unread_count > 0): ?>
                    <span class="badge-count"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>

            <!-- Notifications -->
            <a 
                href="<?php echo BASE_URL; ?>/public/notifications/" 
                class="navbar-action-link"
                aria-label="Notifications"
                title="Notifications (N)"
            >
                🔔
            </a>

            <!-- User Menu -->
            <a 
                href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" 
                class="navbar-action-link hide-mobile"
                aria-label="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) . ' account' : 'Account'; ?>"
                title="My account (U)"
            >
                👤
            </a>
        <?php } else { ?>
            <!-- Login link for guests -->
            <a 
                href="<?php echo BASE_URL; ?>/public/login.php" 
                class="navbar-action-link"
                aria-label="Log in to your account"
            >
                Log in
            </a>
        <?php } ?>

    </div>
</nav>

<!-- Sidebar Overlay (click to close drawer on mobile) -->
<div id="sidebar-overlay" class="sidebar-overlay"></div>
