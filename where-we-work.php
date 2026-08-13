<?php
/** Where we work — regions and districts of operation. */

require_once __DIR__ . '/includes/functions.php';

$locations = fetch_all('SELECT * FROM locations WHERE is_active = 1 ORDER BY sort_order, id');

$pageTitle       = 'Where We Work';
$pageDescription = 'CMSR-TZ operates across Tanzania Mainland — Dodoma, Kagera, Songwe and Katavi regions — and, with partners, in Zanzibar.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Where We Work';
$heroSubtitle = 'CMSR-TZ has a mandate to operate community development projects across Tanzania Mainland';
$heroImage    = 'photos/water and sanitation/Community benefiting from Shallowell water services at Ibihwa village Bahi district.jpg';
$heroCrumbs   = ['Resources' => 'resources.php', 'Where We Work' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mx-auto text-center mb-5">
                <span class="section-eyebrow">Our footprint</span>
                <h2 class="mb-3 section-title-underline">Regions recently Operated</h2>
                <p class="mb-0">Our head office is in Dodoma City. From there we implement projects in the surrounding districts and,
                    with our partners, in regions as far as Kagera, Songwe and Katavi.</p>
            </div>
        </div>

        <?php foreach ($locations as $i => $location): ?>
            <div class="row align-items-center mb-5 pb-4<?= $i < count($locations) - 1 ? ' border-bottom' : '' ?>">
                <div class="col-lg-5 col-12 <?= $i % 2 ? 'order-lg-2' : '' ?> mb-4 mb-lg-0">
                    <img src="<?= h(img($location['image'], 900)) ?>" class="img-fluid rounded shadow-lg" alt="<?= h($location['region']) ?>">
                </div>
                <div class="col-lg-7 col-12 <?= $i % 2 ? 'order-lg-1 pe-lg-5' : 'ps-lg-5' ?>">
                    <span class="sector-icon"><i class="bi-geo-alt"></i></span>
                    <h3 class="mb-2"><?= h($location['region']) ?></h3>
                    <?php if ($location['districts'] !== ''): ?>
                        <p class="section-eyebrow" style="letter-spacing:1px;"><?= h($location['districts']) ?></p>
                    <?php endif; ?>
                    <p class="mb-0" style="line-height:1.75;"><?= h($location['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!$locations): ?>
            <p class="text-center py-5">Location information has not been added yet.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section-padding section-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-12 mx-auto text-center mb-4">
                <h3 class="mb-3">Find our head office</h3>
                <p class="mb-0"><?= h(setting('contact_address')) ?></p>
            </div>
            <div class="col-12">
                <iframe class="map-frame" src="<?= h(setting('contact_map_embed')) ?>" loading="lazy"
                        title="CMSR-TZ office location" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
