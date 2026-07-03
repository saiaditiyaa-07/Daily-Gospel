<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

set_time_limit(SEARCH_EXECUTION_LIMIT);

$type = strtolower(trim($_GET['type'] ?? 'date'));
$query = trim($_GET['q'] ?? '');
$year = (int) ($_GET['year'] ?? date('Y'));
$month = (int) ($_GET['month'] ?? date('n'));

if ($year < 1970 || $year > 2100) {
    $year = (int) date('Y');
}
if ($month < 1 || $month > 12) {
    $month = (int) date('n');
}

if ($query === '' && $type !== 'date') {
    json_response(['success' => false, 'error' => __('search_query_required')], 400);
}

if (!in_array($type, ['date', 'reference'], true)) {
    json_response(['success' => false, 'error' => __('search_invalid_type')], 400);
}

$results = [];
$warnings = [];

try {
    switch ($type) {
        case 'date':
            $readingService = new ReadingService();
            $date = sanitize_date($query !== '' ? $query : null);
            $data = $readingService->getByDate($date);

            if ($data['success'] ?? false) {
                $results[] = [
                    'type' => 'date',
                    'date' => $data['date'],
                    'title' => $data['celebration'] ?? $data['formatted_date'],
                    'subtitle' => $data['formatted_date'] ?? format_display_date($data['date']),
                    'url' => url('index.php?date=' . $data['date']),
                ];
            } else {
                json_response([
                    'success' => false,
                    'error' => $data['error'] ?? __('error_loading_readings'),
                ], 200); // 200 OK prevents web servers from replacing handled errors with HTML
            }
            break;



        case 'reference':
            $readingProvider = Language::get() === 'ta'
                ? new TamilProvider()
                : new UniversalisReadingProvider(new ApiClient(10, 5));
            $daysInMonth = get_days_in_month($month, $year);
            $searchRef = strtolower(preg_replace('/\s+/', '', $query) ?? '');

            if ($searchRef === '') {
                json_response(['success' => false, 'error' => __('search_enter_ref')], 400);
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateObj = DateTimeImmutable::createFromFormat('Y-n-j', "$year-$month-$day");
                if ($dateObj === false) {
                    continue;
                }

                try {
                    $data = $readingProvider->getReadingsForDate($dateObj);
                } catch (Throwable $e) {
                    continue;
                }

                $sources = [];
                foreach (['first_reading', 'psalm', 'second_reading', 'gospel'] as $key) {
                    if (!empty($data[$key]['source'])) {
                        $sources[] = $data[$key]['source'];
                    }
                }

                $combined = strtolower(preg_replace('/\s+/', '', implode(' ', $sources)) ?? '');
                if ($combined === '' || !str_contains($combined, $searchRef)) {
                    continue;
                }

                $matched = implode(', ', array_filter($sources, static function (string $src) use ($query): bool {
                    return stripos($src, $query) !== false;
                }));

                $results[] = [
                    'type' => 'reference',
                    'date' => $data['date'],
                    'title' => $matched ?: $query,
                    'subtitle' => $data['formatted_date'] ?? format_display_date($data['date']),
                    'url' => url('index.php?date=' . $data['date']),
                ];

                if (count($results) >= SEARCH_MAX_RESULTS) {
                    break;
                }
            }

            if ($daysInMonth > 0 && count($results) === 0) {
                $warnings[] = __('search_warning_ref');
            }
            break;
    }
} catch (Throwable $e) {
    error_log('Search API error: ' . $e->getMessage());
    json_response([
        'success' => false,
        'error' => __('search_failed'),
    ], 500);
}

json_response([
    'success' => true,
    'type' => $type,
    'query' => $query,
    'year' => $year,
    'month' => $month,
    'count' => count($results),
    'results' => $results,
    'warnings' => $warnings,
]);
