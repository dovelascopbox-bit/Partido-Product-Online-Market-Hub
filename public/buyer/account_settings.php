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
    setFlashMessage('Error loading buyer info.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

if (!$buyer_info) {
    setFlashMessage('Buyer account not found.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

// Get user info for email/full_name
$user_info = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user_info = $stmt->fetch();
} catch (Exception $e) {
    setFlashMessage('Error loading user info.', 'error');
    secureRedirect(BASE_URL . '/public/buyer/dashboard.php');
}

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token validation failed. Please try again.';
        logSecurityEvent('csrf_failure', 'Account settings form CSRF token mismatch');
    } else {
        $full_name = sanitizeInput($_POST['full_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $full_address = sanitizeInput($_POST['full_address'] ?? '');
        $barangay = sanitizeInput($_POST['barangay'] ?? '');
        $municipality = sanitizeInput($_POST['municipality'] ?? '');
        $province = sanitizeInput($_POST['province'] ?? '');
        $postal_code = sanitizeInput($_POST['postal_code'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($full_name) || empty($email)) {
            $error = 'Full name and email are required.';
        } else if (!validateEmail($email)) {
            $error = 'Invalid email address.';
        } else {
            try {
                $pdo->beginTransaction();

                // Check if email is taken by another user
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :user_id");
                $stmt->execute([':email' => $email, ':user_id' => $_SESSION['user_id']]);
                if ($stmt->fetch()) {
                    $error = 'Email address is already in use.';
                    $pdo->rollBack();
                } else {
                    // Update users table
                    $stmt = $pdo->prepare("
                        UPDATE users 
                        SET full_name = :full_name, email = :email, updated_at = NOW()
                        WHERE user_id = :user_id
                    ");
                    $stmt->execute([
                        ':full_name' => $full_name,
                        ':email' => $email,
                        ':user_id' => $_SESSION['user_id']
                    ]);

                    // Update session
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['email'] = $email;

                    // Update buyers table
                    $stmt = $pdo->prepare("
                        UPDATE buyers 
                        SET phone = :phone,
                            full_address = :full_address,
                            barangay = :barangay,
                            municipality = :municipality,
                            province = :province,
                            postal_code = :postal_code
                        WHERE buyer_id = :buyer_id
                    ");
                    $stmt->execute([
                        ':phone' => $phone,
                        ':full_address' => $full_address,
                        ':barangay' => $barangay,
                        ':municipality' => $municipality,
                        ':province' => $province,
                        ':postal_code' => $postal_code,
                        ':buyer_id' => $buyer_info['buyer_id']
                    ]);

                    // Handle password change if provided
                    if (!empty($new_password)) {
                        if (empty($current_password)) {
                            $error = 'Current password is required to set a new password.';
                            $pdo->rollBack();
                        } else if ($new_password !== $confirm_password) {
                            $error = 'New passwords do not match.';
                            $pdo->rollBack();
                        } else if (!validatePasswordStrength($new_password)) {
                            $error = 'New password must be at least 8 characters with uppercase, lowercase, and numbers.';
                            $pdo->rollBack();
                        } else {
                            $auth = new Auth($pdo);
                            $result = $auth->login($user_info['email'], $current_password);
                            if (!$result['success']) {
                                $error = 'Current password is incorrect.';
                                $pdo->rollBack();
                            } else {
                                $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
                                $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE user_id = :user_id");
                                $stmt->execute([':hash' => $new_hash, ':user_id' => $_SESSION['user_id']]);
                            }
                        }
                    }

                    if (empty($error)) {
                        $pdo->commit();
                        $success = 'Account settings updated successfully!';

                        // Refresh buyer info
                        $stmt = $pdo->prepare("SELECT * FROM buyers WHERE user_id = :user_id");
                        $stmt->execute([':user_id' => $_SESSION['user_id']]);
                        $buyer_info = $stmt->fetch();

                        // Refresh user info
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
                        $stmt->execute([':user_id' => $_SESSION['user_id']]);
                        $user_info = $stmt->fetch();

                        logSecurityEvent('account_settings_updated', 'Buyer account settings updated', $_SESSION['user_id']);
                    }
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Failed to update account information. Please try again.';
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
    <meta name="description" content="Account Settings - <?php echo APP_NAME; ?>">
    <title>Account Settings - <?php echo APP_NAME; ?></title>
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
                    <span class="text-sm font-semibold bg-primary bg-opacity-20 text-primary px-3 py-1 rounded-full">Buyer</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/index.php" class="text-text font-medium hover:text-primary transition">💬 Messages</a>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-12">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <a href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php" class="text-blue-600 hover:underline">Dashboard</a>
            <span class="text-gray-500"> / </span>
            <span class="text-gray-700">Account Settings</span>
        </div>

        <!-- Page Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Account Settings</h1>
        <p class="text-gray-600 mb-8">Manage your profile, delivery address, and password</p>

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

                <!-- Profile Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>
                    
                    <!-- Full Name -->
                    <div class="mb-4">
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            required
                            value="<?php echo htmlspecialchars($user_info['full_name'] ?? ''); ?>"
                            placeholder="Your full name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>"
                            placeholder="you@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="<?php echo htmlspecialchars($buyer_info['phone'] ?? ''); ?>"
                            placeholder="e.g., 09123456789"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                <!-- Delivery Address Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Address</h3>
                    
                    <!-- Full Address -->
                    <div class="mb-4">
                        <label for="full_address" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                        <textarea 
                            id="full_address" 
                            name="full_address" 
                            rows="3"
                            placeholder="House number, street name, building, etc."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        ><?php echo htmlspecialchars($buyer_info['full_address'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Barangay -->
                        <div>
                            <label for="barangay" class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                            <input 
                                type="text" 
                                id="barangay" 
                                name="barangay" 
                                value="<?php echo htmlspecialchars($buyer_info['barangay'] ?? ''); ?>"
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
                                value="<?php echo htmlspecialchars($buyer_info['municipality'] ?? ''); ?>"
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
                                value="<?php echo htmlspecialchars($buyer_info['province'] ?? ''); ?>"
                                placeholder="e.g., Camarines Sur"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            >
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                            <input 
                                type="text" 
                                id="postal_code" 
                                name="postal_code" 
                                value="<?php echo htmlspecialchars($buyer_info['postal_code'] ?? ''); ?>"
                                placeholder="e.g., 4429"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            >
                        </div>
                </div>

                <!-- Password Change Section -->
                <div class="mb-6 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                    <p class="text-sm text-gray-500 mb-4">Leave blank if you don't want to change your password</p>
                    
                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            placeholder="Enter current password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            placeholder="Min 8 chars: uppercase, lowercase, numbers"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Re-enter new password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
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
                        href="<?php echo BASE_URL; ?>/public/buyer/dashboard.php"
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
                <strong>💡 Tip:</strong> Keep your delivery address up to date so sellers know where to send your orders. A complete address helps ensure smooth deliveries in Partido.
            </p>
        </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
