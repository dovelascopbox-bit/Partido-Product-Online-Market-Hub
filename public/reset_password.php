<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// If already logged in, redirect to dashboard
if (Auth::isAuthenticated()) {
    secureRedirect(Auth::getDashboardUrl($_SESSION['role']));
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

// Validate token presence
if (empty($token)) {
    $error = 'Invalid reset link. Please request a new password reset.';
}

// Process reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token validation failed. Please try again.';
    } else {
        $token = sanitizeInput($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        if (empty($password)) {
            $error = 'New password is required.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $error = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $error = 'Password must contain at least one lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $error = 'Password must contain at least one number.';
        } elseif ($password !== $password_confirm) {
            $error = 'Passwords do not match.';
        } else {
            // Reset password
            $auth = new Auth($pdo);
            $result = $auth->resetPassword($token, $password);
            
            if ($result['success']) {
                $success = $result['message'];
                // Clear token so form doesn't show again
                $token = '';
            } else {
                $error = $result['message'];
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
    <meta name="description" content="Reset your password - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>New Password - <?php echo APP_NAME; ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/helpers.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('partido_theme_preference');
                const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemDark ? 'dark' : 'light');
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

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
        }
        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }
        .auth-header h1 {
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }
        html.dark .auth-header h1 {
            color: var(--primary-light);
        }
        .auth-header p {
            color: var(--gray-600);
        }
        html.dark .auth-header p {
            color: var(--gray-400);
        }
        .form-group {
            margin-bottom: var(--spacing-lg);
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
        .form-input {
            width: 100%;
            padding: var(--spacing-md);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 1rem;
            background: white;
            color: var(--gray-900);
            transition: all var(--transition-fast);
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
        .form-hint {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: var(--spacing-xs);
        }
        html.dark .form-hint {
            color: var(--gray-400);
        }
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: var(--spacing-md) var(--spacing-xl);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all var(--transition-fast);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        html.dark .btn-primary {
            background: var(--primary-light);
            color: #0f172a;
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
        .auth-footer a {
            color: var(--primary);
            font-weight: 600;
        }
        html.dark .auth-footer a {
            color: var(--primary-light);
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a href="<?php echo BASE_URL; ?>/public/index.php" class="navbar-logo">
            <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" height="64">Partido Online Hub
        </a>
    </nav>

    <main id="main-content" role="main" tabindex="-1">
        <div class="auth-container">
            <div class="auth-box">
                <?php if ($success): ?>
                    <div class="auth-header">
                        <h1>Password Reset Complete!</h1>
                        <p>Your password has been reset successfully.</p>
                    </div>
                    <div class="success-box" role="status"><?php echo htmlspecialchars($success); ?></div>
                    <div class="auth-footer">
                        <p>
                            <a href="<?php echo BASE_URL; ?>/public/login.php" class="btn-primary" style="display: inline-block; width: auto; padding: var(--spacing-md) var(--spacing-xl);">Sign In</a>
                        </p>
                    </div>
                <?php elseif ($error && empty($token)): ?>
                    <div class="auth-header">
                        <h1>Invalid Link</h1>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <div class="auth-footer">
                        <p>
                            <a href="<?php echo BASE_URL; ?>/public/forgot_password.php">Request New Reset</a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="auth-header">
                        <h1>Create New Password</h1>
                        <p>Enter your new password below.</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="error-box" role="alert"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="auth-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="form-group">
                            <label for="password" class="form-label">New Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input"
                                placeholder="Enter new password"
                                required
                                autocomplete="new-password"
                            >
                            <small class="form-hint">Min 8 characters with uppercase, lowercase, and numbers</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input 
                                type="password" 
                                id="password_confirm" 
                                name="password_confirm" 
                                class="form-input"
                                placeholder="Confirm new password"
                                required
                                autocomplete="new-password"
                            >
                        </div>

                        <button type="submit" class="btn-primary">
                            Reset Password
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p><a href="<?php echo BASE_URL; ?>/public/login.php">Back to Sign In</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
