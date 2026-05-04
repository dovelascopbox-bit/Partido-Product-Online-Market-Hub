# ADMIN PANEL - QUICK REFERENCE & USER GUIDE

**Version**: 1.0.0  
**Last Updated**: April 21, 2026  
**Status**: Production Ready  

---

## Admin Panel Access

### Login Credentials

```
URL: http://localhost/public/login.php
Default Admin Email: admin@partido.com
Default Admin Password: Admin@123

⚠️ IMPORTANT: Change these credentials immediately in production!
```

### Navigation

After login, admins see the sidebar with following sections:

```
📊 DASHBOARD          → Main overview with metrics
👥 USER MANAGEMENT   → View, filter, and manage users
📦 PRODUCTS          → Manage all platform products
🤝 DEALS             → Track marketplace deals
⭐ RATINGS           → Moderate user reviews
🚩 FLAGS             → Handle content reports
📈 ANALYTICS         → Platform statistics & trends
```

---

## Dashboard Overview

### Dashboard Features

**Metrics Cards** (6 total):
- 👤 Total Users - Count of all registered users
- 🛍️ Total Sellers - Active seller accounts
- 🏪 Total Buyers - Active buyer accounts  
- 📦 Total Products - Products listed on platform
- 🤝 Active Deals - Ongoing transactions
- 🚩 Flagged Items - Items awaiting review

**Quick Actions**:
- Add New Admin
- View Latest Reports
- Generate Report
- System Settings

**System Information**:
- Platform Name
- Current Stage/Version
- Session ID
- Admin Name & Email

---

## User Management

### Access: Admin Panel → User Management

### Features

**User Table**:
- User ID, Username, Email, Full Name
- Role (Buyer/Seller/Admin)
- Account Status (Active/Suspended)
- Join Date & Last Activity

**Filters**:
```
Filter by Role: Buyer | Seller | Admin
Filter by Status: Active | Suspended
Search: By email or username
Pagination: 20 users per page
```

### Common Tasks

#### Task 1: Find a User
1. Use search box with email or username
2. View results
3. Click user row for details

**Example**: Search "john@email.com" → Displays John's profile

#### Task 2: Suspend a User
1. Locate user in table
2. Click "Suspend" button
3. Confirm in dialog
4. Action logged in audit trail

**When to use**: User violating terms of service, suspicious activity, payment issues

#### Task 3: Reactivate a User
1. Filter Status = "Suspended"
2. Click "Reactivate" button
3. Confirm action
4. User account restored

**Effect**: User can login and use platform again

#### Task 4: View User Details
1. Click user row
2. See profile, transaction history, ratings

---

## Product Management

### Access: Admin Panel → Product Management

### Features

**Product Table**:
- Product ID & Name
- Seller Name & Contact
- Category & Price
- Quantity & Status
- Listed Date
- Action Buttons

**Operations**:
```
Search: Find product by name
Filter: By category, status
Sort: By price, date, seller
```

### Common Tasks

#### Task 1: Search for Product
1. Enter product name in search
2. View matching results
3. See all product details

**Example**: Search "iPhone" → Shows all iPhone listings

#### Task 2: Remove Product
1. Click "Remove" button next to product
2. Enter reason for removal
3. Confirm action
4. Product deleted, seller notified

**Reasons to Remove**:
- Inappropriate content
- Counterfeit/fraud
- Duplicate listing
- Policy violation
- Low quality/scam

#### Task 3: Check Seller Profile
1. Click seller name in product row
2. View seller's profile
3. See seller's rating and other products
4. Check buyer feedback

#### Task 4: Bulk Actions
1. Select multiple products
2. Choose action (Remove All)
3. Enter reason
4. Confirm batch operation

---

## Deal Management

### Access: Admin Panel → Deal Management

### Features

**Deal Table**:
- Deal ID & Reference
- Buyer & Seller Names
- Product Name
- Deal Amount
- Deal Status
- Created & Completed Dates

**Filters**:
```
Status: Pending | Confirmed | Completed | Disputed
Date Range: Custom date selection
Seller/Buyer: Filter by participant
```

### Deal Statuses

