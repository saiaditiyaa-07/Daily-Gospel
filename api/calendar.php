<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

$year = (int) ($_GET['year'] ?? date('Y'));
$month = (int) ($_GET['month'] ?? date('n'));

if ($year < 1970 || $year > 2100 || $month < 1 || $month > 12) {
    json_response(['success' => false, 'error' => 'Invalid year or month'], 400);
}

try {
    $calendarService = new LiturgicalCalendarService();
    $monthData = $calendarService->getMonth($year, $month);
} catch (Throwable $e) {
    error_log('Calendar API error: ' . $e->getMessage());

    $fallback = new LiturgicalCalendarService();
    $days = $fallback->buildBasicMonth($year, $month);

    json_response([
        'success' => true,
        'year' => $year,
        'month' => $month,
        'days' => $days,
        'partial' => true,
        'source' => 'basic',
        'message' => 'Liturgical details unavailable; showing dates only.',
    ]);
}

json_response([
    'success' => true,
    'year' => $year,
    'month' => $month,
    'days' => $monthData['days'],
    'partial' => $monthData['partial'],
    'source' => $monthData['source'],
    'message' => $monthData['partial']
        ? 'Some liturgical details could not be loaded; dates are still available.'
        : null,
]);
