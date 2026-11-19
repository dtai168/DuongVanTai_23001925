<?php
declare(strict_types=1);

const PRODUCT_IMAGE_DIRECTORY = __DIR__ . '/../uploads';

function removeProductImage(?string $path): void
{
    // Chỉ xóa tệp có tên do API tạo trong thư mục uploads.
    if ($path !== null && preg_match('~^uploads/[a-f0-9]{32}\.(?:jpg|png)$~D', $path)) {
        @unlink(PRODUCT_IMAGE_DIRECTORY . '/' . basename($path));
    }
}