| Status | Meaning | Admin Action |
|--------|---------|--------------|
| Pending | Awaiting confirmation | Monitor |
| Confirmed | Both parties agreed | Monitor |
| Completed | Transaction finished | Verify |
| Disputed | Parties in conflict | Investigate |

### Common Tasks

#### Task 1: Monitor Active Deals
1. Go to Deal Management
2. Filter: Status = "Confirmed"
3. View all active transactions
4. Check for unusual activity

#### Task 2: Investigate Disputed Deal
1. Filter: Status = "Disputed"
2. Click deal for details
3. Review buyer/seller messages
4. Make resolution decision
5. Take action (refund, resolve)

#### Task 3: Track Completion
1. Filter: Status = "Completed"
2. View deal timeline
3. Verify ratings left by both parties
4. Ensure transaction closed properly

#### Task 4: Monitor Trends
1. Access Analytics page
2. View deal completion rate
3. Check for delays or issues
4. Monitor platform health

---

## Ratings & Reviews Management

### Access: Admin Panel → Ratings

### Features

**Ratings Table**:
- Rating ID & Review Text
- Seller Name & Product
- Star Rating (1-5 stars)
- Buyer Name & Date
- Flagged Status
- Action Buttons

**Operations**:
```
Flag: Mark review as inappropriate
Remove: Delete review from platform
View: See full review text
```

### Common Tasks

#### Task 1: View All Ratings
1. Go to Ratings Management
2. See all platform reviews
3. Sort by date, rating, seller
4. Search by seller name

#### Task 2: Flag Inappropriate Review
1. Locate review
2. Click "Flag" button
3. System marks for review
4. Moves to Flag Reports

**When to flag**:
- Abusive language
- Off-topic content
- Suspicious/fake review
- Personal information shared
- Policy violation

#### Task 3: Remove Bad Review
1. Click "Remove" button
2. Enter removal reason
3. Confirm action
4. Reason shown to seller

**Valid removal reasons**:
- Violates conduct policy
- Suspicious activity
- False claims
- Abusive language
- Spam/advertising

#### Task 4: Protect Seller Rating
1. Flag inappropriate reviews
2. Remove violating reviews
3. Help maintain fair ratings
4. Monitor for rating attacks

---

## Content Moderation (Flags)

### Access: Admin Panel → Flags

### Features

**Pending Reports Table**:
- Report ID & Type (Product/Seller/Rating)
- Item Name/ID
- Reporter & Date
- Reason for Report
- Current Status

**Review Modal**:
- Full report details
- Reporter comments
- Admin notes field
- Three action options

### Report Types

| Type | What | Who Reports |
|------|------|-------------|
| Product | Inappropriate product | Buyers/Sellers |
| Seller | Problematic seller | Buyers |
| Rating | Fake/abusive review | Sellers |

### Common Tasks

#### Task 1: Review Pending Reports
1. Go to Flags page
2. View all pending reports
3. Oldest reports listed first
4. Click "Review" on report

#### Task 2: Investigate Report
1. Click "Review" button
2. Read full report details
3. Check report reason
4. Review any comments
5. Enter your admin notes

**Investigation Checklist**:
- [ ] Is report valid?
- [ ] Is content harmful?
- [ ] Are there patterns?
- [ ] Should item be removed?

#### Task 3: Dismiss Report
1. Review report
2. Determine report invalid
3. Click "Dismiss"
4. Move to completed reports
5. No item removal

**When to dismiss**:
- Report seems false
- No policy violation
- Misunderstanding
- Legitimate content

#### Task 4: Remove & Resolve Report
1. Review report
2. Confirm policy violation
3. Click "Remove"
4. Confirm final action
5. Item deleted, user notified
6. Report marked resolved

**When to remove**:
- Clear policy violation
- Content is harmful
- Item is counterfeit
- Seller ban needed

#### Task 5: Keep & Note
1. Review report
2. Keep item but note issue
3. Enter warning in notes
4. Monitor seller/buyer
5. Take future action if needed

---

## Analytics & Reporting

### Access: Admin Panel → Analytics

### Dashboard Sections

