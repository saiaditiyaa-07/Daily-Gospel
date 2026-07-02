<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$month = (int) ($_GET['month'] ?? date('n'));
$year = (int) ($_GET['year'] ?? date('Y'));

$pageTitle = __('nav_saints') . ' — ' . APP_NAME;
$pageDescription = __('saints_subtitle');
$currentPage = 'saints';
$breadcrumbs = [
    ['label' => __('bc_saints')],
];

$calendarService = new LiturgicalCalendarService();
$monthData = $calendarService->getMonth($year, $month);
$days = $monthData['days'];
$saints = [];
foreach ($days as $day) {
    if (!empty($day['saint'])) {
        $saints[] = $day;
    } elseif (!empty($day['celebration']) && preg_match('/Saint|Saints|Blessed|St\./i', $day['celebration'])) {
        $saints[] = $day;
    }
}

$isTamil = Language::get() === 'ta';
$monthNamesTa = [
    1 => 'ஜனவரி', 2 => 'பிப்ரவரி', 3 => 'மார்ச்', 4 => 'ஏப்ரல்',
    5 => 'மே', 6 => 'ஜூன்', 7 => 'ஜூலை', 8 => 'ஆகஸ்ட்',
    9 => 'செப்டம்பர்', 10 => 'அக்டோபர்', 11 => 'நவம்பர்', 12 => 'டிசம்பர்'
];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?= e(__('saints_title')) ?></h1>
        <p class="page-subtitle"><?= e(__('saints_subtitle')) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="get" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="saint-month" class="form-label fw-semibold"><?= e(__('archive_month')) ?></label>
                        <select class="form-select" name="month" id="saint-month">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>"<?= $m === $month ? ' selected' : '' ?>><?= $isTamil ? $monthNamesTa[$m] : date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="saint-year" class="form-label fw-semibold"><?= e(__('archive_year')) ?></label>
                        <select class="form-select" name="year" id="saint-year">
                            <?php for ($y = (int) date('Y') - 1; $y <= (int) date('Y') + 2; $y++): ?>
                            <option value="<?= $y ?>"<?= $y === $year ? ' selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill"><?= e(__('btn_view_saints')) ?></button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($saints === []): ?>
        <div class="alert alert-info rounded-4 text-center">
            <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
            <?= e(__('no_saints_found')) ?>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($saints as $saint): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card saint-card h-100 border-0 shadow-sm rounded-4 <?= colour_class($saint['liturgical_colour'] ?? 'white') ?>">
                    <div class="card-body p-4">
                        <div class="saint-date badge bg-primary mb-3"><?= e(format_display_date($saint['date'])) ?></div>
                        <h3 class="h5 card-title"><?= e($saint['saint'] ?: $saint['celebration']) ?></h3>
                        <?php
                        $seasonVal = $saint['season_label'] ?? '';
                        $seasonText = $isTamil
                            ? ($seasonVal === 'Advent' ? 'வருகைக் காலம்' : ($seasonVal === 'Christmas' ? 'பிறப்புக் காலம்' : ($seasonVal === 'Lent' ? 'தவக் காலம்' : ($seasonVal === 'Easter' ? 'பாஸ்கா காலம்' : ($seasonVal === 'Ordinary Time' ? 'பொதுக்காலம்' : $seasonVal)))))
                            : $seasonVal;
                        ?>
                        <p class="card-text text-muted small mb-3"><?= e($seasonText) ?></p>
                        <a href="<?= url('index.php?date=' . e($saint['date'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                            <?= e(__('btn_view_readings')) ?> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
