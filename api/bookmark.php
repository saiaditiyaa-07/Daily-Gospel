<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

start_app_session();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
if (!is_array($input)) {
    json_response(['success' => false, 'error' => 'Invalid JSON'], 400);
}

$date = sanitize_date($input['date'] ?? null);
$title = trim((string) ($input['title'] ?? ''));
$sessionId = session_id();

if ($title === '') {
    $title = __('title_daily_mass_readings') . ' — ' . format_display_date($date);
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare(
        'INSERT INTO bookmarks (session_id, reading_date, title, created_at)
         VALUES (:session_id, :reading_date, :title, NOW())'
    );
    $stmt->execute([
        'session_id' => $sessionId,
        'reading_date' => $date,
        'title' => $title,
    ]);

    json_response([
        'success' => true,
        'message' => __('bookmark_added'),
        'id' => (int) $db->lastInsertId(),
    ]);
} catch (Throwable $e) {
    error_log('Bookmark error: ' . $e->getMessage());
    json_response(['success' => false, 'error' => __('validation_error')], 500);
}
