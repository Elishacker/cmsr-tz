<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed.', 405);
}

$_SESSION = [];
session_destroy();

json_response(['loggedOut' => true]);
