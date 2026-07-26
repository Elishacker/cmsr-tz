<?php
require __DIR__ . '/config/db.php';
$allNews = $pdo->query('SELECT * FROM news ORDER BY id DESC')->fetchAll();
$recentNews = array_slice($allNews, 0, 2);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Latest news and updates from CMSR-TZ – Community Mobilisation for Reciprocal Development in Tanzania.">
    <meta name="author" content="">

    <title>Latest News &amp; Updates | CMSR-TZ Tanzania</title>

    <!-- CSS FILES -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="css/templatemo-kind-heart-charity.css" rel="stylesheet">
    <!--

-->
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12 d-flex flex-wrap">
                    <p class="d-flex me-4 mb-0"><i class="bi-geo-alt me-2"></i>Dodoma City, Tanzania</p>
                    <p class="d-flex mb-0"><i class="bi-envelope me-2"></i><a href="mailto:info@cmsr-tz.org">info@cmsr-tz.org</a></p>
                </div>
                <div class="col-lg-3 col-12 ms-auto d-lg-block d-none">
                    <ul class="social-icon">
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-facebook"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-instagram"></a></li>
                        <li class="social-icon-item"><a href="#" class="social-icon-link bi-youtube"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" class="logo img-fluid" alt="CMSR-TZ">
                <span>CMSR-TZ<small>Community Mobilisation for Reciprocal Development</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="about.html" role="button" data-bs-toggle="dropdown">ABOUT US</a>
                        <ul class="dropdown-menu dropdown-menu-light">
                            <li><a class="dropdown-item" href="who-we-are.html">Who We Are</a></li>
                            <li><a class="dropdown-item" href="vision-mission.html">Vision &amp; Mission</a></li>
                            <li><a class="dropdown-item" href="core-values.html">Core Values</a></li>
                            <li><a class="dropdown-item" href="board.html">Board of Directors</a></li>
                            <li><a class="dropdown-item" href="leadership.html">Leadership</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="what-we-do.html" role="button" data-bs-toggle="dropdown">WHAT WE DO</a>
                        <ul class="dropdown-menu dropdown-menu-light">
                            <li><a class="dropdown-item" href="education.html">Education &#8211; Shule Program</a></li>
                            <li><a class="dropdown-item" href="health.html">Health</a></li>
                            <li><a class="dropdown-item" href="women-empowerment.html">Women Empowerment &#8211; SWALA</a></li>
                            <li><a class="dropdown-item" href="agriculture.html">Agriculture</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="where-we-work.html" role="button" data-bs-toggle="dropdown">WHERE WE WORK</a>
                        <ul class="dropdown-menu dropdown-menu-light">
                            <li><a class="dropdown-item" href="where-we-work.html#dodoma">Dodoma Region</a></li>
                            <li><a class="dropdown-item" href="where-we-work.html#kagera">Kagera Region</a></li>
                            <li><a class="dropdown-item" href="where-we-work.html#zanzibar">Zanzibar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="resources.php" role="button" data-bs-toggle="dropdown">RESOURCES</a>
                        <ul class="dropdown-menu dropdown-menu-light">
                            <li><a class="dropdown-item" href="resources.html#annual-reports">Annual Reports</a></li>
                            <li><a class="dropdown-item" href="resources.html#publications">Publications</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link active" href="news.php">LATEST</a></li>
                    <li class="nav-item"><a class="nav-link custom-btn btn staff-btn" href="staff-login.html"><i class="bi-person-lock me-1"></i>STAFF</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>

        <section class="news-detail-header-section text-center">
            <div class="section-overlay"></div>

            <div class="container">
                <div class="row">

                    <div class="col-lg-12 col-12">
                        <h1 class="text-white">Latest News &amp; Updates</h1>
                    </div>

                </div>
            </div>
        </section>

        <section class="news-section section-padding">
            <div class="container">
                <div class="row">

                    <div class="col-lg-7 col-12">
                        <?php foreach ($allNews as $n): ?>
                        <div class="news-block mt-3">
                            <div class="news-block-top">
                                <a href="news-detail.php?id=<?= (int)$n['id'] ?>">
                                    <img src="<?= htmlspecialchars($n['image']) ?>"
                                        class="news-image img-fluid" alt="<?= htmlspecialchars($n['title']) ?>">
                                </a>
                            </div>

                            <div class="news-block-info">
                                <div class="d-flex mt-2">
                                    <div class="news-block-date">
                                        <p>
                                            <i class="bi-calendar4 custom-icon me-1"></i>
                                            <?= htmlspecialchars($n['news_date']) ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="news-block-title mb-2">
                                    <h4><a href="news-detail.php?id=<?= (int)$n['id'] ?>" class="news-block-title-link"><?= htmlspecialchars($n['title']) ?></a></h4>
                                </div>

                                <div class="news-block-body">
                                    <p><?= htmlspecialchars($n['excerpt']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!$allNews): ?>
                        <p class="text-muted">No news posted yet — check back soon.</p>
                        <?php endif; ?>
                    </div>

                    <div class="col-lg-4 col-12 mx-auto mt-4 mt-lg-0">
                        <form class="custom-form search-form" action="#" method="post" role="form">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search">

                            <button type="submit" class="form-control">
                                <i class="bi-search"></i>
                            </button>
                        </form>

                        <h5 class="mt-5 mb-3">Recent news</h5>

                        <?php foreach ($recentNews as $n): ?>
                        <div class="news-block news-block-two-col d-flex mt-4">
                            <div class="news-block-two-col-image-wrap">
                                <a href="news-detail.php?id=<?= (int)$n['id'] ?>">
                                    <img src="<?= htmlspecialchars($n['image']) ?>"
                                        class="news-image img-fluid" alt="<?= htmlspecialchars($n['title']) ?>">
                                </a>
                            </div>

                            <div class="news-block-two-col-info">
                                <div class="news-block-title mb-2">
                                    <h6><a href="news-detail.php?id=<?= (int)$n['id'] ?>" class="news-block-title-link"><?= htmlspecialchars($n['title']) ?></a>
                                    </h6>
                                </div>

                                <div class="news-block-date">
                                    <p>
                                        <i class="bi-calendar4 custom-icon me-1"></i>
                                        <?= htmlspecialchars($n['news_date']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div class="category-block d-flex flex-column">
                            <h5 class="mb-3">Categories</h5>

                            <a href="education.html" class="category-block-link">Education<span class="badge">5</span></a>

                            <a href="health.html" class="category-block-link">Health<span class="badge">3</span></a>

                            <a href="women-empowerment.html" class="category-block-link">Women Empowerment<span class="badge">4</span></a>

                            <a href="agriculture.html" class="category-block-link">Agriculture<span class="badge">4</span></a>

                            <a href="resources.php" class="category-block-link">Annual Reports<span class="badge">1</span></a>
                        </div>

                        <div class="tags-block">
                            <h5 class="mb-3">Tags</h5>

                            <a href="#" class="tags-block-link">Education</a>

                            <a href="#" class="tags-block-link">Health</a>

                            <a href="#" class="tags-block-link">Women</a>

                            <a href="#" class="tags-block-link">Tanzania</a>

                            <a href="#" class="tags-block-link">Agriculture</a>

                            <a href="#" class="tags-block-link">Dodoma</a>

                            <a href="#" class="tags-block-link">CMSR-TZ</a>
                        </div>

                        <form class="custom-form subscribe-form" action="#" method="post" role="form">
                            <h5 class="mb-4">Stay Updated</h5>

                            <input type="email" name="subscribe-email" id="subscribe-email" pattern="[^ @]*@[^ @]*"
                                class="form-control" placeholder="Email Address" required>

                            <div class="col-lg-12 col-12">
                                <button type="submit" class="form-control">Subscribe</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Search CMSR-TZ</h5>
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
                        <li class="footer-menu-item"><a href="resources.php" class="footer-menu-link">Resources</a></li>
                        <li class="footer-menu-item"><a href="news.php" class="footer-menu-link">Latest</a></li>
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
    <script src="js/custom.js"></script>
</body>
</html>