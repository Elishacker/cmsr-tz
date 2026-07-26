<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

require_login();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $row = $pdo->query('SELECT image, heading, paragraphs FROM overview WHERE id = 1')->fetch();
    if (!$row) {
        json_response(['image' => '', 'heading' => '', 'paragraphs' => []]);
    }
    $paragraphs = $row['paragraphs'] !== null && $row['paragraphs'] !== ''
        ? array_values(array_filter(array_map('trim', explode("\n", $row['paragraphs']))))
        : [];
    json_response(['image' => $row['image'], 'heading' => $row['heading'], 'paragraphs' => $paragraphs]);
}

if ($method === 'POST') {
    $body = read_json_body();
    $image = trim((string)($body['image'] ?? ''));
    $heading = trim((string)($body['heading'] ?? ''));
    $paragraphs = is_array($body['paragraphs'] ?? null) ? $body['paragraphs'] : [];
    $paragraphsText = implode("\n", array_map('trim', $paragraphs));

    $stmt = $pdo->prepare(
        'INSERT INTO overview (id, image, heading, paragraphs) VALUES (1, ?, ?, ?)
         ON DUPLICATE KEY UPDATE image = VALUES(image), heading = VALUES(heading), paragraphs = VALUES(paragraphs)'
    );
    $stmt->execute([$image, $heading, $paragraphsText]);

    json_response(['image' => $image, 'heading' => $heading, 'paragraphs' => $paragraphs]);
}

json_error('Method not allowed.', 405);
