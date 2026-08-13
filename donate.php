<?php
/** Support Our Work — how to give, and a form that reaches the office. */

require_once __DIR__ . '/includes/functions.php';

$page   = get_page('donate');
$errors = [];
$sent   = false;

$values = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

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
    if (post('website') !== '') {
        $errors[] = 'Your message could not be sent.';
    }

    if (!$errors) {
        db_insert('messages', [
            'type'       => 'donation',
            'name'       => $values['name'],
            'email'      => $values['email'],
            'phone'      => $values['phone'],
            'subject'    => $values['subject'] !== '' ? $values['subject'] : 'Support enquiry',
            'message'    => $values['message'],
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
        $sent = true;
        $values = array_fill_keys(array_keys($values), '');
    }
}

$sectors = all_sectors();

$pageTitle       = 'Support Our Work';
$pageDescription = $page['meta_description'];

require __DIR__ . '/includes/header.php';

$heroTitle    = $page['title'];
$heroSubtitle = $page['subtitle'];
$heroImage    = $page['hero_image'];
$heroCrumbs   = ['Support Us' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-12">
                <span class="section-eyebrow">Why it matters</span>
                <h2 class="mb-4 section-title-underline">Your support, at work</h2>
                <div class="prose"><?= safe_html($page['body']) ?></div>
            </div>

            <div class="col-lg-5 col-12 mt-5 mt-lg-0">
                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Giving details</h5>
                    <ul class="list-unstyled mb-0" style="font-size:15px;">
                        <li class="d-flex mb-3"><i class="bi-bank text-primary me-2 mt-1"></i>
                            <span><strong>Bank</strong><br><?= h(setting('donate_bank_name')) ?></span></li>
                        <li class="d-flex mb-3"><i class="bi-person-badge text-primary me-2 mt-1"></i>
                            <span><strong>Account name</strong><br><?= h(setting('donate_account_name')) ?></span></li>
                        <li class="d-flex mb-3"><i class="bi-credit-card text-primary me-2 mt-1"></i>
                            <span><strong>Account number</strong><br><?= h(setting('donate_account_no')) ?></span></li>
                        <li class="d-flex mb-0"><i class="bi-globe text-primary me-2 mt-1"></i>
                            <span><strong>SWIFT</strong><br><?= h(setting('donate_swift')) ?></span></li>
                    </ul>
                </div>

                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Transparency</h5>
                    <p class="mb-3" style="font-size:15px;">Every project is reported to the Board, our donors and the relevant
                        Government authorities. Our annual reports are available on request.</p>
                    <a href="<?= url('resources.php') ?>" class="custom-btn btn btn-sm w-100">See our reports</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHERE SUPPORT GOES -->
<section class="section-padding section-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 mx-auto text-center mb-5">
                <span class="section-eyebrow">Choose a focus</span>
                <h2 class="mb-3 section-title-underline">Where you can direct your support</h2>
            </div>

            <?php foreach ($sectors as $sector): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="value-tile h-100 d-flex flex-column">
                        <i class="<?= h($sector['icon']) ?>"></i>
                        <h5><?= h($sector['name']) ?></h5>
                        <p><?= h(excerpt($sector['summary'], 150)) ?></p>
                        <a href="<?= url('sector.php?slug=' . $sector['slug']) ?>" class="mt-auto pt-3"
                           style="color:var(--primary-color);font-weight:600;font-size:14px;text-decoration:none;">
                            About this sector <i class="bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ENQUIRY FORM -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                <form class="custom-form contact-form" action="<?= url('donate.php') ?>" method="post" role="form">
                    <?= csrf_field() ?>
                    <h2>Talk to us about giving</h2>
                    <p class="mb-4">Tell us what you would like to support and our office will come back to you with the
                        current bank details and any documentation your organisation requires.</p>

                    <?php if ($sent): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi-check-circle me-2"></i>Thank you — your enquiry has reached our office.
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
                        <div class="col-lg-6 col-12">
                            <input type="text" name="name" class="form-control" placeholder="Your name or organisation"
                                   value="<?= h($values['name']) ?>" required>
                        </div>
                        <div class="col-lg-6 col-12">
                            <input type="email" name="email" class="form-control" placeholder="E-mail address"
                                   value="<?= h($values['email']) ?>" required>
                        </div>
                        <div class="col-lg-6 col-12">
                            <input type="text" name="phone" class="form-control" placeholder="Telephone (optional)"
                                   value="<?= h($values['phone']) ?>">
                        </div>
                        <div class="col-lg-6 col-12">
                            <select name="subject" class="form-control">
                                <option value="">What would you like to support?</option>
                                <option value="Sponsor a student">Sponsor a student (Shule Programme)</option>
                                <?php foreach ($sectors as $sector): ?>
                                    <option value="<?= h($sector['name']) ?>"><?= h($sector['name']) ?></option>
                                <?php endforeach; ?>
                                <option value="General support">General support</option>
                                <option value="Partnership">Partnership / proposal</option>
                            </select>
                        </div>
                    </div>

                    <textarea name="message" rows="4" class="form-control" placeholder="Anything you would like to tell us (optional)"><?= h($values['message']) ?></textarea>

                    <input type="text" name="website" tabindex="-1" autocomplete="off"
                           style="position:absolute;left:-9999px;" aria-hidden="true">

                    <button type="submit" class="form-control">Send enquiry</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
