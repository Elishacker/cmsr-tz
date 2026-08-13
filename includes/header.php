<?php
/**
 * Public site header: top contact bar + main navigation.
 *
 * Pages set $pageTitle / $pageDescription before including this file.
 */

require_once __DIR__ . '/functions.php';

$navSectors = all_sectors();
$shortName  = setting('site_short_name', 'CMSR-TZ');
$tagline    = setting('site_tagline');
$title      = isset($pageTitle) && $pageTitle !== ''
    ? $pageTitle . ' | ' . $shortName
    : $shortName . ' | ' . setting('site_name');
$description = $pageDescription ?? setting('meta_description');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= h($description) ?>">
    <meta name="author" content="CMSR-TZ">
    <title><?= h($title) ?></title>

    <link rel="icon" href="<?= url(setting('site_logo', 'assets/images/logo.png')) ?>">

    <!-- CSS FILES -->
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/templatemo-kind-heart-charity.css') ?>" rel="stylesheet">
    <?php // ?v= busts the browser cache whenever the stylesheet is edited ?>
    <link href="<?= url('assets/css/cmsr.css') ?>?v=<?= @filemtime(ROOT_PATH . '/assets/css/cmsr.css') ?: 1 ?>" rel="stylesheet">
</head>

<body id="section_1">

    <header class="site-header">
        <div class="container">
            <div class="row">

                <div class="col-lg-8 col-12 d-flex flex-wrap">
                    <p class="d-flex me-4 mb-0">
                        <i class="bi-geo-alt me-2"></i>
                        <?= h(setting('contact_location')) ?>
                    </p>

                    <p class="d-flex mb-0">
                        <i class="bi-envelope me-2"></i>
                        <a href="mailto:<?= h(setting('contact_email')) ?>"><?= h(setting('contact_email')) ?></a>
                    </p>
                </div>

                <div class="col-lg-3 col-12 ms-auto d-lg-block d-none">
                    <ul class="social-icon">
                        <li class="social-icon-item"><a href="<?= h(setting('social_facebook', '#')) ?>" class="social-icon-link bi-facebook" aria-label="Facebook"></a></li>
                        <li class="social-icon-item"><a href="<?= h(setting('social_instagram', '#')) ?>" class="social-icon-link bi-instagram" aria-label="Instagram"></a></li>
                        <li class="social-icon-item"><a href="<?= h(setting('social_youtube', '#')) ?>" class="social-icon-link bi-youtube" aria-label="YouTube"></a></li>
                        <li class="social-icon-item"><a href="<?= h(setting('social_whatsapp', '#')) ?>" class="social-icon-link bi-whatsapp" aria-label="WhatsApp"></a></li>
                    </ul>
                </div>

            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= url('index.php') ?>">
                <img src="<?= url(setting('site_logo', 'assets/images/logo.png')) ?>" class="logo img-fluid" alt="<?= h($shortName) ?>">
                <span>
                    <?= h($shortName) ?>
                    <small><?= h($tagline) ?></small>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link<?= is_current('index.php') ? ' active' : '' ?>" href="<?= url('index.php') ?>">HOME</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?= is_current('about.php') || is_current('board.php') ? ' active' : '' ?>"
                            href="<?= url('about.php') ?>" id="aboutDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">ABOUT US</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item" href="<?= url('about.php') ?>">Who We Are</a></li>
                            <li><a class="dropdown-item" href="<?= url('about.php#vision-mission') ?>">Vision &amp; Mission</a></li>
                            <li><a class="dropdown-item" href="<?= url('about.php#core-values') ?>">Core Values</a></li>
                            <li><a class="dropdown-item" href="<?= url('about.php#how-we-work') ?>">How We Work</a></li>
                            <li><a class="dropdown-item" href="<?= url('board.php') ?>">Board &amp; Leadership</a></li>
                            <li><a class="dropdown-item" href="<?= url('partners.php') ?>">Partners &amp; Networks</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?= is_current('what-we-do.php') || is_current('sector.php') ? ' active' : '' ?>"
                            href="<?= url('what-we-do.php') ?>" id="whatWeDoDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">WHAT WE DO</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="whatWeDoDropdown">
                            <?php foreach ($navSectors as $s): ?>
                                <li><a class="dropdown-item" href="<?= url('sector.php?slug=' . $s['slug']) ?>"><?= h($s['name']) ?> Sector</a></li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('what-we-do.php') ?>">All Sectors</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?= is_current('resources.php') ? ' active' : '' ?>"
                            href="<?= url('resources.php') ?>" id="resourcesDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">RESOURCES</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="resourcesDropdown">
                            <li><a class="dropdown-item" href="<?= url('resources.php#annual-reports') ?>">Annual Reports</a></li>
                            <li><a class="dropdown-item" href="<?= url('resources.php#publications') ?>">Publications</a></li>
                            <li><a class="dropdown-item" href="<?= url('gallery.php') ?>">Photo Gallery</a></li>
                            <li><a class="dropdown-item" href="<?= url('where-we-work.php') ?>">Where We Work</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link<?= is_current('projects.php') || is_current('project.php') ? ' active' : '' ?>" href="<?= url('projects.php') ?>">PROJECTS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link<?= is_current('news.php') || is_current('news-detail.php') ? ' active' : '' ?>" href="<?= url('news.php') ?>">LATEST</a>
                    </li>

                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link custom-btn custom-border-btn btn" href="<?= url('donate.php') ?>">SUPPORT US</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
<?php foreach (take_flashes() as $msg): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= h($msg['type'] === 'error' ? 'danger' : $msg['type']) ?> alert-dismissible fade show" role="alert">
                <?= h($msg['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
<?php endforeach; ?>
