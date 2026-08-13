<?php
/** The single "about" block on the homepage. */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/fields.php';

require_login();

$fields = [
    'eyebrow'    => ['type' => 'text', 'label' => 'Small label above the heading', 'col' => 6],
    'heading'    => ['type' => 'text', 'label' => 'Heading', 'col' => 6, 'required' => true],
    'image'      => ['type' => 'image', 'label' => 'Picture'],
    'paragraphs' => ['type' => 'textarea', 'label' => 'Paragraphs', 'rows' => 9, 'hint' => 'One paragraph per line.'],
    'btn_text'   => ['type' => 'text', 'label' => 'Button label', 'col' => 6],
    'btn_link'   => ['type' => 'text', 'label' => 'Button link', 'col' => 6, 'hint' => 'e.g. about.php'],
];

if (is_post()) {
    require_edit();
    csrf_check();

    $existing = fetch_one('SELECT * FROM overview WHERE id = 1') ?: [];
    [$values, $errors] = read_fields($fields, $existing);

    if ($errors) {
        foreach ($errors as $error) {
            flash($error, 'danger');
        }
    } else {
        if ($existing) {
            db_update('overview', $values, 1);
        } else {
            db_insert('overview', array_merge(['id' => 1], $values));
        }
        log_activity('update', 'overview', 1, 'Homepage about block updated');
        flash('Homepage about block saved.');
    }
    redirect('admin/overview.php');
}

$record = fetch_one('SELECT * FROM overview WHERE id = 1') ?: [];

$adminTitle  = 'Homepage about block';
$adminActive = 'overview';
require __DIR__ . '/includes/header.php';
?>

<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <i class="bi-card-text" style="font-size:18px;color:var(--a-primary);"></i>
            <div>
                <h2>Homepage about block</h2>
                <div class="form-hint mt-1">The picture and text shown just below the sector shortcuts on the homepage.</div>
            </div>
            <a href="<?= url('index.php#section_2') ?>" target="_blank" class="ms-auto btn btn-sm btn-outline-cmsr">
                <i class="bi-box-arrow-up-right me-1"></i>View on the website
            </a>
        </div>
        <div class="admin-card-body">
            <div class="row">
                <?php foreach ($fields as $name => $field): ?>
                    <?= render_field($name, $field, $record[$name] ?? field_default($field)) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if (can_edit()): ?>
        <button class="btn btn-cmsr mb-5"><i class="bi-check-lg me-1"></i>Save changes</button>
    <?php endif; ?>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
