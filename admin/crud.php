<?php
/**
 * The one screen behind every managed content type.
 *
 * ?entity=projects            list
 * ?entity=projects&action=new create form
 * ?entity=projects&action=edit&id=5
 * POST action=save / action=delete / action=toggle
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/entities.php';
require_once __DIR__ . '/includes/fields.php';

require_login();

$key = (string) get('entity');
$def = entity($key);

if (!$def) {
    flash('Unknown content type.', 'danger');
    redirect('admin/index.php');
}

$table  = $def['table'];
$fields = $def['fields'];
$action = (string) get('action', 'list');
$id     = (int) get('id', 0);

// ---------------------------------------------------------------------
// Write actions
// ---------------------------------------------------------------------
if (is_post()) {
    require_edit();
    csrf_check();
    $postAction = (string) post('action');

    // ---- delete -----------------------------------------------------
    if ($postAction === 'delete') {
        if (!$def['can_delete']) {
            flash('Records of this type cannot be deleted.', 'warning');
            redirect('admin/crud.php?entity=' . $key);
        }
        $deleteId = (int) post('id');
        $row = fetch_one("SELECT * FROM `$table` WHERE id = ?", [$deleteId]);
        if ($row) {
            db_delete($table, $deleteId);
            log_activity('delete', $table, $deleteId, $def['label'] . ' deleted');
            flash($def['label'] . ' deleted.');
        }
        redirect('admin/crud.php?entity=' . $key);
    }

    // ---- quick publish/unpublish -------------------------------------
    if ($postAction === 'toggle') {
        $toggleId = (int) post('id');
        $column   = (string) post('column');
        if (isset($fields[$column]) && $fields[$column]['type'] === 'checkbox') {
            $row = fetch_one("SELECT `$column` FROM `$table` WHERE id = ?", [$toggleId]);
            if ($row) {
                $new = (int) $row[$column] === 1 ? 0 : 1;
                db_update($table, [$column => $new], $toggleId);
                log_activity('update', $table, $toggleId, $column . ' set to ' . $new);
                flash('Updated.');
            }
        }
        redirect('admin/crud.php?entity=' . $key);
    }

    // ---- create / update ---------------------------------------------
    if ($postAction === 'save') {
        $saveId   = (int) post('id');
        $existing = $saveId ? fetch_one("SELECT * FROM `$table` WHERE id = ?", [$saveId]) : [];

        [$values, $errors] = read_fields($fields, $existing ?: []);

        // Slugs: generate from the source field when left blank, keep unique.
        foreach ($fields as $name => $field) {
            if ($field['type'] !== 'slug') {
                continue;
            }
            $source = $field['from'] ?? '';
            $base = trim((string) ($values[$name] ?? ''));
            if ($base === '') {
                $base = (string) ($values[$source] ?? $existing[$source] ?? '');
            }
            $values[$name] = unique_slug($table, slugify($base), $saveId ?: null);
        }

        if ($errors) {
            $_SESSION['form_old'] = $_POST;
            foreach ($errors as $error) {
                flash($error, 'danger');
            }
            redirect('admin/crud.php?entity=' . $key . '&action=' . ($saveId ? 'edit&id=' . $saveId : 'new'));
        }

        if ($saveId && $existing) {
            db_update($table, $values, $saveId);
            log_activity('update', $table, $saveId, $def['label'] . ' updated');
            flash($def['label'] . ' saved.');
        } else {
            if (!$def['can_create']) {
                flash('New records of this type cannot be created here.', 'warning');
                redirect('admin/crud.php?entity=' . $key);
            }
            $saveId = db_insert($table, $values);
            log_activity('create', $table, $saveId, $def['label'] . ' created');
            flash($def['label'] . ' created.');
        }

        redirect('admin/crud.php?entity=' . $key . (post('stay') === '1' ? '&action=edit&id=' . $saveId : ''));
    }
}

$adminActive = $key;

// =====================================================================
// Create / edit form
// =====================================================================
if ($action === 'new' || $action === 'edit') {
    require_edit();

    $record = [];
    if ($action === 'edit') {
        $record = fetch_one("SELECT * FROM `$table` WHERE id = ?", [$id]) ?: [];
        if (!$record) {
            flash('That record no longer exists.', 'warning');
            redirect('admin/crud.php?entity=' . $key);
        }
    }

    // Repopulate after a validation failure.
    $old = $_SESSION['form_old'] ?? [];
    unset($_SESSION['form_old']);

    $adminTitle = ($action === 'edit' ? 'Edit ' : 'New ') . strtolower($def['label']);
    require __DIR__ . '/includes/header.php';
    ?>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a href="<?= url('admin/crud.php?entity=' . $key) ?>" class="btn btn-sm btn-outline-cmsr">
            <i class="bi-arrow-left me-1"></i>Back to <?= h(strtolower($def['plural'])) ?>
        </a>
        <?php if ($action === 'edit' && $def['view_url'] !== ''): ?>
            <?php $viewUrl = str_replace('{slug}', rawurlencode((string) ($record['slug'] ?? '')), $def['view_url']); ?>
            <a href="<?= url($viewUrl) ?>" target="_blank" class="btn btn-sm btn-outline-cmsr">
                <i class="bi-box-arrow-up-right me-1"></i>View on the website
            </a>
        <?php endif; ?>
    </div>

    <form method="post" enctype="multipart/form-data" action="<?= url('admin/crud.php?entity=' . $key) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) ($record['id'] ?? 0) ?>">

        <div class="admin-card">
            <div class="admin-card-header">
                <i class="<?= h($def['icon']) ?>" style="font-size:18px;color:var(--a-primary);"></i>
                <h2><?= h($adminTitle) ?></h2>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <?php foreach ($fields as $name => $field): ?>
                        <?php
                        $value = $old[$name] ?? $record[$name] ?? field_default($field);
                        if (isset($old[$name]) && $field['type'] === 'checkbox') {
                            $value = (int) $old[$name];
                        }
                        echo render_field($name, $field, $value);
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-5">
            <button type="submit" class="btn btn-cmsr"><i class="bi-check-lg me-1"></i>Save</button>
            <button type="submit" name="stay" value="1" class="btn btn-outline-cmsr">Save and keep editing</button>
            <a href="<?= url('admin/crud.php?entity=' . $key) ?>" class="btn btn-outline-cmsr">Cancel</a>
        </div>
    </form>

    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// =====================================================================
// List screen
// =====================================================================
$search  = (string) get('q');
$perPage = 20;
$page    = max(1, (int) get('page', 1));

$where  = ['1 = 1'];
$params = [];

if ($search !== '' && $def['search']) {
    $parts = [];
    foreach ($def['search'] as $column) {
        $parts[] = "`$column` LIKE ?";
        $params[] = '%' . $search . '%';
    }
    $where[] = '(' . implode(' OR ', $parts) . ')';
}

foreach ($def['filters'] as $column => $filter) {
    $value = (string) get('filter_' . $column);
    if ($value !== '' && isset($filter['options'][$value])) {
        $where[] = "`$column` = ?";
        $params[] = $value;
    }
}

$whereSql = implode(' AND ', $where);
$total    = (int) fetch_value("SELECT COUNT(*) FROM `$table` WHERE $whereSql", $params, 0);
$pages    = max(1, (int) ceil($total / $perPage));
$page     = min($page, $pages);

$rows = fetch_all(
    "SELECT * FROM `$table` WHERE $whereSql ORDER BY {$def['order']} LIMIT $perPage OFFSET " . (($page - 1) * $perPage),
    $params
);

// Look-up tables so foreign keys show a name instead of a number.
$lookups = [];
foreach ($def['list_columns'] as $column => $heading) {
    if (isset($fields[$column]) && $fields[$column]['type'] === 'select' && !empty($fields[$column]['options_sql'])) {
        $lookups[$column] = field_options($fields[$column]);
    }
}

/** Preserve the current filters in a link. */
function list_url(string $entityKey, array $overrides = []): string
{
    $params = $_GET;
    unset($params['action'], $params['id']);
    $params = array_merge($params, $overrides, ['entity' => $entityKey]);
    return url('admin/crud.php?' . http_build_query(array_filter($params, fn($v) => $v !== '')));
}

