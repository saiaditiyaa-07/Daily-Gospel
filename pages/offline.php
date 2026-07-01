<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Offline — ' . APP_NAME;
$pageDescription = 'You are currently offline.';
$currentPage = '';

require __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
    <div class="container text-center py-5">
        <div class="error-page">
            <i class="bi bi-wifi-off display-1 text-muted mb-4 d-block"></i>
            <h1 class="h2 mb-3">You're Offline</h1>
            <p class="lead text-muted mb-4">Please check your internet connection and try again. Daily readings require an active connection to load from external services.</p>
            <button type="button" class="btn btn-primary btn-lg rounded-pill px-4" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
            </button>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
