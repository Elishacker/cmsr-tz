<?php
/** The signed-in user's own details and password. */

require_once __DIR__ . '/includes/auth.php';

require_login();

$userId = (int) current_user()['id'];

if (is_post()) {
    csrf_check();

    if (post('action') === 'details') {
        $data = [
            'full_name' => (string) post('full_name'),
            'email'     => (string) post('email'),
            'phone'     => (string) post('phone'),
        ];
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('Please enter a valid e-mail address.', 'danger');
        } else {
            db_update('users', $data, $userId);
            $_SESSION['user']['full_name'] = $data['full_name'] !== '' ? $data['full_name'] : current_user()['username'];
            $_SESSION['user']['email'] = $data['email'];
            log_activity('update', 'users', $userId, 'Own profile updated');
            flash('Your details have been saved.');
        }
        redirect('admin/profile.php');
    }

    if (post('action') === 'password') {
        $current = (string) post('current_password');
        $new     = (string) post('new_password');
        $confirm = (string) post('confirm_password');

        $row = fetch_one('SELECT password_hash FROM users WHERE id = ?', [$userId]);

        if (!$row || !password_verify($current, $row['password_hash'])) {
            flash('Your current password is not correct.', 'danger');
        } elseif (strlen($new) < 8) {
            flash('The new password must be at least 8 characters.', 'danger');
        } elseif ($new !== $confirm) {
            flash('The two new passwords do not match.', 'danger');
        } else {
            db_update('users', ['password_hash' => password_hash($new, PASSWORD_DEFAULT)], $userId);
            log_activity('update', 'users', $userId, 'Password changed');
            flash('Your password has been changed.');
        }
        redirect('admin/profile.php');
    }
}

$user     = fetch_one('SELECT * FROM users WHERE id = ?', [$userId]) ?: [];
$myLog    = fetch_all('SELECT * FROM activity_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 10', [$userId]);

$adminTitle  = 'My profile';
$adminActive = 'profile';
require __DIR__ . '/includes/header.php';
?>

<div class="row g-3">
    <div class="col-lg-6 col-12">
        <div class="admin-card h-100">
            <div class="admin-card-header"><h2>My details</h2></div>
            <div class="admin-card-body">
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="details">

                    <div class="field-block">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= h($user['username'] ?? '') ?>" readonly>
                        <div class="form-hint">Only an administrator can change your username or role
                            (currently <strong><?= h($user['role'] ?? '') ?></strong>).</div>
                    </div>

                    <div class="field-block">
                        <label class="form-label" for="full_name">Full name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= h($user['full_name'] ?? '') ?>">
                    </div>

                    <div class="field-block">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= h($user['email'] ?? '') ?>">
                    </div>

                    <div class="field-block">
                        <label class="form-label" for="phone">Telephone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= h($user['phone'] ?? '') ?>">
                    </div>

                    <button class="btn btn-cmsr"><i class="bi-check-lg me-1"></i>Save details</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-12">
        <div class="admin-card h-100">
            <div class="admin-card-header"><h2>Change password</h2></div>
            <div class="admin-card-body">
                <form method="post" autocomplete="off">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="password">

                    <div class="field-block">
                        <label class="form-label" for="current_password">Current password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="field-block">
                        <label class="form-label" for="new_password">New password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-hint">At least 8 characters.</div>
                    </div>

                    <div class="field-block">
                        <label class="form-label" for="confirm_password">Confirm new password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button class="btn btn-cmsr"><i class="bi-shield-lock me-1"></i>Change password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header"><h2>My recent activity</h2></div>
            <div class="table-responsive">
                <table class="admin-table">
                    <tbody>
                        <?php foreach ($myLog as $entry): ?>
                            <tr>
                                <td>
                                    <div class="row-title"><?= h($entry['details'] ?: $entry['action']) ?></div>
                                    <div class="row-sub"><?= h($entry['entity']) ?> &middot; <?= h(fdate($entry['created_at'], 'j M Y, H:i')) ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$myLog): ?>
                            <tr><td class="text-center py-4" style="color:var(--a-muted);">Nothing recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
