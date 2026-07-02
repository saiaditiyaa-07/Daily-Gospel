<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$year = (int) ($_GET['year'] ?? date('Y'));
$month = (int) ($_GET['month'] ?? date('n'));

if ($year < 1970 || $year > 2100) {
    $year = (int) date('Y');
}
if ($month < 1 || $month > 12) {
    $month = (int) date('n');
}

$isTamil = Language::get() === 'ta';
$monthNamesTa = [
    1 => 'ஜனவரி', 2 => 'பிப்ரவரி', 3 => 'மார்ச்', 4 => 'ஏப்ரல்',
    5 => 'மே', 6 => 'ஜூன்', 7 => 'ஜூலை', 8 => 'ஆகஸ்ட்',
    9 => 'செப்டம்பர்', 10 => 'அக்டோபர்', 11 => 'நவம்பர்', 12 => 'டிசம்பர்'
];
$displayMonthYear = $isTamil 
    ? $monthNamesTa[$month] . ' ' . $year 
    : date('F Y', mktime(0, 0, 0, $month, 1, $year));

$pageTitle = __('archive_title') . ' — ' . $displayMonthYear . ' — ' . APP_NAME;
$pageDescription = __('archive_subtitle');
$currentPage = 'archive';
$breadcrumbs = [
    ['label' => __('bc_archive')],
];
$extraScripts = ['assets/js/archive.js'];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title"><?= e(__('archive_title')) ?></h1>
        <p class="page-subtitle"><?= e(__('archive_subtitle')) ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="archive-controls card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="archive-year" class="form-label fw-semibold"><?= e(__('archive_year')) ?></label>
                        <select class="form-select form-select-lg" id="archive-year">
                            <?php for ($y = (int) date('Y') - 2; $y <= (int) date('Y') + 2; $y++): ?>
                            <option value="<?= $y ?>"<?= $y === $year ? ' selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="archive-month" class="form-label fw-semibold"><?= e(__('archive_month')) ?></label>
                        <select class="form-select form-select-lg" id="archive-month">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>"<?= $m === $month ? ' selected' : '' ?>><?= $isTamil ? $monthNamesTa[$m] : date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary btn-lg w-100 rounded-pill" id="archive-load-btn">
                            <i class="bi bi-calendar3 me-2"></i><?= e(__('btn_load_calendar')) ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="archive-loading" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"><?= e(__('loading_calendar')) ?></span>
            </div>
            <p class="mt-3 text-muted"><?= e(__('loading_liturgical_calendar')) ?></p>
        </div>

        <div id="archive-calendar" class="archive-calendar" data-year="<?= $year ?>" data-month="<?= $month ?>">
            <div class="calendar-header text-center mb-4">
                <h2 class="h3 mb-0" id="calendar-title"><?= e($displayMonthYear) ?></h2>
            </div>
            <div class="calendar-weekdays mb-2 text-center fw-semibold text-muted small">
                <?php
                $weekdays = $isTamil 
                    ? ['ஞாயிறு', 'திங்கள்', 'செவ்வாய்', 'புதன்', 'வியாழன்', 'வெள்ளி', 'சனி'] 
                    : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                foreach ($weekdays as $day):
                ?>
                <div><?= e($day) ?></div>
                <?php endforeach; ?>
            </div>
            <div id="calendar-grid" class="calendar-grid"></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
