-- Chạy bằng phpMyAdmin hoặc mysql để tạo cơ sở dữ liệu cho bài thực hành 8.
CREATE DATABASE IF NOT EXISTS buoi8
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE buoi8;

CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    stock INT NOT NULL,
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
    (3, 'Thước kẻ', 8000, 0);
