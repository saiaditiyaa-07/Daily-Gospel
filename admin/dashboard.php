<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

$admin = get_admin_user();
$db = Database::getConnection();

$stats = [
    'bookmarks' => 0,
    'prayer_pending' => 0,
    'feedback_new' => 0,
];

try {
    $stats['bookmarks'] = (int) $db->query('SELECT COUNT(*) FROM bookmarks')->fetchColumn();
    $stats['prayer_pending'] = (int) $db->query("SELECT COUNT(*) FROM prayer_requests WHERE status = 'pending'")->fetchColumn();
    $stats['feedback_new'] = (int) $db->query("SELECT COUNT(*) FROM feedback WHERE status = 'new'")->fetchColumn();
} catch (Throwable $e) {
    // Stats unavailable
}

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/layout-top.php';
?>

<h1 class="h3 mb-4">Dashboard</h1>
<p class="text-muted">Welcome back, <?= e($admin['username'] ?? 'Admin') ?>.</p>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 admin-stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Bookmarks</p>
                        <h2 class="h3 mb-0"><?= $stats['bookmarks'] ?></h2>
                    </div>
                    <i class="bi bi-bookmark fs-2 text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 admin-stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Pending Prayers</p>
                        <h2 class="h3 mb-0"><?= $stats['prayer_pending'] ?></h2>
                    </div>
                    <i class="bi bi-heart fs-2 text-danger"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 admin-stat-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">New Feedback</p>
                        <h2 class="h3 mb-0"><?= $stats['feedback_new'] ?></h2>
                    </div>
                    <i class="bi bi-chat-dots fs-2 text-success"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3">Quick Actions</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= url('admin/prayer-requests.php') ?>" class="btn btn-outline-primary rounded-pill">View Prayer Requests</a>
            <a href="<?= url('admin/feedback.php') ?>" class="btn btn-outline-primary rounded-pill">View Feedback</a>
            <a href="<?= url('admin/bookmarks.php') ?>" class="btn btn-outline-primary rounded-pill">View Bookmarks</a>
            <a href="<?= url('admin/settings.php') ?>" class="btn btn-outline-secondary rounded-pill">Settings</a>
            <a href="<?= url('index.php') ?>" class="btn btn-primary rounded-pill">View Site</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/layout-bottom.php'; ?>
