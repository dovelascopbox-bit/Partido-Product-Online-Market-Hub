<?php
/**
 * Authentication Class
 * Handles user registration, login, logout, and role-based access
 */

class Auth {
    private $pdo;
    private $table = 'users';
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Register a new user
     */
    public function register($username, $email, $password, $full_name, $role) {
        // Validate input
        if (!$this->validateInput($username, $email, $password, $full_name)) {
            return ['success' => false, 'message' => 'Invalid input data.'];
        }

        // Check if user already exists
        if ($this->userExists($email)) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        try {
            // Insert user
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} 
                (username, email, password_hash, full_name, role, status) 
                VALUES (:username, :email, :password_hash, :full_name, :role, 'active')");
            
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':full_name' => $full_name,
                ':role' => $role
            ]);

            $user_id = $this->pdo->lastInsertId();

            // Create role-specific records
            $this->createRoleRecord($user_id, $role);

            return ['success' => true, 'message' => 'Registration successful!', 'user_id' => $user_id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

/**
     * Login user
     */
    public function login($email, $password) {
        // Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email AND status = 'active'");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => MSG_LOGIN_FAILED];
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => MSG_LOGIN_FAILED];
            }

            // Check email verification (unless SKIP_EMAIL_VERIFICATION is true)
            $email_verified = isset($user['email_verified']) ? (bool)$user['email_verified'] : true;
            if (!SKIP_EMAIL_VERIFICATION && !$email_verified) {
                return ['success' => false, 'message' => MSG_EMAIL_NOT_VERIFIED, 'needs_verification' => true];
            }

            // Update last login
            $update_stmt = $this->pdo->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE user_id = :user_id");
            $update_stmt->execute([':user_id' => $user['user_id']]);

            // Return user data
            return [
                'success' => true,
                'message' => 'Login successful!',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'email_verified' => $email_verified
                ]
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Validate user input
     */
    private function validateInput($username, $email, $password, $full_name) {
        // Username validation (3-50 chars, alphanumeric + underscore)
        if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
            return false;
        }

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Password validation (minimum 8 chars, at least 1 uppercase, 1 lowercase, 1 number)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            return false;
        }

        // Full name validation (2-100 chars)
        if (strlen($full_name) < 2 || strlen($full_name) > 100) {
            return false;
        }

        return true;
    }

    /**
     * Check if user exists
     */
    private function userExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM {$this->table} WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create role-specific records
     */
    private function createRoleRecord($user_id, $role) {
        try {
            switch ($role) {
                case 'admin':
                    $stmt = $this->pdo->prepare("INSERT INTO admins (user_id, permissions) VALUES (:user_id, 'manage_all')");
                    $stmt->execute([':user_id' => $user_id]);
                    break;
                case 'seller':
                    $stmt = $this->pdo->prepare("INSERT INTO sellers (user_id, shop_name) VALUES (:user_id, 'My Shop')");
                    $stmt->execute([':user_id' => $user_id]);
                    break;
                case 'buyer':
                    $stmt = $this->pdo->prepare("INSERT INTO buyers (user_id) VALUES (:user_id)");
                    $stmt->execute([':user_id' => $user_id]);
                    break;
            }
        } catch (PDOException $e) {
            // Log error if needed
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Check session timeout
     */
    public static function checkSessionTimeout() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return true;
        }

        $timeout = SESSION_TIMEOUT;
        if (time() - $_SESSION['last_activity'] > $timeout) {
            session_destroy();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

/**
     * Logout user
     */
    public static function logout() {
        // Only call session_start if no session is active (belt-and-suspenders check)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Clear all session variables
        $_SESSION = [];
        // Destroy the session
        session_destroy();
        return true;
    }

/**
     * Get dashboard redirect URL based on role
     */
    public static function getDashboardUrl($role) {
        $base = BASE_URL . '/public/';
        
        switch ($role) {
            case 'admin':
                return $base . 'admin/dashboard.php';
            case 'seller':
                return $base . 'seller/dashboard.php';
            case 'buyer':
                return $base . 'buyer/dashboard.php';
            default:
                return $base . 'index.php';
        }
    }

    /**
     * Request password reset - generates reset token
     */
    public function requestPasswordReset($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        try {
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT user_id, email FROM {$this->table} WHERE email = :email AND status = 'active'");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                // Return success even if user not found (prevents email enumeration)
                return ['success' => true, 'message' => 'If an account exists with that email, you will receive a password reset link.'];
            }
            
            // Generate secure reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token
            $update_stmt = $this->pdo->prepare("UPDATE {$this->table} SET reset_token = :token, reset_expires = :expires WHERE user_id = :user_id");
            $update_stmt->execute([
                ':token' => $reset_token,
                ':expires' => $reset_expires,
                ':user_id' => $user['user_id']
            ]);
            
            // In production, send email here
            // For now, return the token (development only)
            return [
                'success' => true, 
                'message' => 'If an account exists with that email, you will receive a password reset link.',
                'reset_token' => $reset_token, // Remove in production
                'user_id' => $user['user_id']
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }

    /**
     * Verify reset token
     */
    public function verifyResetToken($token) {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, email FROM {$this->table} 
                WHERE reset_token = :token AND reset_expires > NOW() AND status = 'active'");
            $stmt->execute([':token' => $token]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid or expired reset token.'];
            }
            
            return ['success' => true, 'user_id' => $user['user_id'], 'email' => $user['email']];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'An error occurred.'];
        }
    }

/**
     * Reset password using token
     */
    public function resetPassword($token, $new_password) {
        // First verify token
        $verify = $this->verifyResetToken($token);
        
        if (!$verify['success']) {
            return $verify;
        }
        
        // Validate new password
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $new_password)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase, lowercase, and numbers.'];
        }
        
        try {
            // Hash new password
            $password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 10]);
            
            // Update password and clear reset token
            $update_stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET password_hash = :hash, reset_token = NULL, reset_expires = NULL 
                WHERE user_id = :user_id");
            $update_stmt->execute([
                ':hash' => $password_hash,
                ':user_id' => $verify['user_id']
            ]);
            
            return ['success' => true, 'message' => 'Password reset successful! You can now log in with your new password.'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }

    /**
     * Send email verification email
     */
    public function sendVerificationEmail($user_id, $email, $full_name) {
        try {
            // Generate verification token
            $verification_token = bin2hex(random_bytes(32));
            
            // Store verification token
            $update_stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET verification_token = :token, verification_sent_at = NOW() 
                WHERE user_id = :user_id");
            $update_stmt->execute([
                ':token' => $verification_token,
                ':user_id' => $user_id
            ]);
            
            // Send email using Email class
            $email_obj = new Email($this->pdo);
            $result = $email_obj->sendVerificationEmail($user_id, $email, $full_name, $verification_token);
            
            return $result;
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to send verification email.'];
        }
    }

    /**
     * Verify email address
     */
    public function verifyEmail($token, $email) {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, email_verified FROM {$this->table} 
                WHERE verification_token = :token AND email = :email");
            $stmt->execute([':token' => $token, ':email' => $email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid verification link.'];
            }
            
            if ($user['email_verified']) {
                return ['success' => true, 'message' => 'Email already verified.'];
            }
            
            // Mark email as verified
            $update_stmt = $this->pdo->prepare("UPDATE {$this->table} 
                SET email_verified = TRUE, verification_token = NULL, status = 'active' 
                WHERE user_id = :user_id");
            $update_stmt->execute([':user_id' => $user['user_id']]);
            
            return ['success' => true, 'message' => 'Email verified successfully!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Verification failed.'];
        }
    }

    /**
     * Check if email is verified (for optional enforcement)
     */
    public function isEmailVerified($user_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT email_verified FROM {$this->table} WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch();
            
            return $user ? $user['email_verified'] : false;
            
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, email, full_name, email_verified FROM {$this->table} 
                WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found.'];
            }
            
            if ($user['email_verified']) {
                return ['success' => true, 'message' => 'Email already verified.'];
            }
            
            return $this->sendVerificationEmail($user['user_id'], $user['email'], $user['full_name']);
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to resend verification email.'];
        }
    }
}
?>
