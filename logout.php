<?php
require_once __DIR__ . '/init.php';
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 42000, '/');
header('Location: index.php');
exit();
