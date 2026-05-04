<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

$email = $_GET['email'] ?? '';

if (empty($email)) {
    die('Please provide an email: get_token.php?email=you@example.com');
}

$stmt = $pdo->prepare("SELECT user_id, username, email, verification_token FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if ($user) {
    echo "User ID: " . $user['user_id'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Verification Token: " . ($user['verification_token'] ?: 'NONE') . "\n";
    echo "\nVerification Link:\n";
    echo BASE_URL . "/public/verify_email.php?token=" . $user['verification_token'] . "&email=" . urlencode($user['email']) . "\n";
} else {
    echo "User not found with email: " . $email;
}
?>
