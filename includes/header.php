<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
start_app_session();

/** @var string $pageTitle */
/** @var string|null $pageDescription */
/** @var string|null $currentPage */
/** @var string|null $canonicalDate */

$pageTitle = $pageTitle ?? APP_NAME;
$pageDescription = $pageDescription ?? 'Daily Catholic Mass readings — First Reading, Psalm, Second Reading, and Gospel updated automatically for every liturgical day.';
$currentPage = $currentPage ?? 'home';
$canonicalDate = $canonicalDate ?? null;

$metaTitle = e($pageTitle);
$metaDescription = e($pageDescription);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $metaTitle ?></title>
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="keywords" content="Catholic, Daily Gospel, Mass readings, liturgy, Bible, saints">
    <meta name="author" content="<?= e(APP_NAME) ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?= $metaTitle ?>">
    <meta property="og:description" content="<?= $metaDescription ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url($_SERVER['REQUEST_URI'] ?? '')) ?>">
    <meta property="og:site_name" content="<?= e(APP_NAME) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php if ($canonicalDate): ?>
    <meta property="article:published_time" content="<?= e($canonicalDate) ?>">
    <?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/favicon.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&family=Josefin+Sans:ital,wght@0,300..700;1,300..700&family=Manrope:wght@300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="site-body">
    <div id="offline-banner" class="offline-banner d-none" role="alert">
        <div class="container d-flex align-items-center justify-content-between py-2">
            <span><i class="bi bi-wifi-off me-2"></i>You appear to be offline. Some features may be unavailable.</span>
            <button type="button" class="btn-close btn-close-white" aria-label="Close" id="offline-banner-close"></button>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm" id="main-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
                <img src="<?= url('assets/images/logo.svg') ?>" alt="<?= e(APP_NAME) ?> logo" width="36" height="36" class="brand-logo">
                <span class="brand-text"><?= e(APP_NAME) ?></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'home' ? ' active' : '' ?>" href="<?= url('index.php') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'today' ? ' active' : '' ?>" href="<?= url('index.php') ?>">Today's Readings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'archive' ? ' active' : '' ?>" href="<?= url('pages/archive.php') ?>">Archive</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'search' ? ' active' : '' ?>" href="<?= url('pages/search.php') ?>">Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'saints' ? ' active' : '' ?>" href="<?= url('pages/saints.php') ?>">Saints</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'about' ? ' active' : '' ?>" href="<?= url('pages/about.php') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'contact' ? ' active' : '' ?>" href="<?= url('pages/contact.php') ?>">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" id="dark-mode-toggle" aria-label="Toggle dark mode">
                            <i class="bi bi-moon-stars" id="dark-mode-icon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main id="main-content">
