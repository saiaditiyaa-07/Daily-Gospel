<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'About — ' . APP_NAME;
$pageDescription = 'Learn about Daily Gospel, our mission, and the sources of our Catholic Mass readings.';
$currentPage = 'about';
$breadcrumbs = [
    ['label' => 'About'],
];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">About Daily Gospel</h1>
        <p class="page-subtitle">Bringing the Word of God to your daily life</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="content-card">
                    <h2 class="h4 mb-3">Our Mission</h2>
                    <p>Daily Gospel is a Catholic website dedicated to making the daily Mass readings accessible to everyone, everywhere. We believe that encountering Scripture each day is essential for spiritual growth and living out the faith in daily life.</p>

                    <h2 class="h4 mb-3 mt-4">Dynamic Readings</h2>
                    <p>All readings on this site are fetched dynamically from external services — never hardcoded. When you select a date, the site retrieves the appropriate First Reading, Responsorial Psalm, Second Reading (when applicable), and Gospel for that liturgical day.</p>

                    <h2 class="h4 mb-3 mt-4">Data Sources</h2>
                    <ul class="list-unstyled source-list">
                        <li class="mb-3">
                            <strong><i class="bi bi-book me-2 text-primary"></i>Mass Readings</strong>
                            <p class="mb-0 text-muted">Provided via the Universalis JSON/Webmaster service. Scripture texts are copyright their respective publishers.</p>
                        </li>
                        <li class="mb-3">
                            <strong><i class="bi bi-calendar-event me-2 text-primary"></i>Liturgical Calendar</strong>
                            <p class="mb-0 text-muted">Season, feast, saint, and liturgical colour data from the Church Calendar API (calapi.inadiutorium.cz).</p>
                        </li>
                    </ul>

                    <h2 class="h4 mb-3 mt-4">Architecture</h2>
                    <p>Built with PHP, MySQL, Bootstrap 5, and vanilla JavaScript. The reading provider is designed to be easily replaced — swap <code>UniversalisReadingProvider</code> for another implementation of <code>ReadingProviderInterface</code> without changing the rest of the application.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-3">Quick Links</h3>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><a href="<?= url('index.php') ?>" class="text-decoration-none"><i class="bi bi-house me-2"></i>Today's Readings</a></li>
                            <li class="mb-2"><a href="<?= url('pages/archive.php') ?>" class="text-decoration-none"><i class="bi bi-calendar3 me-2"></i>Archive</a></li>
                            <li class="mb-2"><a href="<?= url('pages/search.php') ?>" class="text-decoration-none"><i class="bi bi-search me-2"></i>Search</a></li>
                            <li><a href="<?= url('pages/contact.php') ?>" class="text-decoration-none"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
