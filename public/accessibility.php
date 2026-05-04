<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Accessibility Statement - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Accessibility - <?php echo APP_NAME; ?></title>
    
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
        .a11y-section {
            margin-bottom: var(--spacing-3xl);
        }
        .a11y-section h2 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-sm);
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        html.dark .a11y-section h2 {
            color: var(--gray-700);
            border-bottom-color: var(--primary-light);
        }
        .a11y-section p {
            color: var(--gray-600);
            line-height: 1.7;
            margin-bottom: var(--spacing-md);
        }
        html.dark .a11y-section p {
            color: var(--gray-400);
        }
        .a11y-section ul {
            color: var(--gray-600);
            line-height: 1.8;
            margin-left: var(--spacing-xl);
            margin-bottom: var(--spacing-md);
        }
        html.dark .a11y-section ul {
            color: var(--gray-400);
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            margin: var(--spacing-xl) 0;
        }
        .feature-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            transition: all var(--transition-base);
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        html.dark .feature-card {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .feature-card h3 {
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
            font-size: 1rem;
        }
        html.dark .feature-card h3 {
            color: var(--primary-light);
        }
        .feature-card p {
            color: var(--gray-600);
            font-size: 0.95rem;
            margin: 0;
        }
        html.dark .feature-card p {
            color: var(--gray-400);
        }
        .keyboard-table {
            width: 100%;
            border-collapse: collapse;
            margin: var(--spacing-lg) 0;
        }
        .keyboard-table th,
        .keyboard-table td {
            padding: var(--spacing-md);
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }
        .keyboard-table th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--gray-900);
        }
        html.dark .keyboard-table th {
            background: var(--gray-150);
            color: var(--gray-700);
        }
        .keyboard-table td {
            color: var(--gray-600);
        }
        html.dark .keyboard-table td {
            color: var(--gray-400);
        }
        .keyboard-table code {
            background: var(--gray-100);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-family: monospace;
            font-size: 0.875rem;
        }
        html.dark .keyboard-table code {
            background: var(--gray-150);
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
                <h1>Accessibility Statement</h1>
                <p>Partido Market Hub is committed to ensuring digital accessibility for all users</p>
            </div>

            <div class="a11y-section">
                <h2>Our Commitment</h2>
                <p>Partido Market Hub is committed to making our platform accessible to everyone, including people with disabilities. We strive to meet or exceed WCAG 2.1 Level AA standards and continuously improve the user experience for all visitors.</p>
            </div>

            <div class="a11y-section">
                <h2>Accessibility Features</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <h3>♿ Keyboard Navigation</h3>
                        <p>Full keyboard support with visible focus indicators and logical tab order</p>
                    </div>
                    <div class="feature-card">
                        <h3>🌓 Dark Mode</h3>
                        <p>Professional dark theme for reduced eye strain and light sensitivity</p>
                    </div>
                    <div class="feature-card">
                        <h3>🔍 Screen Reader Support</h3>
                        <p>ARIA labels, roles, and live regions for assistive technologies</p>
                    </div>
                    <div class="feature-card">
                        <h3>📱 Responsive Design</h3>
                        <p>Works seamlessly on all screen sizes and devices</p>
                    </div>
                    <div class="feature-card">
                        <h3>👁️ High Contrast</h3>
                        <p>Clear color contrast ratios meeting accessibility standards</p>
                    </div>
                    <div class="feature-card">
                        <h3>⌨️ Skip Links</h3>
                        <p>Skip to main content links for efficient keyboard navigation</p>
                    </div>
                </div>
            </div>

            <div class="a11y-section">
                <h2>Keyboard Shortcuts</h2>
                <table class="keyboard-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Shortcut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Toggle Dark Mode</td>
                            <td><code>Ctrl + Shift + D</code></td>
                        </tr>
                        <tr>
                            <td>Skip to Main Content</td>
                            <td><code>Tab</code> (first focusable element)</td>
                        </tr>
                        <tr>
                            <td>Navigate Focus</td>
                            <td><code>Tab</code> / <code>Shift + Tab</code></td>
                        </tr>
                        <tr>
                            <td>Activate Buttons/Links</td>
                            <td><code>Enter</code> or <code>Space</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="a11y-section">
                <h2>Accessibility Toolbar</h2>
                <p>Our platform includes a built-in accessibility toolbar that provides:</p>
                <ul>
                    <li>Font size adjustment (increase/decrease/reset)</li>
                    <li>Dyslexia-friendly font option (OpenDyslexic)</li>
                    <li>High contrast mode toggle</li>
                    <li>Grayscale mode for reduced visual stimulation</li>
                    <li>Link highlighting for better visibility</li>
                </ul>
            </div>

            <div class="a11y-section">
                <h2>Feedback and Support</h2>
                <p>We welcome your feedback on the accessibility of Partido Market Hub. If you encounter any accessibility barriers or have suggestions for improvement, please contact us through our <a href="<?php echo BASE_URL; ?>/public/contact.php" style="color: var(--primary);">Contact Page</a>.</p>
                <p>We aim to respond to accessibility feedback within 48 hours and are committed to addressing issues promptly.</p>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

