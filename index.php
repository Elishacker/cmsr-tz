<?php
/** CMSR-TZ homepage. */

require_once __DIR__ . '/includes/functions.php';

$slides   = fetch_all('SELECT * FROM slideshow WHERE is_active = 1 ORDER BY sort_order, id');
$overview = fetch_one('SELECT * FROM overview WHERE id = 1') ?: [];
$stats    = fetch_all('SELECT * FROM impact_stats WHERE is_active = 1 ORDER BY sort_order, id');
$sectors  = all_sectors();
$featured = fetch_all("SELECT * FROM projects WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order, id LIMIT 3");
$latest   = fetch_all('SELECT * FROM news WHERE is_published = 1 ORDER BY news_date DESC, id DESC LIMIT 3');
$partners = fetch_all("SELECT * FROM partners WHERE is_active = 1 AND logo <> '' ORDER BY sort_order, id LIMIT 12");
$updates  = fetch_all('SELECT * FROM updates WHERE is_active = 1 ORDER BY update_date DESC, sort_order LIMIT 3');

$pageDescription = setting('meta_description');
require __DIR__ . '/includes/header.php';
?>

<!-- ============ HERO ============ -->
<?php if ($slides): ?>
<section class="hero-section hero-section-full-height">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-12 p-0">
                <div id="hero-slide" class="carousel carousel-fade slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($slides as $i => $slide): ?>
                            <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                                <img src="<?= h(img($slide['image'], 1920)) ?>" class="carousel-image" alt="<?= h($slide['heading']) ?>">

                                <div class="carousel-caption">
                                    <div class="container">
                                        <div class="hero-caption-inner">
                                            <?php if ($slide['eyebrow'] !== ''): ?>
                                                <small><?= h($slide['eyebrow']) ?></small>
                                            <?php endif; ?>
                                            <h1><?= h($slide['heading']) ?></h1>
                                            <p><?= h(excerpt($slide['description'], 220)) ?></p>

                                            <?php if ($slide['btn1_text'] !== '' || $slide['btn2_text'] !== ''): ?>
                                                <div class="hero-links">
                                                    <?php if ($slide['btn1_text'] !== ''): ?>
                                                        <a href="<?= url($slide['btn1_link'] ?: '#') ?>" class="custom-btn btn"><?= h($slide['btn1_text']) ?></a>
                                                    <?php endif; ?>
                                                    <?php if ($slide['btn2_text'] !== ''): ?>
                                                        <a href="<?= url($slide['btn2_link'] ?: '#') ?>" class="custom-btn hero-btn-ghost btn"><?= h($slide['btn2_text']) ?></a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($slides) > 1): ?>
                        <div class="carousel-indicators hero-indicators">
                            <?php foreach ($slides as $i => $slide): ?>
                                <button type="button" data-bs-target="#hero-slide" data-bs-slide-to="<?= $i ?>"
                                    class="<?= $i === 0 ? 'active' : '' ?>"
                                    <?= $i === 0 ? 'aria-current="true"' : '' ?>
                                    aria-label="<?= h($slide['eyebrow'] !== '' ? $slide['eyebrow'] : $slide['heading']) ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <button class="carousel-control-prev" type="button" data-bs-target="#hero-slide" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hero-slide" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ WELCOME ============ -->
<section class="welcome-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-11 col-12 text-center mx-auto">
                <span class="section-eyebrow">Welcome to <?= h(setting('site_short_name')) ?></span>
                <h2 class="mb-3"><?= h(setting('site_name')) ?></h2>
                <p class="mb-0">A non-governmental organisation working since <?= h(setting('established_year', '1997')) ?> to complement
                    the Government of Tanzania in delivering community development projects that alleviate extreme poverty in rural areas.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ WHO WE ARE ============ -->
<?php if ($overview): ?>
<section class="section-bg overview-section" id="section_2">
    <div class="container">
        <div class="row align-items-stretch g-4">

            <div class="col-lg-6 col-12 d-flex">
                <img src="<?= h(img($overview['image'], 1100)) ?>" class="overview-image" alt="<?= h($overview['heading']) ?>">
            </div>

            <div class="col-lg-6 col-12">
                <div class="custom-text-box h-100 d-flex flex-column">
                    <span class="section-eyebrow"><?= h($overview['eyebrow']) ?></span>
                    <h2 class="mb-4 section-title-underline"><?= h($overview['heading']) ?></h2>

                    <div class="prose">
                        <?php foreach (lines($overview['paragraphs']) as $para): ?>
                            <p><?= h($para) ?></p>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($overview['btn_text'] !== ''): ?>
                        <a href="<?= url($overview['btn_link'] ?: 'about.php') ?>" class="custom-btn btn mt-auto align-self-start"><?= h($overview['btn_text']) ?></a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ IMPACT ============ -->
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

