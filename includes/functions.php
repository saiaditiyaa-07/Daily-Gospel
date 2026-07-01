<?php
/**
 * Shared helper functions.
 */

declare(strict_types=1);

require_once APP_ROOT . '/config.php';
require_once APP_ROOT . '/includes/ApiClient.php';
require_once APP_ROOT . '/includes/ReadingProviderInterface.php';
require_once APP_ROOT . '/includes/UniversalisReadingProvider.php';
require_once APP_ROOT . '/includes/LiturgicalCalendarService.php';
require_once APP_ROOT . '/includes/ReadingService.php';
require_once APP_ROOT . '/includes/Database.php';

/**
 * Start secure session if not already started.
 */
function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Escape HTML output.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Build absolute URL for app paths.
 */
function url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Get a site setting from database.
 */
function get_setting(string $key, string $default = ''): string
{
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();

        return $row ? (string) $row['setting_value'] : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

/**
 * Format liturgical colour as Bootstrap-friendly CSS class.
 */
function colour_class(string $colour): string
{
    $map = [
        'green' => 'liturgical-green',
        'violet' => 'liturgical-violet',
        'white' => 'liturgical-white',
        'red' => 'liturgical-red',
        'rose' => 'liturgical-rose',
        'black' => 'liturgical-black',
    ];

    return $map[strtolower($colour)] ?? 'liturgical-green';
}

/**
 * Format date for display.
 */
function format_display_date(string $date): string
{
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    return $dt ? $dt->format('l, j F Y') : $date;
}

/**
 * Get previous/next date strings.
 *
 * @return array{prev: string, next: string}
 */
function adjacent_dates(string $date): array
{
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    if ($dt === false) {
        $today = new DateTimeImmutable('today');
        return [
            'prev' => $today->modify('-1 day')->format('Y-m-d'),
            'next' => $today->modify('+1 day')->format('Y-m-d'),
        ];
    }

    return [
        'prev' => $dt->modify('-1 day')->format('Y-m-d'),
        'next' => $dt->modify('+1 day')->format('Y-m-d'),
    ];
}

/**
 * Sanitize date input.
 */
function sanitize_date(?string $date): string
{
    if ($date === null || $date === '') {
        return (new DateTimeImmutable('today'))->format('Y-m-d');
    }

    $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    return $parsed ? $parsed->format('Y-m-d') : (new DateTimeImmutable('today'))->format('Y-m-d');
}

/**
 * Send JSON response and exit.
 *
 * @param array<string, mixed> $data
 */
function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Check if current request expects JSON.
 */
function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    return str_contains($accept, 'application/json')
        || strtolower($requestedWith) === 'xmlhttprequest';
}

/**
 * Render a reading block safely (allows Universalis HTML).
 */
function render_reading_html(?array $reading): string
{
    if ($reading === null || empty($reading['text'])) {
        return '<p class="text-muted mb-0">No reading available for this day.</p>';
    }

    return (string) $reading['text'];
}

/**
 * Generate CSRF token.
 */
function csrf_token(): string
{
    start_app_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token.
 */
function validate_csrf(?string $token): bool
{
    start_app_session();
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check admin authentication.
 */
function is_admin_logged_in(): bool
{
    start_app_session();
    return !empty($_SESSION[ADMIN_SESSION_KEY]);
}

/**
 * Require admin login.
 */
function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: ' . url('admin/index.php'));
        exit;
    }
}

/**
 * Get the number of days in a month.
 */
function get_days_in_month(int $month, int $year): int
{
    if (function_exists('cal_days_in_month')) {
        return (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
    return (int) date('t', mktime(0, 0, 0, $month, 1, $year));
}

