<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function productId(mixed $id): int
{
    if (!is_string($id) || !ctype_digit($id)) {
        respond(400, ['success' => false, 'message' => 'ID phải là số nguyên dương.']);
    }

    $validated = filter_var($id, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]);
    if ($validated === false) {
        respond(400, ['success' => false, 'message' => 'ID phải nằm trong khoảng 1 đến 2147483647.']);
    }

    return $validated;
}
