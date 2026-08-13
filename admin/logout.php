<?php
/** End the portal session. */

require_once __DIR__ . '/includes/auth.php';

logout_user();
flash('You have been signed out.');
redirect('admin/login.php');
