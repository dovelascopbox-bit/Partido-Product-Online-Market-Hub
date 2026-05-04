# Partido Product Online Market Hub - Stage 2 Complete

## 🎯 Project Overview

Partido is a local marketplace web application for buying and selling products locally (no payment/delivery integration). Built with PHP 8+ OOP, MySQL, and Tailwind CSS.

**Stage 2 Focus**: Seller product management and buyer marketplace discovery

---

## ✅ What's Included in Stage 2

### Core Features
- ✅ **Product Management**: Create, Read, Update, Delete (CRUD) products
- ✅ **Image Upload**: Drag-drop interface with MIME validation and size limits
- ✅ **Status Toggle**: Mark products available/unavailable with instant AJAX updates
- ✅ **Buyer Marketplace**: Browse, search, and sort available products
- ✅ **Deal Infrastructure**: Backend skeleton for buyer-seller deals
- ✅ **Security**: CSRF protection, prepared statements, input sanitization

### Database
- ✅ Products table (srp, image_path, status enum)
- ✅ Deals table (dual confirmation mechanism)
- ✅ Full foreign key relationships

### Backend Classes
- ✅ **Product.php** (340 lines) - Full CRUD with ownership verification
- ✅ **Deal.php** (245 lines) - Deal lifecycle management

### Pages Created
- ✅ `/public/seller/products/add.php` - Create product
- ✅ `/public/seller/products/edit.php` - Edit product
- ✅ `/public/seller/products/list.php` - Product dashboard
- ✅ `/public/seller/products/delete.php` - Delete handler
- ✅ `/public/seller/deals.php` - Deals management
- ✅ `/public/buyer/marketplace.php` - Product discovery
- ✅ Updated dashboards with links and stats

---

## 📁 File Structure

```
ParProOMH/
├── classes/
│   ├── Product.php (340 lines)
│   ├── Deal.php (245 lines)
│   └── Auth.php
├── includes/
│   ├── functions.php (5 new file upload functions)
│   └── init.php
├── public/
│   ├── seller/
│   │   ├── dashboard.php (updated)
│   │   ├── products/
│   │   │   ├── add.php (430 lines)
│   │   │   ├── edit.php (380 lines)
│   │   │   ├── list.php (420 lines)
│   │   │   └── delete.php (50 lines)
│   │   └── deals.php (280 lines)
│   ├── buyer/
│   │   ├── dashboard.php (updated)
│   │   └── marketplace.php (280 lines)
│   └── index.php
├── assets/
│   ├── uploads/products/ (product images)
│   ├── css/main.css
│   └── images/
├── partido_market.sql (updated schema)
├── STAGE_2_COMPLETION.md
├── STAGE_2_CHECKLIST.md
├── TESTING_GUIDE.md
├── API_REFERENCE.md
└── README.md (this file)
```

---

## 🚀 Quick Start

### 1. Database Setup
```bash
# Import the SQL schema
mysql -u root -p < partido_market.sql

# Or in MySQL prompt:
mysql> source c:\Users\velas\Desktop\ParProOMH\partido_market.sql;
```

### 2. Verify Installation
```bash
# Check application accessible at:
http://localhost/public/index.php
```

### 3. Test with Sample Data
- **Seller Account**: seller@test.com
- **Buyer Account**: buyer@test.com
- **Admin Account**: admin@test.com
(Use credentials from Stage 1 setup)

### 4. Run Test Scenario
See [TESTING_GUIDE.md](TESTING_GUIDE.md) for detailed test cases

---

## 🔑 Key Features Explained

### Product Lifecycle
```
Seller Creates Product
    ↓
Product Status: Available
    ↓
Buyer Sees in Marketplace
    ↓
Seller Toggles Unavailable
    ↓
Buyer No Longer Sees
    ↓
Seller Deletes (if needed)
    ↓
Product Removed + Image Deleted
```

### AJAX Status Toggle
- Click "Hide" button on product
- Status changes instantly without page reload
- JavaScript fetch() sends POST request
- Server returns JSON response
- UI updates immediately

### Image Upload System
- Drag-drop or click to upload
- Validates MIME type (jpg/png/webp only)
- Enforces 2MB size limit
- Stores with seller_id prefix: `seller_1_randomhash.jpg`
- Fallback to placeholder if missing

### Marketplace Filtering
- Shows only `status='available'` products
- Requires `quantity > 0`
- Includes seller shop name and rating
- Search filters by product name/description
- Sort by price or date

---

## 🛡️ Security Features

| Feature | Implementation |
|---------|-----------------|
| CSRF Protection | Tokens on all forms |
| SQL Injection | PDO prepared statements |
| XSS Prevention | htmlspecialchars + filter_var |
| File Upload | MIME validation + size limits |
| Access Control | Role-based middleware |
| Ownership Verification | Seller ID checks |
| Password Security | bcrypt hashing |
| Session Management | Secure session handling |

---

## 📊 API Reference

