<?php
/** Public site footer. */
require_once __DIR__ . '/functions.php';

$footerSectors = all_sectors();
?>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">

                <!-- Brand -->
                <div class="col-lg-4 col-12">
                    <img src="<?= url(setting('site_logo', 'assets/images/logo.png')) ?>" class="logo img-fluid mb-3" alt="<?= h(setting('site_short_name')) ?>">
                    <h5 class="text-white mb-2"><?= h(setting('site_short_name')) ?></h5>
                    <p class="text-white-50 mb-3" style="font-size:14px;line-height:1.6;">
                        <?php foreach (lines(setting('footer_about')) as $line): ?>
                            <?= h($line) ?><br>
                        <?php endforeach; ?>
                    </p>
                    <ul class="social-icon">
                        <li class="social-icon-item"><a href="<?= h(setting('social_facebook', '#')) ?>" class="social-icon-link bi-facebook" aria-label="Facebook"></a></li>
                        <li class="social-icon-item"><a href="<?= h(setting('social_instagram', '#')) ?>" class="social-icon-link bi-instagram" aria-label="Instagram"></a></li>
                        <li class="social-icon-item"><a href="<?= h(setting('social_youtube', '#')) ?>" class="social-icon-link bi-youtube" aria-label="YouTube"></a></li>
                        <li class="social-icon-item"><a href="<?= h(setting('social_whatsapp', '#')) ?>" class="social-icon-link bi-whatsapp" aria-label="WhatsApp"></a></li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-4 col-6">
                    <h5 class="site-footer-title">Quick Links</h5>
                    <ul class="footer-menu">
                        <li class="footer-menu-item"><a href="<?= url('index.php') ?>" class="footer-menu-link">Home</a></li>
                        <li class="footer-menu-item"><a href="<?= url('about.php') ?>" class="footer-menu-link">About Us</a></li>
                        <li class="footer-menu-item"><a href="<?= url('what-we-do.php') ?>" class="footer-menu-link">What We Do</a></li>
                        <li class="footer-menu-item"><a href="<?= url('projects.php') ?>" class="footer-menu-link">Projects</a></li>
                        <li class="footer-menu-item"><a href="<?= url('where-we-work.php') ?>" class="footer-menu-link">Where We Work</a></li>
                    </ul>
                </div>

                <!-- Programs -->
                <div class="col-lg-2 col-md-4 col-6">
                    <h5 class="site-footer-title">Programs</h5>
                    <ul class="footer-menu">
                        <?php foreach ($footerSectors as $s): ?>
                            <li class="footer-menu-item">
                                <a href="<?= url('sector.php?slug=' . $s['slug']) ?>" class="footer-menu-link"><?= h($s['name']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-4 col-md-4 col-12">
                    <h5 class="site-footer-title">Contact</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 d-flex align-items-start">
                            <i class="bi-geo-alt text-primary me-2 mt-1"></i>
                            <span class="footer-contact-text"><?= h(setting('contact_address')) ?></span>
                        </li>
                        <li class="mb-2 d-flex align-items-start">
                            <i class="bi-envelope text-primary me-2 mt-1"></i>
                            <a href="mailto:<?= h(setting('contact_email')) ?>" class="site-footer-link" style="font-size:14px;"><?= h(setting('contact_email')) ?></a>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="bi-patch-check text-primary me-2 mt-1"></i>
                            <span class="footer-contact-text">Reg. No: <?= h(setting('registration_no')) ?></span>
                        </li>
                    </ul>
                    <a href="<?= url(setting('footer_cta_link', 'donate.php')) ?>" class="custom-btn btn btn-sm"><?= h(setting('footer_cta_text', 'Support Our Work')) ?></a>
                </div>

            </div>
        </div>

        <div class="site-footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-12">
                        <p class="copyright-text mb-0"><?= h(setting('footer_copyright')) ?></p>
                    </div>
                    <div class="col-lg-4 col-12 text-lg-end mt-2 mt-lg-0">
                        <p class="copyright-text mb-0">
                            <a href="<?= url('admin/login.php') ?>" class="staff-login-link">
                                <i class="bi-person-lock me-1"></i>Staff Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT FILES -->
    <script src="<?= url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= url('assets/js/bootstrap.min.js') ?>"></script>
    <script src="<?= url('assets/js/jquery.sticky.js') ?>"></script>
    <script src="<?= url('assets/js/click-scroll.js') ?>"></script>
    <script src="<?= url('assets/js/counter.js') ?>"></script>
    <script src="<?= url('assets/js/custom.js') ?>"></script>

</body>

</html>
