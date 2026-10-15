<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php', true, 303);
        exit;
    }
}
