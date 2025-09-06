<?php
declare(strict_types=1);

session_start();

/**
 * In production, replace this with real login logic.
 * For now we fake the logged-in user based on your header:
 * name: Michael Thompson, role: Staff
 */
if (!isset($_SESSION['user'])) {
  $_SESSION['user'] = [
    'user_id' => 1,                 // ensure a matching row exists in users table
    'name'    => 'Michael Thompson',
    'email'   => 'michael@example.com',
    'role'    => 'Staff',
    'avatar'  => 'Michael Thompson.png',
  ];
}

function current_user(): array {
  return $_SESSION['user'];
}

function require_login(): void {
  if (!isset($_SESSION['user'])) {
    http_response_code(302);
    header('Location: /login.php');
    exit;
  }
}

function is_admin(): bool {
  return (current_user()['role'] ?? '') === 'Admin';
}
