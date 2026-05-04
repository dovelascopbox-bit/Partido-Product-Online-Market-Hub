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

// Get all products for this seller
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Product.php';
$product_obj = new Product($pdo);
$products = $product_obj->getBySellerID($seller_info['seller_id']);

// Get flash message if exists
$flash = getFlashMessage();

// Handle status toggle via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'toggle_status' && isset($_POST['product_id'])) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Product.php';
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
            exit;
        }

        $product_id = intval($_POST['product_id']);
        $product_obj = new Product($pdo);
        $result = $product_obj->toggleStatus($product_id, $seller_info['seller_id']);
        echo json_encode($result);
        exit;
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - <?php echo APP_NAME; ?></title>
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
            <span class="text-gray-700">Products</span>
        </div>

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Products</h1>
                <p class="text-gray-600">Manage your product listings</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/public/seller/products/add.php" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                ➕ Add Product
            </a>
        </div>

        <!-- Flash Message -->
        <?php if ($flash): ?>
            <div class="mb-6 p-4 bg-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-50 border border-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-200 rounded-lg text-<?php echo $flash['type'] === 'success' ? 'green' : 'red'; ?>-700">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Total Products</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo count($products); ?></p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Available</p>
                <p class="text-3xl font-bold text-green-600 mt-2">
                    <?php echo count(array_filter($products, fn($p) => $p['status'] === 'available')); ?>
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Unavailable</p>
                <p class="text-3xl font-bold text-red-600 mt-2">
                    <?php echo count(array_filter($products, fn($p) => $p['status'] === 'unavailable')); ?>
                </p>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if (empty($products)): ?>
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8-4"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No products yet</h3>
                    <p class="text-gray-600 mb-6">Start by adding your first product to your shop.</p>
                    <a href="<?php echo BASE_URL; ?>/public/seller/products/add.php" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Add Your First Product
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Qty</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex gap-4">
                                            <?php if (!empty($product['image_path'])): ?>
                                                <img src="<?php echo getProductImageUrl($product['image_path']); ?>" alt="Product" class="w-12 h-12 rounded object-cover">
                                            <?php else: ?>
                                                <div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['product_name']); ?></p>
                                                <p class="text-sm text-gray-600"><?php echo substr(htmlspecialchars($product['product_description']), 0, 50); ?>...</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900"><?php echo formatCurrency($product['srp']); ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-gray-900"><?php echo $product['quantity']; ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $product['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo formatDate($product['created_at'], 'M d, Y'); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <!-- Toggle Status -->
                                            <button 
                                                class="px-3 py-1 text-sm rounded font-medium transition toggle-status-btn
                                                    <?php echo $product['status'] === 'available' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?>"
                                                data-product-id="<?php echo $product['product_id']; ?>"
                                                data-csrf-token="<?php echo htmlspecialchars($csrf_token); ?>"
                                            >
                                                <?php echo $product['status'] === 'available' ? '🔒 Hide' : '🔓 Show'; ?>
                                            </button>

                                            <!-- Edit -->
                                            <a href="<?php echo BASE_URL; ?>/public/seller/products/edit.php?id=<?php echo $product['product_id']; ?>" class="px-3 py-1 text-sm rounded font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                ✏️ Edit
                                            </a>

                                            <!-- Delete -->
                                            <a href="<?php echo BASE_URL; ?>/public/seller/products/delete.php?id=<?php echo $product['product_id']; ?>&csrf=<?php echo htmlspecialchars($csrf_token); ?>" class="px-3 py-1 text-sm rounded font-medium bg-red-100 text-red-700 hover:bg-red-200 transition" onclick="return confirm('Are you sure you want to delete this product?');">
                                                🗑️ Delete
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

    <script>
        // Toggle status via AJAX
        document.querySelectorAll('.toggle-status-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const productId = this.dataset.productId;
                const csrfToken = this.dataset.csrfToken;

                try {
                    const response = await fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'action': 'toggle_status',
                            'product_id': productId,
                            'csrf_token': csrfToken
                        })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Show toast notification
                        showNotification(result.message, 'success');
                        
                        // Update button appearance
                        if (result.new_status === 'available') {
                            this.textContent = '🔒 Hide';
                            this.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                            this.classList.add('bg-red-100', 'text-red-700', 'hover:bg-red-200');
                        } else {
                            this.textContent = '🔓 Show';
                            this.classList.remove('bg-red-100', 'text-red-700', 'hover:bg-red-200');
                            this.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                        }
                        
                        // Reload after 1 second
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(result.message || 'Error updating product', 'error');
                    }
                } catch (error) {
                    showNotification('An error occurred', 'error');
                    console.error(error);
                }
            });
        });

        function showNotification(message, type) {
            const div = document.createElement('div');
            div.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} z-50`;
            div.textContent = message;
            document.body.appendChild(div);
            
            setTimeout(() => div.remove(), 3000);
        }
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
