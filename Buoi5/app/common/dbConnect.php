<?php
declare(strict_types=1);

// Có thể sửa các giá trị mặc định hoặc cấu hình bằng biến môi trường.
function getDbConnection(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_NAME') ?: 'shopping_cart';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD');
    $password = $password === false ? '' : $password;

    try {
        $connection = new PDO(
            "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ]
        );
    } catch (PDOException $error) {
        throw new RuntimeException('Kết nối MySQL thất bại.', 0, $error);
    }

    return $connection;
}
