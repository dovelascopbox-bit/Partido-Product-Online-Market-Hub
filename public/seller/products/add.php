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
}

if (!$seller_info) {
    setFlashMessage('Seller account not found.', 'error');
    secureRedirect(BASE_URL . '/public/seller/dashboard.php');
}

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token validation failed. Please try again.';
        logSecurityEvent('csrf_failure', 'Add product form CSRF token mismatch');
    } else {
        $product_name = sanitizeInput($_POST['product_name'] ?? '');
        $product_description = sanitizeInput($_POST['product_description'] ?? '');
        $srp = floatval($_POST['srp'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);

        // Validate inputs
        if (empty($product_name) || empty($product_description) || $srp <= 0 || $quantity < 0) {
            $error = 'Please fill in all fields correctly.';
        } else if (strlen($product_name) < 3 || strlen($product_name) > 255) {
            $error = 'Product name must be between 3 and 255 characters.';
        } else if (strlen($product_description) < 10) {
            $error = 'Product description must be at least 10 characters.';
        } else {
            // Handle image upload
            $image_path = null;
            
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_result = uploadProductImage($_FILES['product_image'], $seller_info['seller_id']);
                
                if (!$upload_result['success']) {
                    $error = $upload_result['message'];
                } else {
                    $image_path = $upload_result['path'];
                }
            }

            // Create product if no error
            if (empty($error)) {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Product.php';
                $product = new Product($pdo);
                $result = $product->create(
                    $seller_info['seller_id'],
                    $product_name,
                    $product_description,
                    $srp,
                    $quantity,
                    $image_path
                );

                if ($result['success']) {
                    $success = 'Product added successfully!';
                    logSecurityEvent('product_created', 'New product created: ' . $product_name, $_SESSION['user_id']);
                    
                    // Redirect after 2 seconds
                    header('refresh:2;url=' . BASE_URL . '/public/seller/products/list.php');
                } else {
                    $error = $result['message'];
                }
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - <?php echo APP_NAME; ?></title>
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

    <div class="max-w-2xl mx-auto px-4 py-12">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <a href="<?php echo BASE_URL; ?>/public/seller/dashboard.php" class="text-blue-600 hover:underline">Dashboard</a>
            <span class="text-gray-500"> / </span>
            <a href="<?php echo BASE_URL; ?>/public/seller/products/list.php" class="text-blue-600 hover:underline">Products</a>
            <span class="text-gray-500"> / </span>
            <span class="text-gray-700">Add Product</span>
        </div>

        <!-- Page Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Product</h1>
        <p class="text-gray-600 mb-8">List a new product in your shop</p>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    <?php echo htmlspecialchars($success); ?> Redirecting...
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <!-- Product Name -->
                <div class="mb-6">
                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                    <input 
                        type="text" 
                        id="product_name" 
                        name="product_name" 
                        required
                        placeholder="e.g., Hand-woven basket"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">3-255 characters</p>
                </div>

                <!-- Product Description -->
                <div class="mb-6">
                    <label for="product_description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea 
                        id="product_description" 
                        name="product_description" 
                        required
                        rows="5"
                        placeholder="Describe your product, materials, condition, etc."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimum 10 characters</p>
                </div>

                <!-- Price (SRP) -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="srp" class="block text-sm font-medium text-gray-700 mb-2">Suggested Retail Price (₱) *</label>
                        <input 
                            type="number" 
                            id="srp" 
                            name="srp" 
                            required
                            step="0.01"
                            min="0.01"
                            placeholder="0.00"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity Available *</label>
                        <input 
                            type="number" 
                            id="quantity" 
                            name="quantity" 
                            required
                            min="0"
                            placeholder="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>
                </div>

                <!-- Product Image -->
                <div class="mb-6">
                    <label for="product_image" class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition cursor-pointer" id="drop-area">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-700 font-medium mb-1">Drag and drop or click to select</p>
                        <p class="text-xs text-gray-500">JPG, PNG, WEBP • Max 2MB</p>
                        <input 
                            type="file" 
                            id="product_image" 
                            name="product_image" 
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                        >
                    </div>
                    <div id="file-preview" class="mt-4 hidden">
                        <img id="preview-img" class="max-h-64 mx-auto rounded-lg border border-gray-300">
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-semibold"
                    >
                        Add Product
                    </button>
                    <a 
                        href="<?php echo BASE_URL; ?>/public/seller/products/list.php"
                        class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition font-semibold text-center"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>💡 Tip:</strong> Add high-quality photos and detailed descriptions to attract more buyers. Products can be marked as unavailable anytime from your products list.
            </p>
        </div>
    </div>

    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('product_image');
        const filePreview = document.getElementById('file-preview');
        const previewImg = document.getElementById('preview-img');

        // Click to select file
        dropArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('border-blue-500', 'bg-blue-50');
            fileInput.files = e.dataTransfer.files;
            showPreview();
        });

        // File input change
        fileInput.addEventListener('change', showPreview);

        function showPreview() {
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    filePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
