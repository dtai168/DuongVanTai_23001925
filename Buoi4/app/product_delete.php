<?php
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

$id = productId();
try {
    $product = $id !== null ? getProductById($id) : null;
} catch (Throwable $error) {
    showDatabaseError($error);
}

$pageTitle = 'Xóa sản phẩm';
$errorMessage = '';
if ($product === null) {
    http_response_code(404);
    $errorMessage = 'Sản phẩm không tồn tại hoặc ID không hợp lệ.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validCsrfToken()) {
        http_response_code(403);
        $errorMessage = 'Phiên xác nhận không hợp lệ. Vui lòng gửi lại biểu mẫu.';
    } else {
        try {
            if (deleteProduct($id)) {
                redirectWithMessage('Xóa sản phẩm thành công.');
            }
            http_response_code(404);
            $product = null;
            $errorMessage = 'Sản phẩm không còn tồn tại.';
        } catch (Throwable $error) {
            error_log($error->getMessage());
            http_response_code(500);
            $errorMessage = 'Không thể xóa sản phẩm. Vui lòng kiểm tra kết nối MySQL và thử lại.';
        }
    }
}

require __DIR__ . '/view/header.php';
?>
<?php if ($errorMessage !== ''): ?>
    <p class="error" role="alert"><?= escapeHtml($errorMessage) ?></p>
<?php endif; ?>
<?php if ($product !== null): ?>
    <p>Bạn có chắc muốn xóa sản phẩm <strong><?= escapeHtml($product['name']) ?></strong> (ID: <?= escapeHtml($id) ?>)?</p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
        <button type="submit" class="danger">Xác nhận xóa</button>
        <a href="product_list.php">Hủy</a>
    </form>
<?php else: ?>
    <p><a href="product_list.php">Quay lại danh sách</a></p>
<?php endif; ?>
<?php require __DIR__ . '/view/footer.php'; ?>