**1. User Growth Chart**
- Shows 12-month user registration trend
- Interactive line chart
- Hover for specific month data
- Export data option

**2. Deal Activity Chart**
- Monthly deal completion rate
- Bar chart visualization
- Shows business growth
- Compare month-over-month

**3. Deal Status Distribution**
- Pie chart of pending/completed/disputed
- Shows platform health
- Click segment for details
- Drill-down capability

**4. Top Sellers Table**
- Top 5 best-performing sellers
- Rating & review count
- Total sales volume
- Contact information

**5. Platform Health**
- Deal completion rate percentage
- Average seller rating
- Trends (up/down arrows)
- Quick status overview

**6. Export Features**
- Export data as CSV
- Generate PDF reports
- Schedule weekly reports
- Email reports to team

### Common Tasks

#### Task 1: Monitor User Growth
1. Go to Analytics
2. View user growth chart
3. Check trend (up/down)
4. Identify seasonal patterns

**What to look for**:
- Consistent growth rate
- Sudden spikes or drops
- Growth vs. retention
- Seasonal trends

#### Task 2: Check Business Health
1. View deal completion rate
2. Check average seller rating
3. Monitor dispute rate
4. Assess platform activity

**Healthy indicators**:
- > 85% deal completion rate
- > 4.2 average rating
- < 5% dispute rate
- Growing user base

#### Task 3: Identify Top Performers
1. View Top Sellers table
2. See best-rated sellers
3. Check their sales volume
4. Consider for promotion/featured

#### Task 4: Generate Reports
1. Select date range
2. Choose metrics
3. Click "Generate Report"
4. Export as CSV/PDF
5. Share with team

---

## Common Admin Workflows

### Workflow 1: Handle User Complaint

```
1. User submits complaint via email/form
2. Go to Flags/Reports section
3. Find corresponding report
4. Click "Review"
5. Investigate issue
6. Decide: Dismiss | Keep | Remove
7. Enter notes explaining decision
8. Notify user of outcome
9. Document in audit log
```

### Workflow 2: Manage Problematic Seller

```
1. Receive complaint or flag report
2. Go to User Management
3. Search for seller
4. Review their profile & history
5. Check their ratings/reviews
6. Check their products
7. If issues found:
   - Remove problematic products
   - Add warning to account
   - If severe: Suspend seller
8. Monitor for repeat issues
```

### Workflow 3: Platform Performance Review

```
Weekly Review Process:
1. Check dashboard metrics
2. Review pending reports
3. Monitor deal completion
4. Check user growth
5. Review suspended accounts
6. Generate weekly report
7. Meeting with team
8. Plan improvements
```

### Workflow 4: Content Moderation Shift

```
Daily Moderation:
1. Start of shift: Check queue
2. Review 10-20 pending reports
3. Investigate each
4. Take action (dismiss/remove)
5. Add notes to each
6. Mid-shift break
7. Continue with remaining queue
8. End of shift report
9. Document decisions
```

