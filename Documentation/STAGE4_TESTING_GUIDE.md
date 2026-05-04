# Stage 4 - In-App Messenger System - Testing & Documentation

## Overview
Stage 4 implements the in-app messenger system for deal negotiations. Buyers and sellers communicate to arrange meetups, negotiate prices, and exchange transaction details - no payments on the platform.

## Architecture

### Database Tables
```sql
conversations:
  - conversation_id (PK)
  - deal_id (FK, UNIQUE)
  - buyer_id (FK)
  - seller_id (FK)
  - created_at
  - updated_at

messages:
  - message_id (PK)
  - conversation_id (FK)
  - sender_id (FK)
  - message_text (TEXT)
  - is_read (BOOL)
  - sent_at
```

### Classes & Methods

#### Messenger.php
```php
createConversation($deal_id)              // Create or return existing conversation
getConversations($user_id)                // Get all conversations for user
getMessages($conversation_id, $limit)     // Get messages paginated
getNewMessages($conversation_id, $user_id) // Get unread messages
sendMessage($conversation_id, $sender_id, $message_text) // Send message
markAsRead($conversation_id, $user_id)    // Mark messages as read
checkAccess($conversation_id, $user_id)   // Verify user is participant
getConversationDetails($conversation_id)  // Get full conversation info
getUnreadCount($user_id)                  // Get total unread count
```

### Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| /public/messenger/index.php | GET | List all conversations |
| /public/messenger/conversation.php?deal_id=X | GET | Open conversation view |
| /public/messenger/send.php | POST | Send message (AJAX) |
| /public/messenger/fetch.php | GET | Fetch new messages (polling) |

---

## User Workflow

### Complete Flow
1. **Buyer** browses marketplace `/public/buyer/market.php`
2. **Buyer** clicks product → `/public/buyer/product.php?id=X`
3. **Buyer** clicks "🤝 INITIATE DEAL" button
4. **System** creates deal and conversation
5. **System** redirects to `/public/messenger/conversation.php?deal_id=Y`
6. **Buyer** sees product context header, deal status banner
7. **Buyer** types message and clicks Send (JavaScript form)
8. **System** validates CSRF, sanitizes message, stores in DB
9. **JavaScript** polls `/public/messenger/fetch.php` every 4 seconds
10. **Seller** logs in separately, goes to `/public/messenger/index.php`
11. **Seller** sees unread badge on conversation
12. **Seller** clicks conversation → opens `/public/messenger/conversation.php?deal_id=Y`
13. **Seller** sees buyer's messages in real-time
14. **Seller** replies
15. **JavaScript** on buyer's tab fetches new message and displays
16. Both parties can see entire conversation history

---

## Test Scenarios

### Test 1: Deal Initiation → Redirect to Messenger ✅
**Objective**: Verify clicking DEAL button redirects to messenger

**Steps**:
1. Login as buyer
2. Navigate to `/public/buyer/market.php`
3. Click "View Details" on any product
4. Click "🤝 INITIATE DEAL" button
5. Form POSTs to `initiate_deal.php`

**Expected Result**:
- ✅ CSRF token verified
- ✅ Product availability checked
- ✅ Duplicate deal prevented
- ✅ Deal created in database
- ✅ Conversation created automatically
- ✅ Redirect to `/public/messenger/conversation.php?deal_id=X`
- ✅ Product context header displays
- ✅ Message input box visible

**Database Verification**:
```sql
SELECT * FROM deals WHERE deal_id = X;
SELECT * FROM conversations WHERE deal_id = X;
-- Should have both records created
```

---

### Test 2: Send Message as Buyer 📧
**Objective**: Verify message sending and storage

**Steps**:
1. In open conversation
2. Type a message: "Hey, can you deliver to my area?"
3. Click "Send" button
4. Watch JavaScript handle POST to `/public/messenger/send.php`

**Expected Result**:
- ✅ Message text sent via AJAX (no page reload)
- ✅ Message sanitized with htmlspecialchars
- ✅ Message length validated (max 1000 chars)
- ✅ CSRF token verified
- ✅ Conversation access checked
- ✅ Deal status checked (must be ongoing)
- ✅ Message inserted into database
- ✅ Message appears immediately in chat as blue bubble on right
- ✅ Message shows sender name + timestamp
- ✅ is_read = FALSE initially

