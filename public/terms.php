<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Terms of Service - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Terms of Service - <?php echo APP_NAME; ?></title>
    
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
            max-width: 900px;
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
        .page-header p {
            color: var(--gray-600);
            font-size: 1.125rem;
        }
        html.dark .page-header p {
            color: var(--gray-400);
        }
        .terms-section {
            margin-bottom: var(--spacing-3xl);
        }
        .terms-section h2 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        html.dark .terms-section h2 {
            color: var(--gray-700);
            border-bottom-color: var(--primary-light);
        }
        .terms-section h3 {
            color: var(--gray-800);
            margin: var(--spacing-xl) 0 var(--spacing-md);
        }
        html.dark .terms-section h3 {
            color: var(--gray-600);
        }
        .terms-section p {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: var(--spacing-md);
        }
        html.dark .terms-section p {
            color: var(--gray-400);
        }
        .terms-section ul {
            color: var(--gray-600);
            line-height: 1.8;
            margin-left: var(--spacing-xl);
            margin-bottom: var(--spacing-md);
        }
        html.dark .terms-section ul {
            color: var(--gray-400);
        }
        .last-updated {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            text-align: center;
            margin-bottom: var(--spacing-2xl);
            color: var(--gray-600);
        }
        html.dark .last-updated {
            background: var(--gray-100);
            border-color: var(--gray-200);
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
                <h1>Terms of Service</h1>
                <p>Please read these terms carefully before using Partido Market Hub</p>
            </div>

            <div class="last-updated">
                <strong>Last Updated:</strong> January 2026
            </div>

            <div class="terms-section">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using Partido Market Hub, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our platform. These terms apply to all visitors, users, buyers, and sellers.</p>
            </div>

            <div class="terms-section">
                <h2>2. Account Registration</h2>
                <p>To use certain features of our platform, you must register for an account. You agree to:</p>
                <ul>
                    <li>Provide accurate, current, and complete information during registration</li>
                    <li>Maintain and update your information to keep it accurate</li>
                    <li>Maintain the security of your password and account</li>
                    <li>Notify us immediately of any unauthorized access or security breach</li>
                    <li>Be responsible for all activities that occur under your account</li>
                </ul>
                <p>We reserve the right to suspend or terminate accounts that provide false information or violate these terms.</p>
            </div>

            <div class="terms-section">
                <h2>3. User Conduct</h2>
                <p>You agree not to use our platform to:</p>
                <ul>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Infringe on the rights of others</li>
                    <li>Post false, misleading, or fraudulent content</li>
                    <li>Harass, abuse, or harm other users</li>
                    <li>Distribute spam, malware, or malicious content</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                    <li>Interfere with the proper functioning of the platform</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2>4. Seller Responsibilities</h2>
                <p>Sellers using our platform agree to:</p>
                <ul>
                    <li>Provide accurate product descriptions, pricing, and images</li>
                    <li>Maintain adequate stock levels for listed products</li>
                    <li>Respond to buyer inquiries and deal requests promptly</li>
                    <li>Deliver products as described and in a timely manner</li>
                    <li>Comply with all applicable laws regarding the sale of goods</li>
                    <li>Not list prohibited items including illegal goods, counterfeit products, or hazardous materials</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2>5. Buyer Responsibilities</h2>
                <p>Buyers using our platform agree to:</p>
                <ul>
                    <li>Provide accurate delivery and contact information</li>
                    <li>Honor confirmed deals and payment arrangements</li>
                    <li>Communicate respectfully with sellers</li>
                    <li>Report issues through proper channels</li>
                    <li>Not abuse the rating and review system</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2>6. Transactions and Payments</h2>
                <p>Partido Market Hub facilitates connections between buyers and sellers. We are not a party to transactions. Users agree that:</p>
                <ul>
                    <li>Payment terms are arranged directly between buyer and seller</li>
                    <li>We are not responsible for payment disputes or transaction issues</li>
                    <li>Sellers are responsible for collecting applicable taxes</li>
                    <li>Buyers should verify product details before confirming deals</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2>7. Content and Intellectual Property</h2>
                <p>Users retain ownership of content they post but grant us a license to use, display, and distribute that content on our platform. You represent that you have the right to post any content you upload and that it does not violate third-party rights.</p>
            </div>

            <div class="terms-section">
                <h2>8. Termination</h2>
                <p>We reserve the right to suspend or terminate your account at any time for violations of these terms or for any other reason at our discretion. You may also delete your account at any time by contacting support.</p>
            </div>

            <div class="terms-section">
                <h2>9. Disclaimer of Warranties</h2>
                <p>Our platform is provided "as is" without warranties of any kind. We do not guarantee that the platform will be uninterrupted, secure, or error-free. We are not responsible for the quality, safety, or legality of products listed by sellers.</p>
            </div>

            <div class="terms-section">
                <h2>10. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, Partido Market Hub shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the platform.</p>
            </div>

            <div class="terms-section">
                <h2>11. Changes to Terms</h2>
                <p>We may modify these terms at any time. We will notify users of significant changes. Continued use of the platform after changes constitutes acceptance of the updated terms.</p>
            </div>

            <div class="terms-section">
                <h2>12. Contact Information</h2>
                <p>For questions about these Terms of Service, please contact us through our <a href="<?php echo BASE_URL; ?>/public/contact.php" style="color: var(--primary);">Contact Page</a>.</p>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

