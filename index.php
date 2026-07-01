<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

start_app_session();

$requestedDate = sanitize_date($_GET['date'] ?? null);
$readingService = new ReadingService();
$data = $readingService->getByDate($requestedDate);
$adjacent = adjacent_dates($requestedDate);
$todayDate = (new DateTimeImmutable('today'))->format('Y-m-d');
$isToday = $requestedDate === $todayDate;

$pageTitle = ($data['success'] ?? false)
    ? 'Daily Gospel — ' . ($data['formatted_date'] ?? format_display_date($requestedDate))
    : 'Daily Gospel — Readings Unavailable';

$pageDescription = ($data['success'] ?? false)
    ? 'Catholic Mass readings for ' . ($data['formatted_date'] ?? $requestedDate) . '. ' . ($data['celebration'] ?? '')
    : 'Daily Catholic Mass readings';

$currentPage = 'today';
$canonicalDate = $requestedDate;
$breadcrumbs = [
    ['label' => "Today's Mass Readings"],
];
$sidebarLiturgicalColour = $data['liturgical_colour'] ?? 'green';
$sidebarCelebration = $data['celebration'] ?? '';
$sidebarDate = $requestedDate;
$sidebarSeason = $data['season'] ?? '';
$extraScripts = ['assets/js/date-picker.js'];

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/breadcrumbs.php';
?>

