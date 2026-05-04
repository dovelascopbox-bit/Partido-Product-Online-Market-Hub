<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

// Require authentication
requireAuth();

// Get messenger
$messenger = new Messenger($pdo);

// Get user's conversations
$conversations = $messenger->getConversations($_SESSION['user_id']);

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messenger - <?php echo APP_NAME; ?></title>
    <meta name="theme-color" content="#0f766e">
    
    <!-- Design System -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/tokens.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/accessibility.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dark-mode.css">
    
    <!-- Dark mode no-flash -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('partido_theme_preference');
                const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (systemDark ? 'dark' : 'light');
                if (theme === 'dark') document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    
    <style>
        body { background: var(--gray-50); }
        html.dark body { background: #0f172a; }
        .navbar { z-index: 1020; }
        .navbar-actions { flex-wrap: wrap; gap: 0.5rem; }
        .navbar-actions a { 
            color: #374151 !important; 
            font-weight: 600; 
            padding: 0.375rem 0.75rem; 
            border-radius: 0.5rem; 
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .navbar-actions a:hover { 
            background-color: #f3f4f6 !important; 
            color: var(--primary) !important;
        }
        html.dark .navbar-actions a:hover { 
            background-color: rgba(15, 118, 110, 0.2) !important; 
        }
        #theme-toggle-btn { opacity: 1 !important; }
        #theme-toggle-btn:hover { transform: scale(1.1); }
        .btn.btn-danger { 
            background-color: #dc2626 !important; 
            color: white !important; 
            font-weight: 600; 
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn.btn-danger:hover { 
            background-color: #991b1b !important; 
        }
        .messenger-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .conversations-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
        .conversation-card { 
            background: white; 
            border: 1px solid var(--gray-200); 
            border-radius: 1rem; 
            padding: 1.5rem; 
            transition: all 0.3s; 
            cursor: pointer; 
            text-decoration: none;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        html.dark .conversation-card { 
            background: #1e293b;
            border-color: #334155;
        }
        .conversation-card:hover { 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            transform: translateY(-2px);
            border-color: var(--primary);
        }
        .conversation-header { display: flex; gap: 1rem; margin-bottom: 1rem; align-items: flex-start; }
        .conversation-image { width: 60px; height: 60px; background: var(--gray-200); border-radius: 0.75rem; overflow: hidden; flex-shrink: 0; }
        html.dark .conversation-image { background: #334155; }
        .conversation-image img { width: 100%; height: 100%; object-fit: cover; }
        .conversation-info { flex: 1; min-width: 0; }
        .conversation-title { font-size: 1rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.25rem; line-clamp: 2; }
        html.dark .conversation-title { color: white; }
        .conversation-price { font-size: 1.25rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem; }
        .conversation-party { font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.75rem; }
        html.dark .conversation-party { color: #cbd5e1; }
        .conversation-message { font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem; line-clamp: 2; }
        html.dark .conversation-message { color: #cbd5e1; }
        .conversation-time { font-size: 0.75rem; color: var(--gray-500); margin-bottom: 1rem; }
        .conversation-badges { display: flex; gap: 0.5rem; margin-top: auto; flex-wrap: wrap; }
        .badge { display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; }
        .badge-ongoing { background: rgba(234, 179, 8, 0.15); color: #854d0e; }
        html.dark .badge-ongoing { background: rgba(234, 179, 8, 0.25); color: #fbbf24; }
        .badge-completed { background: rgba(34, 197, 94, 0.15); color: #166534; }
        html.dark .badge-completed { background: rgba(34, 197, 94, 0.25); color: #86efac; }
        .badge-cancelled { background: rgba(239, 68, 68, 0.15); color: #991b1b; }
        html.dark .badge-cancelled { background: rgba(239, 68, 68, 0.25); color: #fca5a5; }
        .unread-badge { background: var(--danger); color: white; }
        .empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 1rem; border: 2px dashed var(--gray-300); }
        html.dark .empty-state { background: #1e293b; border-color: #334155; }
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .empty-state-title { font-size: 1.5rem; font-weight: 700; color: var(--gray-900); margin-bottom: 0.5rem; }
        html.dark .empty-state-title { color: white; }
        .empty-state-text { color: var(--gray-600); margin-bottom: 2rem; }
        html.dark .empty-state-text { color: #cbd5e1; }
        @media (max-width: 768px) { 
            .messenger-container { padding: 1rem; }
            .conversations-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <a href="<?php echo BASE_URL; ?>/" class="navbar-logo">
            <span style="font-size: 1.5rem; font-weight: 700; color: #0f766e;">Partido</span>
        </a>
        <div style="flex: 1; margin-left: 2rem;">
            <span style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">💬 Messenger</span>
        </div>
        <div class="navbar-actions">
            <a href="<?php echo BASE_URL; ?>/public/<?php echo $_SESSION['role']; ?>/dashboard.php" style="color: #374151; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; transition: all 0.2s; text-decoration: none; font-size: 0.95rem;">
                🏠 Dashboard
            </a>
            <span style="font-size: 0.875rem; color: #374151; font-weight: 600; padding: 0.5rem 1rem;">
                <?= htmlspecialchars($_SESSION["full_name"]) ?>
            </span>
            <button 
                id="theme-toggle-btn"
                class="navbar-action-btn"
                aria-label="Toggle dark mode"
                title="Toggle dark mode (Ctrl+Shift+D)"
                style="opacity: 1; font-size: 1.25rem; transition: all 0.2s; background: none; border: none; cursor: pointer; padding: 0.5rem;"
                onclick="if(window.themeSwitcher) window.themeSwitcher.toggleDarkMode();"
            >
                <span id="theme-icon">☀️</span>
            </button>
            <a href="<?php echo BASE_URL; ?>/public/logout.php" class="btn btn-danger" style="background: #dc2626 !important; color: white !important; font-weight: 600; padding: 0.5rem 1rem !important; border-radius: 0.5rem;">
                Logout
            </a>
        </div>
    </nav>

    <div class="messenger-container">
        <!-- Page Header -->
        <div style="margin-bottom: 3rem;">
            <h1 style="font-size: 3rem; font-weight: 800; color: var(--gray-900); margin-bottom: 1rem;">Messenger</h1>
            <p style="color: var(--gray-600); font-size: 1.125rem;">Manage your conversations and negotiate deals</p>
        </div>
        
        <!-- Flash Messages -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div style="margin-bottom: 2rem; padding: 1.5rem; border-radius: 0.75rem; border-left: 4px solid var(--success); background: rgba(34, 197, 94, 0.1);">
                <?= htmlspecialchars($flash["message"]) ?>
            </div>
        <?php endif; ?>

        <!-- Conversations Grid -->
        <?php if (empty($conversations)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">💬</div>
                <h2 class="empty-state-title">No conversations yet</h2>
                <p class="empty-state-text">Initiate a deal in the marketplace to start negotiating with sellers</p>
                <a href="<?php echo BASE_URL; ?>/public/buyer/market.php" class="btn btn-primary" style="display: inline-block; padding: 0.75rem 2rem; background: var(--primary); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 600; transition: all 0.2s;">
                    🛍️ Browse Market
                </a>
            </div>
        <?php else: ?>
            <div class="conversations-grid">
                <?php foreach ($conversations as $conv): ?>
                    <a href="<?php echo BASE_URL; ?>/public/messenger/conversation.php?deal_id=<?php echo $conv['deal_id']; ?>" class="conversation-card">
                        <!-- Header with Image -->
                        <div class="conversation-header">
                            <div class="conversation-image">
                                <?php $img = getProductImageUrl($conv['image_path'] ?? ''); ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" alt="product" onerror="this.style.display='none'">
                            </div>
                            <div class="conversation-info">
                                <h3 class="conversation-title"><?php echo htmlspecialchars($conv['product_name']); ?></h3>
                                <p class="conversation-price"><?php echo formatCurrency($conv['srp']); ?></p>
                            </div>
                        </div>
                        
                        <!-- Party Info -->
                        <p class="conversation-party">💬 with <strong><?php echo htmlspecialchars($conv['other_party_name'] ?? 'Unknown'); ?></strong></p>
                        
                        <!-- Last Message Preview -->
                        <?php if ($conv['last_message']): ?>
                            <p class="conversation-message"><?php 
                                $preview = $conv['last_message'];
                                if (strlen($preview) > 100) $preview = substr($preview, 0, 100) . '...';
                                echo htmlspecialchars($preview);
                            ?></p>
                            <p class="conversation-time"><?php echo formatDate($conv['last_message_time'], 'M d g:i A'); ?></p>
                        <?php else: ?>
                            <p class="conversation-time" style="font-style: italic; color: var(--gray-500);">No messages yet</p>
                        <?php endif; ?>
                        
                        <!-- Badges -->
                        <div class="conversation-badges">
                            <!-- Unread Badge -->
                            <?php if ($conv['unread_count'] > 0): ?>
                                <span class="badge unread-badge">🔔 <?php echo min($conv['unread_count'], 99); ?> unread</span>
                            <?php endif; ?>
                            
                            <!-- Status Badge -->
                            <span class="badge <?php 
                                if ($conv['deal_status'] == 'ongoing') echo 'badge-ongoing';
                                elseif ($conv['deal_status'] == 'completed') echo 'badge-completed';
                                else echo 'badge-cancelled';
                            ?>">
                                <?php 
                                if ($conv['deal_status'] == 'ongoing') echo '🤝 Ongoing';
                                elseif ($conv['deal_status'] == 'completed') echo '✓ Completed';
                                else echo '✕ Cancelled';
                                ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        window.themeSwitcher = {
            toggleDarkMode() {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('partido_theme_preference', isDark ? 'dark' : 'light');
                document.getElementById('theme-icon').textContent = isDark ? '🌙' : '☀️';
            }
        };
    </script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
