<?php
/**
 * Buyer Rating Page
 * Page for buyer to rate seller after deal completion
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Check authentication
if (!Auth::isAuthenticated() || $_SESSION['user_role'] !== 'buyer') {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

// Get buyer_id
$buyer_result = $pdo->query("SELECT buyer_id FROM buyers WHERE user_id = {$_SESSION['user_id']}")->fetch();
if (!$buyer_result) {
    header('Location: ' . BASE_URL . '/public/buyer/deals.php');
    exit;
}
$buyer_id = $buyer_result['buyer_id'];

// Get deal_id from query string
$deal_id = isset($_GET['deal_id']) ? (int)$_GET['deal_id'] : 0;
if (!$deal_id) {
    header('Location: ' . BASE_URL . '/public/buyer/deals.php');
    exit;
}

// Get deal details
$deal_stmt = $pdo->prepare("SELECT d.*, p.product_name, p.srp, 
    u.full_name as seller_name, s.shop_name
    FROM deals d
    JOIN products p ON d.product_id = p.product_id
    JOIN sellers s ON d.seller_id = s.seller_id
    JOIN users u ON s.user_id = u.user_id
    WHERE d.deal_id = :deal_id");
$deal_stmt->execute([':deal_id' => $deal_id]);
$deal = $deal_stmt->fetch();

if (!$deal) {
    header('Location: ' . BASE_URL . '/public/buyer/deals.php');
    exit;
}

// Verify buyer is part of this deal
if ($deal['buyer_id'] != $buyer_id) {
    header('Location: ' . BASE_URL . '/public/buyer/deals.php');
    exit;
}

// Verify deal is completed
if ($deal['status'] !== 'completed') {
    header('Location: ' . BASE_URL . '/public/buyer/deals.php?message=Deal%20not%20completed%20yet');
    exit;
}

// Check if rating already exists
$rating = new Rating($pdo);
if ($rating->hasRating($deal_id)) {
    header('Location: ' . BASE_URL . '/public/buyer/deals.php?message=Rating%20already%20submitted');
    exit;
}

// Handle rating submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = 'CSRF token verification failed';
    } else {
        // Get rating data
        $stars = isset($_POST['stars']) ? (int)$_POST['stars'] : 0;
        $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

        // Create rating
        $result = $rating->create($deal_id, $buyer_id, $deal['seller_id'], $stars, $review_text);

        if ($result['success']) {
            // Get seller's user_id for notification
            $seller_stmt = $pdo->prepare("SELECT user_id FROM sellers WHERE seller_id = :seller_id");
            $seller_stmt->execute([':seller_id' => $deal['seller_id']]);
            $seller = $seller_stmt->fetch();

            if ($seller) {
                // Notify seller about rating
                Notification::notifySellerRatingReceived(
                    $pdo,
                    $seller['user_id'],
                    $_SESSION['full_name'],
                    $stars,
                    $deal['product_name'],
                    $deal_id
                );
            }

            // Redirect with success message
            $_SESSION['success_message'] = 'Rating submitted successfully!';
            header('Location: ' . BASE_URL . '/public/buyer/deals.php');
            exit;
        } else {
            $error_message = $result['message'];
        }
    }
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Seller - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
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
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Rate This Deal</h1>
            <p class="text-gray-600 mb-6">Share your experience with this seller</p>

            <!-- Error Message -->
            <?php if ($error_message): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <!-- Deal Context Card -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h2 class="font-semibold text-gray-900 mb-2">Product</h2>
                <p class="text-gray-700"><?php echo htmlspecialchars($deal['product_name']); ?></p>
                <p class="text-gray-600 text-sm mt-1">
                    <span class="font-semibold">Seller:</span> <?php echo htmlspecialchars($deal['seller_name']); ?>
                </p>
                <p class="text-gray-600 text-sm">
                    <span class="font-semibold">Price:</span> <?php echo htmlspecialchars(formatCurrency($deal['srp'])); ?>
                </p>
                <p class="text-gray-600 text-sm">
                    <span class="font-semibold">Date:</span> <?php echo htmlspecialchars(formatDate($deal['completed_at'])); ?>
                </p>
            </div>

            <!-- Rating Form -->
            <form method="POST" class="space-y-6">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="stars" id="stars_input" value="0">

                <!-- Star Rating -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Rate Your Experience</label>
                    <div class="flex gap-2" id="star_rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button 
                                type="button" 
                                class="star-btn text-4xl transition-all hover:scale-110"
                                data-value="<?php echo $i; ?>"
                                style="color: #d1d5db; cursor: pointer;">
                                ★
                            </button>
                        <?php endfor; ?>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        <span id="star_label">Click to select stars</span>
                    </div>
                </div>

                <!-- Review Text Area -->
                <div>
                    <label for="review_text" class="block text-sm font-semibold text-gray-900 mb-2">
                        Review (Optional)
                    </label>
                    <textarea 
                        id="review_text" 
                        name="review_text" 
                        rows="4" 
                        maxlength="500"
                        placeholder="How was your experience? (max 500 characters)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </textarea>
                    <div class="mt-1 text-xs text-gray-500 text-right">
                        <span id="char_count">0</span>/500
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4">
                    <a href="<?php echo BASE_URL; ?>/public/buyer/deals.php" 
                       class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Skip for Now
                    </a>
                    <button 
                        type="submit" 
                        id="submit_btn" 
                        disabled
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Submit Rating
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const starBtns = document.querySelectorAll('.star-btn');
        const starsInput = document.getElementById('stars_input');
        const starLabel = document.getElementById('star_label');
        const reviewText = document.getElementById('review_text');
        const charCount = document.getElementById('char_count');
        const submitBtn = document.getElementById('submit_btn');

        let selectedStars = 0;

        // Star rating interaction
        starBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                selectedStars = parseInt(btn.dataset.value);
                starsInput.value = selectedStars;
                updateStarDisplay();
                updateSubmitButton();
            });

            btn.addEventListener('mouseenter', () => {
                const value = parseInt(btn.dataset.value);
                updateStarDisplay(value);
            });
        });

        document.getElementById('star_rating').addEventListener('mouseleave', () => {
            updateStarDisplay();
        });

        function updateStarDisplay(hoverValue = 0) {
            const value = hoverValue || selectedStars;
            starBtns.forEach((btn, idx) => {
                if (idx < value) {
                    btn.style.color = '#fbbf24';
                } else {
                    btn.style.color = '#d1d5db';
                }
            });

            if (selectedStars > 0) {
                const labels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                starLabel.textContent = labels[selectedStars] + ' (' + selectedStars + '/5)';
            }
        }

        // Character count
        reviewText.addEventListener('input', () => {
            charCount.textContent = reviewText.value.length;
        });

        // Update submit button state
        function updateSubmitButton() {
            if (selectedStars > 0) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        // Initial setup
        updateSubmitButton();
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
