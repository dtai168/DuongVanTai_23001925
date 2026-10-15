<?php
declare(strict_types=1);

require_once __DIR__ . '/../common/dbConnect.php';

function getUserByUsername(string $username): ?array
{
    $statement = getDbConnection()->prepare('SELECT id, username, password FROM users WHERE username = :username');
    $statement->execute(['username' => $username]);
    $user = $statement->fetch();
    return $user === false ? null : $user;
}

function createUser(string $username, string $password): void
{
    $statement = getDbConnection()->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $statement->execute([
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
}
