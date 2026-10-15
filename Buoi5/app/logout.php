<?php
require_once __DIR__ . '/common/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validCsrfToken()) {
    http_response_code(403);
    $pageTitle = 'Đăng xuất';
    require __DIR__ . '/view/header.php';
    echo '<p class="error" role="alert">Vui lòng dùng nút Đăng xuất trên trang quản lý.</p>';
    require __DIR__ . '/view/footer.php';
    exit;
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
header('Location: login.php', true, 303);
exit;
