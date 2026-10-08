<?php require_once __DIR__ . '/../common/functions.php'; ?>
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
        <a href="product_list.php">Danh sách sản phẩm</a>
        <a href="product_add.php">Thêm sản phẩm</a>
    </nav>
</header>
<main>
    <h2><?= escapeHtml($pageTitle ?? 'Quản lý sản phẩm') ?></h2>
