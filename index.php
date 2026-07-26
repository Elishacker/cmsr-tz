<?php
require __DIR__ . '/config/db.php';

$slides = $pdo->query('SELECT * FROM slideshow ORDER BY sort_order ASC, id ASC')->fetchAll();
$overview = $pdo->query('SELECT * FROM overview WHERE id = 1')->fetch();
$overviewParagraphs = ($overview && $overview['paragraphs'])
    ? array_values(array_filter(array_map('trim', explode("\n", $overview['paragraphs']))))
    : [];
$programs = $pdo->query('SELECT * FROM programs ORDER BY sort_order ASC, id ASC')->fetchAll();
$latestNews = $pdo->query('SELECT * FROM news ORDER BY id DESC LIMIT 3')->fetchAll();
$updates = $pdo->query('SELECT * FROM updates ORDER BY id DESC LIMIT 5')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CMSR-TZ – Community Mobilisation for Reciprocal Development in Tanzania. Working since 1997 to alleviate poverty through education, health, women empowerment and agriculture.">
    <meta name="author" content="CMSR-TZ">
    <title>CMSR-TZ | Community Mobilisation for Reciprocal Development – Tanzania</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/templatemo-kind-heart-charity.css" rel="stylesheet">
</head>
<body id="section_1">

    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12 d-flex flex-wrap">
                    <p class="d-flex me-4 mb-0">
                        <i class="bi-geo-alt me-2"></i>
                        Dodoma City, Tanzania
                    </p>
                    <p class="d-flex mb-0">
                        <i class="bi-envelope me-2"></i>
                        <a href="mailto:info@cmsr-tz.org">info@cmsr-tz.org</a>
                    </p>
                </div>
                <div class="col-Improving Maternal & Neonatal Health Care (IMaNHC) lg-3 col-12 ms-auto d-lg-block d-none">
                    <ul class="social-icon">
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-facebook"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-instagram"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-youtube"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-whatsapp"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" class="logo img-fluid" alt="CMSR-TZ">
                <span>
                    CMSR-TZ
                    <small>Community Mobilisation for Reciprocal Development in Tanzania</small>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">HOME</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="about.html" id="aboutDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">ABOUT US</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="aboutDropdown">
                            <li><a class="dropdown-item" href="who-we-are.html">Who We Are</a></li>
                            <li><a class="dropdown-item" href="vision-mission.html">Vision &amp; Mission</a></li>
                            <li><a class="dropdown-item" href="core-values.html">Core Values</a></li>
                            <li><a class="dropdown-item" href="board.html">Board of Directors</a></li>
                            <li><a class="dropdown-item" href="leadership.html">Leadership</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="what-we-do.html" id="whatWeDoDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">WHAT WE DO</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="whatWeDoDropdown">
                            <li><a class="dropdown-item" href="water-sanitation.html">Water and Sanitation Sector</a></li>
                            <li><a class="dropdown-item" href="education.html">Education Sector</a></li>
                            <li><a class="dropdown-item" href="health.html">Health Sector</a></li>
                            <li><a class="dropdown-item" href="agriculture.html">Agriculture Sector</a></li>
                            <li><a class="dropdown-item" href="youth-empowerment.html">Youth Empowerment Sector</a></li>
                        </ul>
                    </li>
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="where-we-work.html" id="whereWeWorkDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">WHERE WE WORK</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="whereWeWorkDropdown">
                            <li><a class="dropdown-item" href="where-we-work.html#dodoma">Dodoma Region</a></li>
                            <li><a class="dropdown-item" href="where-we-work.html#kagera">Kagera Region</a></li>
                            <li><a class="dropdown-item" href="where-we-work.html#zanzibar">Zanzibar</a></li>
                        </ul>
                    </li> -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="resources.php" id="resourcesDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">RESOURCES</a>
                        <ul class="dropdown-menu dropdown-menu-light" aria-labelledby="resourcesDropdown">
                            <li><a class="dropdown-item" href="resources.html#annual-reports">Annual Reports</a></li>
                            <li><a class="dropdown-item" href="resources.html#publications">Publications</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">PROJECTS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">LATEST</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link custom-btn btn staff-btn" href="staff-login.html">
                            <i class="bi-person-lock me-1"></i>STAFF
                        </a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>

    <main>

        <!-- HERO -->
        <?php if ($slides): ?>
        <section class="hero-section hero-section-full-height">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-12 p-0">
                        <div id="hero-slide" class="carousel carousel-fade slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($slides as $i => $s): ?>
                                <button type="button" data-bs-target="#hero-slide" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $i + 1 ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($slides as $i => $s): ?>
                                <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                                    <img src="<?= htmlspecialchars($s['image']) ?>" class="carousel-image img-fluid w-100" style="height:600px; object-fit:cover;" alt="<?= htmlspecialchars(strip_tags($s['heading'])) ?>">
                                    <div class="carousel-caption d-flex flex-column justify-content-end">
                                        <h1><?= $s['heading'] ?></h1>
                                        <p><?= htmlspecialchars($s['description']) ?></p>
                                        <div class="d-flex justify-content-end gap-2">
                                            <?php if ($s['btn1_text']): ?><a href="<?= htmlspecialchars($s['btn1_link']) ?>" class="custom-btn btn"><?= htmlspecialchars($s['btn1_text']) ?></a><?php endif; ?>
                                            <?php if ($s['btn2_text']): ?><a href="<?= htmlspecialchars($s['btn2_link']) ?>" class="donate-btn btn"><?= htmlspecialchars($s['btn2_text']) ?></a><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
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

        <!-- STATS -->
        <section class="section-padding section-bg impact-stats">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <small class="small-title">Our Impact</small>
                        <h2>Reaching Communities Across Tanzania Since 1997</h2>
                    </div>
                </div>
                <div class="row g-4">

                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="counter-thumb stat-card p-4 rounded h-100 text-center">
                            <span class="stat-icon-badge"><i class="bi-award-fill stat-icon"></i></span>
                            <div class="counter-number">
                                <span class="counter" data-from="20" data-to="29">29</span><span>+</span>
                            </div>
                            <span class="counter-text">Years of Impact</span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="counter-thumb stat-card p-4 rounded h-100 text-center">
                            <span class="stat-icon-badge"><i class="bi-droplet-fill stat-icon"></i></span>
                            <div class="counter-number">
                                <span class="counter" data-from="1600" data-to="2000">2000</span><span>+</span>
                            </div>
                            <span class="counter-text">Water &amp; Sanitation</span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="counter-thumb stat-card p-4 rounded h-100 text-center">
                            <span class="stat-icon-badge"><i class="bi-heart-fill stat-icon"></i></span>
                            <div class="counter-number">
                                <span class="counter" data-from="8" data-to="14">14</span>
                            </div>
                            <span class="counter-text">Schools Reached &#8211; Health</span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="counter-thumb stat-card p-4 rounded h-100 text-center">
                            <span class="stat-icon-badge"><i class="bi-book-fill stat-icon"></i></span>
                            <div class="counter-number">
                                <span class="counter" data-from="180" data-to="245">245</span><span>+</span>
                            </div>
                            <span class="counter-text">Students Supported</span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="counter-thumb stat-card p-4 rounded h-100 text-center">
                            <span class="stat-icon-badge"><i class="bi-flower1 stat-icon"></i></span>
                            <div class="counter-number">
                                <span class="counter" data-from="0" data-to="2">2</span>
                            </div>
                            <span class="counter-text">Agriculture Projects</span>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="counter-thumb stat-card p-4 rounded h-100 text-center">
                            <span class="stat-icon-badge"><i class="bi-shield-check stat-icon"></i></span>
                            <div class="counter-number">
                                <span class="counter" data-from="30" data-to="50">50</span><span>+</span>
                            </div>
                            <span class="counter-text">Youth Empowered</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ABOUT -->
        <section class="section-padding section-bg" id="about">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12 mb-4 mb-lg-0">
                        <img src="<?= htmlspecialchars($overview['image'] ?? '') ?>" class="about-image img-fluid" alt="CMSR-TZ team in the field" style="height:350px;object-fit:cover;width:100%;border-radius:8px;">
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="custom-text-block">
                            <h4 class="mb-3"><?= htmlspecialchars($overview['heading'] ?? '') ?></h4>
                            <?php foreach ($overviewParagraphs as $p): ?>
                            <p><?= $p ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VISION CTA -->
        <!-- <section class="cta-section section-padding section-bg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-12 mb-4 mb-lg-0">
                        <h2 class="text-white mb-2">Our Vision</h2>
                        <p class="text-white mb-0">"Actively contribute to the social and economic development of the community in which we operate and continue facilitating people in the struggle for poverty alleviation through sustainable social services and economic development."</p>
                    </div>
                    <div class="col-lg-4 col-12 d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="about.html#vision-mission" class="custom-btn btn">Our Mission</a>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- PROGRAMS -->
        <section class="section-padding" id="programs">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <small class="small-title">What We Do</small>
                        <h2>Our Programs &amp; Sectors</h2>
                        <p class="col-lg-8 mx-auto">CMSR-TZ implements community-based development projects across four key sectors, funded by development partners and the private sector.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <?php foreach ($programs as $p): ?>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="causes-thumb">
                            <img src="<?= htmlspecialchars($p['image']) ?>" class="causes-image img-fluid" alt="<?= htmlspecialchars($p['title']) ?>" style="height:220px;object-fit:cover;width:100%;">
                            <div class="causes-info">
                                <small><?= htmlspecialchars($p['category']) ?></small>
                                <h4 class="causes-title"><a href="<?= htmlspecialchars($p['link']) ?>"><?= htmlspecialchars($p['title']) ?></a></h4>
                                <p><?= htmlspecialchars($p['description']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!$programs): ?>
                    <div class="col-12"><p class="text-center text-muted">Programs will appear here once added from the Staff Dashboard.</p></div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-5">
                    <a href="what-we-do.html" class="custom-btn btn">View All Programs</a>
                </div>
            </div>
        </section>

        <!-- WHERE WE WORK -->
        <section class="section-padding section-bg">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <small class="small-title">Our Reach</small>
                        <h2>Where We Work</h2>
                        <p class="col-lg-8 mx-auto">CMSR-TZ operates across Tanzania Mainland and Zanzibar, with a special focus on rural communities.</p>
                    </div>
                </div>
                <div class="row g-4 text-center">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <i class="bi-geo-fill fs-1 text-primary mb-3 d-block"></i>
                            <h4>Dodoma Region</h4>
                            <p>Our headquarters. We implement the Shule Program (8 schools), PMHM health project (14 schools), IMaNHC maternal health, and SWALA women empowerment at Chikopelo Bwawani, Bahi District.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <i class="bi-geo-fill fs-1 text-primary mb-3 d-block"></i>
                            <h4>Kagera Region</h4>
                            <p>In Ngara District, CMSR-TZ leads the Rulenge project &#8211; a comprehensive initiative covering agriculture training, entrepreneurship, deep water well, solar energy, and classroom construction.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <i class="bi-geo-fill fs-1 text-primary mb-3 d-block"></i>
                            <h4>Zanzibar</h4>
                            <p>CMSR-TZ served as lead organization for the IMaNHC multi-country project, implemented in Zanzibar by partner CUAMM to improve maternal and neonatal health care.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PARTNERS -->
        <section class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <small class="small-title">Collaboration</small>
                        <h2>Our Development Partners</h2>
                        <p>We work closely with local and international partners, government authorities, and community stakeholders.</p>
                    </div>
                </div>
                <div class="row text-center g-3 justify-content-center">
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="p-3 border rounded h-100"><strong>CMSR-Italy</strong><br><small class="text-muted">Lead Donor</small></div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="p-3 border rounded h-100"><strong>CUAMM</strong><br><small class="text-muted">Health Partner</small></div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="p-3 border rounded h-100"><strong>AICS</strong><br><small class="text-muted">Development Partner</small></div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="p-3 border rounded h-100"><strong>AVSI</strong><br><small class="text-muted">Partner</small></div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="p-3 border rounded h-100"><strong>COPE</strong><br><small class="text-muted">Partner</small></div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6">
                        <div class="p-3 border rounded h-100"><strong>CEI</strong><br><small class="text-muted">Partner</small></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- LATEST NEWS -->
        <section class="news-section section-padding section-bg" id="latest">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <small class="small-title">Updates</small>
                        <h2>Latest News &amp; Updates</h2>
                    </div>
                </div>
                <div class="row g-4">
                    <?php foreach ($latestNews as $n): ?>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="news-block">
                            <div class="news-block-top">
                                <a href="news-detail.php?id=<?= (int)$n['id'] ?>">
                                    <img src="<?= htmlspecialchars($n['image']) ?>" class="news-image img-fluid" alt="<?= htmlspecialchars($n['title']) ?>">
                                </a>
                            </div>
                            <div class="news-block-info">
                                <div class="news-block-date">
                                    <p><i class="bi-calendar4 me-2"></i><?= htmlspecialchars($n['news_date']) ?></p>
                                </div>
                                <div class="news-block-title mb-2">
                                    <h4><a href="news-detail.php?id=<?= (int)$n['id'] ?>" class="news-block-title-link"><?= htmlspecialchars($n['title']) ?></a></h4>
                                </div>
                                <div class="news-block-body">
                                    <p><?= htmlspecialchars($n['excerpt']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!$latestNews): ?>
                    <div class="col-12"><p class="text-center text-muted">No news posted yet.</p></div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-5">
                    <a href="news.php" class="custom-btn btn">View All News</a>
                </div>
            </div>
        </section>

        <!-- UPDATES -->
        <?php if ($updates): ?>
        <section class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <small class="small-title">In Brief</small>
                        <h2>Latest Updates</h2>
                    </div>
                </div>
                <div class="row g-3">
                    <?php foreach ($updates as $u): ?>
                    <div class="col-lg-6 col-12">
                        <div class="p-3 border rounded d-flex">
                            <div class="me-3 text-primary"><i class="bi-megaphone fs-4"></i></div>
                            <div>
                                <small class="text-muted d-block mb-1"><?= htmlspecialchars($u['update_date']) ?></small>
                                <h6 class="mb-1"><?= htmlspecialchars($u['title']) ?></h6>
                                <?php if ($u['body']): ?><p class="mb-0 small text-muted"><?= htmlspecialchars($u['body']) ?></p><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- GET INVOLVED CTA -->
        <section class="cta-section section-padding">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-12 mb-4 mb-lg-0">
                        <h2 class="text-white mb-2">Get Involved with CMSR-TZ</h2>
                        <p class="text-white mb-0">Partner with us, volunteer, or learn more about how CMSR-TZ is transforming lives across Tanzania through community development.</p>
                    </div>
                    <div class="col-lg-4 col-12 d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="about.html" class="custom-btn btn btn-lg">Partner With Us</a>
                        <a href="where-we-work.html" class="custom-btn btn btn-lg">Our Reach</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="searchModalLabel">Search CMSR-TZ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-4">
                    <form action="news.php" method="get">
                        <div class="input-group">
                            <input type="search" name="q" class="form-control form-control-lg" placeholder="Search programs, news, reports...">
                            <button class="btn custom-btn" type="submit"><i class="bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">

                <!-- Brand -->
                <div class="col-lg-4 col-12">
                    <img src="images/logo.png" class="logo img-fluid mb-3" alt="CMSR-TZ">
                    <h5 class="text-white mb-2">CMSR-TZ</h5>
                    <p class="text-white-50 mb-3" style="font-size:14px;line-height:1.6;">
                        Community Mobilisation for Reciprocal Development in Tanzania.<br>
                        Established 1997 &mdash; Reg. No. 00NGO/R1/00411.
                    </p>
                    <ul class="social-icon">
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-facebook" aria-label="Facebook"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-instagram" aria-label="Instagram"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-youtube" aria-label="YouTube"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-whatsapp" aria-label="WhatsApp"></a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-4 col-6">
                    <h5 class="site-footer-title">Quick Links</h5>
                    <ul class="footer-menu">
                        <li class="footer-menu-item"><a href="index.php" class="footer-menu-link">Home</a></li>
                        <li class="footer-menu-item"><a href="about.html" class="footer-menu-link">About Us</a></li>
                        <li class="footer-menu-item"><a href="what-we-do.html" class="footer-menu-link">What We Do</a></li>
                        <li class="footer-menu-item"><a href="where-we-work.html" class="footer-menu-link">Where We Work</a></li>
                    </ul>
                </div>

                <!-- Programs -->
                <div class="col-lg-2 col-md-4 col-6">
                    <h5 class="site-footer-title">Programs</h5>
                    <ul class="footer-menu">
                        <li class="footer-menu-item"><a href="education.html" class="footer-menu-link">Education</a></li>
                        <li class="footer-menu-item"><a href="health.html" class="footer-menu-link">Health</a></li>
                        <li class="footer-menu-item"><a href="women-empowerment.html" class="footer-menu-link">Women Empowerment</a></li>
                        <li class="footer-menu-item"><a href="agriculture.html" class="footer-menu-link">Agriculture</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-4 col-md-4 col-12">
                    <h5 class="site-footer-title">Contact</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-start">
                            <i class="bi-geo-alt text-primary me-2 mt-1"></i>
                            <span class="text-white-75" style="color:rgba(255,255,255,0.75);font-size:14px;">
                                P.O. Box, Dodoma City, Tanzania
                            </span>
                        </li>
                        <li class="mb-2 d-flex align-items-start">
                            <i class="bi-envelope text-primary me-2 mt-1"></i>
                            <a href="mailto:info@cmsr-tz.org" class="site-footer-link" style="font-size:14px;">info@cmsr-tz.org</a>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi-patch-check text-primary me-2 mt-1"></i>
                            <span class="text-white-75" style="color:rgba(255,255,255,0.75);font-size:14px;">
                                Reg. No: 00NGO/R1/00411
                            </span>
                        </li>
                    </ul>
                    <a href="donate.html" class="custom-btn btn btn-sm">Support Our Work</a>
                </div>

            </div>
        </div>
        <div class="site-footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-12">
                        <p class="copyright-text mb-0">
                            Copyright &copy; 2025 CMSR-TZ &mdash; Community Mobilisation for Reciprocal Development, Tanzania.
                        </p>
                    </div>
                    <div class="col-lg-4 col-12 text-lg-end mt-2 mt-lg-0">
                        <p class="copyright-text mb-0">
                            <a href="staff-login.html" style="color:rgba(255,255,255,0.5);font-size:13px;">
                                <i class="bi-person-lock me-1"></i>Staff Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/click-scroll.js"></script>
    <script src="js/counter.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>