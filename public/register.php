<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// If already logged in, redirect to dashboard
requireGuest();

$error = '';
$success = '';
$username = '';
$email = '';
$full_name = '';
$role = sanitizeInput($_GET['role'] ?? 'buyer');

// Validate role
if (!in_array($role, ['admin', 'seller', 'buyer'])) {
    $role = 'buyer';
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token validation failed. Please try again.';
        logSecurityEvent('csrf_failure', 'Register form CSRF token mismatch');
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $full_name = sanitizeInput($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $role = sanitizeInput($_POST['role'] ?? 'buyer');

        // Validate inputs
        if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
            $error = 'All fields are required.';
        } else if (strlen($username) < 3 || strlen($username) > 50) {
            $error = 'Username must be between 3 and 50 characters.';
        } else if (!validateEmail($email)) {
            $error = 'Invalid email address.';
        } else if (strlen($full_name) < 2 || strlen($full_name) > 100) {
            $error = 'Full name must be between 2 and 100 characters.';
        } else if (!validatePasswordStrength($password)) {
            $error = 'Password must be at least 8 characters and contain uppercase, lowercase, and numbers.';
        } else if ($password !== $password_confirm) {
            $error = 'Passwords do not match.';
        } else if (!in_array($role, ['seller', 'buyer'])) {
            $error = 'Invalid role selected.';
        } else {
            // Attempt registration
            $auth = new Auth($pdo);
            $result = $auth->register($username, $email, $password, $full_name, $role);

if ($result['success']) {
                // Send verification email
                $auth->sendVerificationEmail($result['user_id'], $email, $full_name);
                
                $success = 'Registration successful! Please check your email to verify your account before signing in.';
                
                // Log security event
                logSecurityEvent('user_registered', 'New user registered - Role: ' . $role, $result['user_id']);

                // Clear form
                $username = '';
                $email = '';
                $full_name = '';
            } else {
                $error = $result['message'];
                logSecurityEvent('registration_failed', 'Registration failed - ' . $result['message']);
            }
        }
    }
}

