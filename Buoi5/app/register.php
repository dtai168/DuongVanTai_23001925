<?php
require_once __DIR__ . '/common/auth.php';
require_once __DIR__ . '/model/user.php';

if (isLoggedIn()) {
    header('Location: product_list.php', true, 303);
    exit;
}

$username = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = postValue('username');
    $password = $_POST['password'] ?? '';
    if ($username === '' || !is_string($password) || trim($password) === '') {
        $errors[] = 'Tên đăng nhập và mật khẩu không được để trống.';
    } elseif (mb_strlen($username, 'UTF-8') > 50) {
        $errors[] = 'Tên đăng nhập không được dài quá 50 ký tự.';
    } elseif (strlen($password) > 72) {
        $errors[] = 'Mật khẩu không được vượt quá 72 byte.';
    }
    if (!validCsrfToken()) {
        $errors[] = 'Phiên xác nhận không hợp lệ. Vui lòng gửi lại biểu mẫu.';
    }
    if (!$errors) {
        try {
            if (getUserByUsername($username) !== null) {
                $errors[] = 'Tên đăng nhập đã tồn tại.';
            } else {
                createUser($username, $password);
                $_SESSION['flash_message'] = 'Đăng ký thành công. Vui lòng đăng nhập.';
                header('Location: login.php', true, 303);
                exit;
            }
        } catch (PDOException $error) {
            if (($error->errorInfo[1] ?? null) === 1062) {
                $errors[] = 'Tên đăng nhập đã tồn tại.';
            } else {
                showDatabaseError($error);
            }
        } catch (Throwable $error) {
            showDatabaseError($error);
        }
    }
    http_response_code(422);
}
$pageTitle = 'Đăng ký';
$submitLabel = 'Đăng ký';
require __DIR__ . '/view/header.php';
require __DIR__ . '/view/auth_form.php';
?>
<p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
<?php require __DIR__ . '/view/footer.php'; ?>
