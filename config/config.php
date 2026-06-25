<?php
declare(strict_types=1);

// 1. Load Composer's autoloader (vendor/autoload.php)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Initialize Dotenv to read the secure .env file from the root folder
if (class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad();
}

// 3. Application Configurations (Preserved from your original settings)
define('APP_NAME', 'SobaMobile');
define('APP_ENV', 'development');

// 4. Dynamic URL Detection (Works for teammates locally on Apache and for you on port 8000)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$doc_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$current_dir = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$sub_dir = str_replace($doc_root, '', $current_dir);
$dynamic_base_url = $protocol . "://" . $host . $sub_dir;

define('BASE_URL', getenv('BASE_URL') ?: $dynamic_base_url);

// 5. Database Configurations (Pulled securely from your local .env, with default values if empty)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));
define('DB_NAME', getenv('DB_NAME') ?: 'webbanhang');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');