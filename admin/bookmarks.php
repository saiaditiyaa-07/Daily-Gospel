<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

$db = Database::getConnection();
$bookmarks = $db->query('SELECT * FROM bookmarks ORDER BY created_at DESC LIMIT 100')->fetchAll();

$pageTitle = 'Bookmarks';
$activeNav = 'bookmarks';
require __DIR__ . '/includes/layout-top.php';
?>

<h1 class="h3 mb-4">Bookmarks</h1>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Session</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($bookmarks === []): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No bookmarks yet.</td></tr>
                <?php else: ?>
                <?php foreach ($bookmarks as $bookmark): ?>
                <tr>
                    <td><?= e($bookmark['reading_date']) ?></td>
                    <td><?= e($bookmark['title']) ?></td>
                    <td><code class="small"><?= e(substr($bookmark['session_id'], 0, 12)) ?>...</code></td>
                    <td><?= e($bookmark['created_at']) ?></td>
                    <td>
                        <a href="<?= url('index.php?date=' . e($bookmark['reading_date'])) ?>" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/layout-bottom.php'; ?>
