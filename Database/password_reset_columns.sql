-- Add password reset columns to users table
-- Run this script to enable password reset functionality

ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expires DATETIME NULL,
ADD INDEX idx_reset_token (reset_token);

-- The columns will allow:
-- 1. Store a secure reset token when user requests password reset
-- 2. Set expiration time (1 hour from request)
-- 3. Verify token is valid and not expired when resetting password
-- 4. Clear token after successful password reset
