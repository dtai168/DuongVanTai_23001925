<?php
require_once __DIR__ . '/common/auth.php';
require_once __DIR__ . '/model/user.php';

if (isLoggedIn()) {
    header('Location: product_list.php', true, 303);
    exit;
}
$username = '';
$errors = [];
$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = postValue('username');
    $password = $_POST['password'] ?? '';
    if ($username === '' || !is_string($password) || trim($password) === '') {
        $errors[] = 'Tên đăng nhập và mật khẩu không được để trống.';
    }
    if (!validCsrfToken()) {
        $errors[] = 'Phiên xác nhận không hợp lệ. Vui lòng gửi lại biểu mẫu.';
    }
    if (!$errors) {
        try {
            $user = getUserByUsername($username);
            if ($user !== null && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION = ['user_id' => (int) $user['id'], 'username' => $user['username']];
                header('Location: product_list.php', true, 303);
                exit;
            }
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        } catch (Throwable $error) {
            showDatabaseError($error);
        }
    }
    http_response_code(422);
}
$pageTitle = 'Đăng nhập';
$submitLabel = 'Đăng nhập';
require __DIR__ . '/view/header.php';
if ($message !== ''): ?>
<p class="success" role="status"><?= escapeHtml($message) ?></p>
<?php endif;
require __DIR__ . '/view/auth_form.php'; ?>
<p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
<?php require __DIR__ . '/view/footer.php'; ?>
