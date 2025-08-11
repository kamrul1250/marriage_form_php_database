<?php
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

// CSRF protection
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Escaping for HTML output
function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

// File upload helper
function handle_file_upload($field, $prefix = 'file_') {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, ALLOWED_FILE_TYPES)) {
        throw new Exception("Invalid file type. Allowed: " . implode(', ', ALLOWED_FILE_TYPES));
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception("File too large. Max size: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB");
    }

    $newName = $prefix . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = UPLOAD_DIR . $newName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new Exception("Failed to save uploaded file.");
    }

    return [
        'name' => $file['name'],
        'path' => 'uploads/' . $newName,
        'type' => $file['type'],
        'size' => $file['size']
    ];
}

// Delete file helper
function delete_file($path) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        unlink($fullPath);
        return true;
    }
    return false;
}