<?php
declare(strict_types=1);
ob_start();
/**
 * Daily Gospel - Application Configuration
 *
 * Copy config.example.php to config.local.php to override settings locally.
 */

define('APP_ROOT', __DIR__);
define('APP_NAME', 'Daily Gospel');
define('APP_VERSION', '1.0.0');

// Dynamically determine APP_URL for local development (supports port 8000 and XAMPP subdirectories)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$subDir = (php_sapi_name() === 'cli-server' || strpos($host, ':') !== false) ? '' : '/Gospell';
define('APP_URL', $protocol . $host . $subDir);

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'daily_gospel');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// External APIs
define('LITURGICAL_CALENDAR_BASE', 'https://calapi.inadiutorium.cz/api/v0');
define('LITURGICAL_CALENDAR_LANG', 'en');
define('LITURGICAL_CALENDAR_ID', 'default');
define('UNIVERSALIS_BASE', 'https://universalis.com');
define('UNIVERSALIS_REGION', ''); // e.g. 'Europe.England.Southwark' or empty for General Roman

// API timeouts (seconds)
define('API_TIMEOUT', 30);
define('API_CONNECT_TIMEOUT', 10);
define('CALENDAR_API_TIMEOUT', 12);
define('CALENDAR_API_CONNECT_TIMEOUT', 5);
define('CALENDAR_MONTH_BUDGET', 25); // max seconds to spend enriching one month
define('CALENDAR_CACHE_TTL', 3600); // 1 hour
define('SEARCH_MAX_RESULTS', 15);
define('SEARCH_EXECUTION_LIMIT', 120);

// Session
define('SESSION_NAME', 'daily_gospel_session');
define('ADMIN_SESSION_KEY', 'dg_admin_user');

// Timezone for liturgical day boundaries
define('APP_TIMEZONE', 'UTC');

// Load local overrides if present
$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    require $localConfig;
}

date_default_timezone_set(APP_TIMEZONE);
