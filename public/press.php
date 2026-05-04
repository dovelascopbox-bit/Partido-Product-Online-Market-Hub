<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Press - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Press - <?php echo APP_NAME; ?></title>
    
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
        .press-section {
            margin-bottom: var(--spacing-3xl);
        }
        .press-section h2 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        html.dark .press-section h2 {
            color: var(--gray-700);
            border-bottom-color: var(--primary-light);
        }
        .press-section p {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: var(--spacing-md);
        }
        html.dark .press-section p {
            color: var(--gray-400);
        }
        .press-release {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            transition: all var(--transition-base);
        }
        .press-release:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        html.dark .press-release {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .press-release-date {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin-bottom: var(--spacing-sm);
        }
        html.dark .press-release-date {
            color: var(--gray-400);
        }
        .press-release h3 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-md);
            font-size: 1.125rem;
        }
        html.dark .press-release h3 {
            color: var(--gray-700);
        }
        .press-release p {
            color: var(--gray-600);
            line-height: 1.7;
            margin: 0;
        }
        html.dark .press-release p {
            color: var(--gray-400);
        }
        .contact-box {
            background: rgba(15, 118, 110, 0.05);
            border: 1px solid var(--primary);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
        }
        html.dark .contact-box {
            background: rgba(6, 182, 212, 0.05);
        }
        .contact-box h3 {
            color: var(--primary);
            margin-bottom: var(--spacing-md);
        }
        html.dark .contact-box h3 {
            color: var(--primary-light);
        }
        .contact-box p {
            color: var(--gray-600);
            margin-bottom: var(--spacing-md);
        }
        html.dark .contact-box p {
            color: var(--gray-400);
        }
        .contact-box a {
            color: var(--primary);
            font-weight: 600;
        }
        html.dark .contact-box a {
            color: var(--primary-light);
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
                <h1>Press</h1>
                <p>News and updates from Partido Market Hub</p>
            </div>

            <div class="press-section">
                <h2>Press Releases</h2>
                
                <div class="press-release">
                    <div class="press-release-date">January 20, 2026</div>
                    <h3>Partido Market Hub Officially Launches</h3>
                    <p>Partido Market Hub is now live, connecting local sellers with buyers across the Partido region. The platform aims to empower small businesses and provide a trusted marketplace for authentic local products.</p>
                </div>
                
                <div class="press-release">
                    <div class="press-release-date">January 15, 2026</div>
                    <h3>Platform Reaches 100 Registered Sellers</h3>
                    <p>Within the first week of launch, Partido Market Hub has onboarded over 100 local sellers, offering a diverse range of products from fresh produce to handmade crafts.</p>
                </div>
            </div>

            <div class="press-section">
                <h2>Media Resources</h2>
                <p>Journalists and media professionals can access our brand assets, high-resolution logos, and executive bios. For media inquiries, interview requests, or additional resources, please reach out to our communications team.</p>
            </div>

            <div class="contact-box">
                <h3>Media Contact</h3>
                <p>For press inquiries, please contact us through our <a href="<?php echo BASE_URL; ?>/public/contact.php">Contact Page</a> and select "Press/Media" as your subject.</p>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

