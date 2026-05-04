<?php
/**
 * Product Management Page
 * Admin page for managing products (Stage 6)
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
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get products
$products = $admin->getProducts($status, $search, $limit, $offset);

// Handle product removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $product_id = intval($_POST['product_id']);
    $reason = trim($_POST['reason'] ?? '');
    $result = $admin->removeProduct($product_id, $_SESSION['user_id'], $reason);
    
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - <?php echo APP_NAME; ?></title>
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
                        <a href="products.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text font-medium">Products</a>
                        <a href="deals.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Deals</a>
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
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Product Management</h1>
            <p class="text-gray-600">View and manage all platform products</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Product name, seller, or shop" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="available" <?php echo $status === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="unavailable" <?php echo $status === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="products.php" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($product['seller_name']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($product['shop_name']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">₱<?php echo number_format($product['srp'], 2); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo $product['quantity']; ?> units</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            <?php echo $product['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo date('M d, Y', strtotime($product['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <button onclick="showRemoveModal(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars(addslashes($product['product_name'])); ?>')"
                                                class="px-3 py-1 rounded text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 transition">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No products found
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

    <!-- Remove Modal -->
    <div id="removeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Remove Product</h3>
            <p class="text-gray-600 mb-4">
                Are you sure you want to remove <span id="productName" class="font-semibold"></span>?
            </p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Removal</label>
                <textarea id="removalReason" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                          placeholder="Optional: provide a reason" rows="3"></textarea>
            </div>

            <div class="flex gap-4">
                <button onclick="closeRemoveModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button onclick="confirmRemove()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Remove
                </button>
            </div>
        </div>
    </div>

    <script>
    let selectedProductId = null;

    function showRemoveModal(productId, productName) {
        selectedProductId = productId;
        document.getElementById('productName').textContent = productName;
        document.getElementById('removalReason').value = '';
        document.getElementById('removeModal').classList.remove('hidden');
    }

    function closeRemoveModal() {
        document.getElementById('removeModal').classList.add('hidden');
        selectedProductId = null;
    }

    function confirmRemove() {
        if (!selectedProductId) return;

        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', selectedProductId);
        formData.append('reason', document.getElementById('removalReason').value);

        fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    document.addEventListener('click', function(event) {
        if (event.target.id === 'removeModal') {
            closeRemoveModal();
        }
    });
    </script>
</body>
</html>
