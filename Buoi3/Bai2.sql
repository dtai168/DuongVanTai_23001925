-- BÀI 2 – QUẢN LÝ VÉ XEM PHIM
-- 1. Tạo bảng movies
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL
);

-- 2.1. Thêm ít nhất 5 bộ phim
INSERT INTO movies (title, price, total_seats, available_seats) VALUES
('Avengers: Endgame', 120000.00, 200, 50),
('Spiderman: No Way Home', 90000.00, 150, 60),
('Dune 2', 150000.00, 250, 20),
('Godzilla x Kong', 80000.00, 100, 10),
('Mai', 110000.00, 300, 150);

-- 2.2. Hiển thị toàn bộ danh sách phim
SELECT * FROM movies;

-- 2.3. Hiển thị phim có giá vé lớn hơn 100000
SELECT * FROM movies WHERE price > 100000;

-- 2.4. Hiển thị phim còn nhiều hơn 50 ghế
SELECT * FROM movies WHERE available_seats > 50;

-- 2.5. Sắp xếp phim theo giá vé giảm dần
SELECT * FROM movies ORDER BY price DESC;

-- 2.6. Cập nhật số ghế còn lại của một phim (VD: id = 1)
UPDATE movies SET available_seats = 45 WHERE id = 1;

-- 2.7. Xóa một phim (VD: id = 4)
DELETE FROM movies WHERE id = 4;

-- 2.8. Hiển thị số vé đã bán của từng phim
SELECT title, (total_seats - available_seats) AS sold_tickets 
FROM movies;

-- 2.9. Tính doanh thu của từng phim
SELECT title, ((total_seats - available_seats) * price) AS revenue 
FROM movies;

-- 2.10. Tính tổng doanh thu của tất cả các phim
SELECT SUM((total_seats - available_seats) * price) AS total_revenue 
FROM movies;

-- 2.11. Tìm phim có số vé bán ra nhiều nhất
SELECT title, (total_seats - available_seats) AS sold_tickets
FROM movies
ORDER BY sold_tickets DESC
LIMIT 1;