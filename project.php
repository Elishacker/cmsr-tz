<?php
/** Single project page. */

require_once __DIR__ . '/includes/functions.php';

$slug = (string) get('slug');
$project = fetch_one(
    'SELECT p.*, s.name AS sector_name, s.slug AS sector_slug, s.icon AS sector_icon
       FROM projects p LEFT JOIN sectors s ON s.id = p.sector_id
      WHERE p.slug = ? AND p.is_active = 1',
    [$slug]
);

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Project not found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="section-padding"><div class="container text-center py-5">'
        . '<h2 class="mb-3">Project not found</h2>'
        . '<p class="mb-4">This project may have been moved or removed.</p>'
        . '<a href="' . url('projects.php') . '" class="custom-btn btn">All projects</a>'
        . '</div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$gallery = fetch_all('SELECT * FROM project_gallery WHERE project_id = ? ORDER BY sort_order, id', [$project['id']]);
$related = fetch_all(
    'SELECT * FROM projects WHERE is_active = 1 AND id <> ? AND (sector_id = ? OR sector_id IS NULL) ORDER BY RAND() LIMIT 3',
    [$project['id'], $project['sector_id']]
);

$pageTitle       = $project['title'];
$pageDescription = excerpt($project['summary'] ?: $project['body'], 200);

require __DIR__ . '/includes/header.php';

$heroTitle    = $project['title'];
$heroSubtitle = $project['location'];
$heroImage    = $project['image'];
$heroCrumbs   = ['Projects' => 'projects.php', excerpt($project['title'], 50) => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <img src="<?= h(img($project['image'], 1200)) ?>" class="img-fluid rounded shadow-lg mb-4" alt="<?= h($project['title']) ?>">

                <?php if ($project['summary']): ?>
                    <p class="lead mb-4" style="font-size:18px;line-height:1.7;color:var(--secondary-color);"><?= h($project['summary']) ?></p>
                <?php endif; ?>

                <?php if ($project['body']): ?>
                    <div class="prose"><?= safe_html($project['body']) ?></div>
                <?php endif; ?>

                <?php if ($gallery): ?>
                    <h4 class="mt-5 mb-4">Project gallery</h4>
                    <div class="row g-3">
                        <?php foreach ($gallery as $shot): ?>
                            <div class="col-lg-4 col-md-6 col-12">
                                <a href="<?= h(img($shot['image'], 1600)) ?>" target="_blank" rel="noopener" class="gallery-item">
                                    <img src="<?= h(img($shot['image'], 600)) ?>" alt="<?= h($shot['caption'] ?: $project['title']) ?>">
                                </a>
                                <?php if ($shot['caption'] !== ''): ?>
                                    <p class="mt-2 mb-0" style="font-size:13px;color:var(--p-color);"><?= h($shot['caption']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Fact sheet -->
            <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Project details</h5>
                    <ul class="list-unstyled mb-0" style="font-size:15px;">
                        <li class="d-flex mb-3"><i class="bi-flag text-primary me-2 mt-1"></i>
                            <span><strong>Status</strong><br><?= h($project['status'] === 'completed' ? 'Completed' : ($project['status'] === 'upcoming' ? 'Upcoming' : 'On-going')) ?></span></li>
                        <?php if ($project['sector_name']): ?>
                            <li class="d-flex mb-3"><i class="<?= h($project['sector_icon'] ?: 'bi-diagram-3') ?> text-primary me-2 mt-1"></i>
                                <span><strong>Sector</strong><br>
                                    <a href="<?= url('sector.php?slug=' . $project['sector_slug']) ?>" style="color:var(--secondary-color);text-decoration:none;"><?= h($project['sector_name']) ?></a>
                                </span></li>
                        <?php endif; ?>
                        <?php if ($project['location'] !== ''): ?>
                            <li class="d-flex mb-3"><i class="bi-geo-alt text-primary me-2 mt-1"></i>
                                <span><strong>Location</strong><br><?= h($project['location']) ?></span></li>
                        <?php endif; ?>
                        <?php if ($project['donor'] !== ''): ?>
                            <li class="d-flex mb-3"><i class="bi-people text-primary me-2 mt-1"></i>
                                <span><strong>Donor / partner</strong><br><?= h($project['donor']) ?></span></li>
                        <?php endif; ?>
                        <?php if ($project['duration'] !== ''): ?>
                            <li class="d-flex mb-3"><i class="bi-calendar4 text-primary me-2 mt-1"></i>
                                <span><strong>Duration</strong><br><?= h($project['duration']) ?></span></li>
                        <?php endif; ?>
                        <?php if ($project['beneficiaries_direct'] !== ''): ?>
                            <li class="d-flex mb-3"><i class="bi-person-check text-primary me-2 mt-1"></i>
                                <span><strong>Direct beneficiaries</strong><br><?= h($project['beneficiaries_direct']) ?></span></li>
                        <?php endif; ?>
                        <?php if ($project['beneficiaries_indirect'] !== ''): ?>
                            <li class="d-flex mb-0"><i class="bi-people-fill text-primary me-2 mt-1"></i>
                                <span><strong>Indirect beneficiaries</strong><br><?= h($project['beneficiaries_indirect']) ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <a href="<?= url('donate.php') ?>" class="custom-btn btn w-100 mb-2">Support this work</a>
                <a href="<?= url('projects.php') ?>" class="custom-btn custom-border-btn btn w-100">Back to projects</a>
            </div>
        </div>
    </div>
</section>

<?php if ($related): ?>
<section class="section-padding section-bg">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-5">
                <h3 class="mb-0 section-title-underline">Related projects</h3>
            </div>

            <?php foreach ($related as $item): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="project-card">
                        <div class="project-card-image-wrap">
                            <a href="<?= url('project.php?slug=' . $item['slug']) ?>">
                                <img src="<?= h(img($item['image'], 700)) ?>" class="project-card-image" alt="<?= h($item['title']) ?>">
                            </a>
                            <span class="project-badge<?= $item['status'] === 'completed' ? ' is-completed' : '' ?>"><?= h($item['status'] === 'completed' ? 'Completed' : 'On-going') ?></span>
                        </div>
                        <div class="project-card-body">
                            <h5><a href="<?= url('project.php?slug=' . $item['slug']) ?>"><?= h($item['title']) ?></a></h5>
                            <ul class="project-meta mb-0">
                                <?php if ($item['location'] !== ''): ?><li><i class="bi-geo-alt"></i><span><?= h($item['location']) ?></span></li><?php endif; ?>
                                <?php if ($item['duration'] !== ''): ?><li><i class="bi-calendar4"></i><span><?= h($item['duration']) ?></span></li><?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
