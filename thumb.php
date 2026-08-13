<?php
/**
 * On-the-fly image resizer with a disk cache.
 *
 * The photo library holds camera originals (some over 9 MB). Serving those
 * directly would make every page crawl, so images are requested through
 * thumb.php?f=<path>&w=<width>; the resized copy is written to cache/thumbs
 * and served straight from disk on later hits.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';

$file  = (string) ($_GET['f'] ?? '');
$width = max(80, min(2400, (int) ($_GET['w'] ?? 1200)));

// Keep the request inside the project: no absolute paths, no traversal.
// Only a path *segment* of ".." is traversal — file names may legitimately
// contain two dots ("Installation of solar water pumping system ..jpg").
$file = str_replace('\\', '/', $file);
if ($file === '' || $file[0] === '/' || in_array('..', explode('/', $file), true)) {
    http_response_code(400);
    exit('Bad request');
}

$source = ROOT_PATH . '/' . $file;
$real   = realpath($source);
if ($real === false || !str_starts_with($real, realpath(ROOT_PATH) . DIRECTORY_SEPARATOR) || !is_file($real)) {
    http_response_code(404);
    exit('Not found');
}

$ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
$readers = [
    'jpg'  => 'imagecreatefromjpeg',
    'jpeg' => 'imagecreatefromjpeg',
    'jfif' => 'imagecreatefromjpeg',
    'png'  => 'imagecreatefrompng',
    'bmp'  => 'imagecreatefrombmp',
    'webp' => 'imagecreatefromwebp',
];
if (!isset($readers[$ext]) || !function_exists($readers[$ext])) {
    // Nothing we can resize — stream the original.
    header('Content-Type: ' . (mime_content_type($real) ?: 'application/octet-stream'));
    header('Cache-Control: public, max-age=2592000');
    readfile($real);
    exit;
}

$cacheDir = CACHE_PATH . '/thumbs';
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0775, true);
}
$cacheFile = $cacheDir . '/' . sha1($file . '|' . $width . '|' . filemtime($real)) . '.jpg';

if (!is_file($cacheFile)) {
    $src = @$readers[$ext]($real);
    if (!$src) {
        http_response_code(415);
        exit('Unsupported image');
    }

    // Honour the EXIF orientation of phone photos.
    if (in_array($ext, ['jpg', 'jpeg', 'jfif'], true) && function_exists('exif_read_data')) {
        $exif = @exif_read_data($real);
        $orientation = (int) ($exif['Orientation'] ?? 0);
        if ($orientation === 3) {
            $src = imagerotate($src, 180, 0);
        } elseif ($orientation === 6) {
            $src = imagerotate($src, -90, 0);
        } elseif ($orientation === 8) {
            $src = imagerotate($src, 90, 0);
        }
    }

    $srcW = imagesx($src);
    $srcH = imagesy($src);
    $scale = min(1, $width / max(1, $srcW));
    $dstW = max(1, (int) round($srcW * $scale));
    $dstH = max(1, (int) round($srcH * $scale));

    $dst = imagecreatetruecolor($dstW, $dstH);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
    imagejpeg($dst, $cacheFile, 82);
    imagedestroy($src);
    imagedestroy($dst);
}

$etag = '"' . sha1_file($cacheFile) . '"';
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=2592000');
header('ETag: ' . $etag);

if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

header('Content-Length: ' . filesize($cacheFile));
readfile($cacheFile);