$adminTitle = $def['plural'];
require __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <i class="<?= h($def['icon']) ?>" style="font-size:18px;color:var(--a-primary);"></i>
        <div>
            <h2><?= h($def['plural']) ?></h2>
            <?php if (!empty($def['description'])): ?>
                <div class="form-hint mt-1"><?= h($def['description']) ?></div>
            <?php endif; ?>
        </div>

        <div class="ms-auto d-flex gap-2 flex-wrap">
            <?php if ($def['search']): ?>
                <form method="get" class="d-flex gap-2">
                    <input type="hidden" name="entity" value="<?= h($key) ?>">
                    <input type="search" class="form-control form-control-sm" name="q" value="<?= h($search) ?>"
                           placeholder="Search…" style="min-width:190px;">
                    <button class="btn btn-sm btn-outline-cmsr"><i class="bi-search"></i></button>
                </form>
            <?php endif; ?>

            <?php foreach ($def['filters'] as $column => $filter): ?>
                <form method="get">
                    <input type="hidden" name="entity" value="<?= h($key) ?>">
                    <?php if ($search !== ''): ?><input type="hidden" name="q" value="<?= h($search) ?>"><?php endif; ?>
                    <select class="form-select form-select-sm" name="filter_<?= h($column) ?>" onchange="this.form.submit()">
                        <option value="">All <?= h(strtolower($filter['label'])) ?></option>
                        <?php foreach ($filter['options'] as $value => $text): ?>
                            <option value="<?= h($value) ?>" <?= get('filter_' . $column) === $value ? 'selected' : '' ?>><?= h($text) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endforeach; ?>

            <?php if ($def['can_create'] && can_edit()): ?>
                <a href="<?= url('admin/crud.php?entity=' . $key . '&action=new') ?>" class="btn btn-sm btn-cmsr">
                    <i class="bi-plus-lg me-1"></i>Add <?= h(strtolower($def['label'])) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <?php foreach ($def['list_columns'] as $heading): ?>
                        <th><?= h($heading) ?></th>
                    <?php endforeach; ?>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$rows): ?>
                    <tr>
                        <td colspan="<?= count($def['list_columns']) + 1 ?>" class="text-center py-4" style="color:var(--a-muted);">
                            Nothing here yet<?= $search !== '' ? ' for “' . h($search) . '”' : '' ?>.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($def['list_columns'] as $column => $heading): ?>
                            <td>
                                <?php
                                $value = $row[$column] ?? '';
                                $type  = $fields[$column]['type'] ?? 'text';

                                if ($type === 'image') {
                                    echo $value !== ''
                                        ? '<img class="row-thumb" src="' . h(img((string) $value, 160)) . '" alt="">'
                                        : '<span class="chip">none</span>';
                                } elseif ($type === 'checkbox') {
                                    $on = (int) $value === 1;
                                    if (can_edit()) {
                                        echo '<form method="post" action="' . url('admin/crud.php?entity=' . $key) . '" class="d-inline">'
                                            . csrf_field()
                                            . '<input type="hidden" name="action" value="toggle">'
                                            . '<input type="hidden" name="id" value="' . (int) $row['id'] . '">'
                                            . '<input type="hidden" name="column" value="' . h($column) . '">'
                                            . '<button class="chip ' . ($on ? 'chip-on' : 'chip-off') . '" style="border:0;cursor:pointer;" title="Click to change">'
                                            . ($on ? 'Yes' : 'No') . '</button></form>';
                                    } else {
                                        echo '<span class="chip ' . ($on ? 'chip-on' : 'chip-off') . '">' . ($on ? 'Yes' : 'No') . '</span>';
                                    }
                                } elseif (isset($lookups[$column])) {
                                    echo '<span class="row-sub">' . h($lookups[$column][(string) $value] ?? '—') . '</span>';
                                } elseif ($type === 'file') {
                                    echo $value !== ''
                                        ? '<a href="' . h(url((string) $value)) . '" target="_blank" class="row-sub">' . h(basename((string) $value)) . '</a>'
                                        : '<span class="chip">none</span>';
                                } elseif ($type === 'date' || $column === 'created_at') {
                                    echo '<span class="row-sub">' . h(fdate((string) $value, 'j M Y')) . '</span>';
                                } elseif ($column === array_key_first($def['list_columns']) || in_array($column, ['title', 'name', 'heading', 'label', 'region', 'email'], true)) {
                                    echo '<div class="row-title">' . h(excerpt((string) $value, 70)) . '</div>';
                                } else {
                                    echo '<span class="row-sub">' . h(excerpt((string) $value, 55)) . '</span>';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>

                        <td class="text-end" style="white-space:nowrap;">
                            <?php if (can_edit()): ?>
                                <a href="<?= url('admin/crud.php?entity=' . $key . '&action=edit&id=' . (int) $row['id']) ?>"
                                   class="btn btn-sm btn-outline-cmsr" title="Edit"><i class="bi-pencil"></i></a>
                            <?php endif; ?>

                            <?php if ($def['view_url'] !== '' && !empty($row['slug'])): ?>
                                <a href="<?= url(str_replace('{slug}', rawurlencode((string) $row['slug']), $def['view_url'])) ?>"
                                   target="_blank" class="btn btn-sm btn-outline-cmsr" title="View"><i class="bi-eye"></i></a>
                            <?php endif; ?>

                            <?php if ($def['can_delete'] && can_edit()): ?>
                                <form method="post" action="<?= url('admin/crud.php?entity=' . $key) ?>" class="d-inline"
                                      data-confirm="Delete this <?= h(strtolower($def['label'])) ?>? This cannot be undone.">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <div class="admin-card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="form-hint mb-0"><?= $total ?> records &middot; page <?= $page ?> of <?= $pages ?></span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                        <a class="page-link" href="<?= list_url($key, ['page' => $page - 1]) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                            <a class="page-link" href="<?= list_url($key, ['page' => $i]) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
                        <a class="page-link" href="<?= list_url($key, ['page' => $page + 1]) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
