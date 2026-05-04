<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require seller role
requireAuth(['seller']);

// Get seller info
$seller_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM sellers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $seller_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading seller info.', 'error');
    secureRedirect(BASE_URL . '/public/seller/dashboard.php');
}

if (!$seller_info) {
    setFlashMessage('Seller account not found.', 'error');
    secureRedirect(BASE_URL . '/public/seller/dashboard.php');
}

// Get all deals for this seller
$deal_obj = new Deal($pdo);
$deals_ongoing = $deal_obj->getBySellerID($seller_info['seller_id'], 'ongoing');
$deals_completed = $deal_obj->getBySellerID($seller_info['seller_id'], 'completed');
$deals_cancelled = $deal_obj->getBySellerID($seller_info['seller_id'], 'cancelled');

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Deals - <?php echo APP_NAME; ?></title>
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
                    <span class="text-sm font-semibold bg-success bg-opacity-20 text-success px-3 py-1 rounded-full">Seller</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/index.php" class="text-text font-medium hover:text-primary transition">💬 Messages</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <a href="<?php echo BASE_URL; ?>/public/seller/dashboard.php" class="text-blue-600 hover:underline">Dashboard</a>
            <span class="text-gray-500"> / </span>
            <span class="text-gray-700">Deals</span>
        </div>

        <!-- Page Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Deals</h1>
        <p class="text-gray-600 mb-8">Manage deals initiated on your products</p>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Deals</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">
                    <?php echo count($deals_ongoing) + count($deals_completed) + count($deals_cancelled); ?>
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Ongoing</p>
                <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo count($deals_ongoing); ?></p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Completed</p>
                <p class="text-3xl font-bold text-green-600 mt-2"><?php echo count($deals_completed); ?></p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Cancelled</p>
                <p class="text-3xl font-bold text-red-600 mt-2"><?php echo count($deals_cancelled); ?></p>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white border-b border-gray-200 mb-8">
            <div class="flex gap-0">
                <button class="tab-btn active px-6 py-4 font-semibold border-b-2 border-blue-600 text-blue-600 transition" data-tab="ongoing">
                    Ongoing (<?php echo count($deals_ongoing); ?>)
                </button>
                <button class="tab-btn px-6 py-4 font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition" data-tab="completed">
                    Completed (<?php echo count($deals_completed); ?>)
                </button>
                <button class="tab-btn px-6 py-4 font-semibold border-b-2 border-transparent text-gray-600 hover:text-gray-900 transition" data-tab="cancelled">
                    Cancelled (<?php echo count($deals_cancelled); ?>)
                </button>
            </div>
        </div>

        <!-- Ongoing Deals Tab -->
        <div id="ongoing-tab" class="tab-content">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (empty($deals_ongoing)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-600 mb-4">No ongoing deals yet</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Buyer</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Your Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Their Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Initiated</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deals_ongoing as $deal): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($deal['product_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-900"><?php echo htmlspecialchars($deal['buyer_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo formatCurrency($deal['srp']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($deal['confirmed_by_seller']): ?>
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">✓ Confirmed</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($deal['confirmed_by_buyer']): ?>
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">✓ Confirmed</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">⏳ Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo formatDate($deal['created_at'], 'M d, Y H:i'); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-2">
                                                <?php if (!$deal['confirmed_by_seller']): ?>
                                                    <button type="button" class="px-3 py-1 text-sm rounded font-medium bg-green-600 text-white hover:bg-green-700 transition confirm-deal-btn" data-deal-id="<?php echo $deal['deal_id']; ?>">
                                                        ✓ Mark as Done
                                                    </button>
                                                <?php else: ?>
                                                    <span class="px-3 py-1 text-sm rounded font-medium bg-green-100 text-green-800">✓ Confirmed</span>
                                                <?php endif; ?>
                                                <a href="<?php echo BASE_URL; ?>/public/messenger/conversation.php?deal_id=<?php echo $deal['deal_id']; ?>" 
                                                   class="px-3 py-1 text-sm rounded font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition text-center">
                                                    💬 Message
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Deals Tab -->
        <div id="completed-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (empty($deals_completed)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-600 mb-4">No completed deals yet</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Buyer</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deals_completed as $deal): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($deal['product_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-900"><?php echo htmlspecialchars($deal['buyer_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo formatCurrency($deal['srp']); ?></p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo $deal['completed_at'] ? formatDate($deal['completed_at'], 'M d, Y H:i') : 'N/A'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cancelled Deals Tab -->
        <div id="cancelled-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (empty($deals_cancelled)): ?>
                    <div class="p-8 text-center">
                        <p class="text-gray-600 mb-4">No cancelled deals yet</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Buyer</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Cancelled</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deals_cancelled as $deal): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($deal['product_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-900"><?php echo htmlspecialchars($deal['buyer_name']); ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900"><?php echo formatCurrency($deal['srp']); ?></p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            <?php echo formatDate($deal['created_at'], 'M d, Y H:i'); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>📌 How it works:</strong> When a buyer initiates a deal on your product, it appears here as "Ongoing". Once you confirm and the buyer confirms, the deal is marked "Completed". This is a local marketplace — no payments handled here.
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

        // Deal confirmation
        document.querySelectorAll('.confirm-deal-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const dealId = this.dataset.dealId;
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

                if (!confirm('Mark this deal as done? Buyer will be notified to confirm.')) {
                    return;
                }

                try {
                    const response = await fetch('<?php echo BASE_URL; ?>/public/seller/confirm_deal.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `deal_id=${dealId}&csrf_token=${csrfToken}`
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    alert('Error confirming deal: ' + error.message);
                }
            });
        });

        function cancelDeal(dealId) {
            if (confirm('Are you sure you want to cancel this deal?')) {
                // Stage 3 - implement via AJAX
                alert('Deal cancellation coming in Stage 3!');
            }
        }
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
