<?php
/**
 * Portal authentication and authorisation.
 *
 * Roles:
 *   Admin  — everything, including users and settings
 *   Editor — all content, no user management or settings
 *   Viewer — read-only
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/functions.php';

const SESSION_IDLE_LIMIT = 3600; // seconds

/** The signed-in user, or null. */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function user_role(): string
{
    return current_user()['role'] ?? 'Viewer';
}

function is_admin(): bool
{
    return user_role() === 'Admin';
}

/** Editors and Admins may change content; Viewers may not. */
function can_edit(): bool
{
    return in_array(user_role(), ['Admin', 'Editor'], true);
}

/** Stop the request unless somebody is signed in and still active. */
function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect('admin/login.php');
    }

    $last = $_SESSION['last_activity'] ?? time();
    if (time() - $last > SESSION_IDLE_LIMIT) {
        logout_user();
        flash('Your session timed out. Please sign in again.', 'warning');
        redirect('admin/login.php');
    }
    $_SESSION['last_activity'] = time();
}

/** Stop the request unless the user may modify content. */
function require_edit(): void
{
    require_login();
    if (!can_edit()) {
        http_response_code(403);
        flash('Your account has read-only access.', 'warning');
        redirect('admin/index.php');
    }
}

/** Stop the request unless the user is an Admin. */
function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        flash('That area is restricted to administrators.', 'warning');
        redirect('admin/index.php');
    }
}

/** Verify credentials and start a session. Returns an error string on failure. */
function login_user(string $username, string $password): ?string
{
    $user = fetch_one('SELECT * FROM users WHERE username = ? OR email = ?', [$username, $username]);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return 'Those credentials do not match our records.';
    }
    if ($user['status'] !== 'active') {
        return 'This account has been disabled. Please contact an administrator.';
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'        => (int) $user['id'],
        'username'  => $user['username'],
        'full_name' => $user['full_name'] !== '' ? $user['full_name'] : $user['username'],
        'email'     => $user['email'],
        'role'      => $user['role'],
        'avatar'    => $user['avatar'],
    ];
    $_SESSION['last_activity'] = time();

    q('UPDATE users SET last_login = NOW() WHERE id = ?', [$user['id']]);
    log_activity('login', 'users', (int) $user['id'], 'Signed in');

    return null;
}

function logout_user(): void
{
    if (is_logged_in()) {
        log_activity('logout', 'users', current_user()['id'], 'Signed out');
    }
    unset($_SESSION['user'], $_SESSION['last_activity']);
    session_regenerate_id(true);
}

/** Simple per-IP throttle on failed sign-in attempts. */
function login_attempts_exceeded(): bool
{
    $tries = $_SESSION['login_tries'] ?? ['count' => 0, 'first' => time()];
    if (time() - $tries['first'] > 900) {
        return false;
    }
    return $tries['count'] >= 8;
}

function record_failed_login(): void
{
    $tries = $_SESSION['login_tries'] ?? ['count' => 0, 'first' => time()];
    if (time() - $tries['first'] > 900) {
        $tries = ['count' => 0, 'first' => time()];
    }
    $tries['count']++;
    $_SESSION['login_tries'] = $tries;
}

function clear_failed_logins(): void
{
    unset($_SESSION['login_tries']);
}