**Database Verification**:
```sql
SELECT * FROM messages WHERE conversation_id = Y ORDER BY sent_at DESC LIMIT 1;
-- Should show: sender_id = buyer's user_id, is_read = FALSE, message_text = "Hey..."
```

---

### Test 3: Real-Time Polling (Buyer Tab) ⏱️
**Objective**: Verify JavaScript polls for new messages every 4 seconds

**Steps**:
1. Keep buyer's conversation tab open
2. Switch to another tab or window
3. Login as seller (different browser or incognito)
4. Navigate to `/public/messenger/index.php`
5. Click on the same conversation
6. Seller types: "Yes, I can deliver tomorrow at 5pm"
7. Seller clicks Send
8. Watch buyer's tab (should update within 4 seconds)

**Expected Result**:
- ✅ Seller's message appears in buyer's tab WITHOUT page refresh
- ✅ Message appears in left-aligned gray bubble (received)
- ✅ Shows seller name + timestamp
- ✅ Auto-scrolls to latest message
- ✅ Update happens within 4-6 seconds (polling interval)
- ✅ Message marked as read once fetched by buyer

**Database Verification**:
```sql
SELECT * FROM messages WHERE conversation_id = Y ORDER BY sent_at DESC;
-- Messages should have is_read = TRUE for buyer's fetches
```

---

### Test 4: Message History & Pagination 📜
**Objective**: Verify previous messages load on page open

**Steps**:
1. In open conversation with history
2. Refresh page
3. Scroll up in message container

**Expected Result**:
- ✅ All previous messages displayed in order (oldest first)
- ✅ Chat bubbles preserved (buyer messages right/blue, seller messages left/gray)
- ✅ Timestamps accurate
- ✅ Message text preserved and escaped correctly

---

### Test 5: Conversation List View 📋
**Objective**: Verify messenger index page displays all conversations

**Steps**:
1. Login as user with multiple conversations
2. Navigate to `/public/messenger/index.php`
3. Verify list displays

**Expected Result**:
- ✅ All user's conversations listed
- ✅ Each shows product image, name, price
- ✅ Other party name displayed
- ✅ Last message preview (truncated to 100 chars)
- ✅ Last message timestamp
- ✅ Unread count badge (red) if unread > 0
- ✅ Deal status badge (yellow/green/red)
- ✅ Clicking conversation goes to `/public/messenger/conversation.php?deal_id=X`

---

### Test 6: Unread Count Badge 🔴
**Objective**: Verify unread badge appears for unread messages

**Steps**:
1. As buyer, have multiple unread messages from seller
2. Check `/public/messenger/index.php`
3. Look for red badge with count

**Expected Result**:
- ✅ Red badge shows correct count
- ✅ Badge only shows for messages from OTHER user
- ✅ Badge disappears after viewing conversation
- ✅ Badge count in navbar (if implemented) matches

**Database Verification**:
```sql
SELECT COUNT(*) FROM messages 
WHERE conversation_id = Y AND is_read = FALSE AND sender_id != [buyer_user_id];
-- Should match badge count
```

---

### Test 7: Product Context Header 🛍️
**Objective**: Verify product info stays visible at top of chat

**Steps**:
1. In open conversation
2. Scroll down through messages
3. Verify product header stays visible (pinned)

**Expected Result**:
- ✅ Product image, name, price visible
- ✅ Other party name visible
- ✅ Sticky positioning keeps header in view
- ✅ Product header above messages container

---

### Test 8: Deal Status Banners 🤝
**Objective**: Verify status banners display correctly

**Steps**:
1. Open conversation with status='ongoing'
   - Should show: "🤝 Deal Ongoing — Arrange your meetup here!"
2. Complete deal (mark status='completed')
3. Refresh page
   - Should show: "✓ Deal Completed — This conversation is now read-only"
4. Try to send message
   - Should be blocked

**Expected Result**:
- ✅ Correct banner displays for status
- ✅ Read-only state enforced when deal complete
- ✅ Message input disabled when deal not ongoing
- ✅ Banner color matches status (yellow for ongoing, green for completed, red for cancelled)

---

### Test 9: Access Control - Unauthorized Access 🔐
**Objective**: Verify users can't access conversations they're not part of

