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
    setFlashMessage('Error loading buyer info: ' . $e->getMessage(), 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

if (!$buyer_info) {
    setFlashMessage('Buyer account not found.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

// Debug: Log buyer_id for troubleshooting
error_log("Buyer ID: " . $buyer_info['buyer_id'] . " for User ID: " . $_SESSION['user_id']);

// Get pending ratings (completed deals without ratings)
$pending_ratings = [];
$query_error = '';
try {
    // First, get all completed deals for this buyer
    $stmt = $pdo->prepare("
        SELECT d.deal_id, d.product_id, d.seller_id, d.completed_at, d.status,
               p.product_name, p.srp,
               u.full_name as seller_name, s.shop_name
        FROM deals d
        LEFT JOIN products p ON d.product_id = p.product_id
        LEFT JOIN sellers s ON d.seller_id = s.seller_id
        LEFT JOIN users u ON s.user_id = u.user_id
        WHERE d.buyer_id = :buyer_id 
        AND d.status = 'completed'
        ORDER BY d.completed_at DESC
    ");
    $stmt->execute([':buyer_id' => $buyer_info['buyer_id']]);
    $all_completed = $stmt->fetchAll();
    
    // Filter out deals that already have ratings
    foreach ($all_completed as $deal) {
        $rating_stmt = $pdo->prepare("SELECT rating_id FROM ratings WHERE deal_id = :deal_id AND buyer_id = :buyer_id");
        $rating_stmt->execute([':deal_id' => $deal['deal_id'], ':buyer_id' => $buyer_info['buyer_id']]);
        if (!$rating_stmt->fetch()) {
            // No rating for this deal, add to pending
            $pending_ratings[] = $deal;
        }
    }
    
    error_log("Found " . count($all_completed) . " completed deals, " . count($pending_ratings) . " pending ratings for buyer " . $buyer_info['buyer_id']);
} catch (Exception $e) {
    $query_error = $e->getMessage();
    error_log("Pending ratings query error: " . $query_error);
}

// Get completed ratings
$completed_ratings = [];
try {
    $stmt = $pdo->prepare("
        SELECT r.*, d.product_id, d.seller_id, d.completed_at,
               p.product_name, p.srp,
               u.full_name as seller_name, s.shop_name
        FROM ratings r
        JOIN deals d ON r.deal_id = d.deal_id
        JOIN products p ON d.product_id = p.product_id
        JOIN sellers s ON d.seller_id = s.seller_id
        JOIN users u ON s.user_id = u.user_id
        WHERE r.buyer_id = :buyer_id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':buyer_id' => $buyer_info['buyer_id']]);
    $completed_ratings = $stmt->fetchAll();
} catch (Exception $e) {
    // Handle error silently
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ratings - <?php echo APP_NAME; ?></title>
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
                    <span class="text-sm font-semibold bg-success bg-opacity-20 text-success px-3 py-1 rounded-full">Buyer</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" class="text-text font-medium hover:text-primary transition">📊 Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/index.php" class="text-text font-medium hover:text-primary transition">💬 Messages</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" class="text-blue-600 hover:underline">Dashboard</a>
            <span class="text-gray-500"> / </span>
            <span class="text-gray-700">My Ratings</span>
        </div>

        <!-- Page Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Ratings</h1>
        <p class="text-gray-600 mb-8">Manage your seller ratings and reviews</p>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Pending Ratings</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo count($pending_ratings); ?></p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Completed Ratings</p>
                <p class="text-3xl font-bold text-green-600 mt-2"><?php echo count($completed_ratings); ?></p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white border-b border-gray-200 mb-8">
            <div class="flex gap-0">
                <button class="tab-btn active px-6 py-4 font-semibold border-b-2 border-blue-600 text-blue-600 transition" data-tab="pending">
                    Pending (<?php echo count($pending_ratings); ?>)
                </button>
                <button class="tab-btn px-6 py-4 font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition" data-tab="completed">
                    Completed (<?php echo count($completed_ratings); ?>)
                </button>
            </div>
        </div>

        <!-- Pending Ratings Tab -->
        <div id="pending-tab" class="tab-content">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (empty($pending_ratings)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-600 mb-4">No pending ratings</p>
                        <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" class="text-blue-600 hover:underline">View your deals →</a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Completed</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_ratings as $deal): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($deal['product_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-900"><?php echo htmlspecialchars($deal['seller_name']); ?></p>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($deal['shop_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo formatCurrency($deal['srp']); ?></p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo formatDate($deal['completed_at'], 'M d, Y'); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="<?php echo BASE_URL; ?>/public/buyer/rate.php?deal_id=<?php echo $deal['deal_id']; ?>" 
                                               class="px-3 py-1 text-sm rounded font-medium bg-yellow-600 text-white hover:bg-yellow-700 transition">
                                                ⭐ Rate Now
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Ratings Tab -->
        <div id="completed-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (empty($completed_ratings)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-600 mb-4">No completed ratings yet</p>
                        <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" class="text-blue-600 hover:underline">Complete a deal →</a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($completed_ratings as $rating): ?>
                            <div class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($rating['product_name']); ?></h3>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-semibold">Seller:</span> <?php echo htmlspecialchars($rating['seller_name']); ?> (<?php echo htmlspecialchars($rating['shop_name']); ?>)
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl">
                                            <?php echo str_repeat('⭐', $rating['stars']); ?>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo $rating['stars']; ?>/5</p>
                                    </div>
                                </div>

                                <?php if ($rating['review_text']): ?>
                                    <p class="text-gray-700 mb-3"><?php echo htmlspecialchars($rating['review_text']); ?></p>
                                <?php endif; ?>

                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        Rated on <?php echo formatDate($rating['created_at'], 'M d, Y H:i'); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">Price:</span> <?php echo formatCurrency($rating['srp']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>ℹ️ How ratings work:</strong> After completing a deal, you can rate the seller's service. Your rating helps other buyers make informed decisions. Once you rate a seller, you cannot change your rating.
            </p>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-blue-600', 'text-blue-600', 'active');
                    b.classList.add('border-transparent', 'text-gray-600');
                });
                
                // Add active class to clicked tab
                this.classList.add('border-blue-600', 'text-blue-600', 'active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show selected tab content
                const tab = this.dataset.tab;
                document.getElementById(tab + '-tab').classList.remove('hidden');
            });
        });
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
