<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = __('privacy_title') . ' — ' . APP_NAME;
$pageDescription = __('privacy_title') . ' — ' . APP_NAME;
$currentPage = 'about';
$breadcrumbs = [
    ['label' => __('privacy_title')],
];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';

$isTamil = Language::get() === 'ta';
$dateFormatted = $isTamil ? 'ஜூலை 2, 2026' : date('F j, Y');
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?= e(__('privacy_title')) ?></h1>
        <p class="page-subtitle"><?= e(__('privacy_last_updated', ['date' => $dateFormatted])) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 content-card">
                <h2 class="h5"><?= e(__('privacy_sec_collect')) ?></h2>
                <p><?= e(__('privacy_sec_collect_desc')) ?></p>

                <h2 class="h5 mt-4"><?= e(__('privacy_sec_readings')) ?></h2>
                <p><?= e(__('privacy_sec_readings_desc')) ?></p>

                <h2 class="h5 mt-4"><?= e(__('privacy_sec_cookies')) ?></h2>
                <p><?= e(__('privacy_sec_cookies_desc')) ?></p>

                <h2 class="h5 mt-4"><?= e(__('privacy_sec_third_party')) ?></h2>
                <p><?= e(__('privacy_sec_third_party_desc')) ?></p>

                <h2 class="h5 mt-4"><?= e(__('privacy_sec_contact')) ?></h2>
                <p><?= e(__('privacy_sec_contact_desc')) ?></p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
