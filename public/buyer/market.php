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
    
    <!-- Design System -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/helpers.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <!-- Dark mode no-flash -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('partido_theme_preference');
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemDark ? 'dark' : 'light');
                if (theme === 'dark') document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    
    <style>
        /* Navbar & button fixes for visibility */
        .navbar { z-index: 1020; }
        .navbar-actions { flex-wrap: wrap; gap: 0.5rem; }
        .navbar-actions a { 
            color: #374151 !important; 
            font-weight: 600; 
            padding: 0.375rem 0.75rem; 
            border-radius: 0.5rem; 
        }
        .navbar-actions a:hover { 
            background-color: #f3f4f6 !important; 
            color: #0f766e !important;
        }
        #theme-toggle-btn { opacity: 1 !important; }
        #theme-toggle-btn:hover { transform: scale(1.1); }
        .btn.btn-sm.btn-danger { 
            background-color: #dc2626 !important; 
            color: white !important; 
            font-weight: 600; 
            padding: 0.5rem 1rem !important;
        }
        /* Original market styles (minified for brevity) */
        body { background: var(--gray-50); }
        html.dark body { background: #0f172a; }
        main { max-width: 1400px; margin: 0 auto; padding: 4rem 1.5rem; }
        .search-container { background: white; border: 1px solid var(--gray-200); border-radius: 1rem; padding: 2rem; margin-bottom: 3rem; box-shadow: var(--shadow-sm); }
        html.dark .search-container { background: var(--gray-100); }
        .search-form { display: flex; gap: 1rem; flex-wrap: wrap; }
        .search-input { flex: 1; min-width: 250px; padding: 1rem; border: 2px solid var(--gray-300); border-radius: 0.75rem; font-size: 1rem; }
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1); }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; }
        .product-card { background: white; border-radius: 1rem; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200); transition: all 0.3s; display: flex; flex-direction: column; }
        .product-card:hover { box-shadow: var(--shadow-xl); transform: translateY(-0.5rem); }
        .product-image { position: relative; height: 240px; background: var(--gray-200); overflow: hidden; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-badge { position: absolute; top: 1rem; right: 1rem; background: var(--success); color: white; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; }
        .wishlist-btn { position: absolute; top: 1rem; left: 1rem; background: rgba(255,255,255,0.95); backdrop-blur-md; border: none; padding: 0.75rem; border-radius: 50%; cursor: pointer; box-shadow: var(--shadow-md); }
        .wishlist-btn:hover { background: white; transform: scale(1.1); }
        .wishlist-btn.active svg { color: var(--danger); stroke: var(--danger); fill: var(--danger); }
        .product-info { padding: 2rem; flex: 1; display: flex; flex-direction: column; }
        .product-name { font-size: 1.25rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; overflow: hidden; }
        .product-seller { font-size: 0.875rem; color: var(--gray-600); margin-bottom: 1rem; }
        .product-rating { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--warning); margin-bottom: 1rem; }
        .product-description { font-size: 0.875rem; color: var(--gray-600); margin-bottom: 1.5rem; line-clamp-3; }
        .product-footer { border-top: 1px solid var(--gray-200); padding-top: 1.5rem; margin-top: auto; }
        .product-pricing { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.5rem; }
        .product-price { font-size: 2rem; font-weight: 800; color: var(--primary); }
        .product-stock-label { font-size: 0.75rem; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; }
        .product-stock-value { font-size: 1.5rem; font-weight: 700; color: var(--gray-900); }
        .product-actions { display: flex; gap: 1rem; }
        .product-actions a { flex: 1; padding: 1rem; border-radius: 0.75rem; font-weight: 600; text-align: center; transition: all 0.3s; font-size: 1rem; }
        .details-btn { background: rgba(15, 118, 110, 0.1); color: var(--primary); border: 1px solid var(--primary); }
        .details-btn:hover { background: var(--primary); color: white; }
        .deal-btn { background: var(--success); color: white; }
        .deal-btn:hover { background: #059669; }
        .pagination { display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap; margin-top: 4rem; }
        .pagination a, .pagination span { padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: 1px solid var(--gray-300); background: white; color: var(--gray-900); font-weight: 600; transition: all 0.3s; white-space: nowrap; }
        .pagination a:hover { background: var(--primary); color: white; border-color: var(--primary); transform: translateY(-2px); }
        .pagination .current { background: var(--primary); color: white; border-color: var(--primary); }
        .empty-state { background: white; border-radius: 1rem; padding: 6rem 3rem; text-align: center; border: 2px dashed var(--gray-300); }
        .empty-state-icon { font-size: 6rem; margin-bottom: 2rem; opacity: 0.5; }
        .empty-state-title { font-size: 2rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem; }
        .empty-state-text { color: var(--gray-600); font-size: 1.125rem; margin-bottom: 3rem; }
        @media (max-width: 768px) { .product-grid { grid-template-columns: repeat(auto-fill, minmax(100%, 1fr)); gap: 1.5rem; } main { padding: 2rem 1rem; } .search-form { flex-direction: column; } .search-input { min-width: auto; } .product-actions { flex-direction: column; } }
    </style>
</head>
<body>
    <!-- Skip link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navbar - Enhanced visibility/colors -->
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a href="<?php echo BASE_URL; ?>/public/index.php" class="navbar-logo">
            <span style="font-size: 1.5rem; font-weight: 700; color: #0f766e;">Partido Online Hub</span>
        </a>
        <div style="flex: 1; margin-left: 2rem;">
            <span style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">🛍️ Market Hub</span>
        </div>
        <div class="navbar-actions">
            <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" style="color: #374151; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s; text-decoration: none; font-size: 0.95rem;">
                🏠 Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" style="color: #374151; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s; text-decoration: none; font-size: 0.95rem;">
                Deals
            </a>
            <a href="<?php echo BASE_URL; ?>/public/buyer/my-ratings.php" style="color: #374151; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s; text-decoration: none; font-size: 0.95rem;">
                ⭐ My Ratings
            </a>
            <a href="<?php echo BASE_URL; ?>/public/buyer/wishlist.php" style="position: relative; color: #374151; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s; text-decoration: none; font-size: 0.95rem;">
                Wishlist
                <?php if ($wishlist_count > 0): ?>
                    <span style="position: absolute; top: -0.5rem; right: -0.5rem; background: #dc2626; color: white; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 50%; min-width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center;">
                        <?= $wishlist_count ?>
                    </span>
                <?php endif; ?>
            </a>
            <span style="font-size: 0.875rem; color: #374151; font-weight: 600; padding: 0.5rem 1rem;">
                <?= htmlspecialchars($_SESSION["full_name"]) ?>
            </span>
            <button 
                id="theme-toggle-btn"
                class="navbar-action-btn"
                aria-label="Toggle dark mode"
                title="Toggle dark mode (Ctrl+Shift+D)"
                style="opacity: 1; font-size: 1.25rem; transition: all 0.2s;"
                onclick="if(window.themeSwitcher) window.themeSwitcher.toggleDarkMode();"
            >
                <span id="theme-icon">☀️</span>
            </button>
            <a href="<?php echo BASE_URL; ?>/public/logout.php" class="btn btn-sm btn-danger" style="background: #dc2626 !important; color: white !important; font-weight: 600; padding: 0.5rem 1rem !important; border-radius: 0.5rem;">
                Logout
            </a>
        </div>
    </nav>

    <main id="main-content" role="main">
        <!-- Header -->
        <div style="margin-bottom: 4rem;">
            <h1 style="font-size: 3rem; font-weight: 800; color: var(--gray-900); margin-bottom: 1rem;">Market Hub</h1>
            <p style="color: var(--gray-600); font-size: 1.25rem;">Discover premium products from local sellers</p>
        </div>

        <!-- Flash Messages -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div style="margin-bottom: 2rem; padding: 1.5rem; border-radius: 0.75rem; border-left: 4px solid <?= $flash['type'] === 'error' ? 'var(--danger)' : 'var(--success)' ?>; background: <?= $flash['type'] === 'error' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(34, 197, 94, 0.1)' ?>;">
                <?= htmlspecialchars($flash["message"]) ?>
            </div>
        <?php endif; ?>

        <!-- Search -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search products by name..." class="search-input" aria-label="Search products">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if ($search_active): ?>
                    <a href="<?= BASE_URL ?>/public/buyer/market.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Results count -->
        <div style="margin-bottom: 2rem; color: var(--gray-600); font-size: 1rem;">
            <?php if ($search_active): ?>
                <p>Showing <strong><?= count($products) ?></strong> of <strong><?= $total_count ?></strong> results for "<strong><?= htmlspecialchars($search) ?></strong>"</p>
            <?php else: ?>
                <p>Showing <strong><?= count($products) ?></strong> of <strong><?= $total_count ?></strong> products</p>
            <?php endif; ?>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state text-center">
                <div class="empty-state-icon">🔍</div>
                <h2 class="empty-state-title">No products found</h2>
                <p class="empty-state-text">Try different search terms</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" id="product-<?= $product["product_id"] ?>">
                        <div class="product-image">
                            <?php $image_url = getProductImageUrl($product["image_path"]); ?>
                            <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($product["product_name"]) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="product-image-placeholder" style="display: none;">📷 No image</div>
                            <div class="product-badge">✓ Available</div>
                            <button onclick="event.stopPropagation(); toggleWishlist(<?= $product['product_id'] ?>, this)" class="wishlist-btn" title="Wishlist" aria-label="Toggle wishlist" type="button">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product["product_name"]) ?></h3>
                            <p class="product-seller">by <strong><?= htmlspecialchars($product["shop_name"]) ?></strong></p>
                            <div class="product-rating">
                                <span>⭐ <?= number_format($product["rating"] ?? 0, 1) ?></span>
                            </div>
                            <p class="product-description"><?= htmlspecialchars(substr($product["product_description"], 0, 100)) ?>...</p>
                            <div class="product-footer">
                                <div class="product-pricing">
                                    <div>
                                        <p style="font-size: 0.75rem; color: var(--gray-600);">Price</p>
                                        <p class="product-price"><?= formatCurrency($product["srp"]) ?></p>
                                    </div>
                                    <div class="product-stock">
                                        <p class="product-stock-label">Stock</p>
                                        <p class="product-stock-value"><?= $product["quantity"] ?></p>
                                    </div>
                                </div>
                                <div class="product-actions">
                                    <a href="<?= BASE_URL ?>/public/buyer/product.php?id=<?= $product["product_id"] ?>" class="details-btn">View Details</a>
                                    <a href="<?= BASE_URL ?>/public/buyer/product.php?id=<?= $product["product_id"] ?>#deal" class="deal-btn">Make Deal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?= BASE_URL ?>/public/buyer/market.php?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">← Previous</a>
                    <?php endif; ?>
                    <div style="display: flex; gap: 0.5rem;">
                        <?php $start = max(1, $page - 2); $end = min($total_pages, $page + 2);
                        if ($start > 1) echo '<a href="' . BASE_URL . '/public/buyer/market.php?page=1' . ($search ? '&search=' . urlencode($search) : '') . '">1</a><span>...</span>'; 
                        for ($i = $start; $i <= $end; $i++) echo $i == $page ? '<span class="current">' . $i . '</span>' : '<a href="' . BASE_URL . '/public/buyer/market.php?search=' . urlencode($search) . '&page=' . $i . '">' . $i . '</a>'; 
                        if ($end < $total_pages) echo '<span>...</span><a href="' . BASE_URL . '/public/buyer/market.php?search=' . urlencode($search) . '&page=' . $total_pages . '">' . $total_pages . '</a>'; ?>
                    </div>
                    <?php if ($page < $total_pages): ?>
                        <a href="<?= BASE_URL ?>/public/buyer/market.php?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script>
        // Fixed wishlist toggle + badge update
        function toggleWishlist(productId, btn) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('csrf_token', '<?= $csrf_token ?>');
            btn.disabled = true;
            btn.style.opacity = '0.6';
            
            fetch('<?= BASE_URL ?>/public/buyer/toggle_wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.style.opacity = '1';
                if (data.success) {
                    btn.classList.toggle('active', data.action === 'added');
                    // Fixed badge selector
                    const badge = document.querySelector('a[href*="wishlist"] span:last-of-type');
                    if (badge) {
                        badge.style.display = data.count > 0 ? 'flex' : 'none';
                        badge.textContent = data.count;
                    }
                } else {
                    alert(data.message || 'Error updating wishlist');
                }
            }).catch(err => {
                btn.disabled = false;
                btn.style.opacity = '1';
                console.error('Wishlist error:', err);
                alert('Network error - please try again');
            });
        }

        // Theme toggle
        window.themeSwitcher = {
            toggleDarkMode() {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('partido_theme_preference', isDark ? 'dark' : 'light');
                document.getElementById('theme-icon').textContent = isDark ? '🌙' : '☀️';
            }
        };
    </script>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
