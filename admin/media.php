<?php
/**
 * Media library.
 *
 * Lists everything in the supplied photos/ folders plus anything staff have
 * uploaded, handles new uploads and deletions, and serves the JSON feed used
 * by the picture picker on every image field.
 */

require_once __DIR__ . '/includes/auth.php';

require_login();

const VIEWABLE_EXT = ['jpg', 'jpeg', 'png', 'jfif', 'bmp', 'webp', 'gif'];

/**
 * Every picture available to the site: the photos/ library folders,
 * the uploads folder and anything recorded in the media table.
 */
function picture_library(): array
{
    $items = [];

    // 1. Supplied photo folders
    $photosDir = ROOT_PATH . '/photos';
    if (is_dir($photosDir)) {
        foreach (scandir($photosDir) ?: [] as $folder) {
            if ($folder[0] === '.' || !is_dir($photosDir . '/' . $folder)) {
                continue;
            }
            foreach (scandir($photosDir . '/' . $folder) ?: [] as $file) {
                if ($file[0] === '.') {
                    continue;
                }
                if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), VIEWABLE_EXT, true)) {
                    continue;
                }
                $items[] = ['path' => 'photos/' . $folder . '/' . $file, 'folder' => $folder, 'name' => $file];
            }
        }
    }

    // 2. Uploads
    $uploadsDir = UPLOAD_PATH . '/media';
    if (is_dir($uploadsDir)) {
        foreach (scandir($uploadsDir) ?: [] as $file) {
            if ($file[0] === '.') {
                continue;
            }
            if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), VIEWABLE_EXT, true)) {
                continue;
            }
            $items[] = ['path' => 'uploads/media/' . $file, 'folder' => 'Uploads', 'name' => $file];
        }
    }

    // 3. Site assets worth reusing (logos and the like)
    foreach (['assets/images'] as $dir) {
        $full = ROOT_PATH . '/' . $dir;
        if (!is_dir($full)) {
            continue;
        }
        foreach (scandir($full) ?: [] as $file) {
            if ($file[0] === '.' || !is_file($full . '/' . $file)) {
                continue;
            }
            if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), VIEWABLE_EXT, true)) {
                continue;
            }
            $items[] = ['path' => $dir . '/' . $file, 'folder' => 'Site assets', 'name' => $file];
        }
    }

    usort($items, fn($a, $b) => [$a['folder'], $a['name']] <=> [$b['folder'], $b['name']]);
    return $items;
}

// ---------------------------------------------------------------------
// JSON feed for the picker
// ---------------------------------------------------------------------
if (get('json') === '1') {
    header('Content-Type: application/json');
    $items = picture_library();
    $folders = array_values(array_unique(array_column($items, 'folder')));
    sort($folders);

    echo json_encode([
        'folders' => $folders,
        'items'   => array_map(fn($item) => [
            'path'   => $item['path'],
            'name'   => $item['name'],
            'folder' => $item['folder'],
            'thumb'  => img($item['path'], 300),
        ], $items),
    ]);
    exit;
}

// ---------------------------------------------------------------------
// Upload / delete
// ---------------------------------------------------------------------
if (is_post()) {
    require_edit();
    csrf_check();

    if (post('action') === 'delete') {
        $mediaId = (int) post('id');
        $row = fetch_one('SELECT * FROM media WHERE id = ?', [$mediaId]);
        if ($row) {
            // Only files inside uploads/ may be removed — never the supplied library.
            if (str_starts_with($row['file_path'], 'uploads/') && is_file(ROOT_PATH . '/' . $row['file_path'])) {
                @unlink(ROOT_PATH . '/' . $row['file_path']);
            }
            db_delete('media', $mediaId);
            log_activity('delete', 'media', $mediaId, 'Deleted ' . $row['file_path']);
            flash('Picture removed.');
        }
        redirect('admin/media.php');
    }

    if (post('action') === 'upload') {
        $saved = 0;
        $files = $_FILES['files'] ?? null;

        if ($files && is_array($files['name'])) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                // handle_upload() reads one file at a time from $_FILES.
                $_FILES['single'] = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                try {
                    $path = handle_upload('single', 'media', true);
                    if ($path !== null) {
                        db_insert('media', [
                            'title'       => (string) post('title', '') ?: pathinfo($files['name'][$i], PATHINFO_FILENAME),
                            'file_path'   => $path,
                            'folder'      => 'uploads',
                            'mime_type'   => (string) (mime_content_type(ROOT_PATH . '/' . $path) ?: ''),
                            'file_size'   => (int) filesize(ROOT_PATH . '/' . $path),
                            'alt_text'    => (string) post('alt_text'),
                            'uploaded_by' => current_user()['id'] ?? null,
                        ]);
                        $saved++;
                    }
                } catch (RuntimeException $e) {
                    flash($files['name'][$i] . ': ' . $e->getMessage(), 'danger');
                }
            }
            unset($_FILES['single']);
        }

        if ($saved > 0) {
            log_activity('upload', 'media', null, $saved . ' picture(s) uploaded');
            flash($saved . ' picture' . ($saved === 1 ? '' : 's') . ' uploaded.');
        }
        redirect('admin/media.php');
    }
}

