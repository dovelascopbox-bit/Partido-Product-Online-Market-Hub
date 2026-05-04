<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// If already logged in, redirect to dashboard
if (Auth::isAuthenticated()) {
    secureRedirect(Auth::getDashboardUrl($_SESSION['role']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo APP_NAME; ?> - Empower Local Businesses in Partido">
    <meta name="theme-color" content="#0f766e">
    <title><?php echo APP_NAME; ?> - Buy & Sell Local Products Online</title>
    
    <!-- Design System (Order matters: tokens → main → dark mode) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/helpers.css">
    
    <!-- Professional Dark Mode System (Stage 7-F) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <!-- Theme Switcher Script (Must be early for flicker prevention) -->
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
        /* Professional Landing Page Specific Styles */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            padding: var(--spacing-3xl) var(--spacing-lg);
            text-align: center;
        }

        html.dark .hero-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #0d5a67 100%);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--spacing-xl);
            margin: var(--spacing-2xl) 0;
        }

        .feature-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
            border: 1px solid var(--gray-200);
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

        html.dark .feature-card:hover {
            border-color: var(--primary-light);
            background: var(--gray-150);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto var(--spacing-lg);
            color: var(--primary);
            stroke-width: 2;
        }

        html.dark .feature-icon {
            color: var(--primary-light);
        }

        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-2xl);
            margin: var(--spacing-2xl) 0;
        }

        .step-item {
            display: flex;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }

        .step-number {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            box-shadow: var(--shadow-sm);
        }

        html.dark .step-number {
            background: var(--primary-dark);
            box-shadow: var(--shadow-md);
        }

        .step-content h4 {
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
        }

        .step-content p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        html.dark .step-content h4 {
            color: var(--gray-700);
        }

        html.dark .step-content p {
            color: var(--gray-400);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: #ffffff;
            padding: var(--spacing-3xl) var(--spacing-lg);
            border-radius: var(--radius-lg);
            margin: var(--spacing-3xl) 0;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        html.dark .cta-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #065f46 100%);
            box-shadow: var(--shadow-xl);
        }

        .cta-section h2 {
            color: #ffffff;
            margin-bottom: var(--spacing-xl);
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.125rem;
            margin-bottom: var(--spacing-xl);
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-light {
            background-color: #ffffff;
            color: var(--primary);
            font-weight: 600;
            box-shadow: var(--shadow-md);
        }

        .btn-light:hover {
            background-color: var(--gray-100);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        html.dark .btn-light {
            background-color: var(--gray-800);
            color: var(--gray-50);
        }

        html.dark .btn-light:hover {
            background-color: var(--gray-700);
        }

        .btn-light-outline {
            background-color: transparent;
            color: #ffffff;
            border: 2px solid #ffffff;
            font-weight: 600;
        }

        .btn-light-outline:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        html.dark .btn-light-outline:hover {
            background-color: rgba(248, 250, 252, 0.1);
        }

        /* Footer Styles */
        .site-footer {
            background-color: var(--gray-900);
            color: var(--gray-200);
            padding: var(--spacing-2xl) var(--spacing-lg);
            margin-top: auto;
            transition: background-color 300ms ease, color 300ms ease;
        }

        html.dark .site-footer {
            background-color: #0a0f1a;
            color: var(--gray-300);
        }

        .site-footer h4 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: var(--spacing-md);
        }

html.dark .site-footer h4 {
            color: var(--gray-700);
        }

        .site-footer a {
            color: var(--gray-300);
            text-decoration: none;
            transition: color var(--transition-fast);
        }

        .site-footer a:hover {
            color: #ffffff;
        }

        html.dark .site-footer a {
            color: var(--gray-400);
        }

        html.dark .site-footer a:hover {
            color: var(--primary-light);
        }

        .site-footer .footer-divider {
            border-top: 1px solid var(--gray-700);
            padding-top: var(--spacing-xl);
            text-align: center;
        }

        html.dark .site-footer .footer-divider {
            border-top-color: #1e293b;
        }

        .site-footer .copyright {
            color: var(--gray-400);
            margin: 0;
        }

        html.dark .site-footer .copyright {
            color: var(--gray-500);
        }

        /* Section backgrounds */
        section:nth-child(2) {
            background-color: var(--gray-50);
        }

        html.dark section:nth-child(2) {
            background-color: var(--gray-50);
        }

        section:nth-child(3) {
            background-color: #ffffff;
        }

        html.dark section:nth-child(3) {
            background-color: var(--gray-100);
        }

        /* Smooth transitions */
        body, main, section, footer {
            transition: background-color 300ms ease, color 300ms ease;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: var(--spacing-2xl) var(--spacing-md);
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .cta-section {
                padding: var(--spacing-2xl) var(--spacing-md);
                margin: var(--spacing-2xl) 0;
            }

            .steps-container {
                gap: var(--spacing-xl);
            }
        }

        /* Reduce motion support */
        @media (prefers-reduced-motion: reduce) {
            .feature-card,
            .btn,
            .step-number {
                transition: none;
                transform: none;
            }
        }
    </style>
</head>
<body>
    <!-- Skip to main content link -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/navbar.php'; ?>

    <main id="main-content" role="main" tabindex="-1">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <h1 style="color: #ffffff; margin-bottom: var(--spacing-xl);">
                    Empower Your Business in Partido
                </h1>
                <p style="color: rgba(255, 255, 255, 0.95); font-size: 1.25rem; margin-bottom: var(--spacing-2xl); max-width: 600px; margin-left: auto; margin-right: auto;">
                    Connect local sellers with buyers. Buy, sell, and grow your business online with confidence.
                </p>
                <div class="action-buttons">
                    <a href="<?php echo BASE_URL; ?>/public/register.php?role=seller" class="btn btn-lg btn-light" onclick="return confirmAction('seller');">
                        💼 Become a Seller
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/register.php?role=buyer" class="btn btn-lg btn-light-outline" onclick="return confirmAction('buyer');">
                        🛍️ Start Shopping
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section style="padding: var(--spacing-3xl) var(--spacing-lg); background-color: var(--gray-50);">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: var(--spacing-xl); color: var(--gray-900);">Why Choose Partido Market Hub?</h2>
                
                <div class="features-grid">
                    <!-- Feature 1 -->
                    <div class="feature-card">
                        <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 style="color: var(--primary); margin-bottom: var(--spacing-md);">Fast & Easy</h3>
                        <p style="color: var(--gray-600);">Quick setup and intuitive interface for seamless buying and selling experiences.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card">
                        <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <h3 style="color: var(--primary); margin-bottom: var(--spacing-md);">Secure & Safe</h3>
                        <p style="color: var(--gray-600);">Bank-level security with encrypted transactions and comprehensive data protection.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card">
                        <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 style="color: var(--primary); margin-bottom: var(--spacing-md);">Best Prices</h3>
                        <p style="color: var(--gray-600);">Direct from sellers means competitive pricing and exceptional value for every purchase.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section style="padding: var(--spacing-3xl) var(--spacing-lg);">
            <div class="container">
                <h2 style="text-align: center; margin-bottom: var(--spacing-2xl); color: var(--gray-900);">How It Works</h2>
                
                <div class="steps-container">
                    <!-- For Sellers -->
                    <div>
                        <h3 style="color: var(--primary); margin-bottom: var(--spacing-xl);">👨‍💼 For Sellers</h3>
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Register Your Shop</h4>
                                <p>Create an account and set up your shop profile with business details.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>List Your Products</h4>
                                <p>Add your Partido products with images, descriptions, and pricing.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Manage Orders</h4>
                                <p>Receive and confirm orders from buyers in real-time.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Get Paid</h4>
                                <p>Receive payments safely and build your seller reputation.</p>
                            </div>
                        </div>
                    </div>

                    <!-- For Buyers -->
                    <div>
                        <h3 style="color: var(--secondary); margin-bottom: var(--spacing-xl);">👤 For Buyers</h3>
                        <div class="step-item">
                            <div class="step-number" style="background: var(--secondary);">1</div>
                            <div class="step-content">
                                <h4>Create Account</h4>
                                <p>Sign up as a buyer to start your shopping journey.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number" style="background: var(--secondary);">2</div>
                            <div class="step-content">
                                <h4>Browse Products</h4>
                                <p>Explore authentic Partido products from trusted local sellers.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number" style="background: var(--secondary);">3</div>
                            <div class="step-content">
                                <h4>Place Orders</h4>
                                <p>Add items to your cart and place secure orders with confidence.</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number" style="background: var(--secondary);">4</div>
                            <div class="step-content">
                                <h4>Track & Receive</h4>
                                <p>Track your orders and receive products with delivery confirmation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Ready to Get Started?</h2>
                <p>Join thousands of buyers and sellers in Partido's thriving online marketplace today.</p>
                <div class="action-buttons">
                    <a href="<?php echo BASE_URL; ?>/public/register.php?role=seller" class="btn btn-lg btn-light" onclick="return confirmAction('seller');">
                        Start Selling
                    </a>
                    <a href="<?php echo BASE_URL; ?>/public/register.php?role=buyer" class="btn btn-lg btn-light-outline" onclick="return confirmAction('buyer');">
                        Start Buying
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-xl); margin-bottom: var(--spacing-2xl);">
                <!-- About Section -->
                <div>
                    <h4>About <?php echo APP_NAME; ?></h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <li><a href="<?php echo BASE_URL; ?>/public/about.php">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/about.php">Our Mission</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/contact.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Support Section -->
                <div>
                    <h4>Support</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <li><a href="<?php echo BASE_URL; ?>/public/help.php">Help Center</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/help.php">FAQ</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/documentation.php">Documentation</a></li>
                    </ul>
                </div>

                <!-- Legal Section -->
                <div>
                    <h4>Legal</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <li><a href="<?php echo BASE_URL; ?>/public/privacy.php">Privacy Policy</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/terms.php">Terms of Service</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/accessibility.php">Accessibility</a></li>
                    </ul>
                </div>

                <!-- Company Section -->
                <div>
                    <h4>Company</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <li><a href="<?php echo BASE_URL; ?>/public/blog.php">Blog</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/careers.php">Careers</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/press.php">Press</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-divider">
                <p class="copyright">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved. Build with ❤️ for Partido.</p>
            </div>
        </div>
    </footer>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>

    <!-- Button Action Handlers -->
    <script>
        /**
         * Confirm action before redirecting to registration
         * @param {string} role - The role (seller or buyer)
         * @returns {boolean}
         */
        function confirmAction(role) {
            const roleNames = {
                'seller': 'Seller',
                'buyer': 'Buyer'
            };
            
            const message = `You're about to register as a ${roleNames[role]}. Continue?`;
            
            if (confirm(message)) {
                console.log(`User confirmed registration as ${roleNames[role]}`);
                return true;
            }
            
            console.log(`User cancelled ${roleNames[role]} registration`);
            return false;
        }

        /**
         * Smooth scroll to section
         * @param {string} sectionId - The ID of the section
         */
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }

        /**
         * Add keyboard navigation support
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Focus management for accessibility
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && document.querySelector(href)) {
                        e.preventDefault();
                        scrollToSection(href.substring(1));
                    }
                });
            });

            console.log('Landing page initialized successfully');
        });
    </script>
</body>
</html>