**Steps**:
1. Create conversation between buyer A and seller B
2. Login as buyer C
3. Try to access conversation: `/public/messenger/conversation.php?deal_id=X`

**Expected Result**:
- ✅ Access denied error
- ✅ Redirect to home or appropriate page
- ✅ `checkAccess()` method returns false

**Code Check**:
```php
if (!$messenger->checkAccess($conversation_id, $_SESSION['user_id'])) {
    setFlashMessage('You do not have access to this conversation', 'error');
    secureRedirect(...);
}
```

---

### Test 10: Message Sanitization & XSS Prevention 🛡️
**Objective**: Verify malicious input is escaped

**Steps**:
1. Send message with HTML: `<img src=x onerror=alert('XSS')>`
2. Verify it displays as text, not executed

**Expected Result**:
- ✅ Message stored in DB as-is
- ✅ Message displayed escaped (& → &amp;, < → &lt;, etc.)
- ✅ No JavaScript alert fires
- ✅ Message shows as literal text in bubble

**Code Check**:
```php
// In conversation.php
<?php echo htmlspecialchars($message['message_text']); ?>

// In JavaScript
${escapeHtml(msg.message_text)}  // Using escapeHtml function
```

---

### Test 11: CSRF Protection 🔐
**Objective**: Verify CSRF tokens prevent unauthorized requests

**Steps**:
1. Inspect message form HTML
2. Verify CSRF token hidden input exists
3. Try to POST to `/public/messenger/send.php` without token
4. Try with invalid token value

**Expected Result**:
- ✅ CSRF token present in form
- ✅ Invalid/missing token returns 403 error
- ✅ Valid token passes verification
- ✅ Token regenerates after each use

**Code Check**:
```php
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF token invalid']);
    exit;
}
```

---

### Test 12: Message Length Validation 📏
**Objective**: Verify max 1000 characters enforced

**Steps**:
1. Try to send message with 1100 characters
2. Verify it's truncated to 1000

**Expected Result**:
- ✅ Message truncated to 1000 chars
- ✅ Database stores only 1000 chars
- ✅ No error shown to user (graceful truncation)

**Code Check**:
```php
if (strlen($message_text) > 1000) {
    $message_text = substr($message_text, 0, 1000);
}
```

---

### Test 13: Empty Message Prevention 📭
**Objective**: Verify empty messages are blocked

**Steps**:
1. Try to send only whitespace
2. Try to send empty message

**Expected Result**:
- ✅ Empty/whitespace message not sent
- ✅ Returns error or silently fails
- ✅ Nothing added to database

---

### Test 14: Real-Time Polling Accuracy ⏰
**Objective**: Verify polling interval and fetch accuracy

**Steps**:
1. Open developer console
2. In Network tab, watch for `/public/messenger/fetch.php` requests
3. Verify requests happen every ~4 seconds
4. Send message from other window
5. Verify fetch returns new message

**Expected Result**:
- ✅ Fetch requests sent every 4 seconds (within 1 sec variance)
- ✅ GET request to fetch.php with conversation_id parameter
- ✅ Returns JSON: `{success: true, messages: [...], count: N}`
- ✅ New messages returned only once (not duplicated)
- ✅ is_read updated correctly

**Network Response Example**:
```json
{
  "success": true,
  "messages": [
    {
      "message_id": 5,
      "sender_id": 3,
      "sender_name": "John Seller",
      "message_text": "Can you meet tomorrow?",
      "is_read": true,
      "sent_at": "2026-04-20 14:35:22"
    }
  ],
  "count": 1
}
```

---

### Test 15: Conversation Persistence 💾
**Objective**: Verify conversation survives page refresh

**Steps**:
1. Open conversation
2. Send messages
3. Refresh page
4. Verify all messages still there

**Expected Result**:
- ✅ All messages persist in database
- ✅ Messages load on page refresh
- ✅ Chat history maintained
- ✅ Last message time accurate

---

### Test 16: Seller Access to Buyer's Conversation 🔄
**Objective**: Verify seller can see and reply to buyer

**Steps**:
1. As buyer, initiate deal and send messages
2. Logout
3. Login as seller
4. Go to `/public/messenger/index.php`
5. Verify buyer's conversation visible
6. Click to open
7. Verify can see buyer's messages
8. Send reply
9. Verify buyer can see it (in separate tab or new login)

