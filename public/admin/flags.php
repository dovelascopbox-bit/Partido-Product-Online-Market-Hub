<?php
/**
 * Flag Reports Management Page
 * Admin page for handling user-reported content (Stage 6)
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication and admin role
requireAuth(['admin']);

$admin = new Admin($pdo);

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get flagged items
$flags = $admin->getFlaggedItems($limit, $offset);

// Handle flag resolution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['resolve', 'dismiss', 'remove'])) {
    $report_id = intval($_POST['report_id']);
    $action = $_POST['action'];
    $notes = trim($_POST['notes'] ?? '');
    
    $result = $admin->resolveFlag($report_id, $_SESSION['user_id'], $action, $notes);
    
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flag Reports - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Skip to main content link (WCAG 2.1 AA accessibility) -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <nav class="bg-surface shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="<?php echo BASE_URL; ?>/public/index.php" class="flex items-center gap-2 text-lg font-bold text-blue-600">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="Partido Market Hub" class="h-8 w-auto rounded">
                        <span>Partido Online Hub</span>
                    </a>
                    <span class="text-sm font-semibold bg-info bg-opacity-20 text-info px-3 py-1 rounded-full">Admin Panel</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-1 text-sm">
                        <a href="dashboard.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Dashboard</a>
                        <a href="users.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Users</a>
                        <a href="products.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Products</a>
                        <a href="deals.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Deals</a>
                        <a href="ratings.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Ratings</a>
                        <a href="flags.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text font-medium">Flags</a>
                        <a href="analytics.php" class="px-3 py-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition text-text">Analytics</a>
                    </div>
                    <span class="text-text font-medium"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/public/logout.php" class="px-4 py-2 bg-error text-white rounded-lg hover:opacity-90 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main id="main-content" role="main" tabindex="-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Flag Reports</h1>
            <p class="text-gray-600">Review and handle user-reported content</p>
        </div>

        <!-- Flags Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Report ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Item Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Item Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Reported By</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($flags) > 0): ?>
                            <?php foreach ($flags as $flag): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-blue-600">#<?php echo $flag['report_id']; ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            <?php echo match($flag['item_type']) {
                                                'product' => 'bg-blue-100 text-blue-800',
                                                'seller' => 'bg-green-100 text-green-800',
                                                'rating' => 'bg-purple-100 text-purple-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            }; ?>">
                                            <?php echo ucfirst($flag['item_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo htmlspecialchars($flag['item_name'] ?? '—'); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo htmlspecialchars($flag['reason']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo htmlspecialchars($flag['reporter_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            <?php echo match($flag['status']) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'resolved' => 'bg-green-100 text-green-800',
                                                'dismissed' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            }; ?>">
                                            <?php echo ucfirst($flag['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo date('M d, Y H:i', strtotime($flag['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <?php if ($flag['status'] === 'pending'): ?>
                                            <button onclick="showResolveModal(<?php echo $flag['report_id']; ?>, '<?php echo htmlspecialchars(addslashes($flag['reason'])); ?>')"
                                                    class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                                Review
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-500 text-xs">Resolved</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    No flag reports found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </main>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>

    <!-- Resolve Modal -->
    <div id="resolveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Review Flag Report</h3>
            
            <div class="mb-4 p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Reason:</span> <span id="flagReason"></span>
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                <textarea id="adminNotes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                          placeholder="Your decision and notes" rows="3"></textarea>
            </div>

            <div class="flex gap-2">
                <button onclick="resolveFlag('dismiss')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                    Dismiss
                </button>
                <button onclick="resolveFlag('resolve')" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Keep
                </button>
                <button onclick="resolveFlag('remove')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                    Remove
                </button>
            </div>

            <button onclick="closeResolveModal()" class="w-full mt-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                Cancel
            </button>
        </div>
    </div>

    <script>
    let selectedReportId = null;

    function showResolveModal(reportId, reason) {
        selectedReportId = reportId;
        document.getElementById('flagReason').textContent = reason;
        document.getElementById('adminNotes').value = '';
        document.getElementById('resolveModal').classList.remove('hidden');
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').classList.add('hidden');
        selectedReportId = null;
    }

    function resolveFlag(action) {
        if (!selectedReportId) return;

        const formData = new FormData();
        formData.append('action', action);
        formData.append('report_id', selectedReportId);
        formData.append('notes', document.getElementById('adminNotes').value);

        fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    document.addEventListener('click', function(event) {
        if (event.target.id === 'resolveModal') {
            closeResolveModal();
        }
    });
    </script>
</body>
</html>
