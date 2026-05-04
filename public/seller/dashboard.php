<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication and seller role
requireAuth(['seller']);

// Get seller info
$seller_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM sellers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $seller_info = $stmt->fetch();
} catch (Exception $e) {
    // Handle error
}

// Get product stats
$product_count = 0;
$available_count = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available FROM products WHERE seller_id = :seller_id");
    $stmt->execute([':seller_id' => $seller_info['seller_id'] ?? 0]);
    $stats = $stmt->fetch();
    $product_count = $stats['total'] ?? 0;
    $available_count = $stats['available'] ?? 0;
} catch (Exception $e) {
    // Handle error
}

// Get deals stats
$ongoing_deals = 0;
$completed_deals = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count, status FROM deals WHERE seller_id = :seller_id GROUP BY status");
    $stmt->execute([':seller_id' => $seller_info['seller_id'] ?? 0]);
    $deals_stats = $stmt->fetchAll();
    foreach ($deals_stats as $deal_stat) {
        if ($deal_stat['status'] === 'ongoing') {
            $ongoing_deals = $deal_stat['count'];
        } elseif ($deal_stat['status'] === 'completed') {
            $completed_deals = $deal_stat['count'];
        }
    }
} catch (Exception $e) {
    // Handle error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - <?php echo APP_NAME; ?></title>
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
                    <span class="text-sm font-semibold bg-success bg-opacity-20 text-success px-3 py-1 rounded-full">Seller Dashboard</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/index.php" class="text-text font-medium hover:text-primary transition">💬 Messages</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Seller Dashboard</h1>
            <p class="text-gray-600">Manage your shop and products</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Shop Name -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Shop Name</p>
                <p class="text-2xl font-bold text-gray-900 mt-2"><?php echo htmlspecialchars($seller_info['shop_name'] ?? $_SESSION['username'] ?? 'My Shop'); ?></p>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">My Products</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $product_count; ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $available_count; ?> available</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8-4"></path>
                    </svg>
                </div>
            </div>

            <!-- Active Deals -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Deals</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $ongoing_deals; ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $completed_deals; ?> completed</p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Shop Rating -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Shop Rating</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo ($seller_info['rating'] ?? 0); ?>⭐</p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Product Management -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Product Management</h3>
                <div class="space-y-3">
                    <a href="<?php echo BASE_URL; ?>/public/seller/products/add.php" class="block px-4 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition font-medium">
                        ➕ Add New Product
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/seller/products/list.php" class="block px-4 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition font-medium">
                        📦 View My Products
                    </a>
                </div>
            </div>

            <!-- Deal Management -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Deal Management</h3>
                <div class="space-y-3">
                    <a href="<?php echo BASE_URL; ?>/public/seller/deals.php" class="block px-4 py-2 bg-green-50 text-green-600 rounded hover:bg-green-100 transition font-medium">
                        🤝 View My Deals
                    </a>
                    <div class="px-4 py-2 bg-green-50 text-green-600 rounded font-medium text-sm">
                        <?php echo $ongoing_deals; ?> ongoing • <?php echo $completed_deals; ?> completed
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
                            <th class="pb-3 font-semibold text-gray-700">Buyer</th>
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

        <!-- Shop Info -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-bold mb-4">Shop Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Shop Description</p>
                    <p class="mt-2 text-gray-900"><?php echo htmlspecialchars($seller_info['shop_description'] ?? 'No description set'); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Location</p>
                    <p class="mt-2 text-gray-900">
                        <?php echo htmlspecialchars(($seller_info['barangay'] ?? '') . ', ' . ($seller_info['municipality'] ?? '') . ', ' . ($seller_info['province'] ?? '')); ?>
                    </p>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/public/seller/shop_settings.php" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Edit Shop Info
            </a>
        </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
