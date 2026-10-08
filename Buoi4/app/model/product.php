<?php
declare(strict_types=1);

require_once __DIR__ . '/../common/dbConnect.php';

function getAllProducts(): array
{
    return getDbConnection()->query('SELECT id, name, price, quantity FROM products ORDER BY id ASC')->fetchAll();
}

function getProductById($id): ?array
{
    $statement = getDbConnection()->prepare('SELECT id, name, price, quantity FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    $product = $statement->fetch();
    return $product === false ? null : $product;
}

function addProduct($name, $price, $quantity): bool
{
    $statement = getDbConnection()->prepare('INSERT INTO products (name, price, quantity) VALUES (:name, :price, :quantity)');
    return $statement->execute(['name' => $name, 'price' => $price, 'quantity' => $quantity]);
}

function updateProduct($id, $name, $price, $quantity): bool
{
    $statement = getDbConnection()->prepare('UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id');
    $statement->execute(['id' => $id, 'name' => $name, 'price' => $price, 'quantity' => $quantity]);
    // MySQL có thể trả rowCount = 0 nếu thông tin không thay đổi.
    return $statement->rowCount() > 0 || getProductById($id) !== null;
}

function deleteProduct($id): bool
{
    $statement = getDbConnection()->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    return $statement->rowCount() > 0;
}
