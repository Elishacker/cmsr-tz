<?php
/** Resources — annual reports and publications. */

require_once __DIR__ . '/includes/functions.php';

$reports   = fetch_all('SELECT * FROM reports WHERE is_active = 1 ORDER BY year DESC, sort_order, id');
$resources = [];
foreach (fetch_all('SELECT * FROM resources WHERE is_active = 1 ORDER BY sort_order, id') as $row) {
    $resources[$row['category']][] = $row;
}

$pageTitle       = 'Resources';
$pageDescription = 'Annual reports, programme reports and publications from CMSR-TZ.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Resources';
$heroSubtitle = 'Annual reports, programme documentation and publications';
$heroImage    = 'photos/School Program/IMG_3063 (1).jpeg';
$heroCrumbs   = ['Resources' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<!-- ANNUAL REPORTS -->
<section class="section-padding" id="annual-reports">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mb-5">
                <span class="section-eyebrow">Accountability</span>
                <h2 class="mb-3 section-title-underline">Annual reports</h2>
                <p class="mb-0">Every year the Board approves an implementation report covering all projects and programmes, together
                    with the income and expenditure statements.</p>
            </div>

            <?php if (!$reports): ?>
                <div class="col-12"><p>No annual reports have been published yet.</p></div>
            <?php endif; ?>

            <?php foreach ($reports as $report): ?>
                <div class="col-lg-6 col-12 mb-4">
                    <div class="value-tile d-flex">
                        <div class="me-4 text-center" style="min-width:88px;">
                            <div style="background:var(--primary-color);color:#fff;border-radius:var(--border-radius-small);padding:16px 10px;">
                                <i class="bi-file-earmark-text d-block" style="font-size:26px;"></i>
                                <strong style="font-size:18px;"><?= h($report['year']) ?></strong>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-2"><?= h($report['title']) ?></h5>
                            <p class="mb-3"><?= h($report['description']) ?></p>
                            <?php if ($report['file_link'] !== ''): ?>
                                <a href="<?= h(url($report['file_link'])) ?>" class="custom-btn btn btn-sm" download>
                                    <i class="bi-download me-1"></i>Download
                                </a>
                            <?php else: ?>
                                <a href="<?= url('contact.php?subject=' . rawurlencode('Request: ' . $report['title'])) ?>"
                                   class="custom-btn custom-border-btn btn btn-sm">Request a copy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PUBLICATIONS -->
<section class="section-padding section-bg" id="publications">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mb-5">
                <span class="section-eyebrow">Library</span>
                <h2 class="mb-3 section-title-underline">Publications &amp; documentation</h2>
                <p class="mb-0">Programme reports, sector profiles and reference documents from our work.</p>
            </div>
        </div>

        <?php foreach ($resources as $category => $items): ?>
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <h4 class="mb-0"><?= h($category) ?><?= substr($category, -1) === 's' ? '' : 's' ?></h4>
                </div>
                <?php foreach ($items as $item): ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                        <div class="value-tile h-100 d-flex flex-column">
                            <i class="bi-journal-text"></i>
                            <h5><?= h($item['title']) ?></h5>
                            <p><?= h($item['description']) ?></p>
                            <div class="mt-auto pt-3">
                                <?php if ($item['file_link'] !== ''): ?>
                                    <a href="<?= h(url($item['file_link'])) ?>" class="custom-btn btn btn-sm" download>
                                        <i class="bi-download me-1"></i>Download<?= $item['file_size'] !== '' ? ' (' . h($item['file_size']) . ')' : '' ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('contact.php?subject=' . rawurlencode('Request: ' . $item['title'])) ?>"
                                       style="color:var(--primary-color);font-weight:600;font-size:14px;text-decoration:none;">
                                        Request a copy <i class="bi-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <?php if (!$resources): ?>
            <div class="row"><div class="col-12"><p>No publications have been added yet.</p></div></div>
        <?php endif; ?>
    </div>
</section>

<section class="cta-band-full">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h3>Looking for a specific document?</h3>
                <p>Our office can share project documentation, audited statements and proposals on request.</p>
            </div>
            <a href="<?= url('contact.php') ?>" class="custom-btn btn">Contact the office</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
