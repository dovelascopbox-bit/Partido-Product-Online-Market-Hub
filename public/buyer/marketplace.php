<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require buyer role
requireAuth(['buyer']);

// Get buyer info
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM buyers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading buyer info.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

if (!$buyer_info) {
    setFlashMessage('Buyer account not found.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

// Get search and filter parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';

// Get available products
$product_obj = new Product($pdo);
$products = [];

try {
    if (!empty($search)) {
        // Search products
        $products = $product_obj->search($search);
    } else {
        // Get all available products
        $products = $product_obj->getAvailableProducts();
    }
    
    // Sort products based on sort parameter
    if (!empty($products)) {
        usort($products, function($a, $b) use ($sort) {
            switch ($sort) {
                case 'price_low':
                    return $a['srp'] - $b['srp'];
                case 'price_high':
                    return $b['srp'] - $a['srp'];
                case 'oldest':
                    return strtotime($a['created_at']) - strtotime($b['created_at']);
                case 'newest':
                default:
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
            }
        });
    }
} catch (Exception $e) {
    setFlashMessage('Error loading products.', 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - <?php echo APP_NAME; ?></title>
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
    <style>
        .search-box  { background-color: #ffffff; }
        html.dark .search-box { background-color: var(--gray-100); }

        html.dark .search-box label { color: var(--gray-400); }

        .search-box input,
        .search-box select {
            background-color: #ffffff;
            color: var(--gray-900);
            border-color: var(--gray-300);
        }
        html.dark .search-box input,
        html.dark .search-box select {
            background-color: var(--gray-150);
            color: var(--gray-700);
            border-color: var(--gray-200);
        }
        .search-box input::placeholder { color: var(--gray-400); }
        html.dark .search-box input::placeholder { color: var(--gray-400); }

        .market-title   { color: var(--gray-900); }
        html.dark .market-title { color: var(--gray-700); }

        .market-subtitle { color: var(--gray-600); }
        html.dark .market-subtitle { color: var(--gray-400); }

        .empty-box { background-color: #ffffff; }
        html.dark .empty-box { background-color: var(--gray-100); }
    </style>
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
                    <span class="text-sm font-semibold bg-purple-100 text-purple-800 px-3 py-1 rounded-full">Marketplace</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/wishlist.php" class="text-text hover:text-primary font-medium transition">❤️ Wishlist</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold market-title mb-2">Marketplace</h1>
            <p class="market-subtitle">Browse and discover products from local sellers</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($message = getFlashMessage()): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Section -->
        <div class="search-box rounded-lg shadow p-6 mb-8">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Search Products</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by product name or description..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Sort By</label>
                        <select name="sort" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        🔍 Search
                    </button>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/marketplace.php" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div>
            <?php if (empty($products)): ?>
                <div class="empty-box rounded-lg shadow p-12 text-center">
                    <p class="market-title mb-4">No products available</p>
                    <p class="market-subtitle">Try searching for different keywords or come back later</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden product-card">
                            <!-- Product Image -->
                            <div class="bg-gray-200 h-64 flex items-center justify-center overflow-hidden">
                                <?php $image_url = getProductImageUrl($product['image_path']); ?>
                                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-full object-cover">
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <h3 class="font-bold text-lg text-gray-900 mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                
                                <!-- Description (truncated) -->
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                    <?php echo htmlspecialchars(substr($product['product_description'], 0, 100)); ?>...
                                </p>

                                <!-- Seller Info -->
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <p class="text-xs text-gray-500">Seller: <span class="font-semibold"><?php echo htmlspecialchars($product['shop_name'] ?? 'Unknown Shop'); ?></span></p>
                                </div>

                                <!-- Price and Quantity -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Price</p>
                                        <p class="text-2xl font-bold text-blue-600"><?php echo formatCurrency($product['srp']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Available</p>
                                        <p class="text-lg font-semibold text-gray-900"><?php echo $product['quantity']; ?> pcs</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $product['product_id']; ?>" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm text-center">
                                        👁️ View
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $product['product_id']; ?>#deal" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm text-center">
                                        🤝 Deal
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Results Count -->
                <div class="mt-8 text-center text-gray-600">
                    <p>Showing <?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function viewProduct(productId) {
            // Stage 3 - Product detail page
            alert('Product detail page coming in Stage 3!');
        }

        function initiateDeal(productId) {
            // Stage 3 - Initiate deal
            alert('Deal initiation coming in Stage 3!');
        }
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

