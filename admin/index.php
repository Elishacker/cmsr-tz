<?php
/** Portal dashboard. */

$adminTitle  = 'Dashboard';
$adminActive = 'dashboard';

require_once __DIR__ . '/includes/header.php';

$counts = [
    'projects'      => (int) fetch_value('SELECT COUNT(*) FROM projects WHERE is_active = 1', [], 0),
    'current'       => (int) fetch_value("SELECT COUNT(*) FROM projects WHERE is_active = 1 AND status = 'current'", [], 0),
    'news'          => (int) fetch_value('SELECT COUNT(*) FROM news WHERE is_published = 1', [], 0),
    'sectors'       => (int) fetch_value('SELECT COUNT(*) FROM sectors WHERE is_active = 1', [], 0),
    'slides'        => (int) fetch_value('SELECT COUNT(*) FROM slideshow WHERE is_active = 1', [], 0),
    'partners'      => (int) fetch_value('SELECT COUNT(*) FROM partners WHERE is_active = 1', [], 0),
    'messages'      => (int) fetch_value('SELECT COUNT(*) FROM messages WHERE is_read = 0', [], 0),
    'subscribers'   => (int) fetch_value('SELECT COUNT(*) FROM subscribers WHERE is_active = 1', [], 0),
    'media'         => (int) fetch_value('SELECT COUNT(*) FROM media', [], 0),
    'board'         => (int) fetch_value('SELECT COUNT(*) FROM board_members WHERE is_active = 1', [], 0),
];

$recentMessages = fetch_all('SELECT * FROM messages ORDER BY created_at DESC LIMIT 5');
$recentActivity = fetch_all('SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 8');
$recentNews     = fetch_all('SELECT id, title, news_date, is_published FROM news ORDER BY news_date DESC LIMIT 5');
$popularNews    = fetch_all('SELECT title, views FROM news WHERE views > 0 ORDER BY views DESC LIMIT 5');

$tiles = [
    ['bi-kanban', $counts['projects'], 'Published projects', 'admin/crud.php?entity=projects'],
    ['bi-play-circle', $counts['current'], 'On-going projects', 'admin/crud.php?entity=projects&filter_status=current'],
    ['bi-newspaper', $counts['news'], 'Published articles', 'admin/crud.php?entity=news'],
    ['bi-images', $counts['slides'], 'Live hero slides', 'admin/crud.php?entity=slideshow'],
    ['bi-inbox', $counts['messages'], 'Unread messages', 'admin/messages.php'],
    ['bi-envelope-check', $counts['subscribers'], 'Newsletter subscribers', 'admin/crud.php?entity=subscribers'],
    ['bi-building', $counts['partners'], 'Partners & networks', 'admin/crud.php?entity=partners'],
    ['bi-collection', $counts['media'], 'Uploaded pictures', 'admin/media.php'],
];
?>

