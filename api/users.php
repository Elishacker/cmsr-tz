<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

$admin = require_admin();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $pdo->query('SELECT id, username, role, created_at FROM users ORDER BY created_at ASC')->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (string)$r['id'];
    }
    json_response($rows);
}

if ($method === 'POST') {
    $body = read_json_body();
    $username = trim((string)($body['username'] ?? ''));
    $password = (string)($body['password'] ?? '');
    $role = (string)($body['role'] ?? 'Editor');

    if ($username === '' || $password === '') {
        json_error('Username and password are required.', 422);
    }
    if (!in_array($role, ['Admin', 'Editor'], true)) {
        json_error('Invalid role.', 422);
    }

    $check = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $check->execute([$username]);
    if ($check->fetch()) {
        json_error('That username already exists.', 409);
    }

    $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)');
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role]);

    json_response([
        'id' => (string)$pdo->lastInsertId(),
        'username' => $username,
        'role' => $role,
    ], 201);
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        json_error('Missing user id.', 422);
    }
    if ((string)$id === $admin['id']) {
        json_error('You cannot remove your own account while signed in.', 400);
    }

    $target = $pdo->prepare('SELECT role FROM users WHERE id = ?');
    $target->execute([$id]);
    $targetRow = $target->fetch();
    if (!$targetRow) {
        json_error('User not found.', 404);
    }
    if ($targetRow['role'] === 'Admin') {
        $adminCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'Admin'")->fetch()['c'];
        if ($adminCount <= 1) {
            json_error('Cannot remove the last remaining Admin account.', 400);
        }
    }

    $del = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $del->execute([$id]);

    json_response(['deleted' => true]);
}

json_error('Method not allowed.', 405);
