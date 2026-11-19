<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/http.php';
require_once __DIR__ . '/product-images.php';

const MAX_IMAGE_BYTES = 2 * 1024 * 1024;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    respond(405, ['success' => false, 'message' => 'Chỉ hỗ trợ phương thức POST.']);
}

$file = $_FILES['image'] ?? null;
$uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
if (
    $uploadError === UPLOAD_ERR_INI_SIZE
    || $uploadError === UPLOAD_ERR_FORM_SIZE
    || ($file === null && $_POST === [] && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > MAX_IMAGE_BYTES)
) {
    respond(413, ['success' => false, 'message' => 'Ảnh phải có dung lượng tối đa 2 MB.']);
}
if ($uploadError === UPLOAD_ERR_NO_FILE || is_array($uploadError)) {
    respond(400, ['success' => false, 'message' => 'Hãy chọn một tệp ảnh để tải lên.']);
}
if ($uploadError === UPLOAD_ERR_PARTIAL) {
    respond(400, ['success' => false, 'message' => 'Tệp được gửi chưa đầy đủ. Vui lòng thử lại.']);
}
if ($uploadError !== UPLOAD_ERR_OK) {
    respond(500, ['success' => false, 'message' => 'Máy chủ chưa nhận được ảnh. Vui lòng thử lại.']);
}

$id = productId($_POST['product_id'] ?? null);
$tempFile = $file['tmp_name'];
$size = $file['size'];
if ($size > MAX_IMAGE_BYTES) {
    respond(413, ['success' => false, 'message' => 'Ảnh phải có dung lượng tối đa 2 MB.']);
}
if ($size === 0 || !is_uploaded_file($tempFile)) {
    respond(400, ['success' => false, 'message' => 'Tệp ảnh tải lên trống hoặc không hợp lệ.']);
}

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$expectedMime = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
][$extension] ?? null;
$imageInfo = @getimagesize($tempFile);
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($tempFile);
if (
    $expectedMime === null
    || $mime !== $expectedMime
    || ($imageInfo['mime'] ?? null) !== $expectedMime
    || $imageInfo[0] < 1
    || $imageInfo[1] < 1
) {
    respond(415, ['success' => false, 'message' => 'Tệp phải là ảnh JPG, JPEG hoặc PNG hợp lệ.']);
}

$pdo = null;
$newPath = null;
try {
    $pdo = connectDatabase();
    $pdo->beginTransaction();
    $lookup = $pdo->prepare('SELECT image FROM products WHERE id = :id FOR UPDATE');
    $lookup->execute(['id' => $id]);
    $product = $lookup->fetch();
    if ($product === false) {
        $pdo->rollBack();
        respond(404, ['success' => false, 'message' => 'Không tìm thấy sản phẩm cần tải ảnh.']);
    }

    if (!is_dir(PRODUCT_IMAGE_DIRECTORY) && !@mkdir(PRODUCT_IMAGE_DIRECTORY, 0755) && !is_dir(PRODUCT_IMAGE_DIRECTORY)) {
        throw new RuntimeException('Không thể tạo thư mục uploads.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . ($mime === 'image/png' ? 'png' : 'jpg');
    if (!@move_uploaded_file($tempFile, PRODUCT_IMAGE_DIRECTORY . '/' . $filename)) {
        throw new RuntimeException('Không thể lưu tệp ảnh.');
    }
    $newPath = 'uploads/' . $filename;

    $update = $pdo->prepare('UPDATE products SET image = :image WHERE id = :id');
    $update->execute(['image' => $newPath, 'id' => $id]);
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo !== null && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    removeProductImage($newPath);
    respond(500, ['success' => false, 'message' => 'Không thể lưu ảnh. Vui lòng thử lại.']);
}

removeProductImage($product['image']);
respond(200, [
    'success' => true,
    'data' => ['id' => $id, 'image' => $newPath],
    'message' => 'Đã tải ảnh sản phẩm.',
]);
