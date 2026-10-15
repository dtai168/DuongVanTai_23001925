CREATE DATABASE IF NOT EXISTS shopping_cart
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shopping_cart;

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tai khoan mau: admin / admin123.
-- Hash duoc tao bang password_hash('admin123', PASSWORD_DEFAULT).
INSERT INTO users (username, password)
SELECT 'admin', 'admin123'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');

CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chỉ thêm dữ liệu mẫu khi bảng chưa có sản phẩm.
INSERT INTO products (name, price, quantity)
SELECT samples.name, samples.price, samples.quantity
FROM (
    SELECT 'Bàn phím cơ' AS name, 750000.00 AS price, 15 AS quantity
    UNION ALL SELECT 'Chuột không dây', 250000.00, 30
    UNION ALL SELECT 'Tai nghe', 450000.00, 20
    UNION ALL SELECT 'Màn hình 24 inch', 3200000.00, 8
    UNION ALL SELECT 'USB 64GB', 150000.00, 50
) AS samples
WHERE NOT EXISTS (SELECT 1 FROM products);
