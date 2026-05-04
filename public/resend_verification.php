<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// If already logged in, redirect to dashboard
if (Auth::isAuthenticated()) {
    secureRedirect(Auth::getDashboardUrl($_SESSION['role']));
}

$error = '';
$success = '';
$email = $_GET['email'] ?? '';

// Process resend verification email request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email address is required.';
    } else if (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT user_id, email_verified FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                // Don't reveal if email exists for security
                $success = 'If that email is registered, a verification link has been sent.';
            } else if ($user['email_verified'] == 1) {
                $error = 'This email address is already verified. You can <a href="' . BASE_URL . '/public/login.php">log in</a> directly.';
            } else {
                // Generate new verification token
                $verification_token = bin2hex(random_bytes(32));
                
                // Update verification token in database
                $update_stmt = $pdo->prepare("UPDATE users SET verification_token = :token, verification_sent_at = NOW() WHERE user_id = :user_id");
                $update_stmt->execute([
                    ':token' => $verification_token,
                    ':user_id' => $user['user_id']
                ]);
                
                // In production, send email here
                // For now, show success message
                $success = 'Verification email resent! Please check your inbox (or spam folder) and click the verification link.';
                
                logSecurityEvent('verification_resent', 'Verification email resent', $user['user_id']);
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again later.';
            logSecurityEvent('verification_resend_error', $e->getMessage());
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
    <meta name="description" content="Resend verification email - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Resend Verification - <?php echo APP_NAME; ?></title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
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
            margin-bottom: var(--spacing-md);
            color: var(--gray-900);
        }
        html.dark .auth-header h1 {
            color: var(--gray-700);
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
        .btn {
            width: 100%;
            padding: var(--spacing-md) var(--spacing-lg);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            background: var(--primary);
            color: white;
        }
        .btn:hover {
            background: var(--primary-hover, #0a5a56);
        }
        html.dark .btn {
            background: var(--primary-light);
            color: #0f172a;
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
        .auth-footer {
            text-align: center;
            margin-top: var(--spacing-xl);
        }
        .auth-footer a {
            color: var(--primary);
            font-weight: 600;
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

    <main id="main-content" role="main">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-header">
                    <h1>Resend Verification Email</h1>
                    <p>Enter your email address to receive a new verification link.</p>
                </div>

                <?php if ($error): ?>
                    <div class="error-box" role="alert"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-box" role="status"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" required
                               class="form-input" 
                               value="<?php echo htmlspecialchars($email); ?>"
                               placeholder="you@example.com">
                    </div>

                    <button type="submit" class="btn">Send Verification Link</button>
                </form>

                <div class="auth-footer">
                    <p><a href="<?php echo BASE_URL; ?>/public/login.php">Back to Login</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
