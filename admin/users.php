<?php
/** Staff account management (administrators only). */

require_once __DIR__ . '/includes/auth.php';

require_admin();

$action = (string) get('action', 'list');
$id     = (int) get('id', 0);

if (is_post()) {
    csrf_check();
    $postAction = (string) post('action');

    // ---- delete -----------------------------------------------------
    if ($postAction === 'delete') {
        $deleteId = (int) post('id');
        if ($deleteId === (int) current_user()['id']) {
            flash('You cannot delete your own account.', 'danger');
        } elseif ((int) fetch_value("SELECT COUNT(*) FROM users WHERE role = 'Admin' AND status = 'active'", [], 0) <= 1
                  && fetch_value('SELECT role FROM users WHERE id = ?', [$deleteId]) === 'Admin') {
            flash('There must always be at least one active administrator.', 'danger');
        } else {
            db_delete('users', $deleteId);
            log_activity('delete', 'users', $deleteId, 'Staff account deleted');
            flash('Account deleted.');
        }
        redirect('admin/users.php');
    }

    // ---- create / update ---------------------------------------------
    if ($postAction === 'save') {
        $saveId   = (int) post('id');
        $username = strtolower(preg_replace('/[^A-Za-z0-9_.-]/', '', (string) post('username')) ?? '');
        $password = (string) post('password');
        $confirm  = (string) post('password_confirm');

        $data = [
            'username'  => $username,
            'full_name' => (string) post('full_name'),
            'email'     => (string) post('email'),
            'phone'     => (string) post('phone'),
            'role'      => in_array(post('role'), ['Admin', 'Editor', 'Viewer'], true) ? (string) post('role') : 'Editor',
            'status'    => post('status') === 'disabled' ? 'disabled' : 'active',
        ];

        $errors = [];
        if (strlen($username) < 3) {
            $errors[] = 'The username must be at least 3 characters (letters, numbers, dot, dash or underscore).';
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid e-mail address.';
        }
        $clash = fetch_one('SELECT id FROM users WHERE username = ? AND id <> ?', [$username, $saveId]);
        if ($clash) {
            $errors[] = 'That username is already taken.';
        }
        if (!$saveId && strlen($password) < 8) {
            $errors[] = 'The password must be at least 8 characters.';
        }
        if ($password !== '' && $password !== $confirm) {
            $errors[] = 'The two passwords do not match.';
        }
        if ($password !== '' && strlen($password) < 8) {
            $errors[] = 'The password must be at least 8 characters.';
        }
        // Never let the last administrator lock everyone out.
        if ($saveId && ($data['role'] !== 'Admin' || $data['status'] !== 'active')) {
            $wasAdmin = fetch_value('SELECT role FROM users WHERE id = ?', [$saveId]) === 'Admin';
            $activeAdmins = (int) fetch_value("SELECT COUNT(*) FROM users WHERE role = 'Admin' AND status = 'active'", [], 0);
            if ($wasAdmin && $activeAdmins <= 1) {
                $errors[] = 'There must always be at least one active administrator.';
            }
        }

        if ($errors) {
            foreach ($errors as $error) {
                flash($error, 'danger');
            }
            redirect('admin/users.php?action=' . ($saveId ? 'edit&id=' . $saveId : 'new'));
        }

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($saveId) {
            db_update('users', $data, $saveId);
            log_activity('update', 'users', $saveId, 'Account ' . $username . ' updated');
            flash('Account updated.');
        } else {
            $saveId = db_insert('users', $data);
            log_activity('create', 'users', $saveId, 'Account ' . $username . ' created');
            flash('Account created.');
        }
        redirect('admin/users.php');
    }
}

$adminActive = 'users';

