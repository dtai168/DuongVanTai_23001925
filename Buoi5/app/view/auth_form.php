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
        <label for="username">Tên đăng nhập</label>
        <input id="username" name="username" maxlength="50" autocomplete="username" value="<?= escapeHtml($username) ?>" required>
    </p>
    <p>
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" autocomplete="<?= $pageTitle === 'Đăng ký' ? 'new-password' : 'current-password' ?>" required>
    </p>
    <button type="submit"><?= escapeHtml($submitLabel) ?></button>
</form>
