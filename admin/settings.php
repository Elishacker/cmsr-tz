<?php
/** Site-wide settings, grouped and editable by administrators. */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/fields.php';

require_admin();

$groupTitles = [
    'general' => ['General', 'Organisation name, logo and default description.'],
    'contact' => ['Contact details', 'Shown in the top bar, the footer and on the contact page.'],
    'social'  => ['Social links', 'Used by the icons in the header and the footer.'],
    'footer'  => ['Footer', 'The text and button at the bottom of every page.'],
    'donate'  => ['Giving details', 'Bank information shown on the Support Our Work page.'],
];

if (is_post()) {
    csrf_check();

    $rows = fetch_all('SELECT setting_key, input_type FROM settings');
    $saved = 0;

    foreach ($rows as $row) {
        $key = $row['setting_key'];
        if (!array_key_exists($key, $_POST) && !isset($_FILES[$key . '__upload'])) {
            continue;
        }

        $value = (string) post($key);

        if ($row['input_type'] === 'image') {
            try {
                $uploaded = handle_upload($key . '__upload', 'media', true);
                if ($uploaded !== null) {
                    $value = $uploaded;
                }
            } catch (RuntimeException $e) {
                flash($key . ': ' . $e->getMessage(), 'danger');
            }
        }

        q('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
        $saved++;
    }

    log_activity('update', 'settings', null, $saved . ' settings updated');
    flash('Settings saved.');
    redirect('admin/settings.php');
}

$settings = [];
foreach (fetch_all('SELECT * FROM settings ORDER BY sort_order, setting_key') as $row) {
    $settings[$row['setting_group']][] = $row;
}

$adminTitle  = 'Site settings';
$adminActive = 'settings';
require __DIR__ . '/includes/header.php';
?>

<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php foreach ($settings as $group => $rows): ?>
        <?php [$title, $description] = $groupTitles[$group] ?? [ucfirst($group), '']; ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h2><?= h($title) ?></h2>
                    <?php if ($description !== ''): ?>
                        <div class="form-hint mt-1"><?= h($description) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $field = [
                            'type'  => $row['input_type'] === 'image' ? 'image' : ($row['input_type'] === 'textarea' ? 'textarea' : $row['input_type']),
                            'label' => $row['label'] !== '' ? $row['label'] : $row['setting_key'],
                            'col'   => in_array($row['input_type'], ['textarea', 'image'], true) ? 12 : 6,
                            'rows'  => 3,
                        ];
                        echo render_field($row['setting_key'], $field, $row['setting_value']);
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <button class="btn btn-cmsr mb-5"><i class="bi-check-lg me-1"></i>Save all settings</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