---

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl + K` | Open search |
| `Ctrl + M` | Messages |
| `Ctrl + L` | Logout |
| `Esc` | Close modal |
| `Enter` | Submit form |
| `Tab` | Next field |

---

## Common Issues & Solutions

### Issue 1: Cannot Login

**Error**: "Invalid credentials"

**Solutions**:
1. Verify email is correct
2. Check Caps Lock is OFF
3. Reset password via email
4. Contact admin support

### Issue 2: Page Loads Slowly

**Cause**: Large dataset

**Solutions**:
1. Use filters to narrow results
2. Change pagination size
3. Clear browser cache
4. Check internet connection

### Issue 3: Changes Not Showing

**Cause**: Cache issue

**Solutions**:
1. Refresh browser (F5)
2. Hard refresh (Ctrl + Shift + R)
3. Clear cookies
4. Close and reopen browser

### Issue 4: Cannot Remove Item

**Cause**: Insufficient permissions

**Solutions**:
1. Verify admin role
2. Check if item already removed
3. Contact super admin
4. Check error logs

---

## Best Practices

### Security

✅ DO:
- Change default password immediately
- Use strong password (12+ characters)
- Logout when away from desk
- Review audit logs regularly
- Never share login credentials

❌ DON'T:
- Leave admin session open
- Share admin account
- Write password on sticky notes
- Log in on public WiFi
- Click suspicious links

### Moderation

✅ DO:
- Review all reports thoroughly
- Document decisions in notes
- Follow consistent policies
- Notify users of actions
- Monitor for patterns

❌ DON'T:
- Make quick decisions
- Remove without verification
- Be biased for/against users
- Ignore repeat violators
- Act on incomplete information

### Data Protection

✅ DO:
- Verify backups running
- Monitor disk space
- Keep software updated
- Log security incidents
- Review access logs

❌ DON'T:
- Share user data
- Store passwords in plain text
- Access user data unnecessarily
- Disable security features
- Ignore security warnings

---

## Useful Queries

### Check Recent Admin Actions
```sql
SELECT * FROM admin_actions ORDER BY created_at DESC LIMIT 10;
```

### Find All Suspended Users
```sql
SELECT * FROM users WHERE status = 'suspended';
```

### Count Platform Metrics
```sql
SELECT 
  (SELECT COUNT(*) FROM users) as total_users,
  (SELECT COUNT(*) FROM products) as total_products,
  (SELECT COUNT(*) FROM deals) as total_deals;
```

### Check Pending Reports
```sql
SELECT * FROM flag_reports WHERE status = 'pending' ORDER BY created_at;
```

### Find Top Sellers
```sql
SELECT seller_id, COUNT(*) as deal_count, AVG(stars) as rating
FROM deals d JOIN ratings r ON d.id = r.deal_id
GROUP BY seller_id
ORDER BY rating DESC LIMIT 5;
```

---

## Support & Documentation

### Resources

- **Full API Reference**: See [API_REFERENCE.md](API_REFERENCE.md)
- **Deployment Guide**: See [DEPLOYMENT.md](DEPLOYMENT.md)
- **Testing Guide**: See [STAGE6_TESTING_VERIFICATION.md](STAGE6_TESTING_VERIFICATION.md)
- **Project Overview**: See [README.md](README.md)

### Getting Help

- **Documentation**: Check README.md first
- **Error Logs**: Check `/error_log` for details
- **Database**: Direct SQL queries via phpMyAdmin
- **Support**: Contact development team

---

## Admin Panel Checklist

### Daily Checklist

- [ ] Check pending reports in Flags
- [ ] Review suspended user accounts
- [ ] Monitor deal completion rate
- [ ] Check error logs
- [ ] Verify recent admin actions
- [ ] Respond to escalated issues

### Weekly Checklist

- [ ] Generate analytics report
- [ ] Review top sellers
- [ ] Audit removed products
- [ ] Check user growth
- [ ] Verify backup status
- [ ] Team meeting review

### Monthly Checklist

- [ ] Full security audit
- [ ] Performance review
- [ ] Update documentation
- [ ] Plan improvements
- [ ] Review policies
- [ ] Forecast growth

---

## Tips & Tricks

### Tip 1: Filter Efficiently
Use multiple filters together to narrow results quickly:
```
Role: Seller + Status: Active + Search: "New York"
= All active sellers in New York
```

### Tip 2: Batch Operations
Select multiple items at once for faster processing:
```
Ctrl+Click to select multiple rows
Then: Remove All, Suspend All, etc.
```

### Tip 3: Export Data
Export for analysis, reporting, or backup:
```
View → Export → Choose format (CSV/PDF)
Great for external reporting
```

### Tip 4: Monitor Trends
Use analytics to spot patterns:
```
Declining completion rate = Issues
Spiking reports = Fraud attempt
Rising user count = Success
```

---

## Contact & Support

**For Technical Issues**:
- Email: admin@partido.com
- Phone: [Support Number]
- Slack: #admin-support

**For Feature Requests**:
- Submit via admin dashboard
- Email: features@partido.com
- Sprint planning meeting

**For Security Issues**:
- Email: security@partido.com (URGENT)
- Mark as CONFIDENTIAL
- Do not share publicly

---

**Last Updated**: April 21, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
