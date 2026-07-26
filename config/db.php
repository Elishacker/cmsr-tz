<?php
// Database connection for the CMSR-TZ site (XAMPP/MySQL defaults).
// Override with environment variables in production instead of editing this file.

$DB_HOST = getenv('CMSR_DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('CMSR_DB_PORT') ?: '3306';
$DB_NAME = getenv('CMSR_DB_NAME') ?: 'cmsr_tz';
$DB_USER = getenv('CMSR_DB_USER') ?: 'root';
$DB_PASS = getenv('CMSR_DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Database connection failed.']);
    exit;
}
