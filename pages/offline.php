<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = __('offline_title') . ' — ' . APP_NAME;
$pageDescription = __('offline_desc');
$currentPage = '';

require __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
    <div class="container text-center py-5">
        <div class="error-page">
            <i class="bi bi-wifi-off display-1 text-muted mb-4 d-block"></i>
            <h1 class="h2 mb-3"><?= e(__('offline_title')) ?></h1>
            <p class="lead text-muted mb-4"><?= e(__('offline_desc')) ?></p>
            <button type="button" class="btn btn-primary btn-lg rounded-pill px-4" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i><?= e(__('retry')) ?>
            </button>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
