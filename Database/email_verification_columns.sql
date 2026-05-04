-- Add email verification columns to users table
-- Run this script to enable email verification functionality

ALTER TABLE users 
ADD COLUMN verification_token VARCHAR(64) NULL,
ADD COLUMN email_verified BOOLEAN DEFAULT FALSE,
ADD COLUMN verification_sent_at DATETIME NULL,
ADD INDEX idx_verification_token (verification_token);

-- Update status to include 'pending' for unverified accounts
ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'suspended', 'pending') DEFAULT 'pending';

-- The columns will allow:
-- 1. Store a verification token when user registers
-- 2. Track if email has been verified
-- 3. Track when verification email was sent
-- 4. Require email verification before allowing login (optional)
