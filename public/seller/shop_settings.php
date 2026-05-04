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

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token validation failed. Please try again.';
        logSecurityEvent('csrf_failure', 'Shop settings form CSRF token mismatch');
    } else {
        $shop_name = sanitizeInput($_POST['shop_name'] ?? '');
        $shop_description = sanitizeInput($_POST['shop_description'] ?? '');
        $barangay = sanitizeInput($_POST['barangay'] ?? '');
        $municipality = sanitizeInput($_POST['municipality'] ?? '');
        $province = sanitizeInput($_POST['province'] ?? '');

        // Validate inputs
        if (empty($shop_name)) {
            $error = 'Shop name is required.';
        } else if (strlen($shop_name) > 255) {
            $error = 'Shop name must not exceed 255 characters.';
        } else {
            // Update seller record
            try {
                $stmt = $pdo->prepare("
                    UPDATE sellers 
                    SET shop_name = :shop_name,
                        shop_description = :shop_description,
                        barangay = :barangay,
                        municipality = :municipality,
                        province = :province
                    WHERE seller_id = :seller_id
                ");
                $stmt->execute([
                    ':shop_name' => $shop_name,
                    ':shop_description' => $shop_description,
                    ':barangay' => $barangay,
                    ':municipality' => $municipality,
                    ':province' => $province,
                    ':seller_id' => $seller_info['seller_id']
                ]);

                $success = 'Shop information updated successfully!';
                logSecurityEvent('shop_settings_updated', 'Shop settings updated', $_SESSION['user_id']);

                // Refresh seller info
                $stmt = $pdo->prepare("SELECT * FROM sellers WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $_SESSION['user_id']]);
                $seller_info = $stmt->fetch();
            } catch (Exception $e) {
                $error = 'Failed to update shop information. Please try again.';
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
    <title>Shop Settings - <?php echo APP_NAME; ?></title>
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
            <span class="text-gray-700">Shop Settings</span>
        </div>

        <!-- Page Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Shop Settings</h1>
        <p class="text-gray-600 mb-8">Configure your shop information and location</p>

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
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <!-- Shop Name -->
                <div class="mb-6">
                    <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-2">Shop Name *</label>
                    <input 
                        type="text" 
                        id="shop_name" 
                        name="shop_name" 
                        required
                        value="<?php echo htmlspecialchars($seller_info['shop_name'] ?? $_SESSION['username'] ?? ''); ?>"
                        placeholder="e.g., Maria's Handicrafts"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                    <p class="text-xs text-gray-500 mt-1">If left empty, your username will be used as your shop name.</p>
                </div>

                <!-- Shop Description -->
                <div class="mb-6">
                    <label for="shop_description" class="block text-sm font-medium text-gray-700 mb-2">Shop Description</label>
                    <textarea 
                        id="shop_description" 
                        name="shop_description" 
                        rows="4"
                        placeholder="Describe your shop, what you sell, your specialties, etc."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    ><?php echo htmlspecialchars($seller_info['shop_description'] ?? ''); ?></textarea>
                </div>

                <!-- Location Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Location</h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Barangay -->
                        <div>
                            <label for="barangay" class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                            <input 
                                type="text" 
                                id="barangay" 
                                name="barangay" 
                                value="<?php echo htmlspecialchars($seller_info['barangay'] ?? ''); ?>"
                                placeholder="e.g., Barangay San Jose"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            >
                        </div>

                        <!-- Municipality -->
                        <div>
                            <label for="municipality" class="block text-sm font-medium text-gray-700 mb-2">Municipality / City</label>
                            <input 
                                type="text" 
                                id="municipality" 
                                name="municipality" 
                                value="<?php echo htmlspecialchars($seller_info['municipality'] ?? ''); ?>"
                                placeholder="e.g., Goa"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            >
                        </div>

                        <!-- Province -->
                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                            <input 
                                type="text" 
                                id="province" 
                                name="province" 
                                value="<?php echo htmlspecialchars($seller_info['province'] ?? ''); ?>"
                                placeholder="e.g., Camarines Sur"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            >
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-semibold"
                    >
                        Save Changes
                    </button>
                    <a 
                        href="<?php echo BASE_URL; ?>/public/seller/dashboard.php"
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
                <strong>💡 Tip:</strong> A clear shop name and description help buyers find and trust your shop. Location details help local buyers know where you're based in Partido.
            </p>
        </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