<!-- ============ WHAT WE DO ============ -->
<section class="section-padding" id="section_3">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 col-12 text-center mx-auto mb-5">
                <span class="section-eyebrow">What We Do</span>
                <h2 class="mb-3 section-title-underline">Our sectors</h2>
                <p class="mb-0">CMSR-TZ delivers projects across key social sectors to improve community wellbeing and sustainable development.</p>
            </div>

            <?php foreach ($sectors as $sector): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="sector-card">
                        <img src="<?= h(img($sector['image'], 700)) ?>" class="sector-card-image" alt="<?= h($sector['name']) ?>">
                        <div class="sector-card-body">
                            <h5><?= h($sector['name']) ?></h5>
                            <p><?= h(excerpt($sector['summary'], 165)) ?></p>
                            <a href="<?= url('sector.php?slug=' . $sector['slug']) ?>" class="read-more">
                                Learn more <i class="bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ FEATURED PROJECTS ============ -->
<?php if ($featured): ?>
<section class="section-bg featured-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mb-4">
                <span class="section-eyebrow">Our Work</span>
                <h2 class="mb-0 section-title-underline">Featured projects &amp; programmes</h2>
            </div>
            <div class="col-lg-4 col-12 mb-4 d-flex align-items-end justify-content-lg-end">
                <a href="<?= url('projects.php') ?>" class="custom-btn btn">All projects</a>
            </div>

            <?php foreach ($featured as $project): ?>
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
                            <p class="project-card-summary"><?= h(excerpt($project['summary'], 95)) ?></p>
                            <a href="<?= url('project.php?slug=' . $project['slug']) ?>" class="card-read-more">
                                Read more <i class="bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ LATEST NEWS ============ -->
<?php if ($latest): ?>
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mb-5">
                <span class="section-eyebrow">Latest</span>
                <h2 class="mb-0 section-title-underline">News &amp; updates</h2>
            </div>
            <div class="col-lg-4 col-12 mb-5 d-flex align-items-end justify-content-lg-end">
                <a href="<?= url('news.php') ?>" class="custom-btn btn">All news</a>
            </div>

            <?php foreach ($latest as $item): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="news-card">
                        <a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>">
                            <img src="<?= h(img($item['image'], 700)) ?>" class="news-card-image" alt="<?= h($item['title']) ?>">
                        </a>
                        <div class="news-card-body">
                            <span class="news-date"><i class="bi-calendar4 me-1"></i><?= h(fdate($item['news_date'])) ?></span>
                            <h5><a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>"><?= h($item['title']) ?></a></h5>
                            <p><?= h(excerpt($item['excerpt'], 130)) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- ============ IN BRIEF (full-width band) ============ -->
<?php if ($updates): ?>
<section class="brief-band">
    <div class="container">
        <h5 class="brief-band-title"><i class="bi-megaphone me-2"></i>In brief</h5>
        <?php foreach ($updates as $u): ?>
            <div class="brief-item">
                <span class="brief-date"><?= h(fdate($u['update_date'], 'j M Y')) ?></span>
                <span class="brief-text"><strong><?= h($u['title']) ?></strong> &mdash; <?= h(excerpt($u['body'], 120)) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============ PARTNERS ============ -->
<?php if ($partners): ?>
<section class="section-padding">
    <div class="container">
        <!-- The heading keeps its own row: sharing one with the logo grid
             lets a logo slot into the space beside it. -->
        <div class="row">
            <div class="col-lg-10 col-12 text-center mx-auto mb-5">
                <span class="section-eyebrow">Together</span>
                <h2 class="mb-3 section-title-underline">Our donors &amp; partners</h2>
                <p class="mb-0">We plan and implement together with development partners, government institutions and community networks.</p>
            </div>
        </div>

        <div class="row g-3 justify-content-center partner-grid">
            <?php foreach ($partners as $partner): ?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="partner-logo-card">
                        <span class="partner-logo-frame">
                            <img src="<?= h(img($partner['logo'], 320)) ?>" alt="<?= h($partner['name']) ?>">
                        </span>
                        <span class="partner-logo-name"><?= h($partner['short_name'] !== '' ? $partner['short_name'] : $partner['name']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col-12 text-center mt-5">
                <a href="<?= url('partners.php') ?>" class="custom-btn btn">All partners &amp; networks</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ CTA (full-width band) ============ -->
<section class="cta-band-full">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h3>Make an impact. Change lives.</h3>
                <p>Sponsor a student, fund a water point or partner with us on the next community project.</p>
            </div>
            <div class="cta-actions">
                <a href="<?= url('donate.php') ?>" class="custom-btn btn">Support our work</a>
                <a href="<?= url('contact.php') ?>" class="custom-btn cta-btn-ghost btn">Contact us</a>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
