<?php
// Otomatis redirect ke folder public Laravel jika diakses melalui http://127.0.0.1/lms/ atau http://localhost/lms/
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if (!str_contains($uri, '/public/')) {
    header('Location: public/');
    exit;
}
require_once __DIR__ . '/public/index.php';
