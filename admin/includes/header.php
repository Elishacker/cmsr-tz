<?php
/**
 * Portal chrome: sidebar navigation and top bar.
 *
 * Pages set $adminTitle (and optionally $adminActive) before including.
 */

require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/config/entities.php';

require_login();

$user       = current_user();
$adminTitle = $adminTitle ?? 'Dashboard';
$active     = $adminActive ?? '';
$entities   = entity_definitions();
$unread     = (int) fetch_value('SELECT COUNT(*) FROM messages WHERE is_read = 0', [], 0);

/** Sidebar sections: label => list of [key, url, icon, title]. */
$menu = [
    'Overview' => [
        ['dashboard', 'admin/index.php', 'bi-speedometer2', 'Dashboard'],
        ['messages', 'admin/messages.php', 'bi-inbox', 'Messages', $unread],
    ],
    'Homepage' => [
        ['slideshow', 'admin/crud.php?entity=slideshow', 'bi-images', 'Hero slides'],
        ['overview', 'admin/overview.php', 'bi-card-text', 'About block'],
        ['impact_stats', 'admin/crud.php?entity=impact_stats', 'bi-graph-up', 'Impact counters'],
    ],
    'Content' => [
        ['sectors', 'admin/crud.php?entity=sectors', 'bi-diagram-3', 'Sectors'],
        ['projects', 'admin/crud.php?entity=projects', 'bi-kanban', 'Projects'],
        ['project_gallery', 'admin/crud.php?entity=project_gallery', 'bi-image', 'Project photos'],
        ['news', 'admin/crud.php?entity=news', 'bi-newspaper', 'News articles'],
        ['updates', 'admin/crud.php?entity=updates', 'bi-megaphone', 'Short updates'],
        ['pages', 'admin/crud.php?entity=pages', 'bi-file-text', 'Static pages'],
        ['page_sections', 'admin/crud.php?entity=page_sections', 'bi-layout-text-window', 'Page blocks'],
    ],
    'Organisation' => [
        ['board_members', 'admin/crud.php?entity=board_members', 'bi-people', 'Board members'],
        ['staff', 'admin/crud.php?entity=staff', 'bi-person-badge', 'Leadership team'],
        ['core_values', 'admin/crud.php?entity=core_values', 'bi-award', 'Core values'],
        ['partners', 'admin/crud.php?entity=partners', 'bi-building', 'Partners & networks'],
        ['locations', 'admin/crud.php?entity=locations', 'bi-geo-alt', 'Where we work'],
    ],
    'Library' => [
        ['resources', 'admin/crud.php?entity=resources', 'bi-journal-text', 'Publications'],
        ['reports', 'admin/crud.php?entity=reports', 'bi-file-earmark-text', 'Annual reports'],
        ['media', 'admin/media.php', 'bi-collection', 'Media library'],
    ],
    'Administration' => [
        ['subscribers', 'admin/crud.php?entity=subscribers', 'bi-envelope-check', 'Subscribers'],
        ['settings', 'admin/settings.php', 'bi-gear', 'Site settings'],
        ['users', 'admin/users.php', 'bi-person-lines-fill', 'Staff accounts'],
        ['activity', 'admin/activity.php', 'bi-clock-history', 'Activity log'],
    ],
];

// Non-admins do not see the administration tools.
if (!is_admin()) {
    unset($menu['Administration']);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= h($adminTitle) ?> | CMSR-TZ Portal</title>
    <link rel="icon" href="<?= url('assets/images/logo.png') ?>">
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/templatemo-kind-heart-charity.css') ?>" rel="stylesheet">
    <link href="<?= url('admin/assets/admin.css') ?>?v=<?= @filemtime(ROOT_PATH . '/admin/assets/admin.css') ?: 1 ?>" rel="stylesheet">
</head>

<body class="admin-body">
<div class="admin-shell">

    <aside class="admin-sidebar" id="adminSidebar">
        <a class="admin-brand" href="<?= url('admin/index.php') ?>">
            <img src="<?= url('assets/images/logo.png') ?>" alt="CMSR-TZ">
            <span>
                <strong>CMSR-TZ</strong>
                <span>Staff Portal</span>
            </span>
        </a>

        <nav class="admin-nav">
            <?php foreach ($menu as $group => $items): ?>
                <div class="admin-nav-title"><?= h($group) ?></div>
                <?php foreach ($items as $item): ?>
                    <a href="<?= url($item[1]) ?>" class="<?= $active === $item[0] ? 'active' : '' ?>">
                        <i class="<?= h($item[2]) ?>"></i>
                        <span><?= h($item[3]) ?></span>
                        <?php if (!empty($item[4])): ?>
                            <span class="badge rounded-pill"><?= (int) $item[4] ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>

        </nav>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <button class="btn btn-sm btn-outline-cmsr d-lg-none" type="button" id="sidebarToggle" aria-label="Menu">
                <i class="bi-list"></i>
            </button>
            <h1><?= h($adminTitle) ?></h1>

            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="<?= url('admin/messages.php') ?>" class="position-relative text-decoration-none" title="Messages">
                    <i class="bi-inbox" style="font-size:19px;color:var(--a-slate);"></i>
                    <?php if ($unread > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                              style="background:var(--a-primary);font-size:10px;"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown">
                    <button class="user-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar"><?= h(strtoupper(substr($user['full_name'], 0, 1))) ?></span>
                        <span class="text-start d-none d-md-inline-block">
                            <span class="user-name"><?= h($user['full_name']) ?></span>
                            <span class="user-role"><?= h($user['role']) ?></span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="<?= url('admin/profile.php') ?>">
                            <i class="bi-person-circle me-2"></i>My profile</a></li>
                        <li><a class="dropdown-item" href="<?= url('index.php') ?>" target="_blank">
                            <i class="bi-box-arrow-up-right me-2"></i>View website</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('admin/logout.php') ?>">
                            <i class="bi-power me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="admin-content">
            <?php foreach (take_flashes() as $msg): ?>
                <div class="alert alert-<?= h($msg['type'] === 'error' ? 'danger' : $msg['type']) ?> alert-dismissible fade show" role="alert">
                    <?= h($msg['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
