<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_ROOT', __DIR__);
define('DATA_FILE', APP_ROOT . '/data/students.json');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'aj_project_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('USE_MYSQL', false);
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'txt', 'jpg', 'jpeg', 'png']);
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);
