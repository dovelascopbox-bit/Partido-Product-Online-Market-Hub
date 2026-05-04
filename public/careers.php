<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Careers - <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Careers - <?php echo APP_NAME; ?></title>
    
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
        .careers-intro {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
        }
        .careers-intro p {
            color: var(--gray-600);
            line-height: 1.7;
            font-size: 1.1rem;
        }
        html.dark .careers-intro p {
            color: var(--gray-400);
        }
        .job-listing {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            transition: all var(--transition-base);
        }
        .job-listing:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        html.dark .job-listing {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--spacing-md);
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }
        .job-header h2 {
            color: var(--gray-900);
            font-size: 1.25rem;
        }
        html.dark .job-header h2 {
            color: var(--gray-700);
        }
        .job-type {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-md);
            background: rgba(15, 118, 110, 0.1);
            color: var(--primary);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-weight: 600;
        }
        html.dark .job-type {
            background: rgba(6, 182, 212, 0.15);
            color: var(--primary-light);
        }
        .job-meta {
            display: flex;
            gap: var(--spacing-lg);
            color: var(--gray-500);
            font-size: 0.875rem;
            margin-bottom: var(--spacing-md);
        }
        html.dark .job-meta {
            color: var(--gray-400);
        }
        .job-listing p {
            color: var(--gray-600);
            line-height: 1.7;
        }
        html.dark .job-listing p {
            color: var(--gray-400);
        }
        .apply-btn {
            display: inline-block;
            margin-top: var(--spacing-md);
            padding: var(--spacing-sm) var(--spacing-lg);
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            transition: all var(--transition-fast);
        }
        .apply-btn:hover {
            background: var(--primary-hover, #0a5a56);
        }
        html.dark .apply-btn {
            background: var(--primary-light);
            color: #0f172a;
        }
        html.dark .apply-btn:hover {
            background: #22d3ee;
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
                <h1>Careers</h1>
                <p>Join our team and help shape the future of local commerce</p>
            </div>

            <div class="careers-intro">
                <p>We're building something special at Partido Market Hub. If you're passionate about supporting local businesses and creating meaningful impact in our community, we'd love to hear from you.</p>
            </div>

            <div class="job-listing">
                <div class="job-header">
                    <h2>Community Manager</h2>
                    <span class="job-type">Full-time</span>
                </div>
                <div class="job-meta">
                    <span>📍 Partido Region</span>
                    <span>💼 Operations</span>
                </div>
                <p>Help us build and nurture our seller and buyer community. You'll be responsible for onboarding new sellers, organizing community events, and ensuring a positive experience for all users.</p>
                <a href="<?php echo BASE_URL; ?>/public/contact.php" class="apply-btn">Apply Now</a>
            </div>

            <div class="job-listing">
                <div class="job-header">
                    <h2>Customer Support Specialist</h2>
                    <span class="job-type">Full-time</span>
                </div>
                <div class="job-meta">
                    <span>📍 Remote / Partido</span>
                    <span>💼 Support</span>
                </div>
                <p>Provide exceptional support to our users through email, chat, and phone. Help resolve issues, answer questions, and ensure everyone has a great experience on our platform.</p>
                <a href="<?php echo BASE_URL; ?>/public/contact.php" class="apply-btn">Apply Now</a>
            </div>

            <div class="job-listing">
                <div class="job-header">
                    <h2>Marketing Intern</h2>
                    <span class="job-type">Internship</span>
                </div>
                <div class="job-meta">
                    <span>📍 Partido Region</span>
                    <span>💼 Marketing</span>
                </div>
                <p>Assist with social media management, content creation, and local outreach initiatives. Great opportunity for students or recent graduates interested in digital marketing.</p>
                <a href="<?php echo BASE_URL; ?>/public/contact.php" class="apply-btn">Apply Now</a>
            </div>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

