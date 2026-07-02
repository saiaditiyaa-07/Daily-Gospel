<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

http_response_code(404);
$pageTitle = __('error_404_title') . ' — ' . APP_NAME;
$pageDescription = __('error_404_desc');
$currentPage = '';

require __DIR__ . '/../includes/header.php';
?>

<section class="py-5">
    <div class="container text-center py-5">
        <div class="error-page">
            <div class="error-code display-1 fw-bold text-primary">404</div>
            <h1 class="h2 mb-3"><?= e(__('error_404_title')) ?></h1>
            <p class="lead text-muted mb-4"><?= e(__('error_404_desc')) ?></p>
            <a href="<?= url('index.php') ?>" class="btn btn-primary btn-lg rounded-pill px-4">
                <i class="bi bi-house me-2"></i><?= e(__('btn_return_home')) ?>
            </a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
