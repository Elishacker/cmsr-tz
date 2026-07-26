<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

require_login();

// Whitelisted sections: JS field name => real DB column name.
// Keeps the table/column names the query ever touches out of user input.
const SECTIONS = [
    'slideshow' => [
        'table' => 'slideshow',
        'order' => 'sort_order ASC, id ASC',
        'columns' => [
            'image' => 'image', 'eyebrow' => 'eyebrow', 'heading' => 'heading',
            'description' => 'description', 'btn1Text' => 'btn1_text', 'btn1Link' => 'btn1_link',
            'btn2Text' => 'btn2_text', 'btn2Link' => 'btn2_link',
        ],
    ],
    'programs' => [
        'table' => 'programs',
        'order' => 'sort_order ASC, id ASC',
        'columns' => [
            'image' => 'image', 'category' => 'category', 'title' => 'title',
            'description' => 'description', 'link' => 'link',
        ],
    ],
    'projects' => [
        'table' => 'projects',
        'order' => 'sort_order ASC, id ASC',
        'columns' => [
            'image' => 'image', 'category' => 'category', 'title' => 'title',
            'description' => 'description', 'link' => 'link',
        ],
    ],
    'news' => [
        'table' => 'news',
        'order' => 'id DESC',
        'columns' => [
            'image' => 'image', 'date' => 'news_date', 'title' => 'title',
            'excerpt' => 'excerpt', 'body' => 'body',
        ],
    ],
    'resources' => [
        'table' => 'resources',
        'order' => 'id DESC',
        'columns' => [
            'title' => 'title', 'category' => 'category',
            'fileLink' => 'file_link', 'description' => 'description',
        ],
    ],
    'reports' => [
        'table' => 'reports',
        'order' => 'id DESC',
        'columns' => [
            'title' => 'title', 'year' => 'year',
            'fileLink' => 'file_link', 'description' => 'description',
        ],
    ],
    'updates' => [
        'table' => 'updates',
        'order' => 'id DESC',
        'columns' => [
            'date' => 'update_date', 'title' => 'title', 'body' => 'body',
        ],
    ],
];

$section = (string)($_GET['section'] ?? '');
if (!isset(SECTIONS[$section])) {
    json_error('Unknown section.', 404);
}
$cfg = SECTIONS[$section];
$table = $cfg['table'];
$method = $_SERVER['REQUEST_METHOD'];

function row_to_js(array $row, array $columns): array
{
    $out = ['id' => (string)$row['id']];
    foreach ($columns as $jsKey => $dbCol) {
        $out[$jsKey] = $row[$dbCol] ?? '';
    }
    return $out;
}

if ($method === 'GET') {
    $rows = $pdo->query("SELECT * FROM `$table` ORDER BY {$cfg['order']}")->fetchAll();
    json_response(array_map(fn($r) => row_to_js($r, $cfg['columns']), $rows));
}

if ($method === 'POST') {
    $body = read_json_body();
    $id = isset($body['id']) ? (int)$body['id'] : 0;

    $set = [];
    $values = [];
    foreach ($cfg['columns'] as $jsKey => $dbCol) {
        $set[] = "`$dbCol` = ?";
        $values[] = trim((string)($body[$jsKey] ?? ''));
    }

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE `$table` SET " . implode(', ', $set) . ' WHERE id = ?');
        $stmt->execute([...$values, $id]);
        if ($stmt->rowCount() === 0) {
            $check = $pdo->prepare("SELECT id FROM `$table` WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                json_error('Entry not found.', 404);
            }
        }
    } else {
        $cols = array_map(fn($c) => "`$c`", array_values($cfg['columns']));
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $stmt = $pdo->prepare("INSERT INTO `$table` (" . implode(', ', $cols) . ") VALUES ($placeholders)");
        $stmt->execute($values);
        $id = (int)$pdo->lastInsertId();
    }

    $fetch = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
    $fetch->execute([$id]);
    json_response(row_to_js($fetch->fetch(), $cfg['columns']));
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        json_error('Missing id.', 422);
    }
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    json_response(['deleted' => true]);
}

json_error('Method not allowed.', 405);
