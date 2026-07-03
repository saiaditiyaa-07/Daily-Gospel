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
if (!defined('APP_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $subDir = (php_sapi_name() === 'cli-server' || strpos($host, ':') !== false) ? '' : '/Gospell';
    define('APP_URL', $protocol . $host . $subDir);
}

// Load local overrides if present (allows overriding DB_* and other constants before they are defined)
$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    require $localConfig;
}

// Database
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'daily_gospel');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// External APIs
if (!defined('LITURGICAL_CALENDAR_BASE')) define('LITURGICAL_CALENDAR_BASE', 'https://calapi.inadiutorium.cz/api/v0');
if (!defined('LITURGICAL_CALENDAR_LANG')) define('LITURGICAL_CALENDAR_LANG', 'en');
if (!defined('LITURGICAL_CALENDAR_ID')) define('LITURGICAL_CALENDAR_ID', 'default');
if (!defined('UNIVERSALIS_BASE')) define('UNIVERSALIS_BASE', 'https://universalis.com');
if (!defined('UNIVERSALIS_REGION')) define('UNIVERSALIS_REGION', ''); // e.g. 'Europe.England.Southwark' or empty for General Roman

// API timeouts (seconds)
if (!defined('API_TIMEOUT')) define('API_TIMEOUT', 30);
if (!defined('API_CONNECT_TIMEOUT')) define('API_CONNECT_TIMEOUT', 10);
if (!defined('CALENDAR_API_TIMEOUT')) define('CALENDAR_API_TIMEOUT', 12);
if (!defined('CALENDAR_API_CONNECT_TIMEOUT')) define('CALENDAR_API_CONNECT_TIMEOUT', 5);
if (!defined('CALENDAR_MONTH_BUDGET')) define('CALENDAR_MONTH_BUDGET', 25); // max seconds to spend enriching one month
if (!defined('CALENDAR_CACHE_TTL')) define('CALENDAR_CACHE_TTL', 3600); // 1 hour
if (!defined('SEARCH_MAX_RESULTS')) define('SEARCH_MAX_RESULTS', 15);
if (!defined('SEARCH_EXECUTION_LIMIT')) define('SEARCH_EXECUTION_LIMIT', 120);

// Session
if (!defined('SESSION_NAME')) define('SESSION_NAME', 'daily_gospel_session');
if (!defined('ADMIN_SESSION_KEY')) define('ADMIN_SESSION_KEY', 'dg_admin_user');

// Timezone for liturgical day boundaries
if (!defined('APP_TIMEZONE')) define('APP_TIMEZONE', 'UTC');

date_default_timezone_set(APP_TIMEZONE);

