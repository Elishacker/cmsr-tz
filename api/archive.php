<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

require_login();
$method = $_SERVER['REQUEST_METHOD'];

const UPLOAD_DIR = __DIR__ . '/../uploads/archive/';
const UPLOAD_URL_PREFIX = 'uploads/archive/';
const MAX_UPLOAD_BYTES = 3 * 1024 * 1024; // 3MB
const ALLOWED_MIME = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];

if ($method === 'GET') {
    $rows = $pdo->query('SELECT id, name, url, created_at FROM archive ORDER BY created_at DESC')->fetchAll();
    foreach ($rows as &$r) {
        $r['id'] = (string)$r['id'];
    }
    json_response($rows);
}

if ($method === 'POST') {
    $name = '';
    $url = '';

    if (!empty($_FILES['file'])) {
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            json_error('Upload failed. Please try again.', 400);
        }
        if ($file['size'] > MAX_UPLOAD_BYTES) {
            json_error('That image is too large (max 3MB).', 413);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset(ALLOWED_MIME[$mime])) {
            json_error('Only JPG, PNG, GIF or WEBP images are allowed.', 422);
        }

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0775, true);
        }

        $ext = ALLOWED_MIME[$mime];
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename)) {
            json_error('Could not save the uploaded file.', 500);
        }

        $url = UPLOAD_URL_PREFIX . $filename;
        $name = trim((string)($_POST['name'] ?? '')) ?: pathinfo($file['name'], PATHINFO_FILENAME);
    } else {
        $body = read_json_body();
        $name = trim((string)($body['name'] ?? ''));
        $url = trim((string)($body['url'] ?? ''));
    }

    if ($name === '' || $url === '') {
        json_error('Please provide both a name and an image URL/path.', 422);
    }

    $stmt = $pdo->prepare('INSERT INTO archive (name, url) VALUES (?, ?)');
    $stmt->execute([$name, $url]);

    json_response(['id' => (string)$pdo->lastInsertId(), 'name' => $name, 'url' => $url], 201);
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        json_error('Missing archive id.', 422);
    }

    $stmt = $pdo->prepare('SELECT url FROM archive WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        json_error('Not found.', 404);
    }

    if (str_starts_with($row['url'], UPLOAD_URL_PREFIX)) {
        $path = __DIR__ . '/../' . $row['url'];
        if (is_file($path)) {
            unlink($path);
        }
    }

    $del = $pdo->prepare('DELETE FROM archive WHERE id = ?');
    $del->execute([$id]);

    json_response(['deleted' => true]);
}

json_error('Method not allowed.', 405);
