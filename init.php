<?php
// Error reporting
ini_set('display_errors', 0); // Set to 1 for debugging
error_reporting(E_ALL);

// Start session
session_start();
// Add this near the top of init.php after session_start()
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light'; // default theme
}
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en'; // default language
}

// Add this to the current_user() function to load user preferences
if ($user) {
    try {
        $stmt = $pdo->prepare("SELECT theme_preference, language_preference FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $prefs = $stmt->fetch();
        
        if ($prefs) {
            $_SESSION['theme'] = $prefs['theme_preference'] ?? 'light';
            $_SESSION['language'] = $prefs['language_preference'] ?? 'en';
        }
    } catch (PDOException $e) {
        error_log("Preferences fetch error: " . $e->getMessage());
    }
}

// Include required files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Get current authenticated user
function current_user() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return null;
    
    static $user = null;
    if ($user === null) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("User fetch error: " . $e->getMessage());
            return null;
        }
    }
    return $user;
}

// Check if user is admin (example extension)
function is_admin() {
    $user = current_user();
    return $user && $user['is_admin'] ?? false;
}

// Redirect if not authenticated
function require_auth() {
    if (!current_user()) {
        flash_set('error', 'Please login to access that page.');
        header('Location: login.php');
        exit();
    }
}

// Redirect if authenticated
function require_guest() {
    if (current_user()) {
        header('Location: dashboard.php');
        exit();
    }
}