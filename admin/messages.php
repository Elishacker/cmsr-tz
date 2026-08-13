<?php
/** Inbox for contact, support and volunteer enquiries. */

require_once __DIR__ . '/includes/auth.php';

require_login();

if (is_post()) {
    require_edit();
    csrf_check();

    $action = (string) post('action');
    $id     = (int) post('id');

    if ($action === 'read' || $action === 'unread') {
        db_update('messages', ['is_read' => $action === 'read' ? 1 : 0], $id);
        redirect('admin/messages.php?' . http_build_query(array_filter(['type' => get('type'), 'open' => get('open')])));
    }

    if ($action === 'delete') {
        db_delete('messages', $id);
        log_activity('delete', 'messages', $id, 'Message deleted');
        flash('Message deleted.');
        redirect('admin/messages.php');
    }

    if ($action === 'mark_all_read') {
        q('UPDATE messages SET is_read = 1 WHERE is_read = 0');
        flash('All messages marked as read.');
        redirect('admin/messages.php');
    }
}

$type    = (string) get('type');
$openId  = (int) get('open', 0);
$perPage = 25;
$page    = max(1, (int) get('page', 1));

$where  = ['1 = 1'];
$params = [];
if (in_array($type, ['contact', 'volunteer', 'donation'], true)) {
    $where[] = 'type = ?';
    $params[] = $type;
}
$whereSql = implode(' AND ', $where);

$total = (int) fetch_value("SELECT COUNT(*) FROM messages WHERE $whereSql", $params, 0);
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);

$messages = fetch_all(
    "SELECT * FROM messages WHERE $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage),
    $params
);

$open = null;
if ($openId) {
    $open = fetch_one('SELECT * FROM messages WHERE id = ?', [$openId]);
    if ($open && (int) $open['is_read'] === 0 && can_edit()) {
        db_update('messages', ['is_read' => 1], $openId);
        $open['is_read'] = 1;
    }
}

$adminTitle  = 'Messages';
$adminActive = 'messages';
require __DIR__ . '/includes/header.php';
?>

<?php if ($open): ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <i class="bi-envelope-open" style="font-size:18px;color:var(--a-primary);"></i>
            <h2><?= h($open['subject'] ?: 'Message') ?></h2>
            <div class="ms-auto d-flex gap-2">
                <a href="mailto:<?= h($open['email']) ?>?subject=<?= rawurlencode('Re: ' . $open['subject']) ?>"
                   class="btn btn-sm btn-cmsr"><i class="bi-reply me-1"></i>Reply by e-mail</a>
                <a href="<?= url('admin/messages.php') ?>" class="btn btn-sm btn-outline-cmsr">Close</a>
            </div>
        </div>
        <div class="admin-card-body">
            <div class="row mb-3">
                <div class="col-md-3"><div class="form-hint">From</div><strong><?= h($open['name']) ?></strong></div>
                <div class="col-md-3"><div class="form-hint">E-mail</div><a href="mailto:<?= h($open['email']) ?>"><?= h($open['email']) ?></a></div>
                <div class="col-md-2"><div class="form-hint">Telephone</div><?= h($open['phone'] ?: '—') ?></div>
                <div class="col-md-2"><div class="form-hint">Type</div><span class="chip chip-info"><?= h(ucfirst($open['type'])) ?></span></div>
                <div class="col-md-2"><div class="form-hint">Received</div><?= h(fdate($open['created_at'], 'j M Y, H:i')) ?></div>
            </div>
            <div style="white-space:pre-wrap;line-height:1.7;background:#fbfcfd;border:1px solid var(--a-border);border-radius:8px;padding:16px;">
<?= h($open['message']) ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>Inbox</h2>
        <span class="form-hint mb-0"><?= $total ?> message<?= $total === 1 ? '' : 's' ?></span>

        <div class="ms-auto d-flex gap-2 flex-wrap">
            <a href="<?= url('admin/messages.php') ?>" class="btn btn-sm <?= $type === '' ? 'btn-cmsr' : 'btn-outline-cmsr' ?>">All</a>
            <a href="<?= url('admin/messages.php?type=contact') ?>" class="btn btn-sm <?= $type === 'contact' ? 'btn-cmsr' : 'btn-outline-cmsr' ?>">Contact</a>
            <a href="<?= url('admin/messages.php?type=donation') ?>" class="btn btn-sm <?= $type === 'donation' ? 'btn-cmsr' : 'btn-outline-cmsr' ?>">Support</a>
            <?php if (can_edit()): ?>
                <form method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="mark_all_read">
                    <button class="btn btn-sm btn-outline-cmsr"><i class="bi-check2-all me-1"></i>Mark all read</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th style="width:34px;"></th><th>From</th><th>Subject</th><th>Type</th><th>Received</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php if (!$messages): ?>
                    <tr><td colspan="6" class="text-center py-4" style="color:var(--a-muted);">No messages yet.</td></tr>
                <?php endif; ?>

                <?php foreach ($messages as $message): ?>
                    <tr style="<?= (int) $message['is_read'] === 0 ? 'background:#f7fcfb;' : '' ?>">
                        <td>
                            <i class="<?= (int) $message['is_read'] === 0 ? 'bi-envelope-fill' : 'bi-envelope-open' ?>"
                               style="color:<?= (int) $message['is_read'] === 0 ? 'var(--a-primary)' : 'var(--a-muted)' ?>;"></i>
                        </td>
                        <td>
                            <div class="row-title"><?= h($message['name']) ?></div>
                            <div class="row-sub"><?= h($message['email']) ?></div>
                        </td>
                        <td>
                            <a href="<?= url('admin/messages.php?open=' . (int) $message['id']) ?>" style="color:var(--a-dark);text-decoration:none;">
                                <div class="row-title"><?= h(excerpt($message['subject'], 50)) ?></div>
                                <div class="row-sub"><?= h(excerpt($message['message'], 70)) ?></div>
                            </a>
                        </td>
                        <td><span class="chip chip-info"><?= h(ucfirst($message['type'])) ?></span></td>
                        <td><span class="row-sub"><?= h(fdate($message['created_at'], 'j M Y, H:i')) ?></span></td>
                        <td class="text-end" style="white-space:nowrap;">
                            <a href="<?= url('admin/messages.php?open=' . (int) $message['id']) ?>"
                               class="btn btn-sm btn-outline-cmsr" title="Open"><i class="bi-eye"></i></a>

                            <?php if (can_edit()): ?>
                                <form method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="<?= (int) $message['is_read'] === 1 ? 'unread' : 'read' ?>">
                                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                                    <button class="btn btn-sm btn-outline-cmsr" title="Mark as <?= (int) $message['is_read'] === 1 ? 'unread' : 'read' ?>">
                                        <i class="bi-<?= (int) $message['is_read'] === 1 ? 'envelope' : 'check2' ?>"></i>
                                    </button>
                                </form>
                                <form method="post" class="d-inline" data-confirm="Delete this message permanently?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
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
        <div class="admin-card-body">
            <nav>
                <ul class="pagination pagination-sm mb-0 justify-content-center">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                            <a class="page-link" href="<?= url('admin/messages.php?' . http_build_query(array_filter(['type' => $type, 'page' => $i]))) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
