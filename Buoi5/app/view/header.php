<?php require_once __DIR__ . '/../common/auth.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapeHtml($pageTitle ?? 'Quản lý sản phẩm') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <h1>Quản lý sản phẩm</h1>
    <nav aria-label="Điều hướng">
        <?php if (isLoggedIn()): ?>
        <a href="product_list.php">Danh sách sản phẩm</a>
        <a href="product_add.php">Thêm sản phẩm</a>
        <span>Tài khoản: <?= escapeHtml($_SESSION['username']) ?></span>
        <form method="post" action="logout.php" class="logout-form">
            <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">
            <button type="submit" class="logout-link">Đăng xuất</button>
        </form>
        <?php else: ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng ký</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <h2><?= escapeHtml($pageTitle ?? 'Quản lý sản phẩm') ?></h2>
