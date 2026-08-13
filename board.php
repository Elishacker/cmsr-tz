<?php
/** Governance — Board of Directors and the management team. */

require_once __DIR__ . '/includes/functions.php';

$board = fetch_all('SELECT * FROM board_members WHERE is_active = 1 ORDER BY sort_order, id');
$team  = fetch_all('SELECT * FROM staff WHERE is_active = 1 ORDER BY sort_order, id');

/**
 * Initials fallback when a person has no photograph on file.
 *
 * The honorific is dropped together with its full stop — otherwise
 * "Ms. Josephine Pamilla" yields ".P" instead of "JP".
 */
function initials(string $name): string
{
    $clean = preg_replace('/\b(Mr|Mrs|Ms|Miss|Dr|Prof|Rev|Fr|Eng|Hon)\b\.?/i', '', $name) ?? $name;
    // Keep letters only, so stray punctuation can never become an initial.
    $parts = preg_split('/[^\p{L}]+/u', trim($clean), -1, PREG_SPLIT_NO_EMPTY) ?: [];

    if (!$parts) {
        return 'CT';
    }
    $first = mb_substr($parts[0], 0, 1);
    $last  = count($parts) > 1 ? mb_substr((string) end($parts), 0, 1) : '';

    return mb_strtoupper($first . $last);
}

$pageTitle       = 'Board & Leadership';
$pageDescription = 'The Board of CMSR-TZ and the management team responsible for governance and delivery of our community development projects.';

require __DIR__ . '/includes/header.php';

$heroTitle    = 'Board & Leadership';
$heroSubtitle = 'The governing body and management team of CMSR-TZ';
$heroImage    = 'photos/Health/20250417_145456.jpg';
$heroCrumbs   = ['About Us' => 'about.php', 'Board & Leadership' => ''];
require __DIR__ . '/includes/page-hero.php';
?>

<!-- ABOUT THE BOARD -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-12">
                <span class="section-eyebrow">Governance</span>
                <h2 class="mb-4 section-title-underline">The CMSR-TZ Board</h2>
                <div class="prose">
                    <p>The CMSR Board has six members and serves as the organisation's governing body. Members hold office for
                        three years and may be re-appointed based on their active participation and performance in Board duties.
                        The Board is composed of the Chairperson, the Vice Chairperson and Board Members.</p>
                    <p>The Board members hold office with no remuneration, except for the refund of expenses sustained while
                        carrying out their duties. They meet every six months, or at any time requested by a majority of members.
                        Resolutions are passed by simple majority; in case of equality the Executive Director has a casting vote.</p>
                </div>
            </div>

            <div class="col-lg-5 col-12 mt-5 mt-lg-0">
                <div class="sidebar-block" style="background:var(--section-bg-color);">
                    <h5>Responsibilities of the Board</h5>
                    <ul class="list-unstyled mb-0" style="font-size:15px;line-height:1.7;">
                        <li class="d-flex mb-2"><i class="bi-check2 text-primary me-2 mt-1"></i>Actively participate in Board meetings, discussions and decision-making.</li>
                        <li class="d-flex mb-2"><i class="bi-check2 text-primary me-2 mt-1"></i>Approve major organisational decisions, programmes and related expenditure.</li>
                        <li class="d-flex mb-2"><i class="bi-check2 text-primary me-2 mt-1"></i>Review and approve programmes and budgets for the development of the organisation.</li>
                        <li class="d-flex mb-2"><i class="bi-check2 text-primary me-2 mt-1"></i>Support fundraising through personal and professional networks.</li>
                        <li class="d-flex mb-2"><i class="bi-check2 text-primary me-2 mt-1"></i>Advance and guide the mission of the organisation.</li>
                        <li class="d-flex mb-0"><i class="bi-check2 text-primary me-2 mt-1"></i>Oversee the documentation of agendas and minutes.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BOARD MEMBERS -->
<?php if ($board): ?>
<section class="section-padding section-bg pt-0" style="padding-top:60px !important;">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="section-eyebrow">Our people</span>
                <h2 class="mb-0 section-title-underline">Board members</h2>
            </div>
        </div>

        <!-- Centred so the part-filled last row stays balanced. -->
        <div class="row justify-content-center">

            <?php foreach ($board as $member): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="person-card">
                        <?php if ($member['photo'] !== ''): ?>
                            <img src="<?= h(img($member['photo'], 400)) ?>" class="person-photo" alt="<?= h($member['name']) ?>">
                        <?php else: ?>
                            <div class="person-initials"><?= h(initials($member['name'])) ?></div>
                        <?php endif; ?>
                        <h5><?= h($member['name']) ?></h5>
                        <p class="person-role"><?= h($member['position']) ?></p>
                        <p class="person-bio"><?= h($member['bio']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- MANAGEMENT -->
<?php if ($team): ?>
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <span class="section-eyebrow">Management</span>
                <h2 class="mb-0 section-title-underline">Our leadership team</h2>
            </div>
        </div>

        <div class="row justify-content-center">

            <?php foreach ($team as $person): ?>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="person-card">
                        <?php if ($person['photo'] !== ''): ?>
                            <img src="<?= h(img($person['photo'], 400)) ?>" class="person-photo" alt="<?= h($person['name']) ?>">
                        <?php else: ?>
                            <div class="person-initials"><?= h(initials($person['name'])) ?></div>
                        <?php endif; ?>
                        <h5><?= h($person['name']) ?></h5>
                        <p class="person-role"><?= h($person['position']) ?></p>
                        <p class="person-bio"><?= h($person['bio']) ?></p>
                        <?php if ($person['email'] !== ''): ?>
                            <a href="mailto:<?= h($person['email']) ?>" class="read-more d-inline-block mt-3" style="color:var(--primary-color);font-size:14px;text-decoration:none;">
                                <i class="bi-envelope me-1"></i><?= h($person['email']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
