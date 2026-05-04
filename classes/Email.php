<?php
/**
 * Email Helper Class
 * Handles sending emails for verification, password reset, notifications
 */

class Email {
    private $pdo;
    
    // Email configuration (update for production)
    private $from_email = 'noreply@partidomarket.com';
    private $from_name = 'Partido Market Hub';
    private $reply_to = 'support@partidomarket.com';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Send email verification message
     */
    public function sendVerificationEmail($user_id, $email, $full_name, $token) {
        $verify_url = BASE_URL . '/public/verify_email.php?token=' . $token . '&email=' . urlencode($email);
        
        $subject = 'Verify your email - ' . APP_NAME;
        
        $body = $this->getVerificationEmailTemplate($full_name, $verify_url);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($email, $full_name, $token) {
        $reset_url = BASE_URL . '/public/reset_password.php?token=' . $token;
        
        $subject = 'Password Reset - ' . APP_NAME;
        
        $body = $this->getPasswordResetEmailTemplate($full_name, $reset_url);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send welcome email (after email verification)
     */
    public function sendWelcomeEmail($email, $full_name, $role) {
        $subject = 'Welcome to ' . APP_NAME;
        
        $body = $this->getWelcomeEmailTemplate($full_name, $role);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send email via PHP mail() - Simple implementation
     * In production, use PHPMailer or similar library
     */
    private function send($to_email, $subject, $body) {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $this->from_name . ' <' . $this->from_email . '>';
        $headers[] = 'Reply-To: ' . $this->reply_to;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        
        // In development, just log the email instead of sending
        $this->logEmail($to_email, $subject, $body);
        
        // For production with proper mail server:
        // return mail($to_email, $subject, $body, implode("\r\n", $headers));
        
        // Development mode: simulate success
        return ['success' => true, 'message' => 'Email logged (development mode)'];
    }
    
    /**
     * Log email for development
     */
    private function logEmail($to_email, $subject, $body) {
        $log_entry = sprintf(
            "[%s] TO: %s | SUBJECT: %s\n",
            date('Y-m-d H:i:s'),
            $to_email,
            $subject
        );
        
        $log_file = $_SERVER['DOCUMENT_ROOT'] . '/logs/email.log';
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
    
    /**
     * Email template: Verification
     */
    private function getVerificationEmailTemplate($name, $verify_url) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: #f9f9f9; padding: 30px; border-radius: 10px; }
        .btn { display: inline-block; padding: 15px 30px; background: #0f766e; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📧 Verify Your Email</h2>
        <p>Hi {$name},</p>
        <p>Thank you for registering! Please verify your email address by clicking the button below:</p>
        <p><a href="{$verify_url}" class="btn">Verify Email</a></p>
        <p>Or copy and paste this link: {$verify_url}</p>
        <p><strong>Note:</strong> This link expires in 24 hours.</p>
    </div>
    <div class="footer">
        <p>If you didn't create an account, please ignore this email.</p>
        <p>&copy; {date('Y')} Partido Market Hub</p>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Email template: Password Reset
     */
    private function getPasswordResetEmailTemplate($name, $reset_url) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: #f9f9f9; padding: 30px; border-radius: 10px; }
        .btn { display: inline-block; padding: 15px 30px; background: #dc2626; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .warning { background: #fef3c7; padding: 10px; border-radius: 5px; margin: 15px 0; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Password Reset Request</h2>
        <p>Hi {$name},</p>
        <p>We received a request to reset your password. Click the button below:</p>
        <p><a href="{$reset_url}" class="btn">Reset Password</a></p>
        <div class="warning">
            <strong>⚠️ Security Notice:</strong>
            <ul>
                <li>This link expires in 1 hour</li>
                <li>If you didn't request this, please ignore or change your password</li>
            </ul>
        </div>
    </div>
    <div class="footer">
        <p>&copy; {date('Y')} Partido Market Hub</p>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Email template: Welcome
     */
    private function getWelcomeEmailTemplate($name, $role) {
        $dashboard_url = BASE_URL . '/public/' . $role . '/dashboard.php';
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { background: #f9f9f9; padding: 30px; border-radius: 10px; }
        .btn { display: inline-block; padding: 15px 30px; background: #0f766e; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .features { background: #ecfdf5; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎉 Welcome to Partido Market Hub!</h2>
        <p>Hi {$name},</p>
        <p>Your account has been verified successfully. You're ready to start!</p>
        <div class="features">
            <strong>What you can do as a {$role}:</strong>
            <ul>
                <li>Browse and purchase products</li>
                <li>Connect with local sellers</li>
                <li>Manage your deals and orders</li>
            </ul>
        </div>
        <p><a href="{$dashboard_url}" class="btn">Go to Dashboard</a></p>
    </div>
    <div class="footer">
        <p>Need help? Contact us at support@partidomarket.com</p>
        <p>&copy; {date('Y')} Partido Market Hub</p>
    </div>
</body>
</html>
HTML;
    }
}
?>
