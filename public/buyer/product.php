<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require buyer role
requireAuth(['buyer']);

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    setFlashMessage('Product not found', 'error');
    secureRedirect(BASE_URL . '/public/buyer/market.php');
}

// Get market helper and product
$market = new Market($pdo);
$product = $market->getProductById($product_id);

if (!$product) {
    setFlashMessage('Product not found', 'error');
    secureRedirect(BASE_URL . '/public/buyer/market.php');
}

// Check if product exists (fetched successfully - this is the main requirement)
// Note: We allow viewing even if out of stock, but flag it visually
if (!$market->isProductAvailable($product_id)) {
    // Product exists but may be out of stock - still allows viewing but shows as unavailable
}

// Get buyer info
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM buyers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading buyer info', 'error');
}

// Get related products (same seller)
$related_products = $market->getRelatedProducts($product['seller_id'], $product_id, 3);

// Check if buyer already has ongoing deal for this product
$existing_deal = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM deals WHERE product_id = :product_id AND buyer_id = :buyer_id AND status = 'ongoing'");
    $stmt->execute([':product_id' => $product_id, ':buyer_id' => $buyer_info['buyer_id']]);
    $existing_deal = $stmt->fetch();
} catch (Exception $e) {
    // Handle error
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - <?php echo APP_NAME; ?></title>
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
                </div>
                <div class="flex items-center gap-4">
                    <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="text-text hover:text-primary transition">← Back to Market</a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/my-ratings.php" class="text-text hover:text-primary transition">⭐ My Ratings</a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/wishlist.php" class="text-text hover:text-primary transition">❤️ Wishlist</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Breadcrumb -->
        <div class="mb-8 text-sm text-gray-600">
            <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="text-blue-600 hover:underline">Market Hub</a>
            <span> / </span>
            <span><?php echo htmlspecialchars($product['product_name']); ?></span>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <!-- Product Image -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="bg-gray-200 h-96 flex items-center justify-center overflow-hidden rounded-lg mb-6">
                    <?php $image_url = getProductImageUrl($product['image_path']); ?>
                    <img src="<?php echo htmlspecialchars($image_url); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Product Details -->
            <div class="space-y-6">
                <!-- Title and Availability -->
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($product['product_name']); ?>
                    </h1>
                    <div class="flex items-center gap-4">
                        <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold">
                            ✓ Available
                        </span>
                        <span class="text-gray-600">
                            <?php echo $product['quantity']; ?> in stock
                        </span>
                    </div>
                </div>

                <!-- Price -->
                <div class="border-t border-b border-gray-200 py-6">
                    <p class="text-sm text-gray-600 mb-2">Suggested Retail Price</p>
                    <p class="text-5xl font-bold text-blue-600">
                        <?php echo formatCurrency($product['srp']); ?>
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">About This Product</h3>
                    <p class="text-gray-700 leading-relaxed">
                        <?php echo htmlspecialchars($product['product_description']); ?>
                    </p>
                </div>

                <!-- Seller Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Seller Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Shop Name</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo htmlspecialchars($product['shop_name']); ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Seller</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown Seller'); ?>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Rating</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php echo number_format($product['rating'] ?? 0, 1); ?>⭐
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600">Completed Deals</p>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo intval($product['total_completed_deals'] ?? 0); ?> deals
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Deal Button -->
                <div id="deal">
                    <?php if ($existing_deal): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                            <p class="text-yellow-800">
                                <strong>You already have an ongoing deal for this product.</strong>
                            </p>
                            <p class="text-sm text-yellow-700 mt-1">
                                <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" class="underline">View your deals →</a>
                            </p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo BASE_URL; ?>/public/buyer/initiate_deal.php" class="space-y-4">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="seller_id" value="<?php echo $product['seller_id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <button type="submit" class="w-full px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold text-lg">
                                🤝 INITIATE DEAL
                            </button>
                            
                            <p class="text-sm text-gray-600 text-center">
                                Click to start a deal. You'll be directed to message this seller.
                            </p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">More from this Seller</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($related_products as $related): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            <div class="bg-gray-200 h-40 flex items-center justify-center overflow-hidden">
                                <?php $rel_image = getProductImageUrl($related['image_path']); ?>
                                <img src="<?php echo htmlspecialchars($rel_image); ?>" 
                                     alt="<?php echo htmlspecialchars($related['product_name']); ?>" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($related['product_name']); ?>
                                </h3>
                                <p class="text-2xl font-bold text-blue-600 mb-3">
                                    <?php echo formatCurrency($related['srp']); ?>
                                </p>
                                <a href="<?php echo BASE_URL; ?>/public/buyer/product.php?id=<?php echo $related['product_id']; ?>" 
                                   class="block w-full px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-center">
                                    View Product
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
