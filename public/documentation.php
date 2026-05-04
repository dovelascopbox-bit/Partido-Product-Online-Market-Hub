<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Documentation - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Documentation - <?php echo APP_NAME; ?></title>
    
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
        .doc-section {
            margin-bottom: var(--spacing-3xl);
        }
        .doc-section h2 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        html.dark .doc-section h2 {
            color: var(--gray-700);
            border-bottom-color: var(--primary-light);
        }
        .doc-section h3 {
            color: var(--gray-800);
            margin: var(--spacing-lg) 0 var(--spacing-sm);
        }
        html.dark .doc-section h3 {
            color: var(--gray-500);
        }
        .doc-section p, .doc-section li {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: var(--spacing-sm);
        }
        html.dark .doc-section p, html.dark .doc-section li {
            color: var(--gray-400);
        }
        .doc-section ol, .doc-section ul {
            margin-left: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
        }
        .code-block {
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            font-family: monospace;
            font-size: 0.9rem;
            color: var(--gray-800);
            overflow-x: auto;
        }
        html.dark .code-block {
            background: var(--gray-150);
            border-color: var(--gray-200);
            color: var(--gray-300);
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
                <h1>User Documentation</h1>
                <p>Everything you need to know about using Partido Market Hub</p>
            </div>

            <div class="doc-section">
                <h2>Getting Started</h2>
                <h3>1. Creating Your Account</h3>
                <p>To get started with Partido Market Hub, you'll need to create an account:</p>
                <ol>
                    <li>Visit the homepage and click "Start Buying" or "Start Selling"</li>
                    <li>Fill in your full name, email address, and choose a username</li>
                    <li>Create a secure password (minimum 8 characters with uppercase, lowercase, and numbers)</li>
                    <li>Select your account type: Buyer or Seller</li>
                    <li>Agree to the Terms of Service and Privacy Policy</li>
                    <li>Click "Create Account" to complete registration</li>
                </ol>

                <h3>2. Logging In</h3>
                <p>After registration, you can log in using your email and password. Click "Sign In" from the homepage or navigation menu.</p>
            </div>

            <div class="doc-section">
                <h2>For Buyers</h2>
                <h3>Browsing Products</h3>
                <p>The Market Hub is where you'll find all available products from local sellers. You can:</p>
                <ul>
                    <li>Use the search bar to find specific products</li>
                    <li>Sort by newest, oldest, or price range</li>
                    <li>Click on any product to view detailed information</li>
                </ul>

                <h3>Making a Purchase</h3>
                <ol>
                    <li>Find a product you want to buy</li>
                    <li>Click "View Details" to see more information about the seller</li>
                    <li>Click "Initiate Deal" to start the purchase process</li>
                    <li>Message the seller to arrange pickup or delivery</li>
                    <li>Confirm the deal once you receive your product</li>
                    <li>Rate your experience to help other buyers</li>
                </ol>
            </div>

            <div class="doc-section">
                <h2>For Sellers</h2>
                <h3>Setting Up Your Shop</h3>
                <p>After registering as a seller:</p>
                <ol>
                    <li>Go to your Seller Dashboard</li>
                    <li>Update your shop information (name, description, location)</li>
                    <li>Add your first product with photos and details</li>
                    <li>Start receiving orders from buyers</li>
                </ol>

                <h3>Managing Products</h3>
                <p>From your dashboard you can:</p>
                <ul>
                    <li>Add new products with images, descriptions, and pricing</li>
                    <li>Edit existing product details</li>
                    <li>Mark products as sold out or restocked</li>
                    <li>Delete products you no longer offer</li>
                </ul>

                <h3>Managing Deals</h3>
                <p>When a buyer initiates a deal:</p>
                <ol>
                    <li>You'll receive a notification</li>
                    <li>Message the buyer to confirm details</li>
                    <li>Confirm the deal in your dashboard</li>
                    <li>Arrange pickup or delivery</li>
                    <li>Wait for the buyer to confirm receipt</li>
                </ol>
            </div>

            <div class="doc-section">
                <h2>Account Management</h2>
                <h3>Updating Your Profile</h3>
                <p>You can update your profile information at any time from your dashboard. This includes your name, contact details, and address.</p>

                <h3>Security Tips</h3>
                <ul>
                    <li>Use a strong, unique password</li>
                    <li>Never share your login credentials</li>
                    <li>Log out when using shared computers</li>
                    <li>Report suspicious activity immediately</li>
                </ul>
            </div>

            <div class="doc-section">
                <h2>Accessibility Features</h2>
                <p>Partido Market Hub is designed to be accessible to everyone:</p>
                <ul>
                    <li><strong>Dark Mode:</strong> Toggle between light and dark themes</li>
                    <li><strong>Keyboard Navigation:</strong> Navigate using Tab, Enter, and Escape keys</li>
                    <li><strong>Screen Reader Support:</strong> ARIA labels and semantic HTML</li>
                    <li><strong>High Contrast Mode:</strong> Enhanced visibility option</li>
                    <li><strong>Font Size Adjustment:</strong> Increase or decrease text size</li>
                </ul>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