// =====================================================================
// Form
// =====================================================================
if ($action === 'new' || $action === 'edit') {
    $record = [];
    if ($action === 'edit') {
        $record = fetch_one('SELECT * FROM users WHERE id = ?', [$id]) ?: [];
        if (!$record) {
            flash('That account no longer exists.', 'warning');
            redirect('admin/users.php');
        }
    }

    $adminTitle = $action === 'edit' ? 'Edit staff account' : 'New staff account';
    require __DIR__ . '/includes/header.php';
    ?>

    <a href="<?= url('admin/users.php') ?>" class="btn btn-sm btn-outline-cmsr mb-3">
        <i class="bi-arrow-left me-1"></i>Back to accounts
    </a>

    <form method="post" action="<?= url('admin/users.php') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) ($record['id'] ?? 0) ?>">

        <div class="admin-card">
            <div class="admin-card-header">
                <i class="bi-person-lines-fill" style="font-size:18px;color:var(--a-primary);"></i>
                <h2><?= h($adminTitle) ?></h2>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="username">Username <span style="color:#d9534f">*</span></label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= h($record['username'] ?? '') ?>" required>
                        <div class="form-hint">Letters, numbers, dot, dash or underscore.</div>
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="full_name">Full name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?= h($record['full_name'] ?? '') ?>">
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= h($record['email'] ?? '') ?>">
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="phone">Telephone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="<?= h($record['phone'] ?? '') ?>">
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="role">Role</label>
                        <select class="form-select" id="role" name="role">
                            <?php foreach (['Admin' => 'Admin — full access including accounts and settings',
                                            'Editor' => 'Editor — may add and change content',
                                            'Viewer' => 'Viewer — read-only'] as $value => $text): ?>
                                <option value="<?= h($value) ?>" <?= ($record['role'] ?? 'Editor') === $value ? 'selected' : '' ?>><?= h($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($record['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="disabled" <?= ($record['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                        </select>
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="password">Password<?= $action === 'new' ? ' <span style="color:#d9534f">*</span>' : '' ?></label>
                        <input type="password" class="form-control" id="password" name="password"
                               autocomplete="new-password" <?= $action === 'new' ? 'required' : '' ?>>
                        <div class="form-hint"><?= $action === 'edit' ? 'Leave empty to keep the current password.' : 'At least 8 characters.' ?></div>
                    </div></div>

                    <div class="col-md-6"><div class="field-block">
                        <label class="form-label" for="password_confirm">Confirm password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                               autocomplete="new-password">
                    </div></div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mb-5">
            <button class="btn btn-cmsr"><i class="bi-check-lg me-1"></i>Save account</button>
            <a href="<?= url('admin/users.php') ?>" class="btn btn-outline-cmsr">Cancel</a>
        </div>
    </form>

    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// =====================================================================
// List
// =====================================================================
$users = fetch_all('SELECT * FROM users ORDER BY FIELD(role, "Admin", "Editor", "Viewer"), username');

$adminTitle = 'Staff accounts';
require __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <i class="bi-person-lines-fill" style="font-size:18px;color:var(--a-primary);"></i>
        <div>
            <h2>Staff accounts</h2>
            <div class="form-hint mt-1">Who may sign in to the portal, and what each of them may do.</div>
        </div>
        <a href="<?= url('admin/users.php?action=new') ?>" class="ms-auto btn btn-sm btn-cmsr">
            <i class="bi-plus-lg me-1"></i>Add account
        </a>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>User</th><th>Contact</th><th>Role</th><th>Status</th><th>Last sign-in</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="row-title"><?= h($user['full_name'] ?: $user['username']) ?></div>
                            <div class="row-sub">@<?= h($user['username']) ?></div>
                        </td>
                        <td>
                            <div class="row-sub"><?= h($user['email'] ?: '—') ?></div>
                            <div class="row-sub"><?= h($user['phone']) ?></div>
                        </td>
                        <td><span class="chip <?= $user['role'] === 'Admin' ? 'chip-on' : 'chip-info' ?>"><?= h($user['role']) ?></span></td>
                        <td><span class="chip <?= $user['status'] === 'active' ? 'chip-on' : 'chip-off' ?>"><?= h(ucfirst($user['status'])) ?></span></td>
                        <td><span class="row-sub"><?= $user['last_login'] ? h(fdate($user['last_login'], 'j M Y, H:i')) : 'never' ?></span></td>
                        <td class="text-end" style="white-space:nowrap;">
                            <a href="<?= url('admin/users.php?action=edit&id=' . (int) $user['id']) ?>"
                               class="btn btn-sm btn-outline-cmsr" title="Edit"><i class="bi-pencil"></i></a>
                            <?php if ((int) $user['id'] !== (int) current_user()['id']): ?>
                                <form method="post" class="d-inline" data-confirm="Delete the account for <?= h($user['username']) ?>?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