<section class="readings-page py-4 py-lg-5">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-8 order-2 order-lg-1">
                <header class="readings-page-header mb-4">
                    <p class="readings-eyebrow mb-2">Catholic Daily Mass Readings</p>
                    <h1 class="readings-page-title">Daily Mass Readings</h1>
                    <p class="readings-page-date" id="display-date"><?= e($data['formatted_date'] ?? format_display_date($requestedDate)) ?></p>
                    <?php if ($data['success'] ?? false): ?>
                    <p class="readings-celebration lead mb-0" id="header-celebration"><?= e($data['celebration'] ?? '') ?></p>
                    <?php else: ?>
                    <p class="readings-celebration lead mb-0 d-none" id="header-celebration"></p>
                    <?php endif; ?>
                </header>

                <div class="readings-toolbar card border-0 shadow-sm mb-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-auto">
                                <div class="btn-group date-nav-group w-100" role="group" aria-label="Date navigation">
                                    <a href="#" class="btn btn-outline-primary" id="prev-day-btn" data-date="<?= e($adjacent['prev']) ?>" title="Previous day">
                                        <i class="bi bi-chevron-left"></i><span class="d-none d-sm-inline ms-1">Previous</span>
                                    </a>
                                    <?php if (!$isToday): ?>
                                    <a href="<?= url('index.php') ?>" class="btn btn-primary" id="today-btn" title="Go to today">
                                        <i class="bi bi-sun"></i><span class="d-none d-sm-inline ms-1">Today</span>
                                    </a>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-primary" id="today-btn" disabled title="Currently viewing today">
                                        <i class="bi bi-sun"></i><span class="d-none d-sm-inline ms-1">Today</span>
                                    </button>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-outline-primary" id="next-day-btn" data-date="<?= e($adjacent['next']) ?>" title="Next day">
                                        <span class="d-none d-sm-inline me-1">Next</span><i class="bi bi-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="input-group date-picker-group">
                                    <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                    <input type="date" class="form-control" id="date-picker" value="<?= e($requestedDate) ?>" aria-label="Select date">
                                </div>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="toolbar-actions d-flex gap-2 justify-content-md-end">
                                    <div class="dropdown text-settings-dropdown d-inline-block" id="text-settings-wrapper">
                                        <button type="button" class="btn btn-outline-primary dropdown-toggle" id="text-settings-btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" title="Text Settings">
                                            <i class="bi bi-text-paragraph"></i><span class="d-none d-sm-inline ms-1">Text</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow p-3" aria-labelledby="text-settings-btn">
                                            <div class="text-settings-title mb-2">Font Family</div>
                                            <div class="btn-group font-style-btn-group w-100 mb-3" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary active" id="font-serif-btn">Serif</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="font-sans-btn">Sans-Serif</button>
                                            </div>
                                            <div class="text-settings-title mb-2">Font Size</div>
                                            <div class="font-size-control">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="font-dec-btn" style="width:32px; height:32px; padding:0;"><i class="bi bi-dash"></i></button>
                                                <span class="font-size-val" id="font-size-label">100%</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="font-inc-btn" style="width:32px; height:32px; padding:0;"><i class="bi bi-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" id="share-btn" title="Share readings">
                                        <i class="bi bi-share"></i><span class="d-none d-sm-inline ms-1">Share</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="print-btn" title="Print readings">
                                        <i class="bi bi-printer"></i><span class="d-none d-sm-inline ms-1">Print</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="bookmark-btn" title="Bookmark this day">
                                        <i class="bi bi-bookmark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="loading-skeleton" class="d-none mb-4">
                    <div class="skeleton-reading mb-3"></div>
                    <div class="skeleton-reading mb-3"></div>
                    <div class="skeleton-reading"></div>
                </div>

                <div id="readings-content">
                    <?php if (!($data['success'] ?? false)): ?>
                        <div class="alert alert-danger text-center rounded-4 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                            <?= e($data['error'] ?? "Unable to load today's readings. Please try again later.") ?>
                            <div class="mt-3">
                                <button type="button" class="btn btn-danger rounded-pill" id="retry-btn">Try Again</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="liturgical-meta row g-3 mb-4 animate-slide-up" id="liturgical-meta">
                            <div class="col-sm-4">
                                <div class="meta-chip h-100">
                                    <i class="bi bi-calendar-event"></i>
                                    <div>
                                        <span class="meta-label">Celebration</span>
                                        <span class="meta-value" id="celebration-text"><?= e($data['celebration'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="meta-chip h-100">
                                    <i class="bi bi-flower1"></i>
                                    <div>
                                        <span class="meta-label">Liturgical Season</span>
                                        <span class="meta-value" id="season-text"><?= e($data['season'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="meta-chip h-100 <?= colour_class($data['liturgical_colour'] ?? 'green') ?>" id="colour-chip">
                                    <i class="bi bi-palette"></i>
                                    <div>
                                        <span class="meta-label">Liturgical Colour</span>
                                        <span class="meta-value text-capitalize" id="colour-text"><?= e($data['liturgical_colour'] ?? 'green') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($data['saint'])): ?>
                        <div class="saint-highlight mb-4 animate-slide-up" id="saint-banner">
                            <div class="saint-highlight-icon"><i class="bi bi-star-fill"></i></div>
                            <div>
                                <span class="saint-highlight-label">Saint of the Day</span>
                                <strong class="saint-highlight-name" id="saint-text"><?= e($data['saint']) ?></strong>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="saint-highlight mb-4 animate-slide-up d-none" id="saint-banner">
                            <div class="saint-highlight-icon"><i class="bi bi-star-fill"></i></div>
                            <div>
                                <span class="saint-highlight-label">Saint of the Day</span>
                                <strong class="saint-highlight-name" id="saint-text"></strong>
                            </div>
                        </div>
                        <?php endif; ?>

                        <nav class="reading-jump-nav mb-4 animate-slide-up" id="reading-jump-nav" aria-label="Jump to reading">
                            <a href="#reading-r1" class="jump-link">First Reading</a>
                            <a href="#reading-ps" class="jump-link">Psalm</a>
                            <?php if (!empty($data['second_reading'])): ?>
                            <a href="#reading-r2" class="jump-link" id="jump-r2">Second Reading</a>
                            <?php else: ?>
                            <a href="#reading-r2" class="jump-link d-none" id="jump-r2">Second Reading</a>
                            <?php endif; ?>
                            <a href="#reading-g" class="jump-link">Gospel</a>
                        </nav>

                        <div id="readings-cards" class="readings-accordion accordion">
                            <?php
                            $readings = [
                                ['key' => 'first_reading', 'label' => 'First Reading', 'icon' => 'bi-book', 'id' => 'reading-r1'],
                                ['key' => 'psalm', 'label' => 'Responsorial Psalm', 'icon' => 'bi-music-note-beamed', 'id' => 'reading-ps'],
                                ['key' => 'second_reading', 'label' => 'Second Reading', 'icon' => 'bi-journal-text', 'id' => 'reading-r2'],
                                ['key' => 'gospel', 'label' => 'Gospel', 'icon' => 'bi-cross', 'id' => 'reading-g'],
                            ];
                            $idx = 0;
                            foreach ($readings as $reading):
                                $block = $data[$reading['key']] ?? null;
                                if ($reading['key'] === 'second_reading' && $block === null) {
                                    continue;
                                }
                                $collapseId = 'collapse-' . $reading['id'];
                                $idx++;
                            ?>
                            <article class="accordion-item reading-accordion-item animate-slide-up" id="<?= e($reading['id']) ?>-card">
                                <h2 class="accordion-header">
                                    <button class="accordion-button<?= $idx > 1 ? ' collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= e($collapseId) ?>" aria-expanded="<?= $idx === 1 ? 'true' : 'false' ?>" aria-controls="<?= e($collapseId) ?>">
                                        <i class="bi <?= e($reading['icon']) ?> me-2"></i>
                                        <?= e($reading['label']) ?>
                                        <?php if ($block && !empty($block['source'])): ?>
                                        <span class="reading-source badge bg-primary-subtle text-primary-emphasis ms-2"><?= e($block['source']) ?></span>
                                        <?php endif; ?>
                                    </button>
                                </h2>
                                <div id="<?= e($collapseId) ?>" class="accordion-collapse collapse<?= $idx === 1 ? ' show' : '' ?>" data-bs-parent="#readings-cards">
                                    <div class="accordion-body">
                                        <?php if ($block && !empty($block['heading'])): ?>
                                        <p class="reading-heading"><?= e($block['heading']) ?></p>
                                        <?php endif; ?>
                                        <div class="reading-text">
                                            <?= render_reading_html($block) ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        </div>

                        <div class="share-bar mt-4 animate-slide-up">
                            <span class="share-bar-label">Share:</span>
                            <div class="share-buttons">
                                <a href="#" class="share-btn share-facebook" data-share="facebook" title="Facebook"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="share-btn share-whatsapp" data-share="whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                <a href="#" class="share-btn share-twitter" data-share="twitter" title="X"><i class="bi bi-twitter-x"></i></a>
                                <a href="#" class="share-btn share-telegram" data-share="telegram" title="Telegram"><i class="bi bi-telegram"></i></a>
                                <a href="#" class="share-btn share-email" data-share="email" title="Email"><i class="bi bi-envelope"></i></a>
                            </div>
                        </div>

                        <?php if (!empty($data['copyright_html'])): ?>
                        <div class="copyright-notice mt-4 small text-muted" id="copyright-notice">
                            <?= $data['copyright_html'] ?>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4 order-1 order-lg-2">
                <?php require __DIR__ . '/includes/sidebar.php'; ?>
            </div>
        </div>
    </div>
</section>

<input type="hidden" id="current-date" value="<?= e($requestedDate) ?>">

<?php require __DIR__ . '/includes/footer.php'; ?>
