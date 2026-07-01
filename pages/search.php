<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Search Readings — ' . APP_NAME;
$pageDescription = 'Search Catholic daily readings by date, Bible reference, or saint name.';
$currentPage = 'search';
$breadcrumbs = [
    ['label' => 'Search Readings'],
];
$extraScripts = ['assets/js/search.js'];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Search Readings</h1>
        <p class="page-subtitle">Find Mass readings by date, Bible reference, or saint</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card search-card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <form id="search-form">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Search Type</label>
                                <div class="btn-group w-100 search-type-group" role="group">
                                    <input type="radio" class="btn-check" name="searchType" id="type-date" value="date" checked>
                                    <label class="btn btn-outline-primary" for="type-date"><i class="bi bi-calendar3 me-1"></i> Date</label>
                                    <input type="radio" class="btn-check" name="searchType" id="type-reference" value="reference">
                                    <label class="btn btn-outline-primary" for="type-reference"><i class="bi bi-book me-1"></i> Bible Reference</label>
                                    <input type="radio" class="btn-check" name="searchType" id="type-saint" value="saint">
                                    <label class="btn btn-outline-primary" for="type-saint"><i class="bi bi-star me-1"></i> Saint</label>
                                </div>
                            </div>

                            <div class="mb-3" id="query-field-date">
                                <label for="search-date" class="form-label fw-semibold">Select Date</label>
                                <input type="date" class="form-control form-control-lg" id="search-date" value="<?= e(date('Y-m-d')) ?>">
                            </div>

                            <div class="mb-3 d-none" id="query-field-text">
                                <label for="search-query" class="form-label fw-semibold">Search Query</label>
                                <input type="text" class="form-control form-control-lg" id="search-query" placeholder="e.g. John 3:16 or Saint Francis">
                            </div>

                            <div class="row g-3 mb-4 d-none" id="month-year-fields">
                                <div class="col-6">
                                    <label for="search-month" class="form-label fw-semibold">Month</label>
                                    <select class="form-select" id="search-month">
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>"<?= $m === (int) date('n') ? ' selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="search-year" class="form-label fw-semibold">Year</label>
                                    <select class="form-select" id="search-year">
                                        <?php for ($y = (int) date('Y') - 1; $y <= (int) date('Y') + 2; $y++): ?>
                                        <option value="<?= $y ?>"<?= $y === (int) date('Y') ? ' selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                                <i class="bi bi-search me-2"></i>Search
                            </button>
                        </form>
                    </div>
                </div>

                <div id="search-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <p class="mt-3 text-muted">Searching readings...</p>
                </div>

                <div id="search-results" class="mt-4"></div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
