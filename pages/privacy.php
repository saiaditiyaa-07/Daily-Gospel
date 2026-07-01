<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Privacy Policy — ' . APP_NAME;
$pageDescription = 'Privacy policy for Daily Gospel website.';
$currentPage = 'about';
$breadcrumbs = [
    ['label' => 'Privacy Policy'],
];

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/breadcrumbs.php';
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Privacy Policy</h1>
        <p class="page-subtitle">Last updated: <?= date('F j, Y') ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 content-card">
                <h2 class="h5">Information We Collect</h2>
                <p>When you submit feedback, prayer requests, or bookmarks, we may store the information you provide (name, email, message) in our database. Session identifiers are used for bookmark functionality.</p>

                <h2 class="h5 mt-4">Mass Readings</h2>
                <p>Daily Mass readings are not stored in our database. They are fetched dynamically from Universalis and the Church Calendar API each time you request them.</p>

                <h2 class="h5 mt-4">Cookies</h2>
                <p>We use session cookies for authentication and preferences (such as dark mode, stored in localStorage). No tracking cookies are used.</p>

                <h2 class="h5 mt-4">Third-Party Services</h2>
                <p>Readings are provided by <a href="https://universalis.com" target="_blank" rel="noopener">Universalis</a>. Liturgical calendar data comes from <a href="http://calapi.inadiutorium.cz" target="_blank" rel="noopener">Church Calendar API</a>. Please refer to their respective privacy policies.</p>

                <h2 class="h5 mt-4">Contact</h2>
                <p>For privacy-related inquiries, please <a href="<?= url('pages/contact.php') ?>">contact us</a>.</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
