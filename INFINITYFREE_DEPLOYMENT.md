# InfinityFree Deployment Guide
## Partido Product Online Market Hub

This guide provides step-by-step instructions to deploy the Partido marketplace application to InfinityFree hosting.

---

## Your InfinityFree Account Details

| Setting | Value |
|---------|-------|
| Domain | partido-online-market-hub.page.gd |
| Directory | htdocs |
| MySQL Host | sql100.infinityfree.com |
| MySQL User | if0_41733042 |
| MySQL Database | if0_41733042_partido_market |

---

## Step 1: Database Setup

### 1.1 Import Database Schema

1. Log in to InfinityFree Control Panel
2. Go to **phpMyAdmin**
3. Select database: `if0_41733042_partido_market`
4. Go to **Import** tab
5. Upload and import `INFINITYFREE_SETUP.sql`
6. Click **Go** to execute

---

## Step 2: Configure Files (ALREADY COMPLETE)

The following files have been pre-configured for your InfinityFree account:

### config/database.php
```php
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_41733042');
define('DB_PASS', 'YOUR_MYSQL_PASSWORD_HERE'); // ← REPLACE WITH YOUR PASSWORD
define('DB_NAME', 'if0_41733042_partido_market');
```

### config/constants.php
```php
define('BASE_URL', 'https://partido-online-market-hub.page.gd');
define('ENVIRONMENT', 'production');
define('SKIP_EMAIL_VERIFICATION', false);
```

**IMPORTANT**: Replace `YOUR_MYSQL_PASSWORD_HERE` in `config/database.php` with your actual MySQL password from InfinityFree.

---

## Step 3: Upload Files

### 3.1 Create ZIP Package

Create a ZIP file containing all project files:
- `assets/`
- `classes/`
- `config/`
- `includes/`
- `public/`
- `logs/`
- `.htaccess`
- `INFINITYFREE_SETUP.sql`
- All `.sql` files
- All `.md` files

**EXCLUDE these template files:**
- `config/database.infinityfree.php`
- `config/constants.infinityfree.php`
- `INFINITYFREE_DEPLOYMENT.md`

### 3.2 Upload via File Manager

1. Go to InfinityFree Control Panel → **File Manager**
2. Navigate to `htdocs/` folder
3. Click **Upload**
4. Select your ZIP file
5. After upload, right-click ZIP → **Extract**

### 3.3 Set File Permissions

Set these permissions in File Manager:

| Folder | Permission |
|-------|------------|
| `config/` | 755 |
| `classes/` | 755 |
| `includes/` | 755 |
| `public/` | 755 |
| `assets/` | 755 |
| `assets/uploads/products/` | 777 |

Right-click each folder → **Permissions** → Set value.

---

## Step 4: Test Deployment

1. Visit: `https://partido-online-market-hub.page.gd`
2. Login with admin credentials:
   - Email: `admin@partido.com`
   - Password: `Admin@123`
3. Access admin panel:
   - URL: `https://partido-online-market-hub.page.gd/public/admin/dashboard.php`

---

## Troubleshooting

### Issue: "500 Internal Server Error"
- Check file permissions (755 for folders)
- Verify `.htaccess` syntax
- Check Error Log in Control Panel

### Issue: "Database Connection Failed"
- Verify password in `config/database.php`
- Ensure database exists in MySQL

### Issue: Blank Page
- Enable error reporting in `config/constants.php`:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```

---

## Post-Deployment Checklist

- [ ] Replace MySQL password in `config/database.php`
- [ ] Verify database connection
- [ ] Test admin login
- [ ] Test user registration
- [ ] Set file permissions (especially `assets/uploads/products/`)
- [ ] Enable free SSL in Control Panel

---

## Quick Reference

| Item | Value |
|------|-------|
| Admin Email | admin@partido.com |
| Admin Password | Admin@123 |
| Database File | INFINITYFREE_SETUP.sql |
| Control Panel | https://www.infinityfree.com/panel |

---

**Last Updated:** 2026
**Version:** 1.0.0
