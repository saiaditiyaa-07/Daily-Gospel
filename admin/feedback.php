<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

$db = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'new';
    $allowed = ['new', 'read', 'responded', 'archived'];

    if ($id > 0 && in_array($status, $allowed, true)) {
        $stmt = $db->prepare('UPDATE feedback SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }
}

$items = $db->query('SELECT * FROM feedback ORDER BY created_at DESC LIMIT 100')->fetchAll();

$pageTitle = 'Feedback';
$activeNav = 'feedback';
require __DIR__ . '/includes/layout-top.php';
?>

<h1 class="h3 mb-4">Feedback</h1>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items === []): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No feedback yet.</td></tr>
                <?php else: ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= e($item['subject']) ?></td>
                    <td class="small" style="max-width: 250px;"><?= e($item['message']) ?></td>
                    <td><?= str_repeat('★', (int) $item['rating']) ?: '—' ?></td>
                    <td><span class="badge bg-<?= $item['status'] === 'new' ? 'primary' : 'secondary' ?>"><?= e($item['status']) ?></span></td>
                    <td class="small"><?= e($item['created_at']) ?></td>
                    <td>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="new"<?= $item['status'] === 'new' ? ' selected' : '' ?>>New</option>
                                <option value="read"<?= $item['status'] === 'read' ? ' selected' : '' ?>>Read</option>
                                <option value="responded"<?= $item['status'] === 'responded' ? ' selected' : '' ?>>Responded</option>
                                <option value="archived"<?= $item['status'] === 'archived' ? ' selected' : '' ?>>Archived</option>
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