**Expected Result**:
- ✅ Seller sees conversation in list
- ✅ Shows buyer name
- ✅ Shows buyer's last message
- ✅ Unread badge visible
- ✅ Can reply
- ✅ Messages stored correctly
- ✅ Buyer sees seller's response in real-time polling

---

### Test 17: Multiple Conversations 🗨️
**Objective**: Verify users can have multiple active conversations

**Steps**:
1. As buyer, initiate deals with 3 different sellers
2. Open one conversation, send message
3. Go back to index, open another
4. Verify each conversation is separate

**Expected Result**:
- ✅ Each conversation has own ID
- ✅ Messages isolated per conversation
- ✅ Switching conversations shows correct messages
- ✅ No message bleeding between conversations
- ✅ Last message accurate for each

---

### Test 18: Conversation Last Update Time ⏱️
**Objective**: Verify conversations sorted by most recent

**Steps**:
1. With multiple conversations
2. Send message in oldest conversation
3. Go to messenger index
4. Verify that conversation moved to top

**Expected Result**:
- ✅ Conversations ordered by most recent message
- ✅ After new message, conversation jumps to top
- ✅ Order persists across refreshes

---

## Complete User Journey Test

**Scenario**: Multi-step negotiation and deal confirmation

**Pre-requisites**:
- Two user accounts: buyer@test.com and seller@test.com
- Products available for sale

**Step-by-Step**:

1. **Buyer**: Login and browse market
   ```
   Navigate: /public/buyer/market.php
   Action: Search for product
   Expected: Grid displays with 3-column layout
   ```

2. **Buyer**: View product detail
   ```
   Navigate: /public/buyer/product.php?id=1
   Action: Click product from grid
   Expected: Full details with seller info
   ```

3. **Buyer**: Initiate deal
   ```
   Action: Click "🤝 INITIATE DEAL"
   Expected: Redirect to /public/messenger/conversation.php?deal_id=1
   Database: deals.deal_id=1 created, conversations.conversation_id=1 created
   ```

4. **Buyer**: Send initial message
   ```
   Action: Type "Hi, is this still available?"
   Action: Click Send
   Expected: Message appears as blue bubble on right
   Database: messages.message_id=1 inserted
   ```

5. **Buyer**: See message in history
   ```
   Action: Refresh page
   Expected: Message persists, loads from database
   ```

6. **Seller**: Login separately
   ```
   Action: Login as seller
   Navigate: /public/messenger/index.php
   Expected: See conversation with unread badge
   ```

7. **Seller**: Open conversation
   ```
   Action: Click conversation in list
   Navigate: /public/messenger/conversation.php?deal_id=1
   Expected: See buyer's message
   Expected: See product context header
   Expected: See "🤝 Deal Ongoing" banner
   ```

8. **Seller**: Reply to buyer
   ```
   Action: Type "Yes! I can meet today at 3pm"
   Action: Click Send
   Expected: Message appears as gray bubble on left
   Database: messages.message_id=2 inserted
   ```

9. **Buyer**: See real-time update
   ```
   Wait: ~4 seconds (polling interval)
   Expected: Seller's message appears without page refresh
   Expected: Auto-scrolls to new message
   ```

10. **Buyer**: Agree to meetup
    ```
    Action: Type "Perfect! Let's meet at the mall."
    Action: Click Send
    Expected: Message sent and appears
    ```

11. **Verification**: Check database
    ```sql
    SELECT d.deal_id, d.status, c.conversation_id, COUNT(m.message_id) as message_count
    FROM deals d
    JOIN conversations c ON d.deal_id = c.deal_id
    JOIN messages m ON c.conversation_id = m.conversation_id
    WHERE d.deal_id = 1
    GROUP BY d.deal_id, d.status, c.conversation_id;
    
    Expected: 
    - deal_id: 1
    - status: ongoing
    - conversation_id: 1
    - message_count: 3
    ```

12. **Both**: See conversation in messenger list
    ```
    As buyer: /public/messenger/index.php
    As seller: /public/messenger/index.php
    Expected: Both see conversation in list
    Expected: Unread badges cleared after viewing
    ```

---

## Security Testing Checklist

