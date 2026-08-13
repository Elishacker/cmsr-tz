<?php
/** Projects index — current work as cards, the 2014-2026 record as a table. */

require_once __DIR__ . '/includes/functions.php';

$sectors      = all_sectors();
$sectorSlug   = (string) get('sector');
$statusFilter = (string) get('status');

$where  = ['p.is_active = 1'];
$params = [];

if ($sectorSlug !== '') {
    $where[] = 's.slug = ?';
    $params[] = $sectorSlug;
}
if (in_array($statusFilter, ['current', 'completed', 'upcoming'], true)) {
    $where[] = 'p.status = ?';
    $params[] = $statusFilter;
}

$projects = fetch_all(
    'SELECT p.*, s.name AS sector_name, s.slug AS sector_slug
       FROM projects p
       LEFT JOIN sectors s ON s.id = p.sector_id
      WHERE ' . implode(' AND ', $where) . '
      ORDER BY FIELD(p.status, "current", "upcoming", "completed"), p.sort_order, p.id',
    $params
);

$current   = array_values(array_filter($projects, fn($p) => $p['status'] !== 'completed'));
$completed = array_values(array_filter($projects, fn($p) => $p['status'] === 'completed'));

$activeSector = $sectorSlug !== '' ? fetch_one('SELECT * FROM sectors WHERE slug = ?', [$sectorSlug]) : null;

/** Build a filter URL keeping the other filter intact. */
function filter_url(array $overrides): string
{
    $params = array_filter(array_merge([
        'sector' => (string) get('sector'),
        'status' => (string) get('status'),
    ], $overrides), fn($v) => $v !== '');
    return url('projects.php' . ($params ? '?' . http_build_query($params) : ''));
}

$pageTitle       = 'Projects';
$pageDescription = 'Current and completed CMSR-TZ projects from 2014 to 2026 across health, water and sanitation, education, agriculture and youth empowerment.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Our Projects';
$heroSubtitle = 'Community development projects delivered with our partners across Tanzania';
$heroImage    = 'photos/Education/Far view of classrooms.jpg';
$heroCrumbs   = ['Projects' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section-padding">
    <div class="container">

        <!-- Filters -->
        <div class="filter-bar">
            <a href="<?= filter_url(['sector' => '']) ?>" class="filter-pill<?= $sectorSlug === '' ? ' active' : '' ?>">All sectors</a>
            <?php foreach ($sectors as $s): ?>
                <a href="<?= filter_url(['sector' => $s['slug']]) ?>" class="filter-pill<?= $sectorSlug === $s['slug'] ? ' active' : '' ?>">
                    <?= h($s['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="filter-bar">
            <a href="<?= filter_url(['status' => '']) ?>" class="filter-pill<?= $statusFilter === '' ? ' active' : '' ?>">All</a>
            <a href="<?= filter_url(['status' => 'current']) ?>" class="filter-pill<?= $statusFilter === 'current' ? ' active' : '' ?>">On-going</a>
            <a href="<?= filter_url(['status' => 'completed']) ?>" class="filter-pill<?= $statusFilter === 'completed' ? ' active' : '' ?>">Completed</a>
        </div>

        <?php if ($activeSector): ?>
            <div class="row mb-5">
                <div class="col-lg-9 col-12 mx-auto text-center">
                    <h2 class="mb-3"><?= h($activeSector['name']) ?></h2>
                    <p class="mb-0"><?= h($activeSector['summary']) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$projects): ?>
            <div class="text-center py-5">
                <p class="mb-3">No projects match this filter yet.</p>
                <a href="<?= url('projects.php') ?>" class="custom-btn btn">Show all projects</a>
            </div>
        <?php endif; ?>

        <!-- On-going -->
        <?php if ($current): ?>
            <div class="row">
                <div class="col-12 mb-4">
                    <span class="section-eyebrow">Active</span>
                    <h3 class="mb-0">On-going projects &amp; programmes</h3>
                </div>

                <?php foreach ($current as $project): ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                        <div class="project-card">
                            <div class="project-card-image-wrap">
                                <a href="<?= url('project.php?slug=' . $project['slug']) ?>">
                                    <img src="<?= h(img($project['image'], 700)) ?>" class="project-card-image" alt="<?= h($project['title']) ?>">
                                </a>
                                <span class="project-badge">On-going</span>
                            </div>
                            <div class="project-card-body">
                                <?php if ($project['sector_name']): ?>
                                    <span class="news-date mb-2"><?= h($project['sector_name']) ?></span>
                                <?php endif; ?>
                                <h5><a href="<?= url('project.php?slug=' . $project['slug']) ?>"><?= h($project['title']) ?></a></h5>
                                <ul class="project-meta">
                                    <?php if ($project['location'] !== ''): ?><li><i class="bi-geo-alt"></i><span><?= h($project['location']) ?></span></li><?php endif; ?>
                                    <?php if ($project['donor'] !== ''): ?><li><i class="bi-people"></i><span><?= h($project['donor']) ?></span></li><?php endif; ?>
                                    <?php if ($project['duration'] !== ''): ?><li><i class="bi-calendar4"></i><span><?= h($project['duration']) ?></span></li><?php endif; ?>
                                </ul>
                                <p class="project-card-summary"><?= h(excerpt($project['summary'], 95)) ?></p>
                                <div class="mt-auto">
                                    <?php if ($project['beneficiaries_direct'] !== '' || $project['beneficiaries_indirect'] !== ''): ?>
                                        <div class="mb-2">
                                            <?php if ($project['beneficiaries_direct'] !== ''): ?>
                                                <span class="beneficiary-pill"><?= h($project['beneficiaries_direct']) ?> direct</span>
                                            <?php endif; ?>
                                            <?php if ($project['beneficiaries_indirect'] !== ''): ?>
                                                <span class="beneficiary-pill"><?= h($project['beneficiaries_indirect']) ?> indirect</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="<?= url('project.php?slug=' . $project['slug']) ?>" class="card-read-more">
                                        Read more <i class="bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Completed -->
<?php if ($completed): ?>
<section class="section-padding section-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mb-4">
                <span class="section-eyebrow">Track record</span>
                <h3 class="mb-2">Past projects, 2014 &ndash; 2026</h3>
                <p class="mb-0">Every project we have implemented in the period, with the location, the development partner and the number of people reached.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-projects align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Sector</th>
                        <th>Location</th>
                        <th>Donor / partner</th>
                        <th>Period</th>
                        <th class="text-end">Direct</th>
                        <th class="text-end">Indirect</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completed as $i => $project): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <a href="<?= url('project.php?slug=' . $project['slug']) ?>"
                                   style="color:var(--secondary-color);font-weight:600;text-decoration:none;">
                                    <?= h($project['title']) ?>
                                </a>
                            </td>
                            <td><?= h($project['sector_name'] ?: $project['category']) ?></td>
                            <td><?= h($project['location']) ?></td>
                            <td><?= h($project['donor']) ?></td>
                            <td><?= h($project['duration']) ?></td>
                            <td class="text-end"><?= h($project['beneficiaries_direct'] ?: '—') ?></td>
                            <td class="text-end"><?= h($project['beneficiaries_indirect'] ?: '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <p class="mb-0" style="font-size:14px;color:var(--p-color);">
                    <i class="bi-info-circle text-primary me-1"></i>
                    Beneficiary figures are as recorded in CMSR-TZ project documentation.
                </p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band-full">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h3>Fund the next project</h3>
                <p>We depend on proposals and partnerships. Talk to us about the work you would like to support.</p>
            </div>
            <a href="<?= url('contact.php') ?>" class="custom-btn btn">Start a conversation</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
