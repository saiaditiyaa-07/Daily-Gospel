<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
start_app_session();

/** @var string $pageTitle */
/** @var string|null $pageDescription */
/** @var string|null $currentPage */
/** @var string|null $canonicalDate */

$isTamil = Language::get() === 'ta';
$defaultTitle = $isTamil 
    ? 'இன்றைய நற்செய்தி | தமிழ் திருப்பலி வாசகங்கள்' 
    : 'Daily Gospel | Catholic Daily Readings';
$pageTitle = $pageTitle ?? $defaultTitle;
$pageDescription = $pageDescription ?? ($isTamil
    ? 'இன்றைய திருப்பலி வாசகங்கள் - முதல் வாசகம், பதிலுரைப்பாடல், இரண்டாம் வாசகம் மற்றும் நற்செய்தி வாசகம்.'
    : 'Daily Catholic Mass readings — First Reading, Psalm, Second Reading, and Gospel updated automatically for every liturgical day.');

$currentPage = $currentPage ?? 'home';
$canonicalDate = $canonicalDate ?? null;

$metaTitle = e($pageTitle);
$metaDescription = e($pageDescription);
?>
<!DOCTYPE html>
<html lang="<?= Language::get() ?>" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $metaTitle ?></title>
    <meta name="description" content="<?= $metaDescription ?>">
    <meta name="keywords" content="Catholic, Daily Gospel, Mass readings, liturgy, Bible, saints, திருவிவிலியம், திருப்பலி, நற்செய்தி, வாசகங்கள்">
    <meta name="author" content="<?= e(__('app_name')) ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?= $metaTitle ?>">
    <meta property="og:description" content="<?= $metaDescription ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url($_SERVER['REQUEST_URI'] ?? '')) ?>">
    <meta property="og:site_name" content="<?= e(__('app_name')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php if ($canonicalDate): ?>
    <meta property="article:published_time" content="<?= e($canonicalDate) ?>">
    <?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="<?= url('assets/images/favicon.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if ($isTamil): ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&family=Josefin+Sans:ital,wght@0,300..700;1,300..700&family=Manrope:wght@300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="site-body lang-<?= Language::get() ?>">
    <div id="offline-banner" class="offline-banner d-none" role="alert">
        <div class="container d-flex align-items-center justify-content-between py-2">
            <span><i class="bi bi-wifi-off me-2"></i><?= e(__('offline_message')) ?></span>
            <button type="button" class="btn-close btn-close-white" aria-label="Close" id="offline-banner-close"></button>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm" id="main-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
                <img src="<?= url('assets/images/logo.svg') ?>" alt="<?= e(__('app_name')) ?> logo" width="36" height="36" class="brand-logo">
                <span class="brand-text"><?= e(__('app_name')) ?></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'home' ? ' active' : '' ?>" href="<?= url('index.php') ?>"><?= e(__('nav_home')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'today' ? ' active' : '' ?>" href="<?= url('index.php') ?>"><?= e(__('nav_todays_readings')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'archive' ? ' active' : '' ?>" href="<?= url('pages/archive.php') ?>"><?= e(__('nav_archive')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'search' ? ' active' : '' ?>" href="<?= url('pages/search.php') ?>"><?= e(__('nav_search')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'saints' ? ' active' : '' ?>" href="<?= url('pages/saints.php') ?>"><?= e(__('nav_saints')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'about' ? ' active' : '' ?>" href="<?= url('pages/about.php') ?>"><?= e(__('nav_about')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'contact' ? ' active' : '' ?>" href="<?= url('pages/contact.php') ?>"><?= e(__('nav_contact')) ?></a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <div class="dropdown lang-switcher-dropdown d-inline-block">
                            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 dropdown-toggle" id="lang-switcher-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-translate me-1"></i><?= Language::get() === 'ta' ? 'தமிழ்' : 'English' ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="lang-switcher-btn">
                                <li><a class="dropdown-item lang-switch-link<?= Language::get() === 'en' ? ' active' : '' ?>" href="#" data-lang="en">English</a></li>
                                <li><a class="dropdown-item lang-switch-link<?= Language::get() === 'ta' ? ' active' : '' ?>" href="#" data-lang="ta">தமிழ்</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item ms-lg-1">
                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" id="dark-mode-toggle" aria-label="Toggle dark mode">
                            <i class="bi bi-moon-stars" id="dark-mode-icon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main id="main-content">
