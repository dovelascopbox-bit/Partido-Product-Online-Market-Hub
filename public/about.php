<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="About <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>About Us - <?php echo APP_NAME; ?></title>
    
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
        .page-header p {
            color: var(--gray-600);
            font-size: 1.125rem;
        }
        html.dark .page-header p {
            color: var(--gray-400);
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
        .content-section p {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: var(--spacing-md);
        }
        html.dark .content-section p {
            color: var(--gray-400);
        }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: var(--spacing-xl);
            margin-top: var(--spacing-xl);
        }
        .value-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            transition: all var(--transition-base);
        }
        .value-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        html.dark .value-card {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .value-icon {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
        }
        .value-card h3 {
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }
        html.dark .value-card h3 {
            color: var(--primary-light);
        }
        .value-card p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }
        html.dark .value-card p {
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
                <h1>About Partido Market Hub</h1>
                <p>Empowering local businesses and connecting communities across Partido</p>
            </div>

            <div class="content-section">
                <h2>Who We Are</h2>
                <p>Partido Market Hub is a dedicated online marketplace built specifically for the Partido region. We believe in the power of local commerce and the importance of supporting small businesses within our community.</p>
                <p>Founded with a vision to bridge the gap between local sellers and buyers, our platform provides a secure, easy-to-use environment where entrepreneurs can grow their businesses and customers can discover authentic local products.</p>
            </div>

            <div class="content-section">
                <h2>Our Mission</h2>
                <p>Our mission is to foster economic growth in Partido by providing accessible digital tools for local businesses. We aim to:</p>
                <ul style="color: var(--gray-600); line-height: 1.8; margin-left: var(--spacing-lg);">
                    <li>Empower local sellers with a professional online presence</li>
                    <li>Connect buyers with authentic, high-quality local products</li>
                    <li>Build a trusted community marketplace</li>
                    <li>Support sustainable local economic development</li>
                </ul>
            </div>

            <div class="content-section">
                <h2>Our Values</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">🤝</div>
                        <h3>Community First</h3>
                        <p>We prioritize the needs of our local community above all else.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">🔒</div>
                        <h3>Trust & Safety</h3>
                        <p>Secure transactions and verified sellers for peace of mind.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">🌱</div>
                        <h3>Sustainability</h3>
                        <p>Supporting local reduces carbon footprint and strengthens our region.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">⚡</div>
                        <h3>Innovation</h3>
                        <p>Continuously improving our platform with modern technology.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

