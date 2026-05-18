-- =====================================================
-- TechHub – Shared Database Schema
-- Run this ONCE to set up the database
-- Student Task 1: 23-50573-1
-- =====================================================

CREATE DATABASE IF NOT EXISTS computer_shop
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE computer_shop;

-- Users (Task 1 owns this table setup)
CREATE TABLE IF NOT EXISTS users (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(100)  NOT NULL,
  email           VARCHAR(150)  NOT NULL UNIQUE,
  password_hash   VARCHAR(255)  NOT NULL,
  role            ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  profile_picture VARCHAR(255)  DEFAULT NULL,
  remember_token  VARCHAR(64)   DEFAULT NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories (managed by Task 2)
CREATE TABLE IF NOT EXISTS categories (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  parent_id  INT UNSIGNED DEFAULT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Brands (managed by Task 2)
CREATE TABLE IF NOT EXISTS brands (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Products (managed by Task 2)
CREATE TABLE IF NOT EXISTS products (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name                VARCHAR(200)   NOT NULL,
  description         TEXT,
  manufacturer_review TEXT,
  price               DECIMAL(10,2)  NOT NULL CHECK (price > 0),
  category_id         INT UNSIGNED   NOT NULL,
  brand_id            INT UNSIGNED   NOT NULL,
  image_path          VARCHAR(255)   DEFAULT NULL,
  stock               INT UNSIGNED   NOT NULL DEFAULT 0,
  created_at          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
  FOREIGN KEY (brand_id)    REFERENCES brands(id)     ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Cart (managed by Task 3)
CREATE TABLE IF NOT EXISTS cart (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity   INT UNSIGNED NOT NULL DEFAULT 1,
  added_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Orders (managed by Task 4)
CREATE TABLE IF NOT EXISTS orders (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id         INT UNSIGNED NOT NULL,
  total_amount    DECIMAL(10,2) NOT NULL,
  payment_method  ENUM('cash_on_delivery','online_wallet') NOT NULL,
  status          VARCHAR(50)  NOT NULL DEFAULT 'pending',
  order_date      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Order Items (managed by Task 4)
CREATE TABLE IF NOT EXISTS order_items (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id   INT UNSIGNED   NOT NULL,
  product_id INT UNSIGNED   NOT NULL,
  quantity   INT UNSIGNED   NOT NULL,
  unit_price DECIMAL(10,2)  NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Reviews (managed by Task 4)
CREATE TABLE IF NOT EXISTS reviews (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id    INT UNSIGNED NOT NULL,
  user_id       INT UNSIGNED NOT NULL,
  reviewer_name VARCHAR(100) NOT NULL,
  comment       TEXT         NOT NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Sample seed data for testing Task 1 ──────────────────────────────
INSERT IGNORE INTO categories (name, parent_id) VALUES
  ('CPU', NULL), ('GPU', NULL), ('RAM', NULL),
  ('Storage', NULL), ('Monitor', NULL), ('Motherboard', NULL);

INSERT IGNORE INTO categories (name, parent_id) VALUES
  ('Permanent Storage', 4), ('Portable Storage', 4);
