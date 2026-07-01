<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

$date = sanitize_date($_GET['date'] ?? null);
$readingService = new ReadingService();
$data = $readingService->getByDate($date);

if (!($data['success'] ?? false)) {
    json_response($data, 200);
}

$adjacent = adjacent_dates($date);
$data['prev_date'] = $adjacent['prev'];
$data['next_date'] = $adjacent['next'];

json_response($data);
