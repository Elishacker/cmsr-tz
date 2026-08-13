<?php
/** News listing with search, category filter, pagination and a sidebar. */

require_once __DIR__ . '/includes/functions.php';

$perPage  = 6;
$page     = max(1, (int) get('page', 1));
$search   = (string) get('q');
$category = (string) get('category');

$where  = ['is_published = 1'];
$params = [];

if ($search !== '') {
    $where[] = '(title LIKE ? OR excerpt LIKE ? OR body LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like);
}
if ($category !== '') {
    $where[] = 'category = ?';
    $params[] = $category;
}
$whereSql = implode(' AND ', $where);

$total = (int) fetch_value("SELECT COUNT(*) FROM news WHERE $whereSql", $params, 0);
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);

$items = fetch_all(
    "SELECT * FROM news WHERE $whereSql ORDER BY news_date DESC, id DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage),
    $params
);

$recent     = fetch_all('SELECT id, slug, title, image, news_date FROM news WHERE is_published = 1 ORDER BY news_date DESC, id DESC LIMIT 4');
$categories = fetch_all("SELECT category, COUNT(*) AS total FROM news WHERE is_published = 1 GROUP BY category ORDER BY total DESC");
$updates    = fetch_all('SELECT * FROM updates WHERE is_active = 1 ORDER BY update_date DESC, sort_order LIMIT 5');

function news_url(array $overrides = []): string
{
    $params = array_filter(array_merge([
        'q'        => (string) get('q'),
        'category' => (string) get('category'),
        'page'     => (string) get('page'),
    ], $overrides), fn($v) => $v !== '' && $v !== '1');
    return url('news.php' . ($params ? '?' . http_build_query($params) : ''));
}

$pageTitle       = 'Latest News';
$pageDescription = 'News, updates and announcements from CMSR-TZ projects and programmes.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Latest News & Updates';
$heroSubtitle = 'What is happening across our projects and programmes';
$heroImage    = 'photos/School Program/IMG-20230309-WA0021.jpg';
$heroCrumbs   = ['Latest' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">
        <div class="row">

            <!-- Articles -->
            <div class="col-lg-8 col-12">
                <?php if ($search !== '' || $category !== ''): ?>
                    <p class="mb-4">
                        <?= $total ?> article<?= $total === 1 ? '' : 's' ?>
                        <?= $search !== '' ? 'matching “' . h($search) . '”' : '' ?>
                        <?= $category !== '' ? 'in ' . h($category) : '' ?>
                        &mdash; <a href="<?= url('news.php') ?>" style="color:var(--primary-color);">clear filters</a>
                    </p>
                <?php endif; ?>

                <?php if (!$items): ?>
                    <div class="text-center py-5">
                        <p class="mb-3">No articles found.</p>
                        <a href="<?= url('news.php') ?>" class="custom-btn btn">Show all news</a>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($items as $item): ?>
                        <div class="col-md-6 col-12 mb-4">
                            <div class="news-card">
                                <a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>">
                                    <img src="<?= h(img($item['image'], 700)) ?>" class="news-card-image" alt="<?= h($item['title']) ?>">
                                </a>
                                <div class="news-card-body">
                                    <span class="news-date">
                                        <i class="bi-calendar4 me-1"></i><?= h(fdate($item['news_date'])) ?>
                                        <?php if ($item['category'] !== ''): ?>
                                            &nbsp;·&nbsp;<?= h($item['category']) ?>
                                        <?php endif; ?>
                                    </span>
                                    <h5><a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>"><?= h($item['title']) ?></a></h5>
                                    <p><?= h(excerpt($item['excerpt'] ?: $item['body'], 140)) ?></p>
                                    <a href="<?= url('news-detail.php?slug=' . $item['slug']) ?>" class="mt-auto"
                                       style="color:var(--primary-color);font-weight:600;font-size:14px;text-decoration:none;">
                                        Read more <i class="bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($pages > 1): ?>
                    <nav aria-label="News pages" class="mt-4">
                        <ul class="pagination">
                            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= news_url(['page' => (string) ($page - 1)]) ?>">Previous</a>
                            </li>
                            <?php for ($i = 1; $i <= $pages; $i++): ?>
                                <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                                    <a class="page-link" href="<?= news_url(['page' => (string) $i]) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= news_url(['page' => (string) ($page + 1)]) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-12 mt-5 mt-lg-0">
                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <form class="custom-form search-form d-flex" action="<?= url('news.php') ?>" method="get" role="search">
                        <input class="form-control" type="search" name="q" value="<?= h($search) ?>" placeholder="Search news" aria-label="Search">
                        <button type="submit" class="form-control" style="max-width:60px;"><i class="bi-search"></i></button>
                    </form>
                </div>

                <?php if ($recent): ?>
                    <div class="sidebar-block" style="background:var(--section-bg-color);">
                        <h5>Recent news</h5>
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

                <?php if ($categories): ?>
                    <div class="sidebar-block" style="background:var(--section-bg-color);">
                        <h5>Categories</h5>
                        <?php foreach ($categories as $cat): ?>
                            <a href="<?= news_url(['category' => $cat['category'], 'page' => '']) ?>" class="category-block-link d-flex justify-content-between align-items-center mb-2"
                               style="color:var(--secondary-color);font-size:15px;text-decoration:none;">
                                <?= h($cat['category']) ?>
                                <span class="badge" style="background:var(--primary-color);"><?= (int) $cat['total'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($updates): ?>
                    <div class="sidebar-block" style="background:var(--section-bg-color);">
                        <h5>In brief</h5>
                        <?php foreach ($updates as $u): ?>
                            <div class="mb-3">
                                <div class="news-date" style="font-size:12px;"><?= h(fdate($u['update_date'], 'j M Y')) ?></div>
                                <strong style="font-size:15px;"><?= h($u['title']) ?></strong>
                                <p class="mb-0" style="font-size:14px;line-height:1.6;"><?= h(excerpt($u['body'], 110)) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Newsletter</h5>
                    <p style="font-size:14px;">Get our project updates by e-mail.</p>
                    <form class="custom-form subscribe-form" action="<?= url('subscribe.php') ?>" method="post" role="form">
                        <?= csrf_field() ?>
                        <input type="email" name="email" class="form-control" placeholder="Email address" required>
                        <button type="submit" class="form-control">Subscribe</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
