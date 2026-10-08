<?php
require_once __DIR__ . '/common/functions.php';
require_once __DIR__ . '/model/product.php';

try {
    $products = getAllProducts();
} catch (Throwable $error) {
    showDatabaseError($error);
}

$pageTitle = 'Danh sách sản phẩm';
$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
require __DIR__ . '/view/header.php';
?>
<?php if ($message !== ''): ?>
    <p class="success" role="status"><?= escapeHtml($message) ?></p>
<?php endif; ?>
<p><a href="product_add.php">Thêm sản phẩm mới</a></p>
<div class="table-container">
    <table>
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Tên sản phẩm</th>
            <th scope="col">Giá (VNĐ)</th>
            <th scope="col">Số lượng</th>
            <th scope="col">Chức năng</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$products): ?>
            <tr><td colspan="5">Chưa có sản phẩm. Hãy thêm sản phẩm mới.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= escapeHtml($product['id']) ?></td>
                <td><?= escapeHtml($product['name']) ?></td>
                <td class="number"><?= number_format((float) $product['price'], 2, ',', '.') ?></td>
                <td class="number"><?= escapeHtml($product['quantity']) ?></td>
                <td>
                    <a href="product_edit.php?id=<?= (int) $product['id'] ?>">Sửa</a>
                    <a href="product_delete.php?id=<?= (int) $product['id'] ?>">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/view/footer.php'; ?>
