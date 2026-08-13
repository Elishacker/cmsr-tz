<?php
/** Single news article. */

require_once __DIR__ . '/includes/functions.php';

$slug = (string) get('slug');
$article = fetch_one('SELECT * FROM news WHERE slug = ? AND is_published = 1', [$slug]);

if (!$article) {
    http_response_code(404);
    $pageTitle = 'Article not found';
    require __DIR__ . '/includes/header.php';
    echo '<section class="section-padding"><div class="container text-center py-5">'
        . '<h2 class="mb-3">Article not found</h2>'
        . '<p class="mb-4">This article may have been moved or removed.</p>'
        . '<a href="' . url('news.php') . '" class="custom-btn btn">All news</a>'
        . '</div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Count the read once per session.
$seen = $_SESSION['news_seen'] ?? [];
if (!in_array((int) $article['id'], $seen, true)) {
    q('UPDATE news SET views = views + 1 WHERE id = ?', [$article['id']]);
    $seen[] = (int) $article['id'];
    $_SESSION['news_seen'] = $seen;
}

$recent = fetch_all('SELECT id, slug, title, image, news_date FROM news WHERE is_published = 1 AND id <> ? ORDER BY news_date DESC LIMIT 4', [$article['id']]);
$prev   = fetch_one('SELECT slug, title FROM news WHERE is_published = 1 AND news_date < ? ORDER BY news_date DESC LIMIT 1', [$article['news_date']]);
$next   = fetch_one('SELECT slug, title FROM news WHERE is_published = 1 AND news_date > ? ORDER BY news_date ASC LIMIT 1', [$article['news_date']]);
$tags   = array_values(array_filter(array_map('trim', explode(',', (string) $article['tags']))));

$pageTitle       = $article['title'];
$pageDescription = excerpt($article['excerpt'] ?: $article['body'], 200);

require __DIR__ . '/includes/header.php';

$heroTitle    = $article['title'];
$heroSubtitle = fdate($article['news_date']) . ' · ' . $article['category'];
$heroImage    = $article['image'];
$heroCrumbs   = ['Latest' => 'news.php', excerpt($article['title'], 50) => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">

            <div class="col-lg-8 col-12">
                <img src="<?= h(img($article['image'], 1200)) ?>" class="img-fluid rounded shadow-lg mb-4" alt="<?= h($article['title']) ?>">

                <div class="d-flex flex-wrap align-items-center mb-4" style="font-size:14px;color:var(--p-color);gap:24px;">
                    <span><i class="bi-calendar4 text-primary me-1"></i><?= h(fdate($article['news_date'])) ?></span>
                    <span><i class="bi-person text-primary me-1"></i><?= h($article['author']) ?></span>
                    <span><i class="bi-folder text-primary me-1"></i><?= h($article['category']) ?></span>
                    <span><i class="bi-eye text-primary me-1"></i><?= (int) $article['views'] ?> views</span>
                </div>

                <?php if ($article['excerpt']): ?>
                    <p class="lead mb-4" style="font-size:18px;line-height:1.7;color:var(--secondary-color);"><?= h($article['excerpt']) ?></p>
                <?php endif; ?>

                <div class="prose"><?= safe_html($article['body']) ?></div>

                <?php if ($tags): ?>
                    <div class="tags-block mt-5">
                        <h5 class="mb-3">Tags</h5>
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?= url('news.php?q=' . rawurlencode($tag)) ?>" class="tags-block-link"><?= h($tag) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row mt-5 pt-4 border-top">
                    <div class="col-6">
                        <?php if ($prev): ?>
                            <a href="<?= url('news-detail.php?slug=' . $prev['slug']) ?>" style="color:var(--secondary-color);text-decoration:none;font-size:14px;">
                                <i class="bi-arrow-left me-1 text-primary"></i><?= h(excerpt($prev['title'], 45)) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-6 text-end">
                        <?php if ($next): ?>
                            <a href="<?= url('news-detail.php?slug=' . $next['slug']) ?>" style="color:var(--secondary-color);text-decoration:none;font-size:14px;">
                                <?= h(excerpt($next['title'], 45)) ?><i class="bi-arrow-right ms-1 text-primary"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                <?php if ($recent): ?>
                    <div class="sidebar-block" style="background:var(--section-bg-color);">
                        <h5>More news</h5>
                        <?php foreach ($recent as $item): ?>
                            <div class="sidebar-news-item">
                                <a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>">
                                    <img src="<?= h(img($item['image'], 240)) ?>" alt="<?= h($item['title']) ?>">
                                </a>
                                <div>
                                    <a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>"><?= h(excerpt($item['title'], 60)) ?></a>
                                    <div class="news-date mt-1" style="font-size:12px;"><?= h(fdate($item['news_date'], 'j M Y')) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Support our work</h5>
                    <p style="font-size:15px;">Every contribution goes directly into community development projects in rural Tanzania.</p>
                    <a href="<?= url('donate.php') ?>" class="custom-btn btn w-100">Support us</a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
