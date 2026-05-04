<!-- Responsive Sidebar Navigation (Stage 7-D)
     
     Features:
     - Full sidebar on desktop (260px)
     - Icon-only on tablet (68px)
     - Hidden drawer on mobile (toggle with hamburger)
     - Sticky positioning
     - Accessibility landmarks (navigation list)
     -->

<aside id="sidebar" class="sidebar" role="complementary" aria-label="Side navigation">
    
    <!-- Sidebar Header (logo/branding) -->
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>/" class="navbar-logo hide-mobile">
            <span class="sidebar-icon">📱</span>
            <span class="sidebar-label">Partido</span>
        </a>
    </div>

    <!-- Main Navigation -->
    <nav class="sidebar-nav" role="navigation" aria-label="Sidebar navigation">
        <?php
        // Determine current user role and show appropriate nav
        $role = $_SESSION['role'] ?? 'guest';
        $current_page = basename($_SERVER['PHP_SELF']);

        // Navigation items structure
        $nav_items = [];

        // Guest navigation (visible for all)
        if (!Auth::isAuthenticated()) {
            $nav_items[] = [
                'icon' => '🏠',
                'label' => 'Home',
                'href' => BASE_URL . '/public/index.php',
                'id' => 'nav-home'
            ];
            $nav_items[] = [
                'icon' => '🛍️',
                'label' => 'Browse Market',
                'href' => BASE_URL . '/public/buyer/market.php',
                'id' => 'nav-market'
            ];
        } else {
            // Buyer navigation
            $nav_items[] = [
                'icon' => '🏠',
                'label' => 'Dashboard',
                'href' => BASE_URL . '/public/buyer/dashboard.php',
                'id' => 'nav-dashboard'
            ];
            $nav_items[] = [
                'icon' => '🛍️',
                'label' => 'Market',
                'href' => BASE_URL . '/public/buyer/market.php',
                'id' => 'nav-market'
            ];
            $nav_items[] = [
                'icon' => '💬',
                'label' => 'Messages',
                'href' => BASE_URL . '/public/messenger/',
                'id' => 'nav-messages'
            ];
            $nav_items[] = [
                'icon' => '🤝',
                'label' => 'My Deals',
                'href' => BASE_URL . '/public/buyer/deals.php',
                'id' => 'nav-deals'
            ];

            // Seller items (if user is seller)
            if ($role === 'seller' || $role === 'admin') {
                $nav_items[] = [
                    'icon' => '📦',
                    'label' => 'Seller Hub',
                    'href' => BASE_URL . '/public/seller/dashboard.php',
                    'id' => 'nav-seller'
                ];
                $nav_items[] = [
                    'icon' => '➕',
                    'label' => 'Add Product',
                    'href' => BASE_URL . '/public/seller/add-product.php',
                    'id' => 'nav-add-product'
                ];
            }

            // Admin items
            if ($role === 'admin') {
                $nav_items[] = [
                    'icon' => '⚙️',
                    'label' => 'Admin Panel',
                    'href' => BASE_URL . '/public/admin/dashboard.php',
                    'id' => 'nav-admin'
                ];
                $nav_items[] = [
                    'icon' => '📊',
                    'label' => 'Analytics',
                    'href' => BASE_URL . '/public/admin/analytics.php',
                    'id' => 'nav-analytics'
                ];
                $nav_items[] = [
                    'icon' => '⚠️',
                    'label' => 'Reports',
                    'href' => BASE_URL . '/public/admin/flags.php',
                    'id' => 'nav-reports'
                ];
            }
        }

        // Render navigation items
        foreach ($nav_items as $item) {
            $is_active = (strpos($_SERVER['REQUEST_URI'], str_replace(BASE_URL, '', $item['href'])) !== false);
            $active_class = $is_active ? 'is-active' : '';
        ?>
            <a 
                href="<?php echo $item['href']; ?>"
                class="sidebar-link <?php echo $active_class; ?>"
                id="<?php echo $item['id']; ?>"
                <?php echo $is_active ? 'aria-current="page"' : ''; ?>
                title="<?php echo htmlspecialchars($item['label']); ?>"
            >
                <span class="sidebar-icon"><?php echo $item['icon']; ?></span>
                <span class="sidebar-label"><?php echo htmlspecialchars($item['label']); ?></span>
            </a>
        <?php } ?>
    </nav>

    <!-- Sidebar Footer (user info, logout) -->
    <div class="sidebar-footer">
        <?php if (Auth::isAuthenticated()) { ?>
            <div class="text-center py-2 border-t border-border">
                <p class="text-sm font-medium text-text mb-3">
                    <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
                </p>
                <a 
                    href="<?php echo BASE_URL; ?>/public/logout.php" 
                    class="sidebar-link"
                    id="nav-logout"
                    onclick="return confirm('Are you sure you want to log out?');"
                    title="Log out (L)"
                >
                    <span class="sidebar-icon">🚪</span>
                    <span class="sidebar-label">Log out</span>
                </a>
            </div>
        <?php } else { ?>
            <a 
                href="<?php echo BASE_URL; ?>/public/login.php" 
                class="sidebar-link"
                id="nav-login"
                title="Log in"
            >
                <span class="sidebar-icon">🔓</span>
                <span class="sidebar-label">Log in</span>
            </a>
        <?php } ?>
    </div>

</aside>
