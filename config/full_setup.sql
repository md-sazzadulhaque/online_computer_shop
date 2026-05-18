-- =============================================================
-- TASK 4 — FULL STANDALONE SETUP (for testing without Task 1/2/3)
-- Student: 23-51148-1
-- Run this ENTIRE file in phpMyAdmin SQL tab at once
-- =============================================================

CREATE DATABASE IF NOT EXISTS computer_shop5
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE computer_shop5;

-- -------------------------------------------------------
-- 1. SHARED TABLES (normally from Task 1/2/3)
-- -------------------------------------------------------

CREATE TABLE IF NOT EXISTS users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    parent_id  INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS brands (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    category_id INT DEFAULT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(200) NOT NULL,
    description         TEXT,
    manufacturer_review TEXT,
    price               DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    category_id         INT DEFAULT NULL,
    brand_id            INT DEFAULT NULL,
    image_path          VARCHAR(255) DEFAULT NULL,
    stock               INT NOT NULL DEFAULT 0,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL DEFAULT 1,
    added_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- 2. TASK 4 TABLES
-- -------------------------------------------------------

CREATE TABLE IF NOT EXISTS orders (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    total_amount   DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash_on_delivery','online_wallet') NOT NULL,
    status         ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    order_date     DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    product_id INT NOT NULL,
    quantity   INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS reviews (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    product_id    INT NOT NULL,
    user_id       INT NOT NULL,
    reviewer_name VARCHAR(100) NOT NULL,
    comment       TEXT NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
);

-- -------------------------------------------------------
-- 3. DUMMY DATA
-- password for all users = "password123"
-- hash of "password123" using PHP password_hash()
-- -------------------------------------------------------

INSERT INTO users (id, name, email, password_hash, role) VALUES
(1, 'Admin User',   'admin@test.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Ali Hassan',   'ali@test.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
(3, 'Sara Khan',    'sara@test.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
(4, 'Test Customer','test@customer.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (id, name, parent_id) VALUES
(1, 'Monitor',  NULL),
(2, 'RAM',      NULL),
(3, 'Storage',  NULL)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO brands (id, name, category_id) VALUES
(1, 'ASUS', 1),
(2, 'LG',   1),
(3, 'Kingston', 2)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO products (id, name, description, manufacturer_review, price, category_id, brand_id, stock) VALUES
(1, 'ASUS 27" Monitor',   'Full HD IPS display, 144Hz refresh rate.', 'Best in class color accuracy.', 25000.00, 1, 1, 10),
(2, 'LG UltraWide 34"',   '34 inch curved ultrawide monitor.',       'Immersive viewing experience.',  42000.00, 1, 2, 5),
(3, 'Kingston 16GB DDR4', '3200MHz gaming RAM, dual channel.',        'Reliable and fast.',              6500.00, 2, 3, 20)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Cart items for user 2 (Ali Hassan) — for testing checkout
INSERT INTO cart (user_id, product_id, quantity) VALUES
(2, 1, 1),
(2, 3, 2)
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity);

-- Sample existing review
INSERT INTO reviews (product_id, user_id, reviewer_name, comment) VALUES
(1, 3, 'Sara Khan', 'Excellent monitor! Colors are vivid and display is super smooth.')
ON DUPLICATE KEY UPDATE comment=VALUES(comment);
