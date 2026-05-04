<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Help Center & FAQ - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Help Center - <?php echo APP_NAME; ?></title>
    
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
        .faq-item {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-md);
            overflow: hidden;
            transition: all var(--transition-base);
        }
        html.dark .faq-item {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .faq-question {
            padding: var(--spacing-lg);
            font-weight: 600;
            color: var(--gray-900);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background var(--transition-fast);
        }
        .faq-question:hover {
            background: var(--gray-50);
        }
        html.dark .faq-question {
            color: var(--gray-700);
        }
        html.dark .faq-question:hover {
            background: var(--gray-150);
        }
        .faq-answer {
            padding: 0 var(--spacing-lg) var(--spacing-lg);
            color: var(--gray-600);
            line-height: 1.7;
            display: none;
        }
        html.dark .faq-answer {
            color: var(--gray-400);
        }
        .faq-answer.active {
            display: block;
        }
        .faq-toggle {
            font-size: 1.25rem;
            transition: transform 0.2s;
        }
        .faq-toggle.rotated {
            transform: rotate(180deg);
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
                <h1>Help Center & FAQ</h1>
                <p>Find answers to common questions about using Partido Market Hub</p>
            </div>

            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How do I create an account?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        Click "Start Buying" or "Start Selling" on the homepage, then fill out the registration form with your name, email, username, and password. Choose whether you want to register as a buyer or seller.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How do I buy a product?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        Browse the Market Hub, click on a product to view details, then click "Initiate Deal" to start the purchasing process. You can message the seller directly to arrange pickup or delivery.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How do I sell my products?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        Register as a seller, then go to your Seller Dashboard. Click "Add Product" to list your items with photos, descriptions, and pricing. Your products will appear in the Market Hub for buyers to discover.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Is my payment information secure?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        Yes! Partido Market Hub does not store payment information. All transactions are arranged directly between buyers and sellers through secure messaging. We recommend using trusted payment methods like GCash or cash on delivery.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How do ratings work?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        After a deal is completed and both parties confirm, buyers can rate sellers on a 1-5 star scale and leave a review. These ratings help build trust in our community and help other buyers make informed decisions.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>What if I have a problem with a seller?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        You can report sellers through our reporting system. Go to the seller's profile or product page and click "Report". Our team will review the issue and take appropriate action. You can also contact our support team directly.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How do I change my password?</span>
                        <span class="faq-toggle">▼</span>
                    </div>
                    <div class="faq-answer">
                        Go to your Dashboard, click "Edit Profile" or "Account Settings", and look for the "Change Password" option. You'll need to enter your current password and then your new password twice to confirm.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>

    <script>
        function toggleFaq(element) {
            const answer = element.nextElementSibling;
            const toggle = element.querySelector('.faq-toggle');
            answer.classList.toggle('active');
            toggle.classList.toggle('rotated');
        }
    </script>
</body>
</html>

