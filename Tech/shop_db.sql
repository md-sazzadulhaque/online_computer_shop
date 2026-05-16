CREATE DATABASE IF NOT EXISTS shop_db;
USE shop_db;

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Sample Data
INSERT INTO categories (name) VALUES ('RAM'), ('CPU'), ('SSD'), ('GPU');

INSERT INTO products (category_id, name, description, price, stock) VALUES
(1, 'Corsair 8GB DDR4',    'Good RAM for gaming',         2500.00, 10),
(1, 'Kingston 16GB DDR4',  'High speed RAM',              4500.00, 5),
(2, 'Intel Core i5-12400', 'Fast processor for everyday', 18000.00, 8),
(2, 'AMD Ryzen 5 5600X',   'Best gaming CPU',             21000.00, 6),
(3, 'Samsung 500GB SSD',   'Very fast storage',           7500.00, 15),
(4, 'ASUS RTX 3060',       'Great GPU for gaming',        35000.00, 4);
