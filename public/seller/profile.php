<?php
/**
 * Seller Public Profile Page
 * Display seller information, ratings, reviews, and active products
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Get seller_id from query string
$seller_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$seller_id) {
    header('Location: ' . BASE_URL . '/public/buyer/market.php');
    exit;
}

// Get seller details
$seller_stmt = $pdo->prepare("SELECT s.*, u.full_name, u.created_at as member_since
    FROM sellers s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.seller_id = :seller_id");
$seller_stmt->execute([':seller_id' => $seller_id]);
$seller = $seller_stmt->fetch();

if (!$seller) {
    header('Location: ' . BASE_URL . '/public/buyer/market.php');
    exit;
}

// Get seller ratings
$rating = new Rating($pdo);
$ratings = $rating->getSellerRatings($seller_id, 10);
$rating_stats = $rating->getAverageRating($seller_id);

// Get seller's active products
$products_stmt = $pdo->prepare("SELECT * FROM products 
    WHERE seller_id = :seller_id AND status = 'available'
    ORDER BY created_at DESC
    LIMIT 12");
$products_stmt->execute([':seller_id' => $seller_id]);
$products = $products_stmt->fetchAll();

// Get completed deals count
$deals_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM deals 
    WHERE seller_id = :seller_id AND status = 'completed'");
$deals_stmt->execute([':seller_id' => $seller_id]);
$deals_result = $deals_stmt->fetch();
$completed_deals = $deals_result['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($seller['full_name']); ?> - Seller Profile - Partido Market Hub</title>
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
    <!-- Navigation -->
    <nav class="bg-surface shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                ← Back to Market
            </a>
            <?php if (Auth::isAuthenticated()): ?>
                <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" class="text-blue-600 hover:text-blue-800">
                    My Deals
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Seller Header -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Seller Info -->
                <div class="md:col-span-2">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($seller['full_name']); ?>
                    </h1>
                    <p class="text-xl text-gray-700 mb-4">
                        <?php echo htmlspecialchars($seller['shop_name']); ?>
                    </p>
                    <?php if ($seller['shop_description']): ?>
                        <p class="text-gray-600 mb-4">
                            <?php echo htmlspecialchars($seller['shop_description']); ?>
                        </p>
                    <?php endif; ?>
                    <p class="text-gray-600 mb-2">
                        <span class="font-semibold">Member Since:</span> 
                        <?php echo htmlspecialchars(formatDate($seller['member_since'])); ?>
                    </p>
                    <p class="text-gray-600">
                        <span class="font-semibold">Location:</span> 
                        <?php 
                        $location = array_filter([
                            $seller['barangay'],
                            $seller['municipality'],
                            $seller['province']
                        ]);
                        echo htmlspecialchars(implode(', ', $location)); 
                        ?>
                    </p>
                </div>

                <!-- Rating Summary Card -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Rating</h2>
                    
                    <div class="text-center mb-6">
                        <div class="text-5xl font-bold text-yellow-500 mb-2">
                            <?php echo htmlspecialchars($rating_stats['average']); ?>
                        </div>
                        <div class="text-2xl text-yellow-500 mb-2">
                            <?php 
                            $full_stars = floor($rating_stats['average']);
                            $has_half = ($rating_stats['average'] - $full_stars) >= 0.5;
                            for ($i = 0; $i < $full_stars; $i++) echo '★';
                            if ($has_half) echo '½';
                            for ($i = 0; $i < 5 - $full_stars - ($has_half ? 1 : 0); $i++) echo '☆';
                            ?>
                        </div>
                        <p class="text-gray-600">
                            Based on <?php echo htmlspecialchars($rating_stats['count']); ?> review<?php echo $rating_stats['count'] !== 1 ? 's' : ''; ?>
                        </p>
                    </div>

                    <div class="border-t border-yellow-200 pt-4">
                        <p class="text-gray-700">
                            <span class="font-semibold"><?php echo htmlspecialchars($completed_deals); ?></span> 
                            Completed Deal<?php echo $completed_deals !== 1 ? 's' : ''; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="p-8 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Customer Reviews</h2>
            </div>

            <?php if (empty($ratings)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500">No reviews yet. Be the first to rate this seller!</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($ratings as $rev): ?>
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($rev['buyer_name']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Purchased: <?php echo htmlspecialchars($rev['product_name']); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-yellow-500 text-lg">
                                        <?php 
                                        for ($i = 0; $i < $rev['stars']; $i++) echo '★';
                                        for ($i = 0; $i < 5 - $rev['stars']; $i++) echo '☆';
                                        ?>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars(formatDate($rev['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($rev['review_text']): ?>
                                <p class="text-gray-700 mt-3">
                                    <?php echo htmlspecialchars($rev['review_text']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active Products Section -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-8 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Active Listings</h2>
            </div>

            <?php if (empty($products)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500">No active products at the moment.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-8">
                    <?php foreach ($products as $product): ?>
                        <a href="<?php echo BASE_URL; ?>/public/buyer/product_detail.php?id=<?php echo $product['product_id']; ?>" 
                           class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition group">
                            <!-- Product Image -->
                            <div class="bg-gray-200 h-48 overflow-hidden flex items-center justify-center group-hover:bg-gray-300 transition">
                                <?php if ($product['image_path'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $product['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition">
                                <?php else: ?>
                                    <div class="text-gray-400 text-center">
                                        <p class="text-4xl">📦</p>
                                        <p>No Image</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition mb-2">
                                    <?php echo htmlspecialchars(substr($product['product_name'], 0, 50)); ?>
                                </h3>
                                <p class="text-lg font-bold text-blue-600">
                                    <?php echo htmlspecialchars(formatCurrency($product['srp'])); ?>
                                </p>
                                <p class="text-sm text-gray-500 mt-2">
                                    Stock: <?php echo htmlspecialchars($product['quantity']); ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
