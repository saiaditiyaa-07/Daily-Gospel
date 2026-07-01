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
$subject = trim((string) ($input['subject'] ?? 'General Feedback'));
$message = trim((string) ($input['message'] ?? ''));
$rating = (int) ($input['rating'] ?? 0);

if ($message === '') {
    json_response(['success' => false, 'error' => 'Message is required.'], 422);
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'error' => 'Invalid email address.'], 422);
}

if ($rating < 0 || $rating > 5) {
    $rating = 0;
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare(
        'INSERT INTO feedback (name, email, subject, message, rating, status, created_at)
         VALUES (:name, :email, :subject, :message, :rating, :status, NOW())'
    );
    $stmt->execute([
        'name' => $name ?: 'Anonymous',
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'rating' => $rating,
        'status' => 'new',
    ]);

    json_response(['success' => true, 'message' => 'Thank you for your feedback!']);
} catch (Throwable $e) {
    error_log('Feedback error: ' . $e->getMessage());
    json_response(['success' => false, 'error' => 'Unable to submit feedback.'], 500);
}
