<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication and admin role
requireAuth(['admin']);

$admin = new Admin($pdo);
$stats = $admin->getPlatformStats();
$recent_actions = $admin->getActionLog(10, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
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
<body>
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
                        <a href="dashboard.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-text font-medium">Dashboard</a>
                        <a href="users.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-gray-600">Users</a>
                        <a href="products.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-gray-600">Products</a>
                        <a href="deals.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-gray-600">Deals</a>
                        <a href="ratings.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-gray-600">Ratings</a>
                        <a href="flags.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-gray-600">Flags</a>
                        <a href="analytics.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 transition text-gray-600">Analytics</a>
                    </div>
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main id="main-content">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-text mb-2">Admin Dashboard</h1>
            <p class="text-text-muted">Manage the Partido Product Online Market Hub</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-muted text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-text mt-2"><?php echo $stats['total_users']; ?></p>
                    </div>
                    <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 10H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Sellers -->
            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-muted text-sm font-medium">Total Sellers</p>
                        <p class="text-3xl font-bold text-text mt-2"><?php echo $stats['total_sellers']; ?></p>
                    </div>
                    <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8c0 1.657-.895 3-2 3s-2-1.343-2-3 .895-3 2-3 2 1.343 2 3zM12 14a4 4 0 00-8 0c0 1.657.895 3 2 3h4c1.105 0 2-1.343 2-3zm6-5a2 2 0 11-4 0 2 2 0 014 0zm2 5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Buyers -->
            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-muted text-sm font-medium">Total Buyers</p>
                        <p class="text-3xl font-bold text-text mt-2"><?php echo $stats['total_buyers']; ?></p>
                    </div>
                    <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text-muted text-sm font-medium">Total Products</p>
                        <p class="text-3xl font-bold text-text mt-2"><?php echo $stats['total_products']; ?></p>
                    </div>
                    <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <h3 class="text-lg font-bold mb-4 text-text">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="users.php" class="block px-4 py-2 bg-info bg-opacity-10 text-info rounded hover:bg-opacity-20 transition">
                        👥 Manage Users
                    </a>
                    <a href="products.php" class="block px-4 py-2 bg-primary bg-opacity-10 text-primary rounded hover:bg-opacity-20 transition">
                        📦 Manage Products
                    </a>
                    <a href="deals.php" class="block px-4 py-2 bg-warning bg-opacity-10 text-warning rounded hover:bg-opacity-20 transition">
                        🤝 Manage Deals
                    </a>
                    <a href="ratings.php" class="block px-4 py-2 bg-purple-500 bg-opacity-10 text-purple-600 rounded hover:bg-opacity-20 transition">
                        ⭐ Manage Ratings
                    </a>
                    <a href="flags.php" class="block px-4 py-2 bg-error bg-opacity-10 text-error rounded hover:bg-opacity-20 transition">
                        🚩 Flag Reports
                    </a>
                    <a href="analytics.php" class="block px-4 py-2 bg-success bg-opacity-10 text-success rounded hover:bg-opacity-20 transition">
                        📊 Analytics & Reports
                    </a>
                </div>
            </div>

            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <h3 class="text-lg font-bold mb-4 text-text">System Info</h3>
                <div class="space-y-2 text-sm text-text-muted">
                    <p><span class="font-medium text-text">Application:</span> <?php echo APP_NAME; ?></p>
                    <p><span class="font-medium text-text">Stage:</span> <?php echo STAGE; ?></p>
                    <p><span class="font-medium text-text">Version:</span> <?php echo APP_VERSION; ?></p>
                    <p><span class="font-medium text-text">Session ID:</span> <?php echo substr(session_id(), 0, 8); ?>...</p>
                </div>
            </div>

            <div class="bg-card rounded-lg shadow p-6 border border-border">
                <h3 class="text-lg font-bold mb-4 text-text">Account Info</h3>
                <div class="space-y-2 text-sm text-text-muted">
                    <p><span class="font-medium text-text">Name:</span> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    <p><span class="font-medium text-text">Email:</span> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    <p><span class="font-medium text-text">Role:</span> <span class="inline-block px-2 py-1 bg-info bg-opacity-20 text-info rounded text-xs font-semibold">Admin</span></p>
                    <p><span class="font-medium text-text">Login Time:</span> <?php echo date('M d, Y H:i A'); ?></p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-card rounded-lg shadow p-6 border border-border">
            <h3 class="text-lg font-bold mb-4 text-text">Recent Admin Actions</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-text">
                    <thead class="border-b border-border">
                        <tr>
                            <th class="pb-3 font-semibold text-text">Admin</th>
                            <th class="pb-3 font-semibold text-text">Action Type</th>
                            <th class="pb-3 font-semibold text-text">Description</th>
                            <th class="pb-3 font-semibold text-text">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_actions) > 0): ?>
                            <?php foreach ($recent_actions as $action): ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3"><?php echo htmlspecialchars($action['admin_name']); ?></td>
                                    <td class="py-3"><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold"><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($action['action_type']))); ?></span></td>
                                    <td class="py-3 text-gray-600"><?php echo htmlspecialchars($action['description']); ?></td>
                                    <td class="py-3 text-gray-500"><?php echo date('M d, Y H:i', strtotime($action['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">No recent admin actions</td>
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