// ---------------------------------------------------------------------
// Library screen
// ---------------------------------------------------------------------
$search  = (string) get('q');
$folder  = (string) get('folder');
$perPage = 36;
$page    = max(1, (int) get('page', 1));

$library = picture_library();
$folders = array_values(array_unique(array_column($library, 'folder')));
sort($folders);

$filtered = array_values(array_filter($library, function (array $item) use ($search, $folder) {
    if ($folder !== '' && $item['folder'] !== $folder) {
        return false;
    }
    return $search === '' || stripos($item['path'], $search) !== false;
}));

$total = count($filtered);
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$slice = array_slice($filtered, ($page - 1) * $perPage, $perPage);

// Uploaded pictures carry a database row that can be deleted.
$uploadIds = [];
foreach (fetch_all("SELECT id, file_path FROM media WHERE file_path LIKE 'uploads/%'") as $row) {
    $uploadIds[$row['file_path']] = (int) $row['id'];
}

function media_url(array $overrides = []): string
{
    $params = array_merge([
        'q'      => (string) get('q'),
        'folder' => (string) get('folder'),
        'page'   => (string) get('page'),
    ], $overrides);
    return url('admin/media.php?' . http_build_query(array_filter($params, fn($v) => $v !== '')));
}

$adminTitle  = 'Media library';
$adminActive = 'media';
require __DIR__ . '/includes/header.php';
?>

<?php if (can_edit()): ?>
    <div class="admin-card">
        <div class="admin-card-header"><h2>Upload pictures</h2></div>
        <div class="admin-card-body">
            <form method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload">

                <div class="col-md-6">
                    <label class="form-label" for="files">Choose one or more pictures</label>
                    <input type="file" class="form-control" id="files" name="files[]" accept="image/*" multiple required>
                    <div class="form-hint">JPG, PNG, GIF or WebP, up to <?= (int) round(MAX_UPLOAD_BYTES / 1048576) ?> MB each.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="alt_text">Description (optional)</label>
                    <input type="text" class="form-control" id="alt_text" name="alt_text" placeholder="What the picture shows">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-cmsr w-100"><i class="bi-upload me-1"></i>Upload</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>Picture library</h2>
        <span class="form-hint mb-0"><?= $total ?> pictures</span>

        <form method="get" class="ms-auto d-flex gap-2 flex-wrap">
            <input type="search" class="form-control form-control-sm" name="q" value="<?= h($search) ?>"
                   placeholder="Search file names…" style="min-width:200px;">
            <select class="form-select form-select-sm" name="folder" style="min-width:180px;" onchange="this.form.submit()">
                <option value="">All folders</option>
                <?php foreach ($folders as $name): ?>
                    <option value="<?= h($name) ?>" <?= $folder === $name ? 'selected' : '' ?>><?= h($name) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-outline-cmsr"><i class="bi-search"></i></button>
        </form>
    </div>

    <div class="admin-card-body">
        <?php if (!$slice): ?>
            <p class="text-center py-4 mb-0" style="color:var(--a-muted);">No pictures match this filter.</p>
        <?php endif; ?>

        <div class="media-grid">
            <?php foreach ($slice as $item): ?>
                <div class="media-item">
                    <a href="<?= h(img($item['path'], 1600)) ?>" target="_blank" rel="noopener">
                        <img src="<?= h(img($item['path'], 400)) ?>" alt="<?= h($item['name']) ?>" loading="lazy">
                    </a>
                    <div class="media-meta">
                        <div class="text-truncate" title="<?= h($item['name']) ?>"><?= h($item['name']) ?></div>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="chip chip-info"><?= h($item['folder']) ?></span>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-cmsr py-0 px-1" type="button"
                                        title="Copy the path"
                                        onclick="navigator.clipboard.writeText('<?= h(addslashes($item['path'])) ?>');this.innerHTML='<i class=&quot;bi-check2&quot;></i>';">
                                    <i class="bi-clipboard"></i>
                                </button>
                                <?php if (can_edit() && isset($uploadIds[$item['path']])): ?>
                                    <form method="post" data-confirm="Delete this uploaded picture permanently?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $uploadIds[$item['path']] ?>">
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1" title="Delete"><i class="bi-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination pagination-sm mb-0 justify-content-center">
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                        <a class="page-link" href="<?= media_url(['page' => $page - 1]) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= min($pages, 15); $i++): ?>
                        <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                            <a class="page-link" href="<?= media_url(['page' => $i]) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
                        <a class="page-link" href="<?= media_url(['page' => $page + 1]) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
