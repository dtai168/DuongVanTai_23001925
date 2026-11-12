# Bài thực hành 8 — PHP, MySQL và Vue.js

Ứng dụng quản lý sản phẩm theo PDF: tạo bảng và ba sản phẩm mẫu, API CRUD bằng PHP/PDO, giao diện Vue dùng `v-for`, `v-model`, xác nhận xóa và cập nhật bảng không tải lại trang.

## Chạy bài

Yêu cầu: PHP **8.1 trở lên**, extension `pdo_mysql`, `mbstring`, MySQL/MariaDB và Internet để tải Vue **3.5.13** qua CDN. Không cần thư mục vendor, npm hay công cụ build.

1. Bật **Apache** và **MySQL** trong XAMPP.
2. Mở http://localhost/phpmyadmin/, chọn **Import**, nhập tệp `Buoi8/database.sql`. Tệp tự tạo cơ sở dữ liệu `buoi8`, bảng `products` và ba sản phẩm mẫu. Chỉ cần nhập khi thiết lập lần đầu.
3. Mở PowerShell tại thư mục gốc repository, chạy:

   ```powershell
   & 'D:\Xampp\php\php.exe' -S 127.0.0.1:8000 -t .\Buoi8
   ```

4. Truy cập **http://127.0.0.1:8000/**. Giữ terminal chạy; nhấn `Ctrl+C` để dừng.

Live Server hoặc mở `index.html` bằng `file://` không thực thi PHP. Nếu cổng 8000 đang được dùng, đổi cổng trong lệnh và URL.

### Kết nối MySQL

`api/config.php` đọc các biến môi trường sau; mặc định phù hợp với XAMPP chưa đặt mật khẩu:

| Biến | Mặc định |
| --- | --- |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `buoi8` |
| `DB_USER` | `root` |
| `DB_PASSWORD` | Chuỗi rỗng |

Có thể sửa giá trị mặc định trong `api/config.php` hoặc đặt biến trước khi chạy PHP trong cùng terminal, ví dụ `$env:DB_PORT = '3307'`. Nếu đổi tên cơ sở dữ liệu, sửa cả `database.sql` trước khi nhập.

## Các tệp

| Tệp | Chức năng |
| --- | --- |
| `database.sql` | Bảng products và dữ liệu: Bút bi / 5000 / 20; Vở ô ly / 12000 / 15; Thước kẻ / 8000 / 0 |
| `api/config.php` | Kết nối PDO, UTF-8 |
| `api/products.php` | API JSON, kiểm tra dữ liệu, PDO prepared statements |
| `index.html` | Bảng `v-for`, biểu mẫu `v-model`, Vue qua CDN |
| `app.js` | Gọi API, thêm/sửa/xóa, xác nhận và thông báo lỗi |
| `style.css` | Giao diện máy tính và điện thoại |
| `test.php` | Bộ kiểm thử API chạy từ terminal |

## API

| Phương thức | Đường dẫn | Thành công |
| --- | --- | --- |
| GET | `api/products.php` | 200, `{ "success": true, "data": [...] }` |
| POST | `api/products.php` | 201, sản phẩm mới trong `data` |
| PUT | `api/products.php?id=1` | 200, sản phẩm cập nhật trong `data` |
| DELETE | `api/products.php?id=1` | 200, `success: true` và `message` |

POST và PUT nhận `Content-Type: application/json` với đủ ba trường:

```json
{ "name": "Bút chì", "price": 6000, "stock": 10 }
```

PHP kiểm tra tên sau khi bỏ khoảng trắng hai đầu dài 1–100 ký tự Unicode; giá là số nguyên dương, tồn kho là số nguyên không âm, tối đa `2147483647` theo kiểu MySQL INT. Các trường số phải là số JSON, không phải chuỗi số. Dữ liệu sai bị từ chối trước khi ghi vào MySQL.

Lỗi trả `{ "success": false, "message": "..." }`: HTTP 400 cho dữ liệu/ID sai, 404 khi sửa/xóa sản phẩm không tồn tại, 405 cho phương thức không hỗ trợ, 500 cho lỗi máy chủ/cơ sở dữ liệu. Lưu lại dữ liệu không đổi vẫn thành công với sản phẩm tồn tại.

## Kiểm thử

Khi PHP và MySQL đang chạy, mở terminal thứ hai tại gốc repository:

```powershell
& 'D:\Xampp\php\php.exe' .\Buoi8\test.php http://127.0.0.1:8000
```

Bộ kiểm thử có **55 kiểm tra**, mã thoát `0` khi tất cả đạt. Chỉ tạo/xóa bản ghi tạm của bộ kiểm thử, không sửa/xóa sản phẩm có sẵn. Nên chạy trên dữ liệu thử, tránh thao tác đồng thời. Tệp chặn chạy qua trình duyệt.

| Thao tác theo đề | Kết quả mong đợi |
| --- | --- |
| Thêm sản phẩm rồi tải lại trang | Sản phẩm vẫn xuất hiện |
| Sửa đơn giá rồi Lưu | Bảng hiện giá mới |
| Bấm Xóa rồi hủy | Không gửi DELETE, sản phẩm còn nguyên |
| Xác nhận xóa rồi tải lại trang | Sản phẩm đã xóa không còn |
| Gửi POST trực tiếp với tồn kho -1 | HTTP 400, không tạo bản ghi mới |
| Sửa/xóa ID không tồn tại | API trả lỗi và message |

Thử tồn kho âm trong Console của trình duyệt tại trang ứng dụng:

```javascript
fetch('api/products.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ name: 'Sản phẩm thử lỗi', price: 1000, stock: -1 })
}).then(response => response.json()).then(console.log);
```

Nếu không kết nối được cơ sở dữ liệu, kiểm tra MySQL, cổng/tài khoản và đã nhập SQL. Nếu không tải được Vue, kiểm tra Internet/CDN. Nếu API không trả JSON, kiểm tra máy chủ đang thực thi PHP và các extension đã bật.

Tham khảo: [Vue — dùng qua CDN](https://vuejs.org/guide/quick-start.html#using-vue-from-cdn), [PHP — PDO prepared statements](https://www.php.net/manual/en/pdo.prepared-statements.php).
