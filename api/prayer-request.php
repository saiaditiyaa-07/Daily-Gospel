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

if (!validate_csrf($input['csrf_token'] ?? null)) {
    json_response(['success' => false, 'error' => 'Invalid security token.'], 403);
}

$name = trim((string) ($input['name'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
$request = trim((string) ($input['request'] ?? ''));
$isAnonymous = !empty($input['anonymous']) ? 1 : 0;

if ($request === '') {
    json_response(['success' => false, 'error' => 'Prayer request is required.'], 422);
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'error' => 'Invalid email address.'], 422);
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare(
        'INSERT INTO prayer_requests (name, email, request_text, is_anonymous, status, created_at)
         VALUES (:name, :email, :request_text, :is_anonymous, :status, NOW())'
    );
    $stmt->execute([
        'name' => $isAnonymous ? 'Anonymous' : ($name ?: 'Anonymous'),
        'email' => $email,
        'request_text' => $request,
        'is_anonymous' => $isAnonymous,
        'status' => 'pending',
    ]);

    json_response(['success' => true, 'message' => 'Your prayer request has been submitted.']);
} catch (Throwable $e) {
    error_log('Prayer request error: ' . $e->getMessage());
    json_response(['success' => false, 'error' => 'Unable to submit prayer request.'], 500);
}
