-- ============================================================
-- Online Computer Shop — Complete Schema
-- DB: computer_shop5
-- Run this ONE FILE only in phpMyAdmin
-- Includes: shared schema + cart table + all sample data
-- ============================================================

CREATE DATABASE IF NOT EXISTS computer_shop5;
USE computer_shop5;

-- ------------------------------------------------------------
-- USERS (shared)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- CATEGORIES (shared) — parent_id NULL = top level, NOT NULL = subcategory
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    parent_id  INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- BRANDS (shared)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS brands (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- PRODUCTS (shared)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(150) NOT NULL,
    description         TEXT,
    manufacturer_review TEXT,
    price               DECIMAL(10,2) NOT NULL,
    category_id         INT NOT NULL,
    brand_id            INT NOT NULL,
    image_path          VARCHAR(255) DEFAULT NULL,
    stock               INT NOT NULL DEFAULT 0,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (brand_id)    REFERENCES brands(id)     ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- CART (Task 3 addition)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cart (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL DEFAULT 1,
    added_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Admin (password: admin123 — plain text as in original shared schema)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@shop.com', 'admin123', 'admin');

-- Sample customer (password: password123 — hashed with password_hash())
INSERT INTO users (name, email, password, role) VALUES
('John Customer', 'john@example.com', '$2y$10$TKh8H1.PfY5WQ6RQRqr.weLSPMnFQMKr.3JJRMqgxAzKlIJjAXhqy', 'customer');

-- Top-level categories (parent_id = NULL)
INSERT INTO categories (name, parent_id) VALUES
('RAM',     NULL),
('CPU',     NULL),
('Storage', NULL),
('GPU',     NULL);

-- Subcategories (parent_id points to top-level category id)
-- RAM subcategories  → parent_id = 1
-- CPU subcategories  → parent_id = 2
-- Storage subcategories → parent_id = 3
INSERT INTO categories (name, parent_id) VALUES
('DDR4',      1),
('DDR5',      1),
('Intel CPU', 2),
('AMD CPU',   2),
('SSD',       3),
('HDD',       3);

-- Brands linked to top-level category
-- category_id 1=RAM, 2=CPU, 3=Storage, 4=GPU
INSERT INTO brands (name, category_id) VALUES
('Corsair', 1),
('Kingston', 1),
('Samsung',  3),
('Intel',    2),
('AMD',      2),
('ASUS',     4),
('Seagate',  3);

-- Products
-- category_id here = subcategory id (5=DDR4, 6=DDR5, 7=IntelCPU, 8=AMDCPU, 9=SSD, 10=HDD, 4=GPU top)
INSERT INTO products (name, description, manufacturer_review, price, category_id, brand_id, stock) VALUES
('Corsair Vengeance 16GB DDR4',
 'High performance DDR4 RAM for gaming and productivity. Runs at 3200MHz.',
 'Rated 4.8/5 by Corsair labs. Excellent thermal performance with aluminium heat spreader.',
 4500.00, 5, 1, 10),

('Kingston Fury 8GB DDR4',
 'Budget friendly DDR4 RAM with plug and play simplicity.',
 'Rated 4.5/5 by Kingston quality team. Optimized for Intel XMP 2.0.',
 2200.00, 5, 2, 15),

('Corsair Dominator 32GB DDR5',
 'Next gen DDR5 RAM for extreme performance builds.',
 'Rated 4.9/5 by Corsair. World class speeds up to 5600MHz.',
 12000.00, 6, 1, 5),

('Intel Core i5-12400',
 'Fast 12th generation Intel processor for everyday computing and light gaming.',
 'Rated 4.7/5 by Intel. Excellent single core performance for the price.',
 18000.00, 7, 4, 8),

('AMD Ryzen 5 5600X',
 'Best gaming CPU from AMD at an affordable price point.',
 'Rated 4.8/5 by AMD. Best in class gaming CPU with 6 cores and 12 threads.',
 21000.00, 8, 5, 6),

('Samsung 500GB NVMe SSD',
 'Ultra fast NVMe SSD with sequential read speeds up to 3500 MB/s.',
 'Rated 4.9/5 by Samsung. Industry leading reliability and endurance.',
 7500.00, 9, 3, 20),

('Seagate Barracuda 2TB HDD',
 'High capacity hard drive for mass storage and backup needs.',
 'Rated 4.4/5 by Seagate. Proven durability with over 1 million hours MTBF.',
 5500.00, 10, 7, 12),

('ASUS RTX 3060 12GB',
 'Mid range GPU perfect for 1080p and 1440p gaming with ray tracing support.',
 'Rated 4.7/5 by ASUS. Exceptional ray tracing and DLSS capability.',
 35000.00, 4, 6, 4);
