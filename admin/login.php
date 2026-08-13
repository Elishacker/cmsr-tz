<?php
/** Staff portal sign-in. */

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('admin/index.php');
}

$error    = '';
$username = '';

if (is_post()) {
    csrf_check();
    $username = (string) post('username');
    $password = (string) post('password');

    if (login_attempts_exceeded()) {
        $error = 'Too many failed attempts. Please wait 15 minutes and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $error = login_user($username, $password) ?? '';
        if ($error === '') {
            clear_failed_logins();
            $target = $_SESSION['redirect_after_login'] ?? '';
            unset($_SESSION['redirect_after_login']);
            flash('Welcome back, ' . current_user()['full_name'] . '.');
            redirect($target !== '' ? $target : 'admin/index.php');
        }
        record_failed_login();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Staff Login | CMSR-TZ</title>
    <link rel="icon" href="<?= url('assets/images/logo.png') ?>">
    <link href="<?= url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= url('assets/css/templatemo-kind-heart-charity.css') ?>" rel="stylesheet">
    <link href="<?= url('admin/assets/admin.css') ?>" rel="stylesheet">
</head>

<body class="admin-body">
<div class="login-page">
    <div class="login-card">
        <img src="<?= url('assets/images/logo.png') ?>" class="logo" alt="CMSR-TZ">
        <h1>CMSR-TZ Staff Portal</h1>
        <p class="sub">Sign in to manage the website content</p>

        <?php foreach (take_flashes() as $msg): ?>
            <div class="alert alert-<?= h($msg['type'] === 'error' ? 'danger' : $msg['type']) ?> py-2" role="alert">
                <?= h($msg['message']) ?>
            </div>
        <?php endforeach; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger py-2" role="alert">
                <i class="bi-exclamation-triangle me-1"></i><?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= url('admin/login.php') ?>" autocomplete="off">
            <?= csrf_field() ?>

            <div class="field-block">
                <label class="form-label" for="username">Username or e-mail</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username"
                           value="<?= h($username) ?>" required autofocus>
                </div>
            </div>

            <div class="field-block">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <button class="btn btn-outline-cmsr" type="button" id="togglePassword" aria-label="Show password">
                        <i class="bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-cmsr w-100 mt-2 py-2">
                <i class="bi-box-arrow-in-right me-1"></i>Sign in
            </button>
        </form>

        <p class="text-center mt-4 mb-0" style="font-size:13px;color:var(--a-muted);">
            <a href="<?= url('index.php') ?>" style="color:var(--a-slate);text-decoration:none;">
                <i class="bi-arrow-left me-1"></i>Back to the website
            </a>
        </p>
    </div>
</div>

<script src="<?= url('assets/js/bootstrap.min.js') ?>"></script>
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    var input = document.getElementById('password');
    var shown = input.type === 'text';
    input.type = shown ? 'password' : 'text';
    this.innerHTML = shown ? '<i class="bi-eye"></i>' : '<i class="bi-eye-slash"></i>';
});
</script>
</body>
</html>
