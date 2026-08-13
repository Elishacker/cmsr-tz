<?php
/**
 * Shared helpers for the public site and the staff portal.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

// ---------------------------------------------------------------------
// Output escaping and URLs
// ---------------------------------------------------------------------

/** Escape a value for HTML output. */
function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * URL for something inside the project.
 *
 * Path segments are percent-encoded so photo folders with spaces
 * ("photos/slide show/Maji.jpg") resolve, while any query string or fragment
 * is left alone — encoding the "?" of "crud.php?entity=news" would turn the
 * link into a request for a file that does not exist.
 */
function url(string $path): string
{
    $path = ltrim($path, '/');
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:')) {
        return $path;
    }

    // Split "path?query#fragment" and keep the tail verbatim.
    $tail = '';
    $cut = strcspn($path, '?#');
    if ($cut < strlen($path)) {
        $tail = substr($path, $cut);
        $path = substr($path, 0, $cut);
    }

    $segments = array_map('rawurlencode', explode('/', $path));
    return base_url(implode('/', $segments) . $tail);
}

/**
 * URL of an image, routed through the resizer so 5 MB originals are never
 * shipped to the browser. Falls back to a placeholder when empty/missing.
 */
function img(string $path, int $width = 1200): string
{
    $path = trim($path);
    if ($path === '') {
        return url('assets/images/placeholder.svg');
    }
    if (preg_match('#^(https?:)?//#i', $path)) {
        return $path;
    }
    if (!is_file(ROOT_PATH . '/' . $path)) {
        return url('assets/images/placeholder.svg');
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'jfif', 'bmp'], true)) {
        return url($path);
    }
    return base_url('thumb.php') . '?w=' . $width . '&f=' . rawurlencode($path);
}

/**
 * Markup for an icon field.
 *
 * The value may be a Bootstrap Icons class ("bi-people") or a path to a
 * picture ("assets/images/icons/vision-eye.svg"), so full-colour artwork can
 * be used where a single-colour glyph will not do.
 */
function icon_markup(?string $icon, string $alt = '', string $imgClass = 'icon-image'): string
{
    $icon = trim((string) $icon);
    if ($icon === '') {
        return '';
    }
    if (preg_match('#\.(svg|png|jpe?g|webp|gif)$#i', $icon) || str_contains($icon, '/')) {
        return '<img src="' . h(url($icon)) . '" class="' . h($imgClass) . '" alt="' . h($alt) . '">';
    }
    return '<i class="' . h($icon) . '"></i>';
}

/** True when $page matches the current script name. */
function is_current(string $page): bool
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '') === $page;
}

/** Send the visitor somewhere and stop. */
function redirect(string $path): void
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : base_url($path)));
    exit;
}

// ---------------------------------------------------------------------
// Settings
// ---------------------------------------------------------------------

/** A single site setting, cached for the request. */
function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (fetch_all('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = (string) $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

// ---------------------------------------------------------------------
// Text helpers
// ---------------------------------------------------------------------

/** Turn a title into a URL-safe slug. */
function slugify(string $text): string
{
    $text = strtr($text, ['—' => '-', '–' => '-', '’' => '', "'" => '']);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', $text) ?? '');
    return trim($text, '-') ?: 'item';
}

/** Ensure a slug is unique inside a table. */
function unique_slug(string $table, string $slug, ?int $ignoreId = null): string
{
    $base = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM `$table` WHERE slug = ?" . ($ignoreId ? ' AND id <> ?' : '');
        $params = $ignoreId ? [$slug, $ignoreId] : [$slug];
        if (!fetch_one($sql, $params)) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

/** Plain-text excerpt of an HTML string. */
function excerpt(?string $html, int $length = 160): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $html)) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length), " ,.;:") . '…';
}

/** Split a textarea value into an array of non-empty lines. */
function lines(?string $text): array
{
    if ($text === null || trim($text) === '') {
        return [];
    }
    $text = str_replace('\n', "\n", $text);
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text) ?: [])));
}

/** Format a date column for display. */
function fdate(?string $date, string $format = 'j F Y'): string
{
    if (!$date || $date === '0000-00-00') {
        return '';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : (string) $date;
}

/** Allow a safe subset of HTML from the editor fields. */
function safe_html(?string $html): string
{
    $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><h3><h4><h5><h6><a><blockquote><table><thead><tbody><tr><th><td><hr><span><small>';
    return strip_tags((string) $html, $allowed);
}

// ---------------------------------------------------------------------
// Flash messages
// ---------------------------------------------------------------------

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function take_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

// ---------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . h(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Security token expired. Please go back and try again.');
    }
}

// ---------------------------------------------------------------------
// Request helpers
// ---------------------------------------------------------------------

function post(string $key, $default = '')
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

function get(string $key, $default = '')
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

// ---------------------------------------------------------------------
// Uploads
// ---------------------------------------------------------------------

/**
 * Store an uploaded file under uploads/<folder>/ and return its
 * project-relative path, or null when nothing was uploaded.
 *
 * @throws RuntimeException on a rejected upload
 */
function handle_upload(string $field, string $folder = 'media', bool $imagesOnly = true): ?string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('The file is larger than ' . round(MAX_UPLOAD_BYTES / 1048576) . ' MB.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = $imagesOnly ? ALLOWED_IMAGE_EXT : array_merge(ALLOWED_IMAGE_EXT, ALLOWED_FILE_EXT);
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('File type ".' . $ext . '" is not allowed.');
    }
    if (in_array($ext, ALLOWED_IMAGE_EXT, true) && @getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('That file is not a valid image.');
    }

    $folder = preg_replace('/[^a-z0-9_-]/', '', strtolower($folder)) ?: 'media';
    $dir = UPLOAD_PATH . '/' . $folder;
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Could not create the upload folder.');
    }

    $name = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
    $name = substr($name, 0, 60) . '-' . date('YmdHis') . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }

    return 'uploads/' . $folder . '/' . $name;
}

// ---------------------------------------------------------------------
// Activity log
// ---------------------------------------------------------------------

function log_activity(string $action, string $entity = '', ?int $entityId = null, string $details = ''): void
{
    $user = $_SESSION['user'] ?? null;
    db_insert('activity_log', [
        'user_id'    => $user['id'] ?? null,
        'username'   => $user['username'] ?? 'guest',
        'action'     => $action,
        'entity'     => $entity,
        'entity_id'  => $entityId,
        'details'    => mb_substr($details, 0, 255),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
}

// ---------------------------------------------------------------------
// Content queries used by more than one page
// ---------------------------------------------------------------------

function all_sectors(): array
{
    static $sectors = null;
    if ($sectors === null) {
        $sectors = fetch_all('SELECT * FROM sectors WHERE is_active = 1 ORDER BY sort_order, id');
    }
    return $sectors;
}

function get_page(string $key): array
{
    $page = fetch_one('SELECT * FROM pages WHERE page_key = ?', [$key]);
    return $page ?: ['page_key' => $key, 'title' => ucfirst($key), 'subtitle' => '', 'hero_image' => '', 'body' => '', 'meta_description' => ''];
}
