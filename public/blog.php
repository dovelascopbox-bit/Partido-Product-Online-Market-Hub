<0?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Blog - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Blog - <?php echo APP_NAME; ?></title>
    
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
        .blog-post {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-2xl);
            margin-bottom: var(--spacing-xl);
            transition: all var(--transition-base);
        }
        .blog-post:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        html.dark .blog-post {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .blog-post-meta {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
            color: var(--gray-500);
            font-size: 0.875rem;
        }
        html.dark .blog-post-meta {
            color: var(--gray-400);
        }
        .blog-post h2 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-md);
        }
        html.dark .blog-post h2 {
            color: var(--gray-700);
        }
        .blog-post h2 a {
            color: inherit;
            text-decoration: none;
        }
        .blog-post h2 a:hover {
            color: var(--primary);
        }
        html.dark .blog-post h2 a:hover {
            color: var(--primary-light);
        }
        .blog-post p {
            color: var(--gray-600);
            line-height: 1.7;
        }
        html.dark .blog-post p {
            color: var(--gray-400);
        }
        .blog-post .read-more {
            display: inline-block;
            margin-top: var(--spacing-md);
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        html.dark .blog-post .read-more {
            color: var(--primary-light);
        }
        .blog-post .read-more:hover {
            text-decoration: underline;
        }
        .tag {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-sm);
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary);
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        html.dark .tag {
            background: rgba(6, 182, 212, 0.15);
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
                <h1>Blog</h1>
                <p>News, updates, and stories from the Partido Market Hub community</p>
            </div>

            <article class="blog-post">
                <div class="blog-post-meta">
                    <span class="tag">News</span>
                    <span>January 15, 2026</span>
                </div>
                <h2><a href="#">Welcome to Partido Market Hub</a></h2>
                <p>We're excited to launch Partido Market Hub, a new online marketplace dedicated to empowering local businesses in the Partido region. Our platform connects buyers with trusted local sellers, making it easier than ever to discover and purchase authentic local products.</p>
                <a href="#" class="read-more">Read More →</a>
            </article>

            <article class="blog-post">
                <div class="blog-post-meta">
                    <span class="tag">Tips</span>
                    <span>January 10, 2026</span>
                </div>
                <h2><a href="#">5 Tips for New Sellers</a></h2>
                <p>Starting your online selling journey? Here are five essential tips to help you succeed on Partido Market Hub: take quality product photos, write detailed descriptions, respond quickly to inquiries, keep your inventory updated, and build your reputation through great service.</p>
                <a href="#" class="read-more">Read More →</a>
            </article>

            <article class="blog-post">
                <div class="blog-post-meta">
                    <span class="tag">Community</span>
                    <span>January 5, 2026</span>
                </div>
                <h2><a href="#">Supporting Local: Why It Matters</a></h2>
                <p>When you buy from local sellers, you're doing more than just purchasing a product. You're supporting families, strengthening the local economy, and preserving the unique culture and traditions of Partido. Discover the impact of shopping local.</p>
                <a href="#" class="read-more">Read More →</a>
            </article>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

