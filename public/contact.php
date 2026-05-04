<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } else if (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In production, this would send an email
        $success = 'Thank you for your message! We will get back to you within 24 hours.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact <?php echo APP_NAME; ?>">
    <meta name="theme-color" content="#0f766e">
    <title>Contact Us - <?php echo APP_NAME; ?></title>
    
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
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-3xl);
        }
        .contact-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            transition: all var(--transition-base);
        }
        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        html.dark .contact-card {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }
        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
        }
        .contact-card h3 {
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }
        html.dark .contact-card h3 {
            color: var(--primary-light);
        }
        .contact-card p {
            color: var(--gray-600);
        }
        html.dark .contact-card p {
            color: var(--gray-400);
        }
        .form-group {
            margin-bottom: var(--spacing-lg);
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
        }
        html.dark .form-label {
            color: var(--gray-700);
        }
        .form-input, .form-textarea {
            width: 100%;
            padding: var(--spacing-md);
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 1rem;
            background: white;
            color: var(--gray-900);
            transition: all var(--transition-fast);
            font-family: inherit;
        }
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }
        html.dark .form-input, html.dark .form-textarea {
            background: var(--gray-150);
            color: var(--gray-700);
            border-color: var(--gray-200);
        }
        .form-textarea {
            min-height: 150px;
            resize: vertical;
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
            border-color: var(--success);
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
            border-color: var(--danger);
        }
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: var(--spacing-md) var(--spacing-xl);
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
        }
        .btn-primary:hover {
            background: var(--primary-hover, #0a5a56);
        }
        html.dark .btn-primary {
            background: var(--primary-light);
            color: #0f172a;
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
                <h1>Contact Us</h1>
                <p>We'd love to hear from you. Reach out with any questions or feedback.</p>
            </div>

            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">📧</div>
                    <h3>Email</h3>
                    <p>support@partidomarket.com</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">📱</div>
                    <h3>Phone</h3>
                    <p>+63 (912) 345-6789</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">📍</div>
                    <h3>Address</h3>
                    <p>Partido District, Camarines Sur, Philippines</p>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="success-box" role="status"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error-box" role="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" required class="form-input" placeholder="Your name">
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" required class="form-input" placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" name="subject" required class="form-input" placeholder="How can we help?">
                </div>
                <div class="form-group">
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" required class="form-textarea" placeholder="Tell us more..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>

