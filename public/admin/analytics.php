<?php
/**
 * Analytics Page
 * Admin analytics and reporting with Chart.js visualizations (Stage 6)
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication and admin role
requireAuth(['admin']);

$admin = new Admin($pdo);
$analyticsData = $admin->getAnalyticsData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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
                        <a href="deals.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Deals</a>
                        <a href="ratings.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Ratings</a>
                        <a href="flags.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Flags</a>
                        <a href="analytics.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text font-medium">Analytics</a>
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
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Analytics & Reports</h1>
            <p class="text-gray-600">Platform performance metrics and trends</p>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm font-medium mb-2">Total Users</h3>
                <p class="text-4xl font-bold text-gray-900">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                    $result = $stmt->fetch();
                    echo $result['count'];
                    ?>
                </p>
                <p class="text-gray-500 text-xs mt-2">Active platform users</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm font-medium mb-2">Deals Completed</h3>
                <p class="text-4xl font-bold text-gray-900">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM deals WHERE status = 'completed'");
                    $result = $stmt->fetch();
                    echo $result['count'];
                    ?>
                </p>
                <p class="text-gray-500 text-xs mt-2">Successful transactions</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm font-medium mb-2">Completion Rate</h3>
                <p class="text-4xl font-bold text-green-600">
                    <?php echo $analyticsData['completion_rate']; ?>%
                </p>
                <p class="text-gray-500 text-xs mt-2">Deal success rate</p>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Registration Trend -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Registration Trend (Last 12 Months)</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="userRegistrationChart"></canvas>
                </div>
            </div>

            <!-- Deals by Month -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Deals Activity (Last 12 Months)</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="dealsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Sellers Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Rated Sellers</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rank</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Shop Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Average Rating</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Number of Ratings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($analyticsData['top_sellers']) > 0): ?>
                            <?php foreach ($analyticsData['top_sellers'] as $index => $seller): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                            <?php echo $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800'); ?>">
                                            <?php echo $index + 1; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($seller['full_name']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($seller['shop_name']); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <div class="flex">
                                                <?php for ($i = 0; $i < intval($seller['avg_rating']); $i++): ?>
                                                    <span class="text-yellow-400">★</span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="font-medium text-gray-900"><?php echo number_format($seller['avg_rating'], 2); ?>/5</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo $seller['rating_count']; ?> ratings</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No seller ratings yet
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Platform Health -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Average Rating</p>
                <p class="text-3xl font-bold text-yellow-500">
                    <?php
                    $stmt = $pdo->query("SELECT AVG(stars) as avg FROM ratings");
                    $result = $stmt->fetch();
                    echo $result['avg'] ? number_format($result['avg'], 1) : 'N/A';
                    ?>
                    ★
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Active Products</p>
                <p class="text-3xl font-bold text-green-600">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE status = 'available'");
                    $result = $stmt->fetch();
                    echo $result['count'];
                    ?>
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Pending Deals</p>
                <p class="text-3xl font-bold text-blue-600">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM deals WHERE status = 'pending'");
                    $result = $stmt->fetch();
                    echo $result['count'];
                    ?>
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium mb-2">Flagged Items</p>
                <p class="text-3xl font-bold text-red-600">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM flag_reports WHERE status = 'pending'");
                    $result = $stmt->fetch();
                    echo $result['count'];
                    ?>
                </p>
            </div>
        </div>
    </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>

    <script>
    // User Registration Chart
    const userRegData = <?php echo json_encode($analyticsData['user_registrations']); ?>;
    const userRegCtx = document.getElementById('userRegistrationChart').getContext('2d');
    
    new Chart(userRegCtx, {
        type: 'line',
        data: {
            labels: userRegData.map(d => d.month),
            datasets: [{
                label: 'New Users',
                data: userRegData.map(d => d.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Deals Chart
    const dealsData = <?php echo json_encode($analyticsData['deals_by_month']); ?>;
    const dealsCtx = document.getElementById('dealsChart').getContext('2d');
    
    new Chart(dealsCtx, {
        type: 'bar',
        data: {
            labels: dealsData.map(d => d.month),
            datasets: [
                {
                    label: 'Total Deals',
                    data: dealsData.map(d => d.total_deals),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                },
                {
                    label: 'Completed Deals',
                    data: dealsData.map(d => d.completed_deals),
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    </script>
</body>
</html>
