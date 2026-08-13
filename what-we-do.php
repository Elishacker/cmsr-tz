<?php
/** What We Do — overview of the five sectors. */

require_once __DIR__ . '/includes/functions.php';

$sectors = all_sectors();

$counts = [];
foreach (fetch_all('SELECT sector_id, COUNT(*) AS total FROM projects WHERE is_active = 1 GROUP BY sector_id') as $row) {
    $counts[(int) $row['sector_id']] = (int) $row['total'];
}

$pageTitle       = 'What We Do';
$pageDescription = 'CMSR-TZ delivers projects across water and sanitation, health, education, agriculture and youth empowerment.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'What We Do';
$heroSubtitle = 'Projects across key social sectors to improve community wellbeing and sustainable development';
$heroImage    = 'photos/water and sanitation/Water service Bahi Makulu - bahi district.jpg';
$heroCrumbs   = ['What We Do' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mx-auto text-center mb-5">
                <span class="section-eyebrow">Our sectors</span>
                <h2 class="mb-3 section-title-underline">Five areas, one approach</h2>
                <p class="mb-0">CMSR-TZ's approach is bottom-up and holistic. In every sector, communities are active participants in
                    planning and implementation &mdash; this is what makes the results last.</p>
            </div>
        </div>

        <?php foreach ($sectors as $i => $sector): ?>
            <div class="row align-items-center mb-5 pb-4<?= $i < count($sectors) - 1 ? ' border-bottom' : '' ?>">
                <div class="col-lg-5 col-12 <?= $i % 2 ? 'order-lg-2' : '' ?> mb-4 mb-lg-0">
                    <img src="<?= h(img($sector['image'], 900)) ?>" class="img-fluid rounded shadow-lg" alt="<?= h($sector['name']) ?>">
                </div>
                <div class="col-lg-7 col-12 <?= $i % 2 ? 'order-lg-1 pe-lg-5' : 'ps-lg-5' ?>">
                    <span class="sector-icon"><i class="<?= h($sector['icon']) ?>"></i></span>
                    <h3 class="mb-2"><?= h($sector['name']) ?></h3>
                    <p class="section-eyebrow" style="letter-spacing:1px;"><?= h($sector['tagline']) ?></p>
                    <p class="mb-3"><?= h($sector['summary']) ?></p>

                    <?php $highlights = lines($sector['highlights']); ?>
                    <?php if ($highlights): ?>
                        <ul class="list-unstyled mb-4">
                            <?php foreach ($highlights as $item): ?>
                                <li class="d-flex mb-2" style="font-size:15px;">
                                    <i class="bi-check2 text-primary me-2 mt-1"></i><span><?= h($item) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <a href="<?= url('sector.php?slug=' . $sector['slug']) ?>" class="custom-btn btn">Explore this sector</a>
                    <?php if (!empty($counts[(int) $sector['id']])): ?>
                        <a href="<?= url('projects.php?sector=' . $sector['slug']) ?>" class="custom-btn custom-border-btn btn ms-2">
                            <?= (int) $counts[(int) $sector['id']] ?> projects
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="cta-band-full">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h3>See these sectors in action</h3>
                <p>Browse our current and completed projects from 2014 to today.</p>
            </div>
            <a href="<?= url('projects.php') ?>" class="custom-btn btn">View all projects</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
