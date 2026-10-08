<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function escapeHtml($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validCsrfToken(): bool
{
    $token = $_POST['csrf_token'] ?? null;
    return is_string($token) && hash_equals(csrfToken(), $token);
}

function postValue(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function productId(): ?int
{
    $value = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]);
    return $value === false ? null : $value;
}

function validateProduct(string $name, string $price, string $quantity): array
{
    $errors = [];
    if ($name === '') {
        $errors[] = 'Tên sản phẩm không được rỗng.';
    } elseif (mb_strlen($name, 'UTF-8') > 100) {
        $errors[] = 'Tên sản phẩm không được dài quá 100 ký tự.';
    }

    // DECIMAL(10,2): tối đa 8 chữ số nguyên và 2 chữ số thập phân.
    if (!preg_match('/^[0-9]{1,8}(\.[0-9]{1,2})?$/D', $price) || (float) $price <= 0) {
        $errors[] = 'Giá phải lớn hơn 0, tối đa 99.999.999,99 và có tối đa 2 chữ số thập phân (dùng dấu chấm).';
    }

    if (!preg_match('/^[0-9]+$/D', $quantity)
        || filter_var($quantity, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 2147483647],
        ]) === false) {
        $errors[] = 'Số lượng phải là số nguyên từ 0 đến 2.147.483.647.';
    }
    return $errors;
}

function redirectWithMessage(string $message): void
{
    $_SESSION['flash_message'] = $message;
    header('Location: product_list.php', true, 303);
    exit;
}

function showDatabaseError(Throwable $error): void
{
    error_log($error->getMessage());
    http_response_code(500);
    $pageTitle = 'Lỗi cơ sở dữ liệu';
    require __DIR__ . '/../view/header.php';
    echo '<p class="error" role="alert">Không thể kết nối hoặc xử lý dữ liệu. Vui lòng kiểm tra MySQL, cấu hình kết nối và nhập file database.sql.</p>';
    require __DIR__ . '/../view/footer.php';
    exit;
}
