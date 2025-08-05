<?php
// init.php - include at top of pages
ini_set('display_errors', 0); // set to 1 while debugging, 0 in production
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Simple auth helper: get current user record or null
 */
function current_user() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return null;
    static $user = null;
    if ($user === null) {
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}
