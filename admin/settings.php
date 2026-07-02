<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();

$db = Database::getConnection();
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validate_csrf($_POST['csrf_token'] ?? null)) {
    $settings = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_tagline' => trim($_POST['site_tagline'] ?? ''),
        'universalis_region' => trim($_POST['universalis_region'] ?? ''),
        'calendar_id' => trim($_POST['calendar_id'] ?? 'default'),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'default_language' => trim($_POST['default_language'] ?? 'en'),
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
    ];

    $stmt = $db->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );

    foreach ($settings as $key => $value) {
        $stmt->execute(['key' => $key, 'value' => $value]);
    }

    $message = 'Settings saved successfully.';
}

$rows = $db->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
$settingsMap = [];
foreach ($rows as $row) {
    $settingsMap[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Settings';
$activeNav = 'settings';
require __DIR__ . '/includes/layout-top.php';
?>

<h1 class="h3 mb-4">Settings</h1>

<?php if ($message): ?>
<div class="alert alert-<?= e($messageType) ?> rounded-3"><?= e($message) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Site Name</label>
                    <input type="text" class="form-control" name="site_name" value="<?= e($settingsMap['site_name'] ?? APP_NAME) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Site Tagline</label>
                    <input type="text" class="form-control" name="site_tagline" value="<?= e($settingsMap['site_tagline'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Email</label>
                    <input type="email" class="form-control" name="contact_email" value="<?= e($settingsMap['contact_email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Universalis Region</label>
                    <input type="text" class="form-control" name="universalis_region" value="<?= e($settingsMap['universalis_region'] ?? '') ?>" placeholder="e.g. Europe.England.Southwark">
                    <div class="form-text">Leave empty for General Roman Calendar.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Liturgical Calendar ID</label>
                    <input type="text" class="form-control" name="calendar_id" value="<?= e($settingsMap['calendar_id'] ?? 'default') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Default Language</label>
                    <select class="form-select" name="default_language">
                        <option value="en"<?= ($settingsMap['default_language'] ?? 'en') === 'en' ? ' selected' : '' ?>>English</option>
                        <option value="ta"<?= ($settingsMap['default_language'] ?? 'en') === 'ta' ? ' selected' : '' ?>>தமிழ் (Tamil)</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="maintenance_mode" id="maintenance_mode" value="1"<?= ($settingsMap['maintenance_mode'] ?? '0') === '1' ? ' checked' : '' ?>>
                        <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<div class="alert alert-info rounded-4 mt-4">
    <strong>Note:</strong> Mass readings are never stored in the database. They are fetched dynamically from Universalis and the Church Calendar API on each request.
</div>

<?php require __DIR__ . '/includes/layout-bottom.php'; ?>
