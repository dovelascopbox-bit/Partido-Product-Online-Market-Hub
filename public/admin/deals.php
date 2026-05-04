<?php
/**
 * Deal Management Page
 * Admin page for viewing and managing deals (Stage 6)
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication and admin role
requireAuth(['admin']);

$admin = new Admin($pdo);

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filters
$status = isset($_GET['status']) ? $_GET['status'] : null;

// Get deals
$deals = $admin->getDeals($status, $limit, $offset);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deal Management - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Skip to main content link (WCAG 2.1 AA accessibility) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <nav class="bg-surface shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="<?php echo BASE_URL; ?>/public/index.php" class="flex items-center gap-2 text-lg font-bold text-blue-600">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" class="h-8 w-auto rounded">
                        <span>Partido Online Hub</span>
                    </a>
                    <span class="text-sm font-semibold bg-info bg-opacity-20 text-info px-3 py-1 rounded-full">Admin Panel</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-1 text-sm">
                        <a href="dashboard.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Dashboard</a>
                        <a href="users.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Users</a>
                        <a href="products.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Products</a>
                        <a href="deals.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text font-medium">Deals</a>
                        <a href="ratings.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Ratings</a>
                        <a href="flags.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Flags</a>
                        <a href="analytics.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Analytics</a>
                    </div>
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main id="main-content" role="main" tabindex="-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Deal Management</h1>
            <p class="text-gray-600">Monitor all deals and transactions on the platform</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="ongoing" <?php echo $status === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="deals.php" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Deals Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Deal ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Buyer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($deals) > 0): ?>
                            <?php foreach ($deals as $deal): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-blue-600">#<?php echo $deal['deal_id']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($deal['product_name']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($deal['buyer_name']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($deal['seller_name']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">₱<?php echo number_format($deal['srp'], 2); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            <?php echo match($deal['status']) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'ongoing' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            }; ?>">
                                            <?php echo ucfirst($deal['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo date('M d, Y H:i', strtotime($deal['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo $deal['completed_at'] ? date('M d, Y H:i', strtotime($deal['completed_at'])) : '—'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    No deals found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
