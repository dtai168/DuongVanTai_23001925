<?php if ($errors): ?>
    <ul class="error" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= escapeHtml($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
    <p>
        <label for="name">Tên sản phẩm</label>
        <input type="text" id="name" name="name" value="<?= escapeHtml($name) ?>" maxlength="100" required>
    </p>
    <p>
        <label for="price">Giá (VNĐ)</label>
        <input type="number" id="price" name="price" value="<?= escapeHtml($price) ?>" min="0.01" max="99999999.99" step="0.01" required>
    </p>
    <p>
        <label for="quantity">Số lượng</label>
        <input type="number" id="quantity" name="quantity" value="<?= escapeHtml($quantity) ?>" min="0" max="2147483647" step="1" required>
    </p>
    <button type="submit"><?= escapeHtml($submitLabel) ?></button>
    <a href="product_list.php">Hủy</a>
</form>
