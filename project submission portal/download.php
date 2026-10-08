<?php
require_once 'config.php';
require_once 'database.php';

$id = $_GET['id'] ?? '';
$filePath = $_GET['file'] ?? '';

if ($id === '' || $filePath === '') {
    header('Location: index.php');
    exit;
}

$fullPath = __DIR__ . '/' . ltrim($filePath, '/');
if (!file_exists($fullPath)) {
    header('Location: admin.php');
    exit;
}

increment_download_count($id);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($fullPath));
readfile($fullPath);
exit;
