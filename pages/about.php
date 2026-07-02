<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = __('nav_about') . ' — ' . APP_NAME;
$pageDescription = __('about_subtitle');
$currentPage = 'about';
$breadcrumbs = [
    ['label' => __('nav_about')],
];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?= e(__('about_title')) ?></h1>
        <p class="page-subtitle"><?= e(__('about_subtitle')) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="content-card">
                    <h2 class="h4 mb-3"><?= e(__('about_mission')) ?></h2>
                    <p><?= e(__('about_mission_desc')) ?></p>

                    <h2 class="h4 mb-3 mt-4"><?= e(__('about_dynamic')) ?></h2>
                    <p><?= e(__('about_dynamic_desc')) ?></p>

                    <h2 class="h4 mb-3 mt-4"><?= e(__('about_sources')) ?></h2>
                    <ul class="list-unstyled source-list">
                        <li class="mb-3">
                            <strong><i class="bi bi-book me-2 text-primary"></i><?= e(__('about_source_mass')) ?></strong>
                            <p class="mb-0 text-muted"><?= e(__('about_source_mass_desc')) ?></p>
                        </li>
                        <li class="mb-3">
                            <strong><i class="bi bi-calendar-event me-2 text-primary"></i><?= e(__('about_source_cal')) ?></strong>
                            <p class="mb-0 text-muted"><?= e(__('about_source_cal_desc')) ?></p>
                        </li>
                    </ul>

                    <h2 class="h4 mb-3 mt-4"><?= e(__('about_architecture')) ?></h2>
                    <p><?= e(__('about_arch_desc')) ?></p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-3"><?= e(__('about_quick_links')) ?></h3>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><a href="<?= url('index.php') ?>" class="text-decoration-none"><i class="bi bi-house me-2"></i><?= e(__('nav_todays_readings')) ?></a></li>
                            <li class="mb-2"><a href="<?= url('pages/archive.php') ?>" class="text-decoration-none"><i class="bi bi-calendar3 me-2"></i><?= e(__('nav_archive')) ?></a></li>
                            <li class="mb-2"><a href="<?= url('pages/search.php') ?>" class="text-decoration-none"><i class="bi bi-search me-2"></i><?= e(__('nav_search')) ?></a></li>
                            <li><a href="<?= url('pages/contact.php') ?>" class="text-decoration-none"><i class="bi bi-envelope me-2"></i><?= e(__('nav_contact')) ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
