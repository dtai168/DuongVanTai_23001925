<?php
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$name = '';
$price = '';
$quantity = '0';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = postValue('name');
    $price = postValue('price');
    $quantity = postValue('quantity');
    $errors = validateProduct($name, $price, $quantity);
    if (!validCsrfToken()) {
        $errors[] = 'Phiên xác nhận không hợp lệ. Vui lòng gửi lại biểu mẫu.';
    }
    if (!$errors) {
        try {
            addProduct($name, $price, (int) $quantity);
            redirectWithMessage('Thêm sản phẩm thành công.');
        } catch (Throwable $error) {
            error_log($error->getMessage());
            http_response_code(500);
            $errors[] = 'Không thể thêm sản phẩm. Vui lòng kiểm tra kết nối MySQL và thử lại.';
        }
    } else {
        http_response_code(422);
    }
}

$pageTitle = 'Thêm sản phẩm';
$submitLabel = 'Thêm sản phẩm';
require __DIR__ . '/view/header.php';
require __DIR__ . '/view/product_form.php';
require __DIR__ . '/view/footer.php';
