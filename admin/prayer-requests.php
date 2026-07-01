<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $allowed = ['pending', 'prayed', 'archived'];

    if ($id > 0 && in_array($status, $allowed, true)) {
        $stmt = $db->prepare('UPDATE prayer_requests SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }
}

$requests = $db->query('SELECT * FROM prayer_requests ORDER BY created_at DESC LIMIT 100')->fetchAll();

$pageTitle = 'Prayer Requests';
$activeNav = 'prayer';
require __DIR__ . '/includes/layout-top.php';
?>

<h1 class="h3 mb-4">Prayer Requests</h1>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Request</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($requests === []): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No prayer requests yet.</td></tr>
                <?php else: ?>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= e($request['is_anonymous'] ? 'Anonymous' : $request['name']) ?></td>
                    <td class="small" style="max-width: 300px;"><?= e($request['request_text']) ?></td>
                    <td><span class="badge bg-<?= $request['status'] === 'pending' ? 'warning' : ($request['status'] === 'prayed' ? 'success' : 'secondary') ?>"><?= e($request['status']) ?></span></td>
                    <td class="small"><?= e($request['created_at']) ?></td>
                    <td>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $request['id'] ?>">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="pending"<?= $request['status'] === 'pending' ? ' selected' : '' ?>>Pending</option>
                                <option value="prayed"<?= $request['status'] === 'prayed' ? ' selected' : '' ?>>Prayed</option>
                                <option value="archived"<?= $request['status'] === 'archived' ? ' selected' : '' ?>>Archived</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/layout-bottom.php'; ?>
