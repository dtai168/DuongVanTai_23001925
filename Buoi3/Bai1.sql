-- BÀI 1 – QUẢN LÝ GIỎ HÀNG
CREATE DATABASE shopping_cart;
USE shopping_cart;

-- 1. Tạo bảng cart_items
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL
);

-- 2.1. Thêm ít nhất 5 sản phẩm vào bảng
INSERT INTO cart_items (name, price, quantity) VALUES
('Áo thun nam', 150000.00, 2),
('Quần đùi thể thao', 80000.00, 6),
('Giày chạy bộ', 500000.00, 1),
('Bít tất', 25000.00, 10),
('Áo khoác gió', 250000.00, 4);

-- 2.2. Hiển thị toàn bộ sản phẩm
SELECT * FROM cart_items;

-- 2.3. Hiển thị sản phẩm có giá lớn hơn 100000
SELECT * FROM cart_items WHERE price > 100000;

-- 2.4. Hiển thị sản phẩm có số lượng lớn hơn 5
SELECT * FROM cart_items WHERE quantity > 5;

-- 2.5. Sắp xếp sản phẩm theo giá giảm dần
SELECT * FROM cart_items ORDER BY price DESC;

-- 2.6. Cập nhật giá của một sản phẩm (VD: id = 1)
UPDATE cart_items SET price = 160000.00 WHERE id = 1;

-- 2.7. Cập nhật số lượng của một sản phẩm (VD: id = 1)
UPDATE cart_items SET quantity = 3 WHERE id = 1;

-- 2.8. Xóa một sản phẩm (VD: id = 4)
DELETE FROM cart_items WHERE id = 4;

-- 2.9. Hiển thị tên sản phẩm, giá, số lượng và thành tiền
SELECT name, price, quantity, (price * quantity) AS total_amount 
FROM cart_items;

-- 2.10. Tính tổng tiền của toàn bộ giỏ hàng
SELECT SUM(price * quantity) AS grand_total 
FROM cart_items;