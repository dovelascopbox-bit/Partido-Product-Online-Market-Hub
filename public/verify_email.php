<?php
/**
 * Email Verification Page
 * Handles email verification from verification email link
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// If already logged in, redirect to dashboard
if (Auth::isAuthenticated()) {
    secureRedirect(Auth::getDashboardUrl($_SESSION['role']));
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

// Process verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (!empty($token) && !empty($email))) {
    // Clean email input
    $email = sanitizeInput($email);
    
    // Verify email
    $auth = new Auth($pdo);
    $result = $auth->verifyEmail($token, $email);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verify your email - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Verify Email - <?php echo APP_NAME; ?></title>
    
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
        .verify-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
        }
        .verify-box {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
            text-align: center;
        }
        html.dark .verify-box {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .verify-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-lg);
        }
        .verify-title {
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }
        html.dark .verify-title {
            color: var(--primary-light);
        }
        .verify-message {
            color: var(--gray-600);
            margin-bottom: var(--spacing-xl);
        }
        html.dark .verify-message {
            color: var(--gray-400);
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
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: var(--spacing-md) var(--spacing-xl);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: var(--spacing-md);
        }
        .btn-primary:hover {
            background: var(--primary-hover);
        }
        html.dark .btn-primary {
            background: var(--primary-light);
            color: #0f172a;
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
        <div class="verify-container">
            <div class="verify-box">
                <?php if ($success): ?>
                    <div class="verify-icon">✅</div>
                    <h1 class="verify-title">Email Verified!</h1>
                    <p class="success-box"><?php echo htmlspecialchars($success); ?></p>
                    <p class="verify-message">You can now sign in to your account.</p>
                    <a href="<?php echo BASE_URL; ?>/public/login.php" class="btn-primary">Sign In</a>
                    
                <?php elseif ($error): ?>
                    <div class="verify-icon">❌</div>
                    <h1 class="verify-title">Verification Failed</h1>
                    <p class="error-box"><?php echo htmlspecialchars($error); ?></p>
                    <p class="verify-message">The verification link may be invalid or expired.</p>
                    <a href="<?php echo BASE_URL; ?>/public/register.php" class="btn-primary">Register Again</a>
                    
                <?php else: ?>
                    <div class="verify-icon">📧</div>
                    <h1 class="verify-title">Verify Your Email</h1>
                    <p class="verify-message">Please check your email for the verification link.</p>
                    <p class="verify-message">Click the link in the email to verify your account.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
