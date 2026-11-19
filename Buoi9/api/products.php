<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/http.php';
require_once __DIR__ . '/product-images.php';

const PRODUCTS_PER_PAGE = 5;

/** Chỉ chấp nhận một đối tượng JSON và các giá trị nguyên hợp lệ. */
function productInput(): array
{
    try {
        $input = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        respond(400, ['success' => false, 'message' => 'Nội dung yêu cầu phải là JSON hợp lệ.']);
    }

    if (!$input instanceof stdClass) {
        respond(400, ['success' => false, 'message' => 'Nội dung JSON phải là một đối tượng sản phẩm.']);
    }

    if (!isset($input->name) || !is_string($input->name)) {
        respond(400, ['success' => false, 'message' => 'Tên sản phẩm phải là chuỗi từ 1 đến 100 ký tự.']);
    }

    $name = preg_replace('/^[\s\p{Z}]+|[\s\p{Z}]+$/u', '', $input->name);
    $nameLength = mb_strlen($name, 'UTF-8');
    if ($nameLength < 1 || $nameLength > 100) {
        respond(400, ['success' => false, 'message' => 'Tên sản phẩm phải có từ 1 đến 100 ký tự sau khi bỏ khoảng trắng ở hai đầu.']);
    }

    if (!isset($input->price) || !is_int($input->price) || $input->price < 1 || $input->price > 2147483647) {
        respond(400, ['success' => false, 'message' => 'Giá phải là số nguyên từ 1 đến 2147483647.']);
    }

    if (!isset($input->stock) || !is_int($input->stock) || $input->stock < 0 || $input->stock > 2147483647) {
        respond(400, ['success' => false, 'message' => 'Tồn kho phải là số nguyên từ 0 đến 2147483647.']);
    }

    return ['name' => $name, 'price' => $input->price, 'stock' => $input->stock];
}

/** Giữ kiểu số nhất quán trong JSON, không phụ thuộc cấu hình PDO. */
function serializeProduct(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'name' => $row['name'],
        'price' => (int) $row['price'],
        'stock' => (int) $row['stock'],
        'image' => $row['image'],
    ];
}

function requestedPage(): string
{
    $value = $_GET['page'] ?? '1';
    if (!is_string($value) || !ctype_digit($value) || ltrim($value, '0') === '') {
        respond(400, ['success' => false, 'message' => 'page phải là số nguyên dương.']);
    }
    if (($_GET['limit'] ?? '5') !== (string) PRODUCTS_PER_PAGE) {
        respond(400, ['success' => false, 'message' => 'limit phải bằng 5.']);
    }

    return $value;
}

function listProducts(PDO $pdo, string $requestedPage): array
{
    $limit = PRODUCTS_PER_PAGE;
    // Đếm và lấy danh sách từ cùng một ảnh chụp dữ liệu khi có thao tác ghi đồng thời.
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
    $pdo->beginTransaction();
    $counts = $pdo->query(
        'SELECT COUNT(*) AS total, COALESCE(SUM(stock), 0) AS total_stock,
                COALESCE(SUM(stock = 0), 0) AS out_of_stock
         FROM products'
    )->fetch();
    $total = (int) $counts['total'];
    $totalPages = max(1, (int) ceil($total / $limit));
    $page = min((int) $requestedPage, $totalPages);

    $statement = $pdo->prepare(
        'SELECT id, name, price, stock, image FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset'
    );
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
    $statement->execute();
    $rows = $statement->fetchAll();
    $pdo->commit();

    return [
        'success' => true,
        'data' => array_map('serializeProduct', $rows),
        'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => $totalPages],
        'summary' => ['total_stock' => (int) $counts['total_stock'], 'out_of_stock' => (int) $counts['out_of_stock']],
    ];
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, POST, PUT, DELETE');
    respond(405, ['success' => false, 'message' => 'Phương thức HTTP không được hỗ trợ.']);
}

// Kiểm tra đầu vào trước khi mở kết nối; dữ liệu sai không được gửi tới MySQL.
$id = in_array($method, ['PUT', 'DELETE'], true) ? productId($_GET['id'] ?? null) : null;
$input = in_array($method, ['POST', 'PUT'], true) ? productInput() : null;
$page = $method === 'GET' ? requestedPage() : null;

try {
    $pdo = connectDatabase();

    if ($method === 'GET') {
        respond(200, listProducts($pdo, $page));
    }

    if ($method === 'POST') {
        $statement = $pdo->prepare('INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)');
        $statement->execute($input);
        $product = ['id' => (int) $pdo->lastInsertId()] + $input + ['image' => null];
        respond(201, ['success' => true, 'data' => $product, 'message' => 'Đã thêm sản phẩm.']);
    }

    if ($method === 'PUT') {
        $statement = $pdo->prepare('UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id');
        $statement->execute($input + ['id' => $id]);

        // rowCount() có thể bằng 0 khi lưu lại dữ liệu không đổi.
        $lookup = $pdo->prepare('SELECT id, name, price, stock, image FROM products WHERE id = :id');
        $lookup->execute(['id' => $id]);
        $product = $lookup->fetch();
        if ($product === false) {
            respond(404, ['success' => false, 'message' => 'Không tìm thấy sản phẩm cần cập nhật.']);
        }

        respond(200, ['success' => true, 'data' => serializeProduct($product), 'message' => 'Đã cập nhật sản phẩm.']);
    }

    $pdo->beginTransaction();
    $lookup = $pdo->prepare('SELECT image FROM products WHERE id = :id FOR UPDATE');
    $lookup->execute(['id' => $id]);
    $product = $lookup->fetch();
    if ($product === false) {
        $pdo->rollBack();
        respond(404, ['success' => false, 'message' => 'Không tìm thấy sản phẩm cần xóa.']);
    }

    $statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    $pdo->commit();
    removeProductImage($product['image']);

    respond(200, ['success' => true, 'message' => 'Đã xóa sản phẩm.']);
} catch (PDOException $exception) {
    respond(500, [
        'success' => false,
        'message' => 'Không thể truy cập cơ sở dữ liệu. Hãy kiểm tra MySQL, cấu hình kết nối và nhập database.sql.',
    ]);
} catch (Throwable $exception) {
    respond(500, ['success' => false, 'message' => 'Máy chủ gặp lỗi khi xử lý yêu cầu.']);
}
