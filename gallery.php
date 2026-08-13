<?php
/**
 * Photo gallery.
 *
 * Reads the picture library in photos/ (the folders supplied by the office)
 * and anything the staff have uploaded through the portal media library.
 */

require_once __DIR__ . '/includes/functions.php';

/** Albums: folder inside photos/ => label shown on the site. */
$albums = [
    'water and sanitation' => 'Water & Sanitation',
    'Health'               => 'Health',
    'Education'            => 'Education',
    'Agriculture'          => 'Agriculture',
    'School Program'       => 'Shule Programme',
    'slide show'           => 'Featured',
];

$viewable = ['jpg', 'jpeg', 'png', 'jfif', 'bmp', 'webp', 'gif'];

/** Every viewable picture in one album folder. */
function album_images(string $folder, array $viewable): array
{
    $dir = ROOT_PATH . '/photos/' . $folder;
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    foreach (scandir($dir) ?: [] as $file) {
        if ($file[0] === '.') {
            continue;
        }
        if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $viewable, true)) {
            continue;
        }
        $files[] = 'photos/' . $folder . '/' . $file;
    }
    sort($files);
    return $files;
}

$selected = (string) get('album');
$perPage  = 24;
$page     = max(1, (int) get('page', 1));

$images = [];
if ($selected !== '' && isset($albums[$selected])) {
    $images = album_images($selected, $viewable);
} else {
    $selected = '';
    foreach (array_keys($albums) as $folder) {
        $images = array_merge($images, album_images($folder, $viewable));
    }
    shuffle($images);
}

// Staff uploads join the "all" view and the media album.
if ($selected === '' || $selected === 'uploads') {
    foreach (fetch_all("SELECT file_path FROM media WHERE file_path <> '' ORDER BY id DESC") as $row) {
        if (is_file(ROOT_PATH . '/' . $row['file_path'])) {
            array_unshift($images, $row['file_path']);
        }
    }
}

$total = count($images);
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$slice = array_slice($images, ($page - 1) * $perPage, $perPage);

/** Turn a file name into a readable caption. */
function caption_for(string $path): string
{
    $name = pathinfo($path, PATHINFO_FILENAME);
    if (preg_match('/^(IMG|PXL|MVI|_MG|DSC)[-_0-9]*$/i', $name) || preg_match('/^[0-9_-]+$/', $name)) {
        $folder = basename(dirname($path));
        return $folder;
    }
    return ucfirst(trim(str_replace(['-', '_'], ' ', $name)));
}

$pageTitle       = 'Photo Gallery';
$pageDescription = 'Photographs from CMSR-TZ projects in water and sanitation, health, education, agriculture and the Shule Programme.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Photo Gallery';
$heroSubtitle = 'Our work, as recorded in the field';
$heroImage    = 'photos/slide show/Maji.jpg';
$heroCrumbs   = ['Resources' => 'resources.php', 'Photo Gallery' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">

        <div class="filter-bar">
            <a href="<?= url('gallery.php') ?>" class="filter-pill<?= $selected === '' ? ' active' : '' ?>">All photos</a>
            <?php foreach ($albums as $folder => $label): ?>
                <a href="<?= url('gallery.php?album=' . rawurlencode($folder)) ?>"
                   class="filter-pill<?= $selected === $folder ? ' active' : '' ?>"><?= h($label) ?></a>
            <?php endforeach; ?>
        </div>

        <p class="text-center mb-5" style="color:var(--p-color);">
            Showing <?= count($slice) ?> of <?= $total ?> photograph<?= $total === 1 ? '' : 's' ?>
            <?= $selected !== '' ? 'in ' . h($albums[$selected]) : '' ?>
        </p>

        <div class="row g-3">
            <?php foreach ($slice as $path): ?>
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="<?= h(img($path, 1800)) ?>" target="_blank" rel="noopener" class="gallery-item"
                       title="<?= h(caption_for($path)) ?>">
                        <img src="<?= h(img($path, 500)) ?>" alt="<?= h(caption_for($path)) ?>" loading="lazy">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$slice): ?>
            <p class="text-center py-5">No photographs in this album yet.</p>
        <?php endif; ?>

        <?php if ($pages > 1): ?>
            <nav aria-label="Gallery pages" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                        <a class="page-link" href="<?= url('gallery.php?' . http_build_query(array_filter(['album' => $selected, 'page' => $page - 1]))) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= min($pages, 12); $i++): ?>
                        <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                            <a class="page-link" href="<?= url('gallery.php?' . http_build_query(array_filter(['album' => $selected, 'page' => $i]))) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
                        <a class="page-link" href="<?= url('gallery.php?' . http_build_query(array_filter(['album' => $selected, 'page' => $page + 1]))) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
