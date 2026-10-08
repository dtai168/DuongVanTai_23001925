# Quản lý sản phẩm (PHP + MySQL)

## Chuẩn bị

1. Khởi động MySQL trong XAMPP.
2. Nhập `database.sql` bằng phpMyAdmin hoặc lệnh:

   ```powershell
   D:\Xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 --execute="source database.sql"
   ```

   File SQL tạo database `shopping_cart`, bảng `products` và 5 sản phẩm mẫu. Chạy lại file sẽ không thêm bản sao nếu bảng đã có dữ liệu.

3. Cấu hình kết nối trong `app/common/dbConnect.php` hoặc dùng các biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`. Mặc định: `127.0.0.1:3306`, database `shopping_cart`, user `root`, mật khẩu rỗng.

## Chạy ứng dụng

Trong thư mục `Buoi4`, chạy:

```powershell
php -d "session.save_path=$env:TEMP" -S localhost:8000 -t app
```

Mở `http://localhost:8000/` để xem danh sách, thêm, sửa và xóa sản phẩm. Trang xóa yêu cầu xác nhận trước khi thực hiện.
