<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

start_app_session();

function redirect_admin(string $path): never
{
    header('Location: ' . url('admin/' . ltrim($path, '/')));
    exit;
}

function login_admin(array $user): void
{
    start_app_session();
    $_SESSION[ADMIN_SESSION_KEY] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
    ];
}

function logout_admin(): void
{
    start_app_session();
    unset($_SESSION[ADMIN_SESSION_KEY]);
}

function get_admin_user(): ?array
{
    start_app_session();
    return $_SESSION[ADMIN_SESSION_KEY] ?? null;
}
