<?php
// Database configuration
$DB_HOST = 'localhost';
$DB_NAME = 'matrimonial_data_form';
$DB_USER = 'root';
$DB_PASS = ''; // Set your DB password here
$DB_CHARSET = 'utf8mb4';

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx']);

// DSN for PDO
$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_PERSISTENT         => true,
];

// Create PDO instance
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    exit('Database connection failed. Please try again later.');
}

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}