<div class="row g-3 mb-2">
    <?php foreach ($tiles as $tile): ?>
        <div class="col-xl-3 col-md-6 col-12">
            <a class="stat-tile" href="<?= url($tile[3]) ?>">
                <span class="stat-icon"><i class="<?= h($tile[0]) ?>"></i></span>
                <span>
                    <span class="stat-value"><?= (int) $tile[1] ?></span>
                    <p class="stat-label"><?= h($tile[2]) ?></p>
                </span>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">

    <!-- Quick actions -->
    <div class="col-lg-4 col-12">
        <div class="admin-card h-100">
            <div class="admin-card-header"><h2>Quick actions</h2></div>
            <div class="admin-card-body d-grid gap-2">
                <a href="<?= url('admin/crud.php?entity=news&action=new') ?>" class="btn btn-cmsr text-start">
                    <i class="bi-plus-lg me-2"></i>Write a news article
                </a>
                <a href="<?= url('admin/crud.php?entity=projects&action=new') ?>" class="btn btn-outline-cmsr text-start">
                    <i class="bi-plus-lg me-2"></i>Add a project
                </a>
                <a href="<?= url('admin/crud.php?entity=slideshow&action=new') ?>" class="btn btn-outline-cmsr text-start">
                    <i class="bi-plus-lg me-2"></i>Add a hero slide
                </a>
                <a href="<?= url('admin/media.php') ?>" class="btn btn-outline-cmsr text-start">
                    <i class="bi-upload me-2"></i>Upload pictures
                </a>
                <a href="<?= url('admin/settings.php') ?>" class="btn btn-outline-cmsr text-start">
                    <i class="bi-gear me-2"></i>Contact details &amp; social links
                </a>
                <a href="<?= url('index.php') ?>" target="_blank" class="btn btn-outline-cmsr text-start">
                    <i class="bi-box-arrow-up-right me-2"></i>Open the website
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="col-lg-8 col-12">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2>Latest messages</h2>
                <a href="<?= url('admin/messages.php') ?>" class="ms-auto btn btn-sm btn-outline-cmsr">Open inbox</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr><th>From</th><th>Subject</th><th>Type</th><th>Received</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$recentMessages): ?>
                            <tr><td colspan="5" class="text-center py-4" style="color:var(--a-muted);">No messages yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($recentMessages as $message): ?>
                            <tr>
                                <td>
                                    <div class="row-title"><?= h($message['name']) ?></div>
                                    <div class="row-sub"><?= h($message['email']) ?></div>
                                </td>
                                <td><span class="row-sub"><?= h(excerpt($message['subject'], 40)) ?></span></td>
                                <td><span class="chip chip-info"><?= h(ucfirst($message['type'])) ?></span></td>
                                <td><span class="row-sub"><?= h(fdate($message['created_at'], 'j M Y, H:i')) ?></span></td>
                                <td class="text-end">
                                    <?php if ((int) $message['is_read'] === 0): ?>
                                        <span class="chip chip-on">New</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent news -->
    <div class="col-lg-6 col-12">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2>Recent articles</h2>
                <a href="<?= url('admin/crud.php?entity=news') ?>" class="ms-auto btn btn-sm btn-outline-cmsr">Manage</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <tbody>
                        <?php foreach ($recentNews as $item): ?>
                            <tr>
                                <td>
                                    <div class="row-title"><?= h(excerpt($item['title'], 60)) ?></div>
                                    <div class="row-sub"><?= h(fdate($item['news_date'], 'j M Y')) ?></div>
                                </td>
                                <td class="text-end">
                                    <span class="chip <?= (int) $item['is_published'] ? 'chip-on' : 'chip-off' ?>">
                                        <?= (int) $item['is_published'] ? 'Live' : 'Draft' ?>
                                    </span>
                                    <a href="<?= url('admin/crud.php?entity=news&action=edit&id=' . (int) $item['id']) ?>"
                                       class="btn btn-sm btn-outline-cmsr ms-1"><i class="bi-pencil"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$recentNews): ?>
                            <tr><td class="text-center py-4" style="color:var(--a-muted);">No articles yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Activity -->
    <div class="col-lg-6 col-12">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h2>Recent activity</h2>
                <?php if (is_admin()): ?>
                    <a href="<?= url('admin/activity.php') ?>" class="ms-auto btn btn-sm btn-outline-cmsr">Full log</a>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <tbody>
                        <?php foreach ($recentActivity as $entry): ?>
                            <tr>
                                <td>
                                    <div class="row-title"><?= h($entry['details'] ?: $entry['action']) ?></div>
                                    <div class="row-sub"><?= h($entry['username']) ?> &middot; <?= h(fdate($entry['created_at'], 'j M Y, H:i')) ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$recentActivity): ?>
                            <tr><td class="text-center py-4" style="color:var(--a-muted);">Nothing recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($popularNews): ?>
        <div class="col-12">
            <div class="admin-card">
                <div class="admin-card-header"><h2>Most-read articles</h2></div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <tbody>
                            <?php foreach ($popularNews as $item): ?>
                                <tr>
                                    <td><div class="row-title"><?= h($item['title']) ?></div></td>
                                    <td class="text-end" style="width:120px;">
                                        <span class="chip chip-info"><?= (int) $item['views'] ?> views</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
