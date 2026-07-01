<?php
declare(strict_types=1);

/**
 * One-time admin password setup.
 * Visit /admin/setup.php?password=YourSecurePassword then DELETE this file.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

header('Content-Type: text/plain; charset=utf-8');

$password = $_GET['password'] ?? '';

if ($password === '' || strlen($password) < 8) {
    echo "Usage: /admin/setup.php?password=YourSecurePassword\n";
    echo "Password must be at least 8 characters.\n";
    exit;
}

try {
    $db = Database::getConnection();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare('UPDATE users SET password_hash = :hash WHERE username = :username');
    $stmt->execute(['hash' => $hash, 'username' => 'admin']);

    if ($stmt->rowCount() === 0) {
        $insert = $db->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :hash, :role)'
        );
        $insert->execute([
            'username' => 'admin',
            'email' => 'admin@dailygospel.local',
            'hash' => $hash,
            'role' => 'admin',
        ]);
    }

    echo "Admin password updated successfully.\n";
    echo "Username: admin\n";
    echo "DELETE admin/setup.php immediately for security.\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Ensure database.sql has been imported and config.php DB credentials are correct.\n";
}
