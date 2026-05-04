<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// If already logged in, redirect to dashboard
requireGuest();

$error = '';
$email = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token validation failed. Please try again.';
        logSecurityEvent('csrf_failure', 'Login form CSRF token mismatch');
    } else {
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Check rate limiting
        if (!checkRateLimit($email)) {
            $error = 'Too many login attempts. Please try again later.';
            logSecurityEvent('rate_limit_exceeded', 'Too many login attempts', null, $email);
        } else if (empty($email) || empty($password)) {
            $error = 'Email and password are required.';
            incrementLoginAttempts($email);
} else {
            // Attempt login
            $auth = new Auth($pdo);
            $result = $auth->login($email, $password);

            if ($result['success']) {
                // Reset attempts on successful login
                resetLoginAttempts($email);

                // Set session variables
                $_SESSION['user_id'] = $result['user']['user_id'];
                $_SESSION['username'] = $result['user']['username'];
                $_SESSION['email'] = $result['user']['email'];
                $_SESSION['role'] = $result['user']['role'];
                $_SESSION['full_name'] = $result['user']['full_name'];
                $_SESSION['last_activity'] = time();

                // Log security event
                logSecurityEvent('login_success', 'User logged in successfully', $result['user']['user_id']);

                // Redirect to role-based dashboard
                secureRedirect(Auth::getDashboardUrl($result['user']['role']));
            } else {
                // Check if email verification is needed
                if (isset($result['needs_verification']) && $result['needs_verification']) {
                    if (SKIP_EMAIL_VERIFICATION) {
                        // Show dev mode token lookup
                        $error = 'Email verification required. Get token: <a href="' . BASE_URL . '/public/get_token.php?email=' . urlencode($email) . '" target="_blank">Get Verification Link</a>';
                    } else {
                        $error = 'Your email address has not been verified yet. <a href="' . BASE_URL . '/public/resend_verification.php?email=' . urlencode($email) . '">Resend verification email</a>.';
                    }
                } else {
                    $error = $result['message'];
                }
                incrementLoginAttempts($email);
                logSecurityEvent('login_failed', 'Login attempt failed - ' . $result['message'], null, $email);
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
    <meta name="description" content="Login to <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Login - <?php echo APP_NAME; ?></title>
    
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

        /* Button Styling */
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
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue</p>
            </div>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="error-box" role="alert" aria-live="assertive">
                        <strong>❌ Login Failed:</strong><br>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="" id="login-form" class="auth-form" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            aria-required="true"
                            value="<?php echo htmlspecialchars($email); ?>"
                            class="form-input"
                            placeholder="you@example.com"
                            autocomplete="email"
                            aria-describedby="email-hint email-error"
                        >
                        <small id="email-hint" class="form-hint">We'll never share your email address.</small>
                        <span id="email-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                aria-required="true"
                                class="form-input"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                aria-describedby="password-error"
                            >
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="password-toggle"
                                aria-label="Show password"
                                title="Toggle password visibility"
                                onclick="togglePasswordVisibility()"
                            >
                                👁️
                            </button>
                        </div>
                        <span id="password-error" class="form-error" role="alert" hidden></span>
                    </div>

                    <!-- Forgot Password Link -->
                    <div style="text-align: right;">
<a href="<?php echo BASE_URL; ?>/public/forgot_password.php" style="font-size: 0.875rem; color: var(--primary); text-decoration: none; font-weight: 500;">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="btn btn-primary btn-lg"
                        style="width: 100%; margin-top: var(--spacing-md);"
                        aria-label="Sign in to your account"
                    >
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div style="margin: var(--spacing-xl) 0; display: flex; align-items: center; gap: var(--spacing-md); opacity: 0.5;" aria-hidden="true">
                    <div style="flex: 1; height: 1px; background: var(--gray-300);"></div>
                    <span style="font-size: 0.875rem;">or</span>
                    <div style="flex: 1; height: 1px; background: var(--gray-300);"></div>
                </div>

                <!-- Register Link -->
                <div class="auth-footer">
                    <p>Don't have an account? 
                        <a href="<?php echo BASE_URL; ?>/public/register.php" style="color: var(--primary); font-weight: 600; text-decoration: none;">Create one</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
    </main>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('password-toggle');
            
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

        // Keyboard shortcut for theme toggle (Ctrl+Shift+D)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                if (window.themeSwitcher) {
                    window.themeSwitcher.toggleDarkMode();
                }
            }
        });
    </script>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
