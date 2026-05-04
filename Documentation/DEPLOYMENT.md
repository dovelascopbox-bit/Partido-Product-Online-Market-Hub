# DEPLOYMENT.md - Partido Product Online Market Hub

## Production Deployment Guide

Complete guide for deploying the Partido platform to production environments.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Local Development Setup](#local-development-setup)
3. [XAMPP Deployment](#xampp-deployment)
4. [cPanel Deployment](#cpanel-deployment)
5. [Linux Server Deployment](#linux-server-deployment)
6. [Database Setup](#database-setup)
7. [Configuration](#configuration)
8. [Security Checklist](#security-checklist)
9. [Performance Optimization](#performance-optimization)
10. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Software

- PHP 8.0 or higher
- MySQL 5.7 or higher (or MariaDB 10.3+)
- Apache 2.4+ or Nginx
- OpenSSL for HTTPS
- Composer (optional, for dependency management)

### System Requirements

- Minimum 512MB RAM (recommended 2GB+)
- Minimum 500MB disk space
- 99.5% uptime SLA recommended

---

## Local Development Setup

### Step 1: Extract Project Files

```bash
# Extract the uploaded file
unzip ParProOMH.zip -d C:\

# Navigate to project
cd C:\ParProOMH
```

### Step 2: Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `partido_market`
3. Import the database schema:
   ```bash
   # In phpMyAdmin, go to Import tab and select partido_market.sql
   # Or via MySQL CLI:
   mysql -u root -p partido_market < partido_market.sql
   ```

### Step 3: Configure Constants

Edit `config/constants.php`:

```php
define('BASE_URL', 'http://localhost/ParProOMH');
define('APP_NAME', 'Partido Product Online Market Hub');
define('STAGE', '6');
define('APP_VERSION', '1.0.0');
define('ENVIRONMENT', 'development'); // or 'production'
```

### Step 4: Configure Database

Edit `config/database.php`:

```php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'partido_market';
```

### Step 5: Set File Permissions

```bash
# Windows (via Command Prompt as Administrator)
# Already set for development

# Linux/Mac
chmod 755 config/
chmod 755 classes/
chmod 755 includes/
chmod 755 public/
chmod 755 assets/
chmod 755 assets/uploads/
chmod 777 assets/uploads/products/ # For file uploads
```

---

## XAMPP Deployment

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Install with default settings
3. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Setup Web Root

```bash
# Copy project to XAMPP htdocs
xcopy C:\ParProOMH C:\xampp\htdocs\ParProOMH /E /I

# Or create symbolic link (Windows)
mklink /D C:\xampp\htdocs\ParProOMH C:\ParProOMH
```

### Step 3: Configure Virtual Host (Optional)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName partido.local
    DocumentRoot "C:\xampp\htdocs\ParProOMH"
    
    <Directory "C:\xampp\htdocs\ParProOMH">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 partido.local
```

### Step 4: Test Installation

1. Open browser and navigate to: `http://localhost/ParProOMH`
2. Login with: `admin@partido.com` / `Admin@123`
3. Access admin panel: `http://localhost/public/admin/dashboard.php`

---

## cPanel Deployment

### Step 1: Upload Files via cPanel File Manager

1. Log in to cPanel
2. Go to File Manager
3. Navigate to `public_html` directory
4. Create folder: `ParProOMH`
5. Upload all project files (use ZIP upload for speed)
6. Extract ZIP file in cPanel

### Step 2: Create MySQL Database

1. In cPanel, go to MySQL Databases
2. Create database: `partido_market`
3. Create user: `partido_user`
4. Set password with high entropy
5. Assign user to database with ALL privileges
6. Import `partido_market.sql`:
   - Go to phpMyAdmin in cPanel
   - Select the database
   - Import tab → Choose file → partido_market.sql → Execute

### Step 3: Update Configuration Files

Edit `config/database.php`:
```php
const DB_HOST = 'localhost'; // or specific host provided by cPanel
const DB_USER = 'cpaneluser_partido';
const DB_PASS = 'your_secure_password';
const DB_NAME = 'cpaneluser_partido_market';
```

Edit `config/constants.php`:
```php
define('BASE_URL', 'https://yourdomain.com/ParProOMH');
define('ENVIRONMENT', 'production');
```

### Step 4: Set File Permissions via SSH

```bash
# SSH into server
ssh user@yourdomain.com

# Navigate to project
cd public_html/ParProOMH

# Set correct permissions
chmod 755 config/
chmod 755 classes/
chmod 755 includes/
chmod 755 public/
chmod 777 assets/uploads/products/

# Secure sensitive directories
chmod 700 config/
```

### Step 5: Enable HTTPS

1. In cPanel, go to AutoSSL or Let's Encrypt
2. Install SSL certificate
3. Update `config/constants.php` to use HTTPS
4. In cPanel, set to redirect HTTP to HTTPS

### Step 6: Test cPanel Installation

1. Visit: `https://yourdomain.com/ParProOMH`
2. Admin login credentials: `admin@partido.com` / `Admin@123`

---

## Linux Server Deployment

### Step 1: SSH into Server

```bash
ssh -i /path/to/key.pem ubuntu@your-server-ip
```

### Step 2: Install Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache, MySQL, PHP
sudo apt install apache2 mysql-server php php-mysql php-mbstring php-xml php-json -y

# Start services
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql

# Install Composer (optional)
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 3: Clone/Upload Project

```bash
# Clone from Git (if available)
cd /var/www/html
sudo git clone https://github.com/your-repo/partido.git ParProOMH

# Or upload via SFTP
sftp user@server
put -r ./ParProOMH /var/www/html/
```

### Step 4: Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/ParProOMH

# Set permissions
sudo chmod 755 /var/www/html/ParProOMH
sudo chmod 755 /var/www/html/{config,classes,includes,public}
sudo chmod 777 /var/www/html/assets/uploads/products

# Secure sensitive directories
sudo chmod 700 /var/www/html/config
```

### Step 5: Create Apache Virtual Host

Create `/etc/apache2/sites-available/partido.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    ServerAdmin admin@yourdomain.com
    
    DocumentRoot /var/www/html/ParProOMH
    
    <Directory /var/www/html/ParProOMH>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/partido-error.log
    CustomLog ${APACHE_LOG_DIR}/partido-access.log combined
</VirtualHost>
```

Enable site and module:
```bash
sudo a2ensite partido.conf
sudo a2enmod rewrite headers ssl
sudo apache2ctl configtest
sudo systemctl reload apache2
```

### Step 6: Setup MySQL Database

```bash
# Log into MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE partido_market;
CREATE USER 'partido_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON partido_market.* TO 'partido_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u partido_user -p partido_market < /var/www/html/partido_market.sql
```

### Step 7: Install SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
sudo systemctl reload apache2
```

### Step 8: Test Installation

```bash
# Check Apache status
sudo systemctl status apache2

# Check MySQL status
sudo systemctl status mysql

# Test connectivity
curl https://yourdomain.com/public/index.php
```

---

## Database Setup

### Manual Database Creation

If automatic import fails:

```bash
# Connect to MySQL
mysql -u root -p

# Run these commands
CREATE DATABASE partido_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE partido_market;

# Import the SQL file
SOURCE /path/to/partido_market.sql;

# Verify
SHOW TABLES;
SELECT COUNT(*) FROM users;
```

### Backup Database

```bash
# Full backup
mysqldump -u partido_user -p partido_market > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup with compression
mysqldump -u partido_user -p partido_market | gzip > backup_$(date +%Y%m%d).sql.gz

# Restore backup
mysql -u partido_user -p partido_market < backup_file.sql
```

---

## Configuration

### Environment Variables

For production, create `.env` file (not in version control):

```bash
# Database
DB_HOST=localhost
DB_USER=partido_user
DB_PASS=secure_password
DB_NAME=partido_market

# App
BASE_URL=https://yourdomain.com/ParProOMH
ENVIRONMENT=production

# Mail (if using email notifications)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=your-email@gmail.com
MAIL_PASS=app_password

# Security
JWT_SECRET=your_jwt_secret_key
ENCRYPTION_KEY=your_encryption_key
```

### PHP Configuration

Recommended `php.ini` settings for production:

```ini
display_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /var/log/php/error.log

# File uploads
upload_max_filesize = 100M
post_max_size = 100M
max_file_uploads = 20

# Session
session.gc_maxlifetime = 3600
session.cookie_secure = On
session.cookie_httponly = On
session.cookie_samesite = Strict

# Performance
max_execution_time = 30
memory_limit = 256M
```

---

## Security Checklist

Before going live:

- [ ] Change default admin password
- [ ] Enable HTTPS/SSL certificate
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Hide sensitive files (.env, config files)
- [ ] Enable .htaccess protection on /classes and /includes
- [ ] Set up firewall rules
- [ ] Configure fail2ban for brute force protection
- [ ] Enable CSRF token validation
- [ ] Validate all user inputs
- [ ] Use prepared statements for all DB queries
- [ ] Remove debug mode in production
- [ ] Set up database backups (daily recommended)
- [ ] Set up error logging and monitoring
- [ ] Configure Content Security Policy headers
- [ ] Enable HTTP/2 if supported
- [ ] Set up regular security updates

### .htaccess Security Rules

Root `.htaccess`:
```apache
# Prevent access to config files
<FilesMatch "^(config|includes|classes)/">
    Deny from all
</FilesMatch>

# Disable directory listing
Options -Indexes

# Enable mod_rewrite
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
</IfModule>
```

---

## Performance Optimization

### Caching

1. **Opcode Cache (PHP)**
   ```bash
   sudo apt install php-opcache
   # Enable in php.ini
   ```

2. **Browser Caching**
   ```apache
   <IfModule mod_expires.c>
       ExpiresActive On
       ExpiresByType image/jpeg "access plus 1 year"
       ExpiresByType image/png "access plus 1 year"
       ExpiresByType text/css "access plus 1 month"
       ExpiresByType application/javascript "access plus 1 month"
   </IfModule>
   ```

3. **Database Query Optimization**
   ```bash
   # Add indexes to frequently queried columns
   mysql> ALTER TABLE deals ADD INDEX (status);
   mysql> ALTER TABLE products ADD INDEX (seller_id);
   mysql> ALTER TABLE ratings ADD INDEX (seller_id);
   ```

### Monitoring

Set up monitoring with tools like:
- New Relic
- Datadog
- Scout
- Application Performance Monitoring (APM)

Monitor:
- Page load times
- Database query performance
- Server resource usage
- Error rates

---

## Troubleshooting

### Common Issues

#### Issue: "Access Denied" error
```
Solution: Check database credentials in config/database.php
```

#### Issue: White screen of death
```
Solution: 
1. Check error logs: tail -f /var/log/apache2/error.log
2. Enable display_errors in development
3. Check file permissions
```

#### Issue: Upload fails
```
Solution:
1. Check upload permissions: chmod 777 assets/uploads/products
2. Verify upload_max_filesize in php.ini
3. Check disk space availability
```

#### Issue: Slow page load
```
Solution:
1. Enable Opcode cache
2. Add database indexes
3. Enable browser caching
4. Optimize images
5. Use CDN for static assets
```

#### Issue: Database connection fails
```
Solution:
1. Verify MySQL service is running: sudo systemctl status mysql
2. Check database credentials
3. Verify database exists: mysql -u user -p -e "SHOW DATABASES;"
4. Check MySQL user permissions: mysql> SELECT * FROM mysql.user;
```

### Debug Mode

To enable detailed error reporting (development only):

Edit `config/constants.php`:
```php
define('DEBUG_MODE', true);
```

This will display detailed error messages instead of generic errors.

---

## Support & Maintenance

### Regular Maintenance Tasks

- Weekly: Check error logs, verify backups
- Monthly: Run security updates, review performance metrics
- Quarterly: Full security audit, database optimization
- Annually: SSL certificate renewal, capacity planning

### Getting Help

- Check application logs: `/var/log/apache2/error.log`
- Check PHP logs: `/var/log/php/error.log`
- MySQL logs: `/var/log/mysql/error.log`

---

## Additional Resources

- [Apache Documentation](https://httpd.apache.org/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)

---

**Last Updated:** April 2026  
**Version:** 1.0.0
