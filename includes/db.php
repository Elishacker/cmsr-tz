<?php
/**
 * PDO connection plus a few thin query helpers used across the site.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        $hint = 'Import <code>database/schema.sql</code> and check the DB_* constants in <code>config/config.php</code>.';
        exit('<h2 style="font-family:sans-serif">Database connection failed</h2>'
            . '<p style="font-family:sans-serif">' . htmlspecialchars($e->getMessage()) . '</p>'
            . '<p style="font-family:sans-serif">' . $hint . '</p>');
    }

    return $pdo;
}

/** Run a prepared statement and return the statement. */
function q(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/** All rows for a query. */
function fetch_all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

/** First row for a query, or null. */
function fetch_one(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row === false ? null : $row;
}

/** First column of the first row. */
function fetch_value(string $sql, array $params = [], $default = null)
{
    $value = q($sql, $params)->fetchColumn();
    return $value === false ? $default : $value;
}

/** Insert an associative array into a table; returns the new id. */
function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = sprintf(
        'INSERT INTO `%s` (%s) VALUES (%s)',
        $table,
        '`' . implode('`, `', $cols) . '`',
        ':' . implode(', :', $cols)
    );
    q($sql, $data);
    return (int) db()->lastInsertId();
}

/** Update a row by primary key; returns affected row count. */
function db_update(string $table, array $data, int $id, string $pk = 'id'): int
{
    $sets = [];
    foreach (array_keys($data) as $col) {
        $sets[] = "`$col` = :$col";
    }
    $data['__pk'] = $id;
    $sql = sprintf('UPDATE `%s` SET %s WHERE `%s` = :__pk', $table, implode(', ', $sets), $pk);
    return q($sql, $data)->rowCount();
}

/** Delete a row by primary key. */
function db_delete(string $table, int $id, string $pk = 'id'): int
{
    return q(sprintf('DELETE FROM `%s` WHERE `%s` = ?', $table, $pk), [$id])->rowCount();
}