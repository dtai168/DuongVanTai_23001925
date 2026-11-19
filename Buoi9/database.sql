-- Chạy bằng phpMyAdmin hoặc mysql để tạo cơ sở dữ liệu cho bài thực hành 9.
CREATE DATABASE IF NOT EXISTS buoi9
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE buoi9;

CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    stock INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_products_price CHECK (price > 0),
    CONSTRAINT chk_products_stock CHECK (stock >= 0)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- ID cố định giúp nhập lại tệp không tạo thêm bản sao hoặc ghi đè sản phẩm đã sửa.
INSERT IGNORE INTO products (id, name, price, stock) VALUES
    (1, 'Bút bi', 5000, 20),
    (2, 'Vở ô ly', 12000, 15),
    (3, 'Thước kẻ', 8000, 0),
    (4, 'Bút chì', 6000, 40),
    (5, 'Tẩy trắng', 4000, 32),
    (6, 'Sổ tay bìa cứng', 35000, 9),
    (7, 'Kẹp giấy màu', 15000, 17),
    (8, 'Keo dán giấy', 11000, 0),
    (9, 'Giấy ghi chú', 16000, 24),
    (10, 'Bút dạ quang', 18000, 11),
    (11, 'Hộp bút', 42000, 6),
    (12, 'Bìa hồ sơ', 9000, 28);
