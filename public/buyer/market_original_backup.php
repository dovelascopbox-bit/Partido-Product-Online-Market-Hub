<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/init.php";
requireAuth(["buyer"]);
$search = isset($_GET["search"]) ? sanitizeInput($_GET["search"]) : "";
$page = isset($_GET["page"]) ? max(1, intval($_GET["page"])) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Market.php";
$market = new Market($pdo);
if (!empty($search)) {
    $products = $market->searchProducts($search, $per_page, $offset);
    $total_count = $market->getProductCount($search);
    $search_active = true;
} else {
    $products = $market->getAvailableProducts($per_page, $offset);
    $total_count = $market->getProductCount();
    $search_active = false;
}
$total_pages = max(1, ceil($total_count / $per_page));
$csrf_token = generateCSRFToken();
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT buyer_id FROM buyers WHERE user_id = :user_id");
    $stmt->execute([":user_id" => $_SESSION["user_id"]]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {}
$buyer_id = $buyer_info["buyer_id"] ?? 0;
$wishlist_count = 0;
if (isset($_SESSION["wishlist"]) && is_array($_SESSION["wishlist"])) {
    foreach ($_SESSION["wishlist"] as $item) {
        if ($item["buyer_id"] == $buyer_id) {
            $wishlist_count++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse products on <?php echo APP_NAME; ?> Market Hub">
    <meta name="theme-color" content="#0f766e">
    <title>Market Hub - <?php echo APP_NAME; ?></title>
    
    <!-- Design System (Order matters: tokens → main → layout → dark mode) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/helpers.css">
    
    <!-- Professional Dark Mode System -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <!-- Theme Switcher Script (Prevent dark mode flash) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('partido_theme_preference');
                const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemDark ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Market Styles -->
    <style>
        /* Smooth transitions */
        body, main, .product-card {
            transition: background-color 300ms ease, color 300ms ease;
        }

        body {
            background: var(--gray-50);
        }

        html.dark body {
            background: #0f172a;
        }

        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--spacing-2xl) var(--spacing-lg);
        }

        /* Search Container */
        .search-container {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
            box-shadow: var(--shadow-sm);
        }

        html.dark .search-container {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }

        .search-form {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: var(--spacing-md);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 1rem;
            background: white;
            color: var(--gray-900);
            transition: all var(--transition-fast);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }

        html.dark .search-input {
            background: var(--gray-150);
            color: var(--gray-700);
            border-color: var(--gray-200);
        }

        html.dark .search-input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        /* Product Card */
        .product-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            transition: all var(--transition-fast);
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }

        html.dark .product-card {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }

        .product-image {
            position: relative;
            width: 100%;
            height: 200px;
            background: var(--gray-200);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image-placeholder {
            color: var(--gray-400);
            font-size: 0.875rem;
        }

        /* Badge */
        .product-badge {
            position: absolute;
            top: var(--spacing-md);
            right: var(--spacing-md);
            background: var(--success);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Wishlist Button */
        .wishlist-btn {
            position: absolute;
            top: var(--spacing-md);
            left: var(--spacing-md);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            border: none;
            padding: 0.5rem;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all var(--transition-fast);
            z-index: 10;
            box-shadow: var(--shadow-sm);
        }

        .wishlist-btn:hover {
            background: white;
            transform: scale(1.1);
        }

        .wishlist-btn svg {
            width: 20px;
            height: 20px;
            color: var(--gray-400);
            fill: none;
            stroke: currentColor;
            transition: all 300ms ease;
        }

        .wishlist-btn.active svg {
            color: var(--danger);
            fill: var(--danger);
            stroke: var(--danger);
        }

        /* Product Info */
        .product-info {
            padding: var(--spacing-lg);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        html.dark .product-name {
            color: var(--gray-700);
        }

        .product-seller {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: var(--spacing-md);
        }

        html.dark .product-seller {
            color: var(--gray-400);
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            color: var(--gray-700);
            margin-bottom: var(--spacing-md);
        }

        html.dark .product-rating {
            color: var(--gray-400);
        }

        .product-description {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: var(--spacing-md);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        html.dark .product-description {
            color: var(--gray-400);
        }

        .product-footer {
            border-top: 1px solid var(--gray-200);
            padding-top: var(--spacing-md);
            margin-top: auto;
        }

        html.dark .product-footer {
            border-color: var(--gray-200);
        }

        .product-pricing {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: var(--spacing-md);
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .product-stock {
            text-align: right;
        }

        .product-stock-label {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        html.dark .product-stock-label {
            color: var(--gray-400);
        }

        .product-stock-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        html.dark .product-stock-value {
            color: var(--gray-700);
        }

        /* Product Actions */
        .product-actions {
            display: flex;
            gap: var(--spacing-md);
        }

        .product-actions a {
            flex: 1;
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all var(--transition-fast);
            font-size: 0.875rem;
        }

        .details-btn {
            background: var(--primary);
            background-opacity: 0.1;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .details-btn:hover {
            background: var(--primary);
            background-opacity: 1;
            color: white;
        }

        .deal-btn {
            background: var(--success);
            color: white;
            border: 1px solid var(--success);
        }

        .deal-btn:hover {
            background: var(--success);
            opacity: 0.9;
        }

        /* Pagination -->
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: var(--spacing-2xl);
        }

        .pagination a, .pagination span {
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-md);
            border: 1px solid var(--gray-300);
            background: white;
            color: var(--gray-900);
            text-decoration: none;
            transition: all var(--transition-fast);
            font-weight: 500;
        }

        html.dark .pagination a, html.dark .pagination span {
            background: var(--gray-150);
            border-color: var(--gray-200);
            color: var(--gray-700);
        }

        .pagination a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pagination .current {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--spacing-3xl);
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        html.dark .empty-state {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
        }

        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
        }

        html.dark .empty-state-title {
            color: var(--gray-700);
        }

        .empty-state-text {
            color: var(--gray-600);
            margin-bottom: var(--spacing-lg);
        }

        html.dark .empty-state-text {
            color: var(--gray-400);
        }
    </style>
</head>
<body>
    <!-- Skip to main content link (WCAG 2.1 AA accessibility) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a href="<?php echo BASE_URL; ?>/" class="navbar-logo">
            <span style="font-size: 1.5rem; font-weight: 700;">Partido</span>
        </a>
        <div class="navbar-breadcrumb" style="flex: 1; margin-left: var(--spacing-lg);">
            <span style="font-size: 0.875rem; color: var(--gray-600);">🛍️ Market Hub</span>
        </div>
        <div class="navbar-actions">
            <!-- Dashboard Link -->
            <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" style="text-decoration: none; color: var(--gray-600); font-weight: 500; transition: color var(--transition-fast);">
                Dashboard
            </a>
            
            <!-- My Deals Link -->
            <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" style="text-decoration: none; color: var(--gray-600); font-weight: 500; transition: color var(--transition-fast);">
                My Deals
            </a>
            
            <!-- Wishlist Link -->
            <a href="<?php echo BASE_URL; ?>/public/buyer/wishlist.php" style="text-decoration: none; color: var(--gray-600); font-weight: 500; transition: color var(--transition-fast); position: relative;">
                Wishlist
                <?php if ($wishlist_count > 0): ?>
                    <span style="position: absolute; top: -8px; right: -12px; background: var(--danger); color: white; font-size: 0.75rem; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); font-weight: 700;">
                        <?php echo $wishlist_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <!-- User Info -->
            <span style="font-size: 0.875rem; color: var(--gray-600); font-weight: 500;">
                <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
            </span>
            
            <!-- Theme Toggle -->
            <button 
                id="theme-toggle-btn"
                class="navbar-action-btn"
                aria-label="Toggle dark mode"
                title="Toggle dark mode (Ctrl+Shift+D)"
                style="font-size: 1.25rem; opacity: 0.7; transition: opacity var(--transition-fast);"
                onclick="window.themeSwitcher && window.themeSwitcher.toggleDarkMode()"
            >
                <span id="theme-icon" style="display: inline-block; transition: transform 0.3s ease;">☀️</span>
            </button>
            
            <!-- Logout -->
            <a href="<?php echo BASE_URL; ?>/public/logout.php" class="btn btn-sm btn-danger">
                Logout
            </a>
        </div>
    </nav>

    <main id="main-content" role="main" tabindex="-1">
        <!-- Header -->
        <div style="margin-bottom: var(--spacing-2xl);">
            <h1 style="font-size: 2.25rem; font-weight: 700; color: var(--gray-900); margin-bottom: var(--spacing-md);">Market Hub</h1>
            <p style="color: var(--gray-600); font-size: 1rem;">Discover products from local sellers</p>
        </div>

        <!-- Flash Messages -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div style="margin-bottom: var(--spacing-lg); padding: var(--spacing-lg); border-radius: var(--radius-md); border-left: 4px solid <?php echo $flash['type'] === 'error' ? 'var(--danger)' : 'var(--success)'; ?>; background: <?php echo $flash['type'] === 'error' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(34, 197, 94, 0.1)'; ?>; color: <?php echo $flash['type'] === 'error' ? 'var(--danger)' : 'var(--success)'; ?>;">
                <?php echo htmlspecialchars($flash["message"]); ?>
            </div>
        <?php endif; ?>

        <!-- Search Container -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="🔍 Search products by name..." 
                    class="search-input"
                    aria-label="Search products"
                >
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if ($search_active): ?>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="btn btn-outlined" style="border-color: var(--gray-300); color: var(--gray-600);">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Results Count -->
        <div style="margin-bottom: var(--spacing-lg); color: var(--gray-600); font-size: 0.875rem;">
            <?php if ($search_active): ?>
                <p>Showing <strong><?php echo count($products); ?></strong> of <strong><?php echo $total_count; ?></strong> results for "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
            <?php else: ?>
                <p>Showing <strong><?php echo count($products); ?></strong> of <strong><?php echo $total_count; ?></strong> products</p>
            <?php endif; ?>
        </div>

        <!-- Products or Empty State -->
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <h2 class="empty-state-title">No products found</h2>
                <p class="empty-state-text">Try searching for different keywords or check back later</p>
            </div>
        <?php else: ?>
            <!-- Product Grid -->
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" id="product-<?php echo $product["product_id"]; ?>">
                        <!-- Product Image -->
                        <div class="product-image">
                            <?php $image_url = getProductImageUrl($product["image_path"]); ?>
                            <img 
                                src="<?php echo htmlspecialchars($image_url); ?>" 
                                alt="<?php echo htmlspecialchars($product["product_name"]); ?>" 
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <div class="product-image-placeholder" style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center;">
                                📷 No Image
                            </div>
                            
                            <!-- Badge -->
                            <div class="product-badge">✓ Available</div>
                            
                            <!-- Wishlist Button -->
                            <button 
                                onclick="event.stopPropagation(); toggleWishlist(<?php echo $product['product_id']; ?>, this)" 
                                class="wishlist-btn" 
                                title="Add to wishlist"
                                aria-label="Add to wishlist"
                                type="button"
                            >
                                <svg viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Product Info -->
                        <div class="product-info">
                            <!-- Name -->
                            <h3 class="product-name"><?php echo htmlspecialchars($product["product_name"]); ?></h3>
                            
                            <!-- Seller -->
                            <p class="product-seller">by <strong><?php echo htmlspecialchars($product["shop_name"]); ?></strong></p>
                            
                            <!-- Rating -->
                            <div class="product-rating">
                                <span>⭐ <?php echo number_format($product["rating"] ?? 0, 1); ?></span>
                            </div>
                            
                            <!-- Description -->
                            <p class="product-description">
                                <?php echo htmlspecialchars(substr($product["product_description"], 0, 100)); ?>...
                            </p>

                            <!-- Footer (Price & Stock) -->
                            <div class="product-footer">
                                <div class="product-pricing">
                                    <div>
                                        <p style="font-size: 0.75rem; color: var(--gray-600);">Price</p>
                                        <p class="product-price"><?php echo formatCurrency($product["srp"]); ?></p>
                                    </div>
                                    <div class="product-stock">
                                        <p class="product-stock-label">Stock</p>
                                        <p class="product-stock-value"><?php echo $product["quantity"]; ?></p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="product-actions">
                                    <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $product["product_id"]; ?>" class="details-btn">View Details</a>
                                    <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $product["product_id"]; ?>#deal" class="deal-btn">Make Deal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo BASE_URL; ?>/public/buyer/market.php?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">← Previous</a>
                    <?php endif; ?>

                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        if ($start > 1) {
                            echo '<a href="' . BASE_URL . '/public/buyer/market.php?search=' . urlencode($search) . '&page=1">1</a>';
                            if ($start > 2) echo '<span>...</span>';
                        }
                        
                        for ($i = $start; $i <= $end; $i++) {
                            if ($i == $page) {
                                echo '<span class="current">' . $i . '</span>';
                            } else {
                                echo '<a href="' . BASE_URL . '/public/buyer/market.php?search=' . urlencode($search) . '&page=' . $i . '">' . $i . '</a>';
                            }
                        }
                        
                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) echo '<span>...</span>';
                            echo '<a href="' . BASE_URL . '/public/buyer/market.php?search=' . urlencode($search) . '&page=' . $total_pages . '">' . $total_pages . '</a>';
                        }
                        ?>
                    </div>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?php echo BASE_URL; ?>/public/buyer/market.php?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <!-- Wishlist Toggle Script -->
    <script>
        function toggleWishlist(productId, btn) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('csrf_token', '<?php echo $csrf_token; ?>');
            
            fetch('<?php echo BASE_URL; ?>/public/buyer/toggle_wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('active', data.action === 'added');
                    
                    // Update wishlist badge
                    const badge = document.querySelector('a[href*=\"wishlist\"] span');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = '';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                } else {
                    alert(data.message || 'Failed to update wishlist');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Network error');
            });
        }

        // Keyboard shortcut for theme toggle (Ctrl+Shift+D)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                if (window.themeSwitcher) {
                    window.themeSwitcher.toggleDarkMode();
                }
            }
        });
    </script>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