### Authentication & Authorization
- [ ] Non-logged-in users redirected to login
- [ ] Users can only access own conversations
- [ ] Users can only send messages to conversations they're part of
- [ ] Access control checked on both GET and POST

### CSRF Protection
- [ ] CSRF token generated for each page
- [ ] Token verified on message send
- [ ] Invalid token rejected with 403
- [ ] Token regenerated after each request

### Input Validation & Sanitization
- [ ] Message text sanitized (htmlspecialchars)
- [ ] No XSS vulnerabilities
- [ ] Empty messages rejected
- [ ] Max 1000 character limit enforced
- [ ] Deal access verified (message only sent if deal ongoing)

### SQL Injection Prevention
- [ ] All queries use PDO prepared statements
- [ ] No string concatenation in queries
- [ ] Parameter binding used for all inputs
- [ ] Verified with SQL analysis

---

## Performance Testing

### Load Testing Considerations
- **Message fetching**: With polling every 4 seconds, consider rate limiting for high-traffic scenarios
- **Database**: Index on `messages.conversation_id`, `messages.sender_id`, `messages.is_read`
- **Optimization**: Consider pagination for conversations with 1000+ messages

### Recommended Indexes
```sql
CREATE INDEX idx_conversation_id ON messages(conversation_id);
CREATE INDEX idx_sender_id ON messages(sender_id);
CREATE INDEX idx_is_read ON messages(is_read);
CREATE INDEX idx_sent_at ON messages(sent_at);
```

---

## Deployment Checklist

- [ ] Database tables created (conversations, messages)
- [ ] Messenger.php class created
- [ ] All endpoints created and tested
- [ ] init.php updated to include Messenger class
- [ ] initiate_deal.php redirects to messenger
- [ ] CSRF token generation working
- [ ] PDO prepared statements verified
- [ ] JavaScript polling implemented
- [ ] Message sanitization working
- [ ] Access control enforced

---

## Known Limitations & Future Enhancements

### Current Stage 4:
- ✅ Real-time messaging with polling
- ✅ Conversation per deal
- ✅ Full message history
- ✅ Unread count tracking
- ✅ Deal status banners

### Future Enhancements:
- ⏳ WebSocket support (replace polling for true real-time)
- ⏳ Typing indicators ("User is typing...")
- ⏳ Message reactions/emojis
- ⏳ File/image sharing
- ⏳ Audio/video call integration
- ⏳ Message search functionality
- ⏳ Conversation export/archive
- ⏳ Automated translation

---

## Database Queries for Verification

### Check all conversations for user
```sql
SELECT c.conversation_id, c.deal_id, p.product_name, 
       ub.full_name as buyer_name, us.full_name as seller_name,
       COUNT(m.message_id) as total_messages
FROM conversations c
JOIN deals d ON c.deal_id = d.deal_id
JOIN products p ON d.product_id = p.product_id
LEFT JOIN buyers b ON c.buyer_id = b.buyer_id
LEFT JOIN users ub ON b.user_id = ub.user_id
LEFT JOIN sellers s ON c.seller_id = s.seller_id
LEFT JOIN users us ON s.user_id = us.user_id
LEFT JOIN messages m ON c.conversation_id = m.conversation_id
WHERE ub.user_id = [user_id] OR us.user_id = [user_id]
GROUP BY c.conversation_id
ORDER BY MAX(m.sent_at) DESC;
```

### Check unread messages
```sql
SELECT c.conversation_id, p.product_name, COUNT(*) as unread_count
FROM messages m
JOIN conversations c ON m.conversation_id = c.conversation_id
JOIN deals d ON c.deal_id = d.deal_id
JOIN products p ON d.product_id = p.product_id
WHERE c.buyer_id = [buyer_id] AND m.sender_id != [user_id] AND m.is_read = FALSE
GROUP BY c.conversation_id
ORDER BY unread_count DESC;
```

### Check conversation history
```sql
SELECT m.message_id, u.full_name, m.message_text, m.is_read, m.sent_at
FROM messages m
JOIN users u ON m.sender_id = u.user_id
WHERE m.conversation_id = [conversation_id]
ORDER BY m.sent_at ASC;
```

---

**Status**: STAGE 4 COMPLETE - READY FOR TESTING ✅

