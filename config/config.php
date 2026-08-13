<?php
/**
 * CMSR-TZ — application configuration.
 *
 * Everything the site needs to boot: database credentials, paths and the
 * session. Edit the DB_* constants if your MySQL user/password differ.
 */

declare(strict_types=1);

// --- Database --------------------------------------------------------
const DB_HOST = 'localhost';
const DB_NAME = 'cmsr_tz';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// --- Paths -----------------------------------------------------------
define('ROOT_PATH', dirname(__DIR__));                 // .../website
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('CACHE_PATH', ROOT_PATH . '/cache');

/**
 * Public base URL of the site, worked out from the script location so the
 * project keeps working whether it sits at /CMSRTZ/website or at the root
 * of a virtual host.
 */
function base_url(string $path = ''): string
{
    static $base = null;
    if ($base === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $dir = rtrim(dirname($script), '/');
        // Anything served from /admin belongs one level up.
        if (substr($dir, -6) === '/admin') {
            $dir = substr($dir, 0, -6);
        }
        $base = $dir === '' ? '' : $dir;
    }
    return $base . '/' . ltrim($path, '/');
}

// --- Uploads ---------------------------------------------------------
const MAX_UPLOAD_BYTES = 12 * 1024 * 1024; // 12 MB
const ALLOWED_IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'bmp'];
const ALLOWED_FILE_EXT  = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip'];

// --- Errors ----------------------------------------------------------
// Set to false on a live server.
const DEBUG = true;
error_reporting(DEBUG ? E_ALL : 0);
ini_set('display_errors', DEBUG ? '1' : '0');

date_default_timezone_set('Africa/Dar_es_Salaam');

// --- Session ---------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_name('CMSRTZSESS');
    session_start();
}
