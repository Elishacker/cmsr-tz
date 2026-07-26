<?php
declare(strict_types=1);
require __DIR__ . '/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error('Method not allowed.', 405);
}

json_response(current_user());
