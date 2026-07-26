<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed.', 405);
}

$body = read_json_body();
$username = trim((string)($body['username'] ?? ''));
$password = (string)($body['password'] ?? '');

if ($username === '' || $password === '') {
    json_error('Username and password are required.', 422);
}

$stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$row = $stmt->fetch();

if (!$row || !password_verify($password, $row['password_hash'])) {
    json_error('Username or password not recognized.', 401);
}

$_SESSION['user'] = [
    'id' => (string)$row['id'],
    'username' => $row['username'],
    'role' => $row['role'],
];

json_response($_SESSION['user']);
