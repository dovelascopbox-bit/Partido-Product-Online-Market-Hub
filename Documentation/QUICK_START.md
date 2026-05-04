# QUICK START GUIDE - Partido Market Hub

## 5-Minute Setup

### 1. Copy Project
```
Copy ParProOMH folder to: C:\xampp\htdocs\
```

### 2. Start XAMPP
- Open XAMPP Control Panel
- Start Apache
- Start MySQL

### 3. Import Database
```
1. Go to http://localhost/phpmyadmin
2. Click "Import" tab
3. Select: ParProOMH/partido_market.sql
4. Click "Import"
```

### 4. Access Application
```
http://localhost/public/
```

---

## 🧪 Quick Test - 3 Minutes

### Login as Admin
```
Email:    admin@partido.com
Password: Admin@123

Expected Result:
✅ Admin Dashboard loads
✅ Shows user/seller/buyer stats
✅ "Logout" button appears in top right
```

### Create Test Seller
```
1. Click "Register" button
2. Select "Seller"
3. Fill form:
   - Username: seller123
   - Full Name: Test Seller
   - Email: seller@test.com
   - Password: TestPass123
4. Click "Create Account"

Expected Result:
✅ "Registration successful" message
✅ Login link appears
✅ Can login and see Seller Dashboard
```

### Create Test Buyer
```
1. Click "Register" button
2. Select "Buyer"
3. Fill form:
   - Username: buyer123
   - Full Name: Test Buyer
   - Email: buyer@test.com
   - Password: TestPass123
4. Click "Create Account"

Expected Result:
✅ "Registration successful" message
✅ Can login and see Buyer Dashboard
```

---

## 📋 File Checklist

```
✅ config/database.php          - Database config
✅ config/constants.php         - App constants
✅ classes/Auth.php             - Authentication
✅ includes/init.php            - App initialization
✅ includes/functions.php       - Security functions
✅ includes/logout.php          - Logout handler
✅ public/index.php             - Landing page
✅ public/login.php             - Login form
✅ public/register.php          - Registration form
✅ public/admin/dashboard.php   - Admin panel
✅ public/seller/dashboard.php  - Seller panel
✅ public/buyer/dashboard.php   - Buyer panel
✅ assets/css/main.css          - Styles
✅ partido_market.sql           - Database
✅ README.md                    - Full documentation
```

---

## 🔑 Demo Credentials

### Admin Account
```
Email:    admin@partido.com
Password: Admin@123
```

---

## 🆘 Common Issues

### "Database Connection Failed"
```
Solution:
1. Ensure MySQL is running
2. Check config/database.php credentials
3. Verify database imported successfully
```

### "Blank Page"
```
Solution:
1. Clear browser cache (Ctrl+Shift+Delete)
2. Check PHP error log
3. Ensure all files uploaded
```

### "Forms Not Working"
```
Solution:
1. Check browser console for errors
2. Verify POST method
3. Clear cache and try again
```

---

## 📞 Need Help?

See full README.md for:
- Complete setup instructions
- Database schema details
- All security features
- 10 comprehensive test scenarios
- Troubleshooting guide
- Technology stack info
- Future roadmap

---

**You're ready to go!** 🚀

Next steps:
1. Register a seller account
2. Register a buyer account
3. Test role-based dashboards
4. Review README.md for advanced features
