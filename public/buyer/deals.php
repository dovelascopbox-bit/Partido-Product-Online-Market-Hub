<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication and buyer role
requireAuth(['buyer']);

// Get buyer info
$buyer_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM buyers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $buyer_info = $stmt->fetch();
} catch (Exception $e) {
    // Handle error
}

// Get active deals count
$active_deals_count = 0;
if ($buyer_info) {
    try {
        $deal_obj = new Deal($pdo);
        $active_deals = $deal_obj->getByBuyerID($buyer_info['buyer_id'], 'ongoing');
        $active_deals_count = count($active_deals);
    } catch (Exception $e) {
        // Handle error
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Buyer Dashboard - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Buyer Dashboard - <?php echo APP_NAME; ?></title>
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
    <nav class="bg-surface shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="<?php echo BASE_URL; ?>/public/index.php" class="flex items-center gap-2 text-lg font-bold text-blue-600">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" class="h-8 w-auto rounded">
                        <span>Partido Online Hub</span>
                    </a>
                    <span class="text-sm font-semibold bg-primary bg-opacity-20 text-primary px-3 py-1 rounded-full">Buyer Dashboard</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/index.php" class="text-text font-medium hover:text-primary transition">💬 Messages</a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/my-ratings.php" class="text-text font-medium hover:text-primary transition">⭐ My Ratings</a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/wishlist.php" class="text-text font-medium hover:text-primary transition">❤️ Wishlist</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Buyer Dashboard</h1>
            <p class="text-gray-600">Browse products and manage your purchases</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Active Deals -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Deals</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $active_deals_count; ?></p>
                    </div>
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Purchases -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Purchases</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $buyer_info['total_purchases'] ?? 0; ?></p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8-4"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Spent</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo formatCurrency($buyer_info['total_spent'] ?? 0); ?></p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Wishlisted -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Wishlisted</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
                    </div>
                    <svg class="w-12 h-12 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Browse Market -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Browse Market</h3>
                <div class="space-y-3">
                    <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="block px-4 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition font-medium">
                        🛍️ View Marketplace
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/marketplace.php" class="block px-4 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition font-medium">
                        🔍 Browse All Products
                    </a>
                </div>
            </div>

            <!-- My Deals -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">My Deals</h3>
                <div class="space-y-3">
                    <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" class="block px-4 py-2 bg-green-50 text-green-600 rounded hover:bg-green-100 transition font-medium">
                        🤝 View My Deals
                    </a>
                    <div class="px-4 py-2 bg-green-50 text-green-600 rounded font-medium text-sm">
                        <?php echo $active_deals_count; ?> active deals
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Recent Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th class="pb-3 font-semibold text-gray-700">Order ID</th>
                            <th class="pb-3 font-semibold text-gray-700">Seller</th>
                            <th class="pb-3 font-semibold text-gray-700">Product</th>
                            <th class="pb-3 font-semibold text-gray-700">Amount</th>
                            <th class="pb-3 font-semibold text-gray-700">Status</th>
                            <th class="pb-3 font-semibold text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td colspan="6" class="py-8 text-center text-gray-500">No orders yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profile Info -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-bold mb-4">Profile Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Full Name</p>
                    <p class="mt-2 text-gray-900"><?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Email</p>
                    <p class="mt-2 text-gray-900"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Delivery Address</p>
                    <p class="mt-2 text-gray-900"><?php echo htmlspecialchars($buyer_info['full_address'] ?? 'No address set'); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Location</p>
                    <p class="mt-2 text-gray-900">
                        <?php echo htmlspecialchars(($buyer_info['barangay'] ?? '') . ', ' . ($buyer_info['municipality'] ?? '') . ', ' . ($buyer_info['province'] ?? '')); ?>
                    </p>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/public/buyer/account_settings.php" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Edit Profile
            </a>
        </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

