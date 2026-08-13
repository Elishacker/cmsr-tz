<?php
/** A single sector page, with the projects delivered under it. */

require_once __DIR__ . '/includes/functions.php';

$slug   = (string) get('slug');
$sector = fetch_one('SELECT * FROM sectors WHERE slug = ? AND is_active = 1', [$slug]);

if (!$sector) {
    http_response_code(404);
    $pageTitle = 'Sector not found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="section-padding"><div class="container text-center py-5">'
        . '<h2 class="mb-3">Sector not found</h2>'
        . '<p class="mb-4">The sector you are looking for is not available.</p>'
        . '<a href="' . url('what-we-do.php') . '" class="custom-btn btn">All sectors</a>'
        . '</div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$projects = fetch_all(
    'SELECT * FROM projects WHERE sector_id = ? AND is_active = 1 ORDER BY FIELD(status, "current", "upcoming", "completed"), sort_order, id',
    [$sector['id']]
);
$others = array_values(array_filter(all_sectors(), fn($s) => $s['slug'] !== $sector['slug']));

$pageTitle       = $sector['name'] . ' Sector';
$pageDescription = excerpt($sector['summary'], 200);

require __DIR__ . '/includes/header.php';

$heroTitle    = $sector['name'] . ' Sector';
$heroSubtitle = $sector['tagline'];
$heroImage    = $sector['image'];
$heroCrumbs   = ['What We Do' => 'what-we-do.php', $sector['name'] => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <span class="section-eyebrow">The context</span>
                <h2 class="mb-4 section-title-underline"><?= h($sector['name']) ?></h2>
                <p class="lead" style="font-size:18px;line-height:1.7;color:var(--secondary-color);"><?= h($sector['intro']) ?></p>
                <div class="prose mt-4"><?= safe_html($sector['body']) ?></div>
            </div>

            <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                <?php $highlights = lines($sector['highlights']); ?>
                <?php if ($highlights): ?>
                    <div class="sidebar-block" style="background:var(--section-bg-color);">
                        <h5>What we deliver</h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($highlights as $item): ?>
                                <li class="d-flex mb-3" style="font-size:15px;line-height:1.6;">
                                    <i class="bi-check2-circle text-primary me-2 mt-1"></i><span><?= h($item) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Other sectors</h5>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($others as $other): ?>
                            <li class="mb-2">
                                <a href="<?= url('sector.php?slug=' . $other['slug']) ?>"
                                   style="color:var(--secondary-color);font-size:15px;text-decoration:none;">
                                    <i class="<?= h($other['icon']) ?> text-primary me-2"></i><?= h($other['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="<?= url('donate.php') ?>" class="custom-btn btn w-100">Support this sector</a>
            </div>
        </div>
    </div>
</section>

<?php if ($projects): ?>
<section class="section-padding section-bg">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-5">
                <span class="section-eyebrow">Delivered</span>
                <h2 class="mb-0 section-title-underline"><?= h($sector['name']) ?> projects</h2>
            </div>

            <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="project-card">
                        <div class="project-card-image-wrap">
                            <a href="<?= url('project.php?slug=' . $project['slug']) ?>">
                                <img src="<?= h(img($project['image'], 700)) ?>" class="project-card-image" alt="<?= h($project['title']) ?>">
                            </a>
                            <span class="project-badge<?= $project['status'] === 'completed' ? ' is-completed' : '' ?>"><?= h($project['status'] === 'completed' ? 'Completed' : 'On-going') ?></span>
                        </div>
                        <div class="project-card-body">
                            <h5><a href="<?= url('project.php?slug=' . $project['slug']) ?>"><?= h($project['title']) ?></a></h5>
                            <ul class="project-meta">
                                <?php if ($project['location'] !== ''): ?><li><i class="bi-geo-alt"></i><span><?= h($project['location']) ?></span></li><?php endif; ?>
                                <?php if ($project['donor'] !== ''): ?><li><i class="bi-people"></i><span><?= h($project['donor']) ?></span></li><?php endif; ?>
                                <?php if ($project['duration'] !== ''): ?><li><i class="bi-calendar4"></i><span><?= h($project['duration']) ?></span></li><?php endif; ?>
                            </ul>
                            <div class="mt-auto">
                                <?php if ($project['beneficiaries_direct'] !== ''): ?>
                                    <span class="beneficiary-pill"><?= h($project['beneficiaries_direct']) ?> direct</span>
                                <?php endif; ?>
                                <?php if ($project['beneficiaries_indirect'] !== ''): ?>
                                    <span class="beneficiary-pill"><?= h($project['beneficiaries_indirect']) ?> indirect</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
