<?php
/** Audit trail of everything done in the portal. */

require_once __DIR__ . '/includes/auth.php';

require_admin();

if (is_post()) {
    csrf_check();
    if (post('action') === 'clear') {
        q('DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)');
        flash('Entries older than 90 days have been removed.');
        redirect('admin/activity.php');
    }
}

$perPage = 40;
$page    = max(1, (int) get('page', 1));
$action  = (string) get('action_filter');

$where  = ['1 = 1'];
$params = [];
if ($action !== '') {
    $where[] = 'action = ?';
    $params[] = $action;
}
$whereSql = implode(' AND ', $where);

$total = (int) fetch_value("SELECT COUNT(*) FROM activity_log WHERE $whereSql", $params, 0);
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);

$entries = fetch_all(
    "SELECT * FROM activity_log WHERE $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage),
    $params
);

$actions = array_column(fetch_all('SELECT DISTINCT action FROM activity_log ORDER BY action'), 'action');

$badge = [
    'login'  => 'chip-info',
    'logout' => 'chip',
    'create' => 'chip-on',
    'update' => 'chip-info',
    'delete' => 'chip-off',
    'upload' => 'chip-on',
];

$adminTitle  = 'Activity log';
$adminActive = 'activity';
require __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <i class="bi-clock-history" style="font-size:18px;color:var(--a-primary);"></i>
        <div>
            <h2>Activity log</h2>
            <div class="form-hint mt-1">Every sign-in and content change made through the portal.</div>
        </div>

        <div class="ms-auto d-flex gap-2 flex-wrap">
            <form method="get">
                <select class="form-select form-select-sm" name="action_filter" onchange="this.form.submit()">
                    <option value="">All actions</option>
                    <?php foreach ($actions as $value): ?>
                        <option value="<?= h($value) ?>" <?= $action === $value ? 'selected' : '' ?>><?= h(ucfirst($value)) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <form method="post" data-confirm="Remove log entries older than 90 days?">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="clear">
                <button class="btn btn-sm btn-outline-cmsr"><i class="bi-eraser me-1"></i>Prune old entries</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>When</th><th>User</th><th>Action</th><th>Where</th><th>Details</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php if (!$entries): ?>
                    <tr><td colspan="6" class="text-center py-4" style="color:var(--a-muted);">Nothing recorded yet.</td></tr>
                <?php endif; ?>

                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><span class="row-sub"><?= h(fdate($entry['created_at'], 'j M Y, H:i:s')) ?></span></td>
                        <td><span class="row-title"><?= h($entry['username']) ?></span></td>
                        <td><span class="chip <?= h($badge[$entry['action']] ?? 'chip') ?>"><?= h($entry['action']) ?></span></td>
                        <td><span class="row-sub"><?= h($entry['entity']) ?><?= $entry['entity_id'] ? ' #' . (int) $entry['entity_id'] : '' ?></span></td>
                        <td><span class="row-sub"><?= h($entry['details']) ?></span></td>
                        <td><span class="row-sub"><?= h($entry['ip_address']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
        <div class="admin-card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="form-hint mb-0"><?= $total ?> entries &middot; page <?= $page ?> of <?= $pages ?></span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($i = max(1, $page - 4); $i <= min($pages, $page + 4); $i++): ?>
                        <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                            <a class="page-link" href="<?= url('admin/activity.php?' . http_build_query(array_filter(['action_filter' => $action, 'page' => $i]))) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
