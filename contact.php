<?php
/** Contact page — enquiry form writes into the messages table. */

require_once __DIR__ . '/includes/functions.php';

$page   = get_page('contact');
$errors = [];
$sent   = false;

$values = [
    'name'    => '',
    'email'   => '',
    'phone'   => '',
    'subject' => (string) get('subject'),
    'message' => '',
];

if (is_post()) {
    csrf_check();

    foreach (array_keys($values) as $field) {
        $values[$field] = (string) post($field);
    }

    if ($values['name'] === '') {
        $errors[] = 'Please tell us your name.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid e-mail address.';
    }
    if (mb_strlen($values['message']) < 10) {
        $errors[] = 'Please write a message of at least 10 characters.';
    }
    // Honeypot: real visitors never fill this in.
    if (post('website') !== '') {
        $errors[] = 'Your message could not be sent.';
    }

    if (!$errors) {
        db_insert('messages', [
            'type'       => 'contact',
            'name'       => $values['name'],
            'email'      => $values['email'],
            'phone'      => $values['phone'],
            'subject'    => $values['subject'] !== '' ? $values['subject'] : 'Website enquiry',
            'message'    => $values['message'],
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
        $sent = true;
        $values = array_fill_keys(array_keys($values), '');
    }
}

$pageTitle       = 'Contact Us';
$pageDescription = $page['meta_description'];

require __DIR__ . '/includes/header.php';

$heroTitle    = $page['title'];
$heroSubtitle = $page['subtitle'];
$heroImage    = $page['hero_image'];
$heroCrumbs   = ['Contact' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="contact-tile">
                    <i class="bi-geo-alt"></i>
                    <h5>Office</h5>
                    <p><?= h(setting('contact_address')) ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="contact-tile">
                    <i class="bi-envelope"></i>
                    <h5>E-mail</h5>
                    <a href="mailto:<?= h(setting('contact_email')) ?>"><?= h(setting('contact_email')) ?></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="contact-tile">
                    <i class="bi-telephone"></i>
                    <h5>Telephone</h5>
                    <a href="tel:<?= h(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= h(setting('contact_phone')) ?></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="contact-tile">
                    <i class="bi-clock"></i>
                    <h5>Office hours</h5>
                    <p><?= h(setting('contact_office_hours')) ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5 col-12 mb-5 mb-lg-0">
                <span class="section-eyebrow">Get in touch</span>
                <h2 class="mb-4 section-title-underline">We would like to hear from you</h2>
                <div class="prose"><?= safe_html($page['body']) ?></div>

                <ul class="social-icon mt-4">
                    <li class="social-icon-item"><a href="<?= h(setting('social_facebook', '#')) ?>" class="social-icon-link bi-facebook" aria-label="Facebook"></a></li>
                    <li class="social-icon-item"><a href="<?= h(setting('social_instagram', '#')) ?>" class="social-icon-link bi-instagram" aria-label="Instagram"></a></li>
                    <li class="social-icon-item"><a href="<?= h(setting('social_youtube', '#')) ?>" class="social-icon-link bi-youtube" aria-label="YouTube"></a></li>
                    <li class="social-icon-item"><a href="<?= h(setting('social_whatsapp', '#')) ?>" class="social-icon-link bi-whatsapp" aria-label="WhatsApp"></a></li>
                </ul>

                <div class="sidebar-block mt-4" style="background:var(--section-bg-color);">
                    <h5>Registration</h5>
                    <p class="mb-0" style="font-size:15px;">
                        <?= h(setting('site_name')) ?><br>
                        Registered under the NGO Act, 2002<br>
                        Certificate No. <?= h(setting('registration_no')) ?>
                    </p>
                </div>
            </div>

            <div class="col-lg-7 col-12">
                <form class="custom-form contact-form" action="<?= url('contact.php') ?>" method="post" role="form">
                    <?= csrf_field() ?>
                    <h2>Send us a message</h2>
                    <p class="mb-4">Or e-mail us directly at
                        <a href="mailto:<?= h(setting('contact_email')) ?>"><?= h(setting('contact_email')) ?></a>
                    </p>

                    <?php if ($sent): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi-check-circle me-2"></i>Thank you — your message has reached our office. We will reply as soon as we can.
                        </div>
                    <?php endif; ?>

                    <?php if ($errors): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="name" class="form-control" placeholder="Your full name"
                                   value="<?= h($values['name']) ?>" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="email" name="email" class="form-control" placeholder="Your e-mail address"
                                   value="<?= h($values['email']) ?>" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="phone" class="form-control" placeholder="Telephone (optional)"
                                   value="<?= h($values['phone']) ?>">
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="subject" class="form-control" placeholder="Subject"
                                   value="<?= h($values['subject']) ?>">
                        </div>
                    </div>

                    <textarea name="message" rows="5" class="form-control" placeholder="How can we help you?" required><?= h($values['message']) ?></textarea>

                    <!-- honeypot -->
                    <input type="text" name="website" tabindex="-1" autocomplete="off"
                           style="position:absolute;left:-9999px;" aria-hidden="true">

                    <button type="submit" class="form-control">Send message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <iframe class="map-frame" src="<?= h(setting('contact_map_embed')) ?>" loading="lazy"
                title="CMSR-TZ office location" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