// Get CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register with <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Register - <?php echo APP_NAME; ?></title>
    
    <!-- Design System (Order matters: tokens → main → dark mode) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/helpers.css">
    
    <!-- Professional Dark Mode System (Stage 7-F) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <!-- Theme Switcher Script (Prevent dark mode flash) -->
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

    <!-- Auth Form Styles -->
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
        }

        .auth-box {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
        }

        html.dark .auth-box {
            background: var(--gray-100);
            border-color: var(--gray-200);
            box-shadow: var(--shadow-xl);
        }

        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .auth-header h1 {
            margin-bottom: var(--spacing-md);
            color: var(--gray-900);
        }

        html.dark .auth-header h1 {
            color: var(--gray-700);
        }

        .auth-header p {
            color: var(--gray-600);
        }

        html.dark .auth-header p {
            color: var(--gray-500);
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .form-group label {
            font-weight: 600;
            color: var(--gray-900);
        }

        html.dark .form-group label {
            color: var(--gray-700);
        }

        .role-selector {
            display: flex;
            gap: var(--spacing-md);
            margin: var(--spacing-lg) 0;
        }

        .role-option {
            flex: 1;
            padding: var(--spacing-md);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all var(--transition-fast);
            text-align: center;
            background: #ffffff;
            color: var(--gray-900);
        }

        html.dark .role-option {
            background: var(--gray-150);
            border-color: var(--gray-200);
            color: var(--gray-700);
        }

        .role-option:hover {
            border-color: var(--primary);
            background: rgba(15, 118, 110, 0.05);
        }

        .role-option.selected {
            border-color: var(--primary);
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary);
            font-weight: 600;
        }

        html.dark .role-option.selected {
            background: rgba(6, 182, 212, 0.15);
            color: var(--primary-light);
        }

        .auth-button {
            margin-top: var(--spacing-md);
        }

        .auth-footer {
            text-align: center;
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--gray-200);
        }

        html.dark .auth-footer {
            border-top-color: var(--gray-200);
        }

        .auth-footer p {
            color: var(--gray-600);
        }

        html.dark .auth-footer p {
            color: var(--gray-500);
        }

        .auth-footer a {
            color: var(--primary);
            font-weight: 600;
        }

        html.dark .auth-footer a {
            color: var(--primary-light);
        }

        .error-box {
            background: var(--danger-light);
            border: 1px solid var(--danger);
            color: #7f1d1d;
            padding: var(--spacing-lg);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-lg);
        }

        html.dark .error-box {
            background: #7f1d1d;
            color: #fca5a5;
            border-color: var(--danger);
        }

        .success-box {
            background: var(--success-light);
            border: 1px solid var(--success);
            color: #166534;
            padding: var(--spacing-lg);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-lg);
        }

        html.dark .success-box {
            background: #064e3b;
            color: #86efac;
            border-color: var(--success);
        }

        /* Smooth transitions */
        body, .auth-container, .auth-box {
            transition: background-color 300ms ease, color 300ms ease;
        }

        /* Form Styling */
        .form-input {
            width: 100%;
            padding: var(--spacing-md);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 1rem;
            background: white;
            color: var(--gray-900);
            transition: all var(--transition-fast);
            font-family: inherit;
        }

        .form-input::placeholder {
            color: var(--gray-400);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }

        html.dark .form-input {
            background: var(--gray-150);
            color: var(--gray-700);
            border-color: var(--gray-200);
        }

        html.dark .form-input::placeholder {
            color: var(--gray-400);
        }

        html.dark .form-input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
        }

        html.dark .form-label {
            color: var(--gray-700);
        }

        .form-hint {
            display: block;
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: var(--spacing-xs);
        }

        html.dark .form-hint {
            color: var(--gray-400);
        }

        .form-error {
            display: block;
            font-size: 0.875rem;
            color: var(--danger);
            margin-top: var(--spacing-xs);
        }

        html.dark .form-error {
            color: #fca5a5;
        }

        .auth-footer {
            text-align: center;
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--gray-200);
        }

        html.dark .auth-footer {
            border-top-color: var(--gray-200);
        }

        .auth-footer p {
            color: var(--gray-600);
        }

        html.dark .auth-footer p {
            color: var(--gray-400);
        }

        /* Role Selector Dark Mode */
        html.dark label[style*="border"][style*="flex"] {
            background-color: var(--gray-150) !important;
        }

        /* Button Styling for Register */
        .btn {
            padding: var(--spacing-md) var(--spacing-lg);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover, #0a5a56);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.3);
        }

        html.dark .btn-primary {
            background: var(--primary-light);
            color: #0f172a;
        }

        html.dark .btn-primary:hover {
            background: #22d3ee;
        }

        .btn-lg {
            padding: var(--spacing-lg) var(--spacing-xl);
            font-size: 1rem;
        }

        .btn-sm {
            padding: var(--spacing-sm) var(--spacing-md);
            font-size: 0.875rem;
        }

        /* Password Toggle */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper .form-input {
            padding-right: 2.5rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            padding: 0;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--gray-500);
            font-size: 1rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            transition: color var(--transition-fast), background var(--transition-fast);
        }

        .password-toggle:hover {
            color: var(--primary);
            background: rgba(15, 118, 110, 0.1);
        }

        .password-toggle:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        html.dark .password-toggle {
            color: var(--gray-400);
        }

        html.dark .password-toggle:hover {
            color: var(--primary-light);
            background: rgba(6, 182, 212, 0.15);
        }
    </style>
