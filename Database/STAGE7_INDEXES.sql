-- ════════════════════════════════════════════════════════════════
-- STAGE 7: DATABASE OPTIMIZATION INDEXES
-- Partido Product Online Market Hub - ISO/IEC 25010 Performance
-- ════════════════════════════════════════════════════════════════

USE partido_market;

-- PRODUCTS TABLE INDEXES (PRIMARY: seller listings)
ALTER TABLE products ADD INDEX idx_seller_id_status (seller_id, status);
ALTER TABLE products ADD INDEX idx_status_created (status, created_at DESC);
ALTER TABLE products ADD INDEX idx_created_at (created_at DESC);

-- DEALS TABLE INDEXES (PRIMARY: transaction tracking)
ALTER TABLE deals ADD INDEX idx_buyer_id_status (buyer_id, status);
ALTER TABLE deals ADD INDEX idx_seller_id_status (seller_id, status);
ALTER TABLE deals ADD INDEX idx_status_created (status, created_at DESC);
ALTER TABLE deals ADD INDEX idx_created_at (created_at DESC);

-- MESSAGES TABLE INDEXES (PRIMARY: messenger polling)
ALTER TABLE conversations ADD INDEX idx_deal_id (deal_id);
ALTER TABLE messages ADD INDEX idx_conversation_id (conversation_id);
ALTER TABLE messages ADD INDEX idx_conversation_sent (conversation_id, sent_at DESC);
ALTER TABLE messages ADD INDEX idx_sender_id (sender_id);

-- RATINGS TABLE INDEXES (PRIMARY: seller reputation)
ALTER TABLE ratings ADD INDEX idx_seller_id (seller_id);
ALTER TABLE ratings ADD INDEX idx_seller_created (seller_id, created_at DESC);

-- TRANSACTIONS TABLE INDEXES
ALTER TABLE transactions ADD INDEX idx_seller_id_status (seller_id, status);
ALTER TABLE transactions ADD INDEX idx_buyer_id_status (buyer_id, status);
ALTER TABLE transactions ADD INDEX idx_status_created (status, created_at DESC);

-- USERS TABLE INDEXES (already has these, verify)
-- These ensure email-based lookups and role filtering are fast
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_role (role);
ALTER TABLE users ADD INDEX idx_status (status);

-- SELLERS/BUYERS TABLE INDEXES (for dashboard queries)
ALTER TABLE sellers ADD INDEX idx_rating (rating DESC);
ALTER TABLE sellers ADD INDEX idx_total_sales (total_sales DESC);

-- ════════════════════════════════════════════════════════════════
-- RUN THIS FILE IN PHPMYADMIN OR VIA MYSQL CLI:
-- mysql -u root -p partido_market < STAGE7_INDEXES.sql
-- ════════════════════════════════════════════════════════════════
