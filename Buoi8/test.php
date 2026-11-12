<?php
declare(strict_types=1);

// Chỉ chạy từ terminal để tránh việc mở URL vô tình làm thay đổi dữ liệu.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Hãy chạy kiểm thử bằng lệnh php test.php trong terminal.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$baseUrl = rtrim($argv[1] ?? 'http://127.0.0.1:8000', '/');
if (!filter_var($baseUrl, FILTER_VALIDATE_URL)
    || !in_array(parse_url($baseUrl, PHP_URL_SCHEME), ['http', 'https'], true)) {
    fwrite(STDERR, "Địa chỉ không hợp lệ. Ví dụ: php test.php http://127.0.0.1:8000\n");
    exit(1);
}

/** Gửi HTTP thật đến API; không cần cài PHPUnit hay extension cURL. */
function requestApi(string $baseUrl, string $method, string $query = '', $payload = null): array
{
    $headers = "Accept: application/json\r\n";
    $options = [
        'method' => $method,
        'ignore_errors' => true, // Đọc được JSON cả khi API trả 400/404/405/500.
        'timeout' => 10,
    ];

    if ($payload !== null) {
        $headers .= "Content-Type: application/json\r\n";
        $options['content'] = is_string($payload)
            ? $payload
            : json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    $options['header'] = $headers;
    $context = stream_context_create(['http' => $options]);
    $raw = @file_get_contents($baseUrl . '/api/products.php' . $query, false, $context);
    if ($raw === false) {
        throw new RuntimeException('Không kết nối được API. Kiểm tra URL và máy chủ PHP.');
    }

    $status = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $status = (int) $matches[1];
        }
    }

    try {
        $body = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $error) {
        throw new RuntimeException('API không trả về JSON hợp lệ (HTTP ' . $status . ').');
    }
    if (!is_array($body) || !array_key_exists('success', $body)) {
        throw new RuntimeException('Phản hồi JSON phải có trường success.');
    }

    return [$status, $body];
}

$passed = 0;
$failed = 0;
$createdIds = [];
$prefix = 'Test Buoi8 ' . bin2hex(random_bytes(5));

$check = function (bool $condition, string $label) use (&$passed, &$failed): void {
    if ($condition) {
        $passed++;
        echo "[PASS] $label\n";
    } else {
        $failed++;
        echo "[FAIL] $label\n";
    }
};

// Ghi lại mọi bản ghi do bộ kiểm thử thêm để dọn dẹp cả khi kiểm tra thất bại.
$request = function (string $method, string $query = '', $payload = null)
    use ($baseUrl, &$createdIds): array {
    $response = requestApi($baseUrl, $method, $query, $payload);
    if ($method === 'POST' && $response[1]['success'] === true) {
        $newId = $response[1]['data']['id'] ?? null;
        if (is_int($newId) && $newId > 0) {
            $createdIds[$newId] = $newId;
        }
    }
    return $response;
};

$expectError = function (array $response, int $expectedStatus, string $label) use ($check): void {
    [$status, $body] = $response;
    $check(
        $status === $expectedStatus
        && $body['success'] === false
        && is_string($body['message'] ?? null)
        && trim($body['message']) !== '',
        $label
    );
};

echo "Kiểm thử API: $baseUrl/api/products.php\n";
echo "Chỉ thêm/xóa bản ghi tạm của bộ kiểm thử; không sửa dữ liệu có sẵn.\n\n";