</head>
<body>
    <!-- Skip to main content link (WCAG 2.1 AA accessibility) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a href="<?php echo BASE_URL; ?>/public/index.php" class="navbar-logo">
            <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" height="64" width="auto">Partido Online Hub
        </a>
        <div style="flex: 1;"></div>
        <div class="navbar-actions">
            <!-- Theme Toggle -->
            <button 
                id="theme-toggle-btn"
                class="navbar-action-btn"
                aria-label="Toggle dark mode"
                title="Toggle dark mode (Ctrl+Shift+D)"
                style="font-size: 1.25rem; opacity: 0.7; transition: opacity var(--transition-fast);"
                onclick="window.themeSwitcher && window.themeSwitcher.toggleDarkMode()"
            >
                <span id="theme-icon" style="display: inline-block; transition: transform 0.3s ease;">☀️</span>
            </button>
        </div>
    </nav>

    <main id="main-content" role="main" tabindex="-1">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Join Partido</h1>
                <p>Create your account to get started today</p>
            </div>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="error-box" role="alert" aria-live="assertive">
                        <strong>❌ Registration Failed:</strong><br>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="success-box" role="status" aria-live="polite">
                        <strong>✓ Success!</strong><br>
                        <?php echo htmlspecialchars($success); ?><br><br>
                        <a href="<?php echo BASE_URL; ?>/public/login.php" class="btn btn-sm btn-primary" style="display: inline-block;">Go to Login</a>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form method="POST" action="" id="register-form" class="auth-form" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <!-- Full Name Field -->
                    <div class="form-group">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="full_name" placeholder="Your full name" required
                               class="form-input" value="<?php echo htmlspecialchars($full_name ?? ''); ?>"
                               autocomplete="name" aria-describedby="fullname-error">
                        <span id="fullname-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="you@example.com" required
                               class="form-input" value="<?php echo htmlspecialchars($email ?? ''); ?>"
                               autocomplete="email" aria-describedby="email-hint email-error">
                        <small id="email-hint" class="form-hint">We'll never share your email</small>
                        <span id="email-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Username Field -->
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" placeholder="Choose a username" required
                               class="form-input" value="<?php echo htmlspecialchars($username ?? ''); ?>"
                               autocomplete="username" aria-describedby="username-hint username-error">
                        <small id="username-hint" class="form-hint">3-50 characters, letters, numbers, underscores</small>
                        <span id="username-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" placeholder="Create a secure password" required
                                   class="form-input" autocomplete="new-password" aria-describedby="password-hint password-error">
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="password-toggle"
                                aria-label="Show password"
                                title="Toggle password visibility"
                                onclick="togglePasswordVisibility('password', 'password-toggle')"
                            >
                                👁️
                            </button>
                        </div>
                        <small id="password-hint" class="form-hint">Min 8 chars: uppercase, lowercase, numbers</small>
                        <span id="password-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirm" id="password_confirm" 
                                   placeholder="Re-enter your password" required class="form-input"
                                   autocomplete="new-password" aria-describedby="confirm-error">
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="password-confirm-toggle"
                                aria-label="Show confirm password"
                                title="Toggle password visibility"
                                onclick="togglePasswordVisibility('password_confirm', 'password-confirm-toggle')"
                            >
                                👁️
                            </button>
                        </div>
                        <span id="confirm-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Account Type Selection -->
                    <div class="form-group">
                        <fieldset>
                            <legend class="form-label">Account Type</legend>
                            <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-md);" role="group" aria-labelledby="role-legend">
                                <label style="flex: 1; display: flex; align-items: center; gap: var(--spacing-sm); padding: var(--spacing-md); border: 2px solid var(--gray-300); border-radius: var(--radius-md); cursor: pointer; transition: all var(--transition-fast); background: white;">
                                    <input type="radio" name="role" value="buyer" required
                                           <?php echo ($role === 'buyer' ? 'checked' : ''); ?>
                                           onchange="updateRoleDisplay()">
                                    <div>
                                        <span style="font-weight: 600; display: block;">👤 Buyer</span>
                                        <span style="font-size: 0.875rem; color: var(--gray-600); display: block;">Purchase products</span>
                                    </div>
                                </label>
                                <label style="flex: 1; display: flex; align-items: center; gap: var(--spacing-sm); padding: var(--spacing-md); border: 2px solid var(--gray-300); border-radius: var(--radius-md); cursor: pointer; transition: all var(--transition-fast); background: white;">
                                    <input type="radio" name="role" value="seller" required
                                           <?php echo ($role === 'seller' ? 'checked' : ''); ?>
                                           onchange="updateRoleDisplay()">
                                    <div>
                                        <span style="font-weight: 600; display: block;">🏪 Seller</span>
                                        <span style="font-size: 0.875rem; color: var(--gray-600); display: block;">Sell your products</span>
                                    </div>
                                </label>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Terms & Conditions -->
                    <div style="margin: var(--spacing-lg) 0;">
                        <label style="display: flex; align-items: flex-start; gap: var(--spacing-sm); cursor: pointer;">
                            <input type="checkbox" name="terms"  id="terms" required
                                   style="margin-top: 0.25rem; cursor: pointer;" aria-describedby="terms-error">
                        <span style="font-size: 0.875rem; color: var(--gray-600);">
                                I agree to the 
                                <a href="<?php echo BASE_URL; ?>/public/terms.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                    Terms of Service
                                </a> and 
                                <a href="<?php echo BASE_URL; ?>/public/privacy.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                    Privacy Policy
                                </a>
                            </span>
                        </label>
                        <span id="terms-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--spacing-md);">
                        Create Account
                    </button>
                </form>

                <!-- Divider -->
                <div style="margin: var(--spacing-xl) 0; display: flex; align-items: center; gap: var(--spacing-md); opacity: 0.5;">
                    <div style="flex: 1; height: 1px; background: var(--gray-300);"></div>
                    <span style="font-size: 0.875rem;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--gray-300);"></div>
                </div>

                <!-- Login Link -->
                <div class="auth-footer">
                    <p>Already have an account? 
                        <a href="<?php echo BASE_URL; ?>/public/login.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </main>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggleBtn = document.getElementById(toggleId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.innerHTML = '🙈';
                toggleBtn.setAttribute('aria-label', 'Hide password');
                toggleBtn.setAttribute('title', 'Hide password');
            } else {
                passwordInput.type = 'password';
                toggleBtn.innerHTML = '👁️';
                toggleBtn.setAttribute('aria-label', 'Show password');
                toggleBtn.setAttribute('title', 'Show password');
            }
        }

        // Role display update for visual feedback
        function updateRoleDisplay() {
            const buyerRadio = document.querySelector('input[name="role"][value="buyer"]');
            const sellerRadio = document.querySelector('input[name="role"][value="seller"]');
            
            if (buyerRadio && buyerRadio.parentElement) {
                buyerRadio.parentElement.style.borderColor = buyerRadio.checked ? 'var(--primary)' : 'var(--gray-300)';
                buyerRadio.parentElement.style.background = buyerRadio.checked ? 'rgba(15, 118, 110, 0.05)' : 'white';
            }
            
            if (sellerRadio && sellerRadio.parentElement) {
                sellerRadio.parentElement.style.borderColor = sellerRadio.checked ? 'var(--primary)' : 'var(--gray-300)';
                sellerRadio.parentElement.style.background = sellerRadio.checked ? 'rgba(15, 118, 110, 0.05)' : 'white';
            }
        }

        // Helper to show field error
        function showFieldError(field, message) {
            const errorEl = document.getElementById(field.id + '-error') || 
                           document.getElementById(field.name + '-error');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.hidden = false;
            }
            field.setAttribute('aria-invalid', 'true');
            field.style.borderColor = 'var(--danger)';
        }

        // Helper to clear field error
        function clearFieldError(field) {
            const errorEl = document.getElementById(field.id + '-error') || 
                           document.getElementById(field.name + '-error');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.hidden = true;
            }
            field.setAttribute('aria-invalid', 'false');
            field.style.borderColor = '';
        }

        // Form validation
        document.getElementById('register-form').addEventListener('submit', function(e) {
            const form = this;
            let isValid = true;

            // Clear all previous errors
            form.querySelectorAll('input, fieldset').forEach(field => {
                clearFieldError(field);
            });

            // Validate username
            const username = form.querySelector('#username');
            if (!username.value || username.value.length < 3 || username.value.length > 50) {
                showFieldError(username, 'Username must be between 3 and 50 characters');
                isValid = false;
            }

            // Validate full name
            const fullName = form.querySelector('#full_name');
            if (!fullName.value || fullName.value.length < 2 || fullName.value.length > 100) {
                showFieldError(fullName, 'Full name must be between 2 and 100 characters');
                isValid = false;
            }

            // Validate email
            const email = form.querySelector('#email');
            if (!email.value || !email.value.includes('@') || !email.value.includes('.')) {
                showFieldError(email, 'Please enter a valid email address');
                isValid = false;
            }

            // Validate password strength
            const password = form.querySelector('#password');
            const hasUpper = /[A-Z]/.test(password.value);
            const hasLower = /[a-z]/.test(password.value);
            const hasNumbers = /[0-9]/.test(password.value);
            if (!password.value || password.value.length < 8 || !hasUpper || !hasLower || !hasNumbers) {
                showFieldError(password, 'Min 8 chars with uppercase, lowercase, and numbers');
                isValid = false;
            }

            // Validate password confirmation
            const confirmPassword = form.querySelector('#password_confirm');
            if (confirmPassword.value !== password.value) {
                showFieldError(confirmPassword, 'Passwords do not match');
                isValid = false;
            }

            // Validate terms acceptance
            const terms = form.querySelector('#terms');
            if (!terms.checked) {
                showFieldError(terms, 'You must agree to the terms of service');
                isValid = false;
            }

            // Validate role selection
            const roleSelected = form.querySelector('input[name="role"]:checked');
            if (!roleSelected) {
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return;
            }
        });
        
        // Event listeners for role selection
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', updateRoleDisplay);
        });
        
        // Initialize on page load
        updateRoleDisplay();

        // Keyboard shortcut for theme toggle (Ctrl+Shift+D)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                if (window.themeSwitcher) {
                    window.themeSwitcher.toggleTheme();
                }
            }
        });
    </script>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
