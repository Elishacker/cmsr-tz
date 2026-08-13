<?php
/** About Us — who we are, vision & mission, core values, how we work, partnership. */

require_once __DIR__ . '/includes/functions.php';

$about       = get_page('about');
$vision      = get_page('vision-mission');
$howWeWork   = get_page('how-we-work');
$partnership = get_page('partnership');
$features    = fetch_all('SELECT * FROM page_sections WHERE page_key = ? AND is_active = 1 ORDER BY sort_order, id', ['about']);
$visionBlocks = fetch_all('SELECT * FROM page_sections WHERE page_key = ? AND is_active = 1 ORDER BY sort_order, id', ['vision-mission']);
$values      = fetch_all('SELECT * FROM core_values WHERE is_active = 1 ORDER BY sort_order, id');
$stats       = fetch_all('SELECT * FROM impact_stats WHERE is_active = 1 ORDER BY sort_order, id');
$networks    = fetch_all("SELECT * FROM partners WHERE is_active = 1 AND type = 'Network' ORDER BY sort_order, id");

$pageTitle       = 'About Us';
$pageDescription = $about['meta_description'];

require __DIR__ . '/includes/header.php';

$heroTitle    = $about['title'];
$heroSubtitle = $about['subtitle'];
$heroImage    = $about['hero_image'];
$heroCrumbs   = ['About Us' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<!-- WHO WE ARE -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12">
                <span class="section-eyebrow">Who we are</span>
                <h2 class="mb-4 section-title-underline">Community Mobilisation for Reciprocal Development</h2>
                <div class="prose"><?= safe_html($about['body']) ?></div>
            </div>

            <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>At a glance</h5>
                    <ul class="list-unstyled mb-0" style="font-size:15px;">
                        <li class="d-flex mb-3"><i class="bi-calendar-check text-primary me-2 mt-1"></i>
                            <span><strong>Established</strong><br><?= h(setting('established_year', '1997')) ?> (August)</span></li>
                        <li class="d-flex mb-3"><i class="bi-patch-check text-primary me-2 mt-1"></i>
                            <span><strong>Registration</strong><br>No. <?= h(setting('registration_no')) ?></span></li>
                        <li class="d-flex mb-3"><i class="bi-geo-alt text-primary me-2 mt-1"></i>
                            <span><strong>Head office</strong><br><?= h(setting('contact_address')) ?></span></li>
                        <li class="d-flex mb-3"><i class="bi-diagram-3 text-primary me-2 mt-1"></i>
                            <span><strong>Sectors</strong><br><?= h(implode(', ', array_column(all_sectors(), 'name'))) ?></span></li>
                        <li class="d-flex mb-0"><i class="bi-globe text-primary me-2 mt-1"></i>
                            <span><strong>Area of operation</strong><br>Tanzania Mainland</span></li>
                    </ul>
                </div>
                <a href="<?= url('resources.php') ?>" class="custom-btn btn w-100">Download our reports</a>
            </div>
        </div>

        <?php if ($features): ?>
            <div class="row mt-5">
                <?php foreach ($features as $feature): ?>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="value-tile">
                            <?= icon_markup($feature['icon'] ?: 'bi-check-circle', $feature['heading'], 'icon-image tile-icon') ?>
                            <h5><?= h($feature['heading']) ?></h5>
                            <p><?= h($feature['body']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- VISION & MISSION -->
<section class="section-padding section-bg" id="vision-mission">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mx-auto text-center mb-5">
                <span class="section-eyebrow"><?= h($vision['subtitle']) ?></span>
                <h2 class="mb-3 section-title-underline"><?= h($vision['title']) ?></h2>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <?php foreach ($visionBlocks as $block): ?>
                <div class="col-lg-6 col-12">
                    <div class="vm-card h-100">
                        <div class="vm-card-head">
                            <div>
                                <h3><?= h($block['heading']) ?></h3>
                                <?php if ($block['subheading'] !== ''): ?>
                                    <p class="vm-eyebrow"><?= h($block['subheading']) ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="vm-icon"><?= icon_markup($block['icon'] ?: 'bi-check-circle', $block['heading']) ?></span>
                        </div>
                        <p class="vm-statement"><?= h($block['body']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (trim(strip_tags((string) $vision['body'])) !== ''): ?>
            <div class="row mt-4">
                <div class="col-lg-10 col-12 mx-auto text-center">
                    <div class="prose" style="font-size:15px;"><?= safe_html($vision['body']) ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CORE VALUES -->
<?php if ($values): ?>
<section class="section-padding" id="core-values">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="section-eyebrow">What we stand for</span>
                <h2 class="mb-3 section-title-underline">Our core values</h2>
            </div>
        </div>

        <!-- Centred so the part-filled last row stays balanced. -->
        <div class="row justify-content-center">
            <?php foreach ($values as $value): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="value-tile">
                        <i class="<?= h($value['icon']) ?>"></i>
                        <h5><?= h($value['title']) ?></h5>
                        <p><?= h($value['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- IMPACT -->
<?php if ($stats): ?>
<section class="impact-strip py-5">
    <div class="container">
        <div class="row">
            <?php foreach ($stats as $stat): ?>
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="impact-item">
                        <i class="<?= h($stat['icon']) ?>"></i>
                        <div class="impact-value"><?= h($stat['value']) ?><?= h($stat['suffix']) ?></div>
                        <p class="impact-label"><?= h($stat['label']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- HOW WE WORK -->
<section class="section-padding" id="how-we-work">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-12">
                <span class="section-eyebrow"><?= h($howWeWork['subtitle']) ?></span>
                <h2 class="mb-4 section-title-underline"><?= h($howWeWork['title']) ?></h2>
                <div class="prose"><?= safe_html($howWeWork['body']) ?></div>
            </div>
            <div class="col-lg-5 col-12 mt-5 mt-lg-0">
                <img src="<?= h(img($howWeWork['hero_image'], 900)) ?>" class="img-fluid rounded shadow-lg mb-4" alt="<?= h($howWeWork['title']) ?>">
                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5><?= h($partnership['title']) ?></h5>
                    <div class="prose" style="font-size:15px;"><?= safe_html($partnership['body']) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NETWORKS -->
<?php if ($networks): ?>
<section class="section-padding section-bg" id="networks">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-12 text-center mx-auto mb-5">
                <span class="section-eyebrow">Collective action</span>
                <h2 class="mb-3 section-title-underline">Our networks</h2>
                <p class="mb-0">We participate in regional and national platforms for shared learning, advocacy and accountability.</p>
            </div>

            <?php foreach ($networks as $network): ?>
                <div class="col-lg-6 col-12 mb-4">
                    <div class="network-card">
                        <h5><?= h($network['name']) ?></h5>
                        <p><?= h($network['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-band-full">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h3>Meet the people behind the work</h3>
                <p>Our Board and management team govern and deliver every project we implement.</p>
            </div>
            <a href="<?= url('board.php') ?>" class="custom-btn btn">Board &amp; leadership</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
