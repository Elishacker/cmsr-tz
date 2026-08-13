<?php
/** Donors, implementing partners, government partners and networks. */

require_once __DIR__ . '/includes/functions.php';

$groups = [
    'Donor'      => ['title' => 'Donors & development partners', 'lead' => 'The institutions whose funding makes our community projects possible.'],
    'Partner'    => ['title' => 'Implementing partners', 'lead' => 'Organisations we plan and deliver projects with, in Tanzania and abroad.'],
    'Government' => ['title' => 'Government partners', 'lead' => 'We align every intervention with national priorities and local government systems.'],
    'Network'    => ['title' => 'Networks we belong to', 'lead' => 'Collective platforms for coordination, advocacy, capacity building and accountability.'],
];

$partners = [];
foreach (fetch_all('SELECT * FROM partners WHERE is_active = 1 ORDER BY sort_order, id') as $row) {
    $partners[$row['type']][] = $row;
}

$partnership = get_page('partnership');

$pageTitle       = 'Partners & Networks';
$pageDescription = 'CMSR-TZ works with donors, implementing partners, government institutions and civil society networks across Tanzania and Italy.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Partners & Networks';
$heroSubtitle = 'Planning together, implementing together';
$heroImage    = 'photos/Agriculture/_MG_9700.jpg';
$heroCrumbs   = ['About Us' => 'about.php', 'Partners & Networks' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding pb-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mx-auto text-center">
                <span class="section-eyebrow">Partnership &amp; collaboration</span>
                <h2 class="mb-4 section-title-underline">How we work with others</h2>
                <div class="prose text-start"><?= safe_html($partnership['body']) ?></div>
            </div>
        </div>
    </div>
</section>

<?php foreach ($groups as $type => $meta): ?>
    <?php if (empty($partners[$type])) { continue; } ?>
    <section class="section-padding<?= $type === 'Partner' || $type === 'Network' ? ' section-bg' : '' ?>">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="mb-3 section-title-underline"><?= h($meta['title']) ?></h2>
                    <p class="mb-0"><?= h($meta['lead']) ?></p>
                </div>
            </div>

            <!-- Centred so a part-filled last row stays balanced. -->
            <div class="row justify-content-center">
                <?php foreach ($partners[$type] as $partner): ?>
                    <?php if ($type === 'Network'): ?>
                        <div class="col-lg-6 col-12 mb-4">
                            <div class="network-card">
                                <h5><?= h($partner['name']) ?></h5>
                                <p><?= h($partner['description']) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="partner-tile">
                                <div class="partner-tile-logo">
                                    <?php if ($partner['logo'] !== ''): ?>
                                        <img src="<?= h(img($partner['logo'], 400)) ?>" alt="<?= h($partner['name']) ?>">
                                    <?php else: ?>
                                        <span class="partner-tile-placeholder"><?= h(mb_substr($partner['short_name'] !== '' ? $partner['short_name'] : $partner['name'], 0, 2)) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="partner-tile-body">
                                    <h5><?= h($partner['short_name'] !== '' ? $partner['short_name'] : $partner['name']) ?></h5>
                                    <?php if ($partner['short_name'] !== '' && $partner['short_name'] !== $partner['name']): ?>
                                        <p class="partner-tile-fullname"><?= h($partner['name']) ?></p>
                                    <?php endif; ?>
                                    <p class="partner-tile-text"><?= h($partner['description']) ?></p>

                                    <?php if ($partner['website'] !== ''): ?>
                                        <a href="<?= h($partner['website']) ?>" target="_blank" rel="noopener" class="card-read-more">
                                            Visit website <i class="bi-box-arrow-up-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<section class="cta-band-full">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h3>Interested in partnering with us?</h3>
                <p>We welcome enquiries from donors, government institutions, civil society and the private sector.</p>
            </div>
            <a href="<?= url('contact.php') ?>" class="custom-btn btn">Get in touch</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
