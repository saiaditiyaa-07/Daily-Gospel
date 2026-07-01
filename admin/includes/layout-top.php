<?php
/** @var string $pageTitle */
/** @var string $activeNav */
$pageTitle = $pageTitle ?? 'Admin';
$activeNav = $activeNav ?? 'dashboard';
$admin = get_admin_user();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Admin — <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('admin/dashboard.php') ?>">
                <i class="bi bi-shield-lock me-2"></i><?= e(APP_NAME) ?> Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small"><?= e($admin['username'] ?? '') ?></span>
                <a href="<?= url('admin/logout.php') ?>" class="btn btn-outline-light btn-sm rounded-pill">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 admin-sidebar py-4">
                <ul class="nav flex-column gap-1 px-2">
                    <li class="nav-item"><a class="nav-link rounded<?= $activeNav === 'dashboard' ? ' active bg-primary text-white' : '' ?>" href="<?= url('admin/dashboard.php') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link rounded<?= $activeNav === 'bookmarks' ? ' active bg-primary text-white' : '' ?>" href="<?= url('admin/bookmarks.php') ?>"><i class="bi bi-bookmark me-2"></i>Bookmarks</a></li>
                    <li class="nav-item"><a class="nav-link rounded<?= $activeNav === 'prayer' ? ' active bg-primary text-white' : '' ?>" href="<?= url('admin/prayer-requests.php') ?>"><i class="bi bi-heart me-2"></i>Prayer Requests</a></li>
                    <li class="nav-item"><a class="nav-link rounded<?= $activeNav === 'feedback' ? ' active bg-primary text-white' : '' ?>" href="<?= url('admin/feedback.php') ?>"><i class="bi bi-chat-dots me-2"></i>Feedback</a></li>
                    <li class="nav-item"><a class="nav-link rounded<?= $activeNav === 'settings' ? ' active bg-primary text-white' : '' ?>" href="<?= url('admin/settings.php') ?>"><i class="bi bi-gear me-2"></i>Settings</a></li>
                </ul>
            </nav>
            <main class="col-md-9 col-lg-10 py-4 px-4">
