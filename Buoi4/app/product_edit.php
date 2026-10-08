<?php
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$id = productId();
try {
    $product = $id !== null ? getProductById($id) : null;
} catch (Throwable $error) {
    showDatabaseError($error);
}

$pageTitle = 'Sửa sản phẩm';
if ($product === null) {
    http_response_code(404);
    require __DIR__ . '/view/header.php';
    echo '<p class="error" role="alert">Sản phẩm không tồn tại hoặc ID không hợp lệ.</p>';
    require __DIR__ . '/view/footer.php';
    exit;
}

$name = $product['name'];
$price = $product['price'];
$quantity = (string) $product['quantity'];
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
            if (updateProduct($id, $name, $price, (int) $quantity)) {
                redirectWithMessage('Cập nhật sản phẩm thành công.');
            }
            http_response_code(404);
            $errors[] = 'Sản phẩm không còn tồn tại.';
        } catch (Throwable $error) {
            error_log($error->getMessage());
            http_response_code(500);
            $errors[] = 'Không thể cập nhật sản phẩm. Vui lòng kiểm tra kết nối MySQL và thử lại.';
        }
    } else {
        http_response_code(422);
    }
}

$submitLabel = 'Lưu thay đổi';
require __DIR__ . '/view/header.php';
require __DIR__ . '/view/product_form.php';
require __DIR__ . '/view/footer.php';