### Product Class
```php
$product = new Product($pdo);

// Create
$id = $product->create($seller_id, $name, $desc, $price, $qty, $img_path);

// Read
$data = $product->getById($product_id);
$all = $product->getBySellerID($seller_id);
$available = $product->getAvailableProducts();

// Update
$product->update($product_id, $seller_id, ['field' => 'value']);

// Delete
$product->delete($product_id, $seller_id);

// Search & Stats
$results = $product->search($keyword);
$count = $product->getSellerProductCount($seller_id);
$toggle = $product->toggleStatus($id, $seller_id);
```

### Deal Class
```php
$deal = new Deal($pdo);

// Create & Retrieve
$id = $deal->create($product_id, $buyer_id, $seller_id);
$data = $deal->getById($deal_id);
$ongoing = $deal->getBySellerID($seller_id, 'ongoing');

// Confirm Flow
$deal->sellerConfirmDeal($deal_id, $seller_id);
$deal->buyerConfirmDeal($deal_id, $buyer_id);
$result = $deal->checkDealCompletion($deal_id);

// Cancel
$deal->cancel($deal_id, $user_id, 'seller'|'buyer');
```

See [API_REFERENCE.md](API_REFERENCE.md) for complete documentation.

---

## 📋 Testing Checklist

Use [TESTING_GUIDE.md](TESTING_GUIDE.md) for comprehensive testing. Quick checklist:

- [ ] Add 3 products with images
- [ ] Edit product (change price, upload new image)
- [ ] Toggle product status without page reload
- [ ] Verify unavailable products hidden from marketplace
- [ ] Search for products
- [ ] Sort by price and date
- [ ] Delete product (verify image removed)
- [ ] Marketplace shows only available products
- [ ] Database queries execute without errors

---

## 🎨 User Experience

### For Sellers
1. Dashboard shows product and deal counts
2. Quick links to manage products and deals
3. Easy product creation with image upload
4. Edit/delete from product list
5. One-click status toggle
6. Deal management interface

### For Buyers
1. Search and discover products
2. See seller information and ratings
3. Sort products by price/date
4. Initiate deals (Stage 3)
5. Manage active deals (Stage 3)
6. Leave ratings (Stage 3)

---

## 🔮 Stage 3 Preview

**Coming Next:**
- Product detail page with full images and description
- Deal initiation workflow (buyer clicks "Deal")
- Real-time messaging between buyer/seller
- Deal confirmation UI
- Automatic deal completion when both confirm
- Rating and review system
- Buyer's deal management page
- Advanced filters and search

---

## 🐛 Troubleshooting

### "Product not appearing in marketplace"
- Check product status is 'available' in database
- Verify quantity > 0
- Clear browser cache (Ctrl+Shift+Delete)

### "AJAX toggle not working"
- Check browser console (F12) for errors
- Verify CSRF token present in form
- Check Network tab for failed requests

### "Image upload failing"
- Verify `/assets/uploads/products/` directory exists
- Check directory permissions (chmod 755)
- Verify file is under 2MB
- Use jpg, png, or webp format

### "Database connection error"
- Verify MySQL running
- Check database name: `partido_market`
- Verify tables imported correctly
- Check `includes/config.php` credentials

---

## 📚 Documentation

- **[STAGE_2_COMPLETION.md](STAGE_2_COMPLETION.md)** - Feature summary
- **[STAGE_2_CHECKLIST.md](STAGE_2_CHECKLIST.md)** - Implementation checklist
- **[TESTING_GUIDE.md](TESTING_GUIDE.md)** - Detailed test scenarios
- **[API_REFERENCE.md](API_REFERENCE.md)** - Class and function reference
- **[partido_market.sql](partido_market.sql)** - Database schema

---

## 💡 Code Quality

✅ **OOP Design** - All features in classes
✅ **Separation of Concerns** - Database logic separate from views
✅ **Error Handling** - Try-catch with user feedback
✅ **Security** - CSRF, prepared statements, validation
✅ **Performance** - Indexes on key columns
✅ **Scalability** - Ready for Stage 3 features
✅ **Mobile Responsive** - Works on all devices
✅ **Accessibility** - Semantic HTML, alt text

---

## 📞 Support

For issues or questions about Stage 2 features:
1. Check [TESTING_GUIDE.md](TESTING_GUIDE.md) troubleshooting section
2. Review [API_REFERENCE.md](API_REFERENCE.md) for correct usage
3. Check browser console (F12) for JavaScript errors
4. Verify database via provided SQL queries
5. Review code comments in source files

---

## 🎓 Learning Resources

This project demonstrates:
- PDO prepared statements for security
- Object-oriented PHP design patterns
- AJAX for seamless UX
- File upload validation
- Role-based access control
- Responsive CSS with Tailwind
- Database design with foreign keys
- Flash messages for user feedback

---

## 📄 License

Part of Partido Product Online Market Hub educational project.

---

## ✨ Next Steps

1. **Test thoroughly** using [TESTING_GUIDE.md](TESTING_GUIDE.md)
2. **Review code** in classes and pages
3. **Understand architecture** from [API_REFERENCE.md](API_REFERENCE.md)
4. **Plan Stage 3** features with product detail page and messaging

---

**Status**: ✅ Stage 2 Complete and Ready for Testing

**Date Completed**: 2024
**Version**: 2.0

---

Last Updated: 2024
