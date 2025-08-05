<?php
// functions.php

// Flash message helpers
function flash_set($key, $message) {
    $_SESSION['flash'][$key] = $message;
}
function flash_get($key) {
    if (!isset($_SESSION['flash'][$key])) return null;
    $val = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $val;
}

// Simple CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}
function csrf_check($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Escaping
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
