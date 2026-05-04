<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Privacy Policy - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Privacy Policy - <?php echo APP_NAME; ?></title>
    
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
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        .page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--spacing-3xl) var(--spacing-lg);
        }
        .page-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
        }
        .page-header h1 {
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }
        html.dark .page-header h1 {
            color: var(--primary-light);
        }
        .content-section {
            margin-bottom: var(--spacing-3xl);
        }
        .content-section h2 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        html.dark .content-section h2 {
            color: var(--gray-700);
            border-bottom-color: var(--primary-light);
        }
        .content-section p, .content-section li {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: var(--spacing-sm);
        }
        html.dark .content-section p, html.dark .content-section li {
            color: var(--gray-400);
        }
        .content-section ul {
            margin-left: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
        }
        .last-updated {
            text-align: center;
            color: var(--gray-500);
            font-style: italic;
            margin-bottom: var(--spacing-2xl);
        }
        html.dark .last-updated {
            color: var(--gray-400);
        }
        body, main {
            transition: background-color 300ms ease, color 300ms ease;
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/navbar.php'; ?>

    <main id="main-content" role="main" tabindex="-1">
        <div class="page-container">
            <div class="page-header">
                <h1>Privacy Policy</h1>
                <p class="last-updated">Last Updated: January 2025</p>
            </div>

            <div class="content-section">
                <h2>Introduction</h2>
                <p>Partido Market Hub ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.</p>
                <p>By using Partido Market Hub, you agree to the collection and use of information in accordance with this policy.</p>
            </div>

            <div class="content-section">
                <h2>Information We Collect</h2>
                <p>We collect the following types of information:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, phone number, and address when you register</li>
                    <li><strong>Account Information:</strong> Username, password, and account preferences</li>
                    <li><strong>Transaction Information:</strong> Deal history, ratings, and messages between users</li>
                    <li><strong>Usage Data:</strong> How you interact with our platform, including pages visited and features used</li>
                    <li><strong>Device Information:</strong> IP address, browser type, and operating system</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>How We Use Your Information</h2>
                <p>We use your information to:</p>
                <ul>
                    <li>Provide and maintain our services</li>
                    <li>Process transactions and manage deals</li>
                    <li>Send notifications about your account and deals</li>
                    <li>Improve our platform and user experience</li>
                    <li>Prevent fraud and ensure platform security</li>
                    <li>Comply with legal obligations</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Information Sharing</h2>
                <p>We do not sell your personal information. We may share information with:</p>
                <ul>
                    <li>Other users as necessary to facilitate deals (e.g., sharing contact details with your deal partner)</li>
                    <li>Service providers who assist in operating our platform</li>
                    <li>Law enforcement when required by law</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Data Security</h2>
                <p>We implement appropriate security measures to protect your personal information, including encryption, secure servers, and regular security assessments. However, no method of transmission over the internet is 100% secure.</p>
            </div>

            <div class="content-section">
                <h2>Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal information</li>
                    <li>Correct inaccurate information</li>
                    <li>Request deletion of your account and data</li>
                    <li>Opt out of marketing communications</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us at <a href="<?php echo BASE_URL; ?>/public/contact.php">our contact page</a>.</p>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