try {
    [$status, $body] = $request('GET');
    $check($status === 200 && $body['success'] === true && is_array($body['data'] ?? null),
        'GET trả danh sách sản phẩm');
    if ($status !== 200 || $body['success'] !== true || !is_array($body['data'] ?? null)) {
        throw new RuntimeException($body['message'] ?? 'Không đọc được danh sách ban đầu.');
    }

    $initialProducts = $body['data'];
    $validTypes = true;
    foreach ($initialProducts as $product) {
        $validTypes = $validTypes
            && is_int($product['id'] ?? null)
            && is_string($product['name'] ?? null)
            && is_int($product['price'] ?? null)
            && is_int($product['stock'] ?? null);
    }
    $check($validTypes, 'GET trả đúng kiểu dữ liệu id, name, price, stock');

    $name = $prefix . ' – Bút thử';
    $validProduct = ['name' => '  ' . $name . '  ', 'price' => 15000, 'stock' => 7];
    [$status, $body] = $request('POST', '', $validProduct);
    $id = $body['data']['id'] ?? null;
    $check($status === 201 && $body['success'] === true && is_int($id) && $id > 0,
        'POST thêm sản phẩm hợp lệ');
    if (!is_int($id) || $id <= 0 || $body['success'] !== true) {
        throw new RuntimeException($body['message'] ?? 'POST không trả id sản phẩm mới.');
    }
    $check(($body['data']['name'] ?? null) === $name,
        'PHP bỏ khoảng trắng hai đầu và giữ đúng tên tiếng Việt');

    [, $list] = $request('GET');
    $saved = array_values(array_filter($list['data'], fn(array $p): bool => $p['id'] === $id));
    $check(count($saved) === 1 && $saved[0]['name'] === $name
        && $saved[0]['price'] === 15000 && $saved[0]['stock'] === 7,
        'GET tiếp theo vẫn đọc được sản phẩm vừa thêm');

    $updated = ['name' => $name, 'price' => 18000, 'stock' => 9];
    [$status, $body] = $request('PUT', '?id=' . $id, $updated);
    $check($status === 200 && $body['success'] === true, 'PUT cập nhật sản phẩm');
    [, $list] = $request('GET');
    $saved = array_values(array_filter($list['data'], fn(array $p): bool => $p['id'] === $id));
    $check(count($saved) === 1 && $saved[0]['price'] === 18000 && $saved[0]['stock'] === 9,
        'GET hiển thị đơn giá và tồn kho mới');
    [$status, $body] = $request('PUT', '?id=' . $id, $updated);
    $check($status === 200 && $body['success'] === true,
        'PUT dữ liệu không đổi vẫn thành công với sản phẩm tồn tại');

    [, $before] = $request('GET');
    $expectError($request('POST', '', ['name' => $prefix . ' tồn âm', 'price' => 1000, 'stock' => -1]),
        400, 'POST trực tiếp với stock = -1 bị PHP từ chối');
    [, $after] = $request('GET');
    $check($before['data'] === $after['data'], 'Tồn kho âm không tạo bản ghi trong MySQL');

    // Các yêu cầu dưới đây đều gửi trực tiếp, không dựa vào validation trên Vue.
    $invalidFields = [
        ['name', '', 'Tên rỗng'],
        ['name', " \t\n ", 'Tên chỉ có khoảng trắng'],
        ['name', str_repeat('ớ', 101), 'Tên vượt quá 100 ký tự Unicode'],
        ['name', 123, 'Tên kiểu số'],
        ['name', null, 'Tên null'],
        ['name', [], 'Tên kiểu mảng'],
        ['price', 0, 'Đơn giá bằng 0'],
        ['price', -1, 'Đơn giá âm'],
        ['price', 1000.5, 'Đơn giá thập phân'],
        ['price', '1000', 'Đơn giá là chuỗi số'],
        ['price', true, 'Đơn giá kiểu boolean'],
        ['price', null, 'Đơn giá null'],
        ['price', [], 'Đơn giá kiểu mảng'],
        ['price', 2147483648, 'Đơn giá vượt phạm vi INT'],
        ['stock', -1, 'Tồn kho âm khi sửa'],
        ['stock', 1.5, 'Tồn kho thập phân'],
        ['stock', '2', 'Tồn kho là chuỗi số'],
        ['stock', false, 'Tồn kho kiểu boolean'],
        ['stock', null, 'Tồn kho null'],
        ['stock', [], 'Tồn kho kiểu mảng'],
        ['stock', 2147483648, 'Tồn kho vượt phạm vi INT'],
    ];
    foreach ($invalidFields as [$field, $value, $label]) {
        $invalid = $updated;
        $invalid[$field] = $value;
        $expectError($request('PUT', '?id=' . $id, $invalid), 400, $label . ' bị từ chối');
    }
    foreach (['name', 'price', 'stock'] as $field) {
        $invalid = $updated;
        unset($invalid[$field]);
        $expectError($request('POST', '', $invalid), 400, 'POST thiếu trường ' . $field . ' bị từ chối');
    }
    $expectError($request('POST', '', '{broken json'), 400, 'JSON sai cú pháp bị từ chối');
    $expectError($request('POST', '', '[]'), 400, 'JSON phải là object sản phẩm');
    [, $after] = $request('GET');
    $check($before['data'] === $after['data'], 'Các yêu cầu không hợp lệ không thay đổi dữ liệu');

    foreach (['', '?id=0', '?id=-1', '?id=abc', '?id=1.5', '?id[]=1'] as $query) {
        $expectError($request('PUT', $query, $updated), 400,
            'PUT từ chối id không hợp lệ: ' . ($query === '' ? '(thiếu id)' : $query));
        $expectError($request('DELETE', $query), 400,
            'DELETE từ chối id không hợp lệ: ' . ($query === '' ? '(thiếu id)' : $query));
    }

    $existingIds = array_column($after['data'], 'id');
    $unknownId = 2147483647;
    while (in_array($unknownId, $existingIds, true)) {
        $unknownId--;
    }
    $expectError($request('PUT', '?id=' . $unknownId, $updated), 404,
        'PUT báo lỗi khi sản phẩm không tồn tại');
    $expectError($request('DELETE', '?id=' . $unknownId), 404,
        'DELETE báo lỗi khi sản phẩm không tồn tại');
    $expectError($request('PATCH', '?id=' . $id, $updated), 405,
        'Phương thức không hỗ trợ vẫn trả lỗi JSON');

    [$status, $body] = $request('DELETE', '?id=' . $id);
    $check($status === 200 && $body['success'] === true, 'DELETE xóa sản phẩm tạm');
    if ($status === 200 && $body['success'] === true) {
        unset($createdIds[$id]);
    }
    [, $list] = $request('GET');
    $check(!in_array($id, array_column($list['data'], 'id'), true),
        'GET tiếp theo không còn sản phẩm đã xóa');
    $expectError($request('DELETE', '?id=' . $id), 404, 'Xóa lại sản phẩm đã xóa trả lỗi');
} catch (Throwable $error) {
    $failed++;
    fwrite(STDERR, '[FAIL] ' . $error->getMessage() . "\n");
} finally {
    foreach ($createdIds as $createdId) {
        try {
            [$status, $body] = requestApi($baseUrl, 'DELETE', '?id=' . $createdId);
            if (!(($status === 200 && $body['success'] === true) || $status === 404)) {
                throw new RuntimeException('API không xóa được sản phẩm tạm.');
            }
        } catch (Throwable $error) {
            $failed++;
            fwrite(STDERR, '[FAIL] Không dọn được sản phẩm tạm id=' . $createdId . ": "
                . $error->getMessage() . "\n");
        }
    }
}

echo "\nKết quả: $passed đạt, $failed lỗi.\n";
exit($failed === 0 ? 0 : 1);
