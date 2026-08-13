<?php
/** Newsletter sign-up target for the sidebar form. */

require_once __DIR__ . '/includes/functions.php';

if (!is_post()) {
    redirect('news.php');
}

csrf_check();

$email = (string) post('email');
$back  = $_SERVER['HTTP_REFERER'] ?? base_url('news.php');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('Please enter a valid e-mail address.', 'danger');
    redirect($back);
}

$existing = fetch_one('SELECT id, is_active FROM subscribers WHERE email = ?', [$email]);

if ($existing) {
    if ((int) $existing['is_active'] === 0) {
        db_update('subscribers', ['is_active' => 1], (int) $existing['id']);
    }
    flash('You are already on our mailing list — thank you!');
} else {
    db_insert('subscribers', ['email' => $email, 'name' => (string) post('name')]);
    flash('Thank you for subscribing to our updates.');
}

redirect($back);
