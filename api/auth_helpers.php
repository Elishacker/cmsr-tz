<?php
// Shared bootstrap for every /api endpoint: session handling + JSON helpers.

declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

function json_response($data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}

function json_error(string $message, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        json_error('You must be signed in.', 401);
    }
    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if ($user['role'] !== 'Admin') {
        json_error('Only Admin accounts can do this.', 403);
    }
    return $user;
}
