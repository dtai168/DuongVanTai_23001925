# Bài thực hành Vue.js — Buổi 6

Mỗi bài có các file HTML, CSS và JavaScript riêng:

```text
Buoi6/
├── Bai1/
│   ├── Bai1.html
│   ├── Bai1.css
│   ├── Bai1.js
│   └── assets/avatar.svg
├── Bai2/
│   ├── Bai2.html
│   ├── Bai2.css
│   └── Bai2.js
├── Bai3/
│   ├── Bai3.html
│   ├── Bai3.css
│   └── Bai3.js
└── README.md
```

## Mở trực tiếp

Mở `Bai1/Bai1.html`, `Bai2/Bai2.html` hoặc `Bai3/Bai3.html` bằng trình duyệt.
Không cần biên dịch hoặc cài thư viện. Cần Internet để tải Vue.js từ CDN.

## Chạy bằng máy chủ cục bộ

Trong VS Code, mở Terminal tại thư mục gốc dự án (thư mục chứa `Buoi6`), rồi chạy:

```powershell
php -S localhost:8000 -t Buoi6
```

Giữ Terminal đang chạy và mở một trong các địa chỉ:

- Bài 1: http://localhost:8000/Bai1/Bai1.html
- Bài 2: http://localhost:8000/Bai2/Bai2.html
- Bài 3: http://localhost:8000/Bai3/Bai3.html

Nếu Terminal đang ở trong `Buoi6`, dùng `php -S localhost:8000`.
Nhấn `Ctrl+C` trong Terminal để dừng máy chủ. Nếu không có lệnh `php`, dùng cách mở trực tiếp ở trên.

## Các thao tác chạy thử

1. **Bài 1:** Đổi họ tên và lớp để xem thông tin cập nhật. Thử điểm `8`, `6.5`, `5`, `4.9` để xem lần lượt Giỏi, Khá, Trung bình, Chưa đạt cùng màu tương ứng. Thử điểm ngoài khoảng 0–10, đổi trạng thái đang học và bật/tắt giới thiệu.
2. **Bài 2:** Nhập họ tên, chọn vé VIP, số lượng `2`, bật combo. Tổng tiền phải là **245.000 đồng**. Bấm Đặt vé để xem xác nhận. Thử tên chỉ gồm khoảng trắng và số lượng `0`, `11`, `1.5`: nút Đặt vé phải bị vô hiệu hóa. Thử tăng/giảm và Đặt lại.
3. **Bài 3:** Thêm việc bằng nút Thêm và phím Enter. Thử tên chỉ gồm khoảng trắng; công việc không được thêm. Đánh dấu hoàn thành để xem gạch ngang và số đếm thay đổi. Thử cả ba bộ lọc, xóa riêng từng việc, xóa các việc đã hoàn thành và xóa hết danh sách để xem thông báo rỗng.

Dữ liệu chỉ được giữ trong bộ nhớ của trang; tải lại trang sẽ khôi phục dữ liệu ban đầu.
