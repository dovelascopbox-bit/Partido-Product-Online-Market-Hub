<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require buyer role
requireAuth(['buyer']);

// Get buyer info
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT buyer_id FROM buyers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading buyer info.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

$buyer_id = $buyer_info['buyer_id'] ?? 0;

// Get wishlist items for this buyer
$wishlist_items = [];
if (isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist'])) {
    foreach ($_SESSION['wishlist'] as $item) {
        if ($item['buyer_id'] == $buyer_id) {
            $wishlist_items[] = $item;
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
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
</head>
<body class="bg-gray-50">
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Navigation -->
    <nav class="bg-surface shadow-md sticky top-0 z-50" role="navigation" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="<?php echo BASE_URL; ?>/public/index.php" class="flex items-center gap-2 text-lg font-bold text-blue-600">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" class="h-8 w-auto rounded">
                        <span>Partido Online Hub</span>
                    </a>
                    <span class="text-sm font-semibold bg-red-100 text-red-800 px-3 py-1 rounded-full">Wishlist</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/my-ratings.php" class="text-text font-medium hover:text-primary transition">⭐ My Ratings</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" class="text-blue-600 hover:underline">Dashboard</a>
            <span class="text-gray-500"> / </span>
            <span class="text-gray-700">My Wishlist</span>
        </div>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">My Wishlist</h1>
            <p class="text-gray-600"><?php echo count($wishlist_items); ?> item<?php echo count($wishlist_items) !== 1 ? 's' : ''; ?> saved</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($message = getFlashMessage()): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($wishlist_items)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="text-6xl mb-4">❤️</div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Your Wishlist is Empty</h2>
                <p class="text-gray-600 mb-6">Browse products and click the heart icon to add items here.</p>
                <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Browse Market
                </a>
            </div>
        <?php else: ?>
            <!-- Wishlist Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden" id="wishlist-item-<?php echo $item['product_id']; ?>">
                        <!-- Product Image -->
                        <div class="bg-gray-200 h-48 flex items-center justify-center overflow-hidden relative">
                            <?php $image_url = getProductImageUrl($item['image_path']); ?>
                            <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full bg-gray-200 items-center justify-center text-gray-400 text-sm">
                                No Image
                            </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-900 mb-1">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">
                                By: <?php echo htmlspecialchars($item['shop_name']); ?>
                            </p>
                            <p class="text-2xl font-bold text-blue-600 mb-4">
                                <?php echo formatCurrency($item['srp']); ?>
                            </p>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $item['product_id']; ?>" 
                                   class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-center text-sm">
                                    View
                                </a>
                                <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $item['product_id']; ?>#deal" 
                                   class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-center text-sm">
                                    🤝 Deal
                                </a>
                            </div>
                            <button 
                                onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)"
                                class="w-full mt-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm"
                            >
                                ❌ Remove
                            </button>
                        </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function removeFromWishlist(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('csrf_token', '<?php echo $csrf_token; ?>');

            fetch('<?php echo BASE_URL; ?>/public/buyer/toggle_wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = document.getElementById('wishlist-item-' + productId);
                    if (item) {
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.9)';
                        setTimeout(() => item.remove(), 300);
                    }
                    // Update count in header
                    const countEl = document.querySelector('p.text-gray-600');
                    if (countEl) {
                        const newCount = data.count;
                        countEl.textContent = newCount + ' item' + (newCount !== 1 ? 's' : '') + ' saved';
                    }
                    if (data.count === 0) {
                        setTimeout(() => location.reload(), 500);
                    }
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error. Please try again.');
            });
        }
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
