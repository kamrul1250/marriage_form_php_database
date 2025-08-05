<?php
// config.php
$DB_HOST = 'localhost';
$DB_NAME = 'matrimonial_data_form';
$DB_USER = 'root';
$DB_PASS = ''; // set your DB password
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production, don't echo the error. Log it instead.
    exit('Database connection failed: ' . $e->getMessage());
}
