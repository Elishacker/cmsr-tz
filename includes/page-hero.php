<?php
/**
 * Inner-page banner.
 *
 * Expects: $heroTitle, and optionally $heroSubtitle, $heroImage, $heroCrumbs
 * ($heroCrumbs is an array of label => url, the last entry is the current page).
 */
$heroImage  = $heroImage ?? '';
$heroCrumbs = $heroCrumbs ?? [];
?>
<section class="page-hero"<?= $heroImage !== '' ? ' style="background-image:url(\'' . h(img($heroImage, 1600)) . '\')"' : '' ?>>
    <div class="container">
        <?php if ($heroCrumbs): ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                    <?php $last = array_key_last($heroCrumbs); ?>
                    <?php foreach ($heroCrumbs as $label => $link): ?>
                        <?php if ($label === $last || $link === ''): ?>
                            <li class="breadcrumb-item active" aria-current="page"><?= h($label) ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?= url($link) ?>"><?= h($label) ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>

        <h1><?= h($heroTitle ?? '') ?></h1>
        <?php if (!empty($heroSubtitle)): ?>
            <p><?= h($heroSubtitle) ?></p>
        <?php endif; ?>
    </div>
</section>
