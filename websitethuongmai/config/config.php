
<?php
// CAU HINH KET NOI DATABASE

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3307');
define('DB_NAME', 'shop_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Tu dong nhan dien duong dan website
$scriptDirectory = str_replace(
    '\\',
    '/',
    dirname($_SERVER['SCRIPT_NAME'] ?? '/public/index.php')
);

$scriptDirectory = rtrim($scriptDirectory, '/');

define('BASE_URL', $scriptDirectory . '/');

// CAU HINH ANH SAN PHAM
define('UPLOAD_ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'webp']);
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('UPLOAD_MIN_SIDE', 300);
define('UPLOAD_MAX_SIDE', 5000);
define('UPLOAD_MAX_IMAGES', 8);
define('IMAGE_MAIN_WIDTH', 1200);
define('IMAGE_THUMB_WIDTH', 400);
