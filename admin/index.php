<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

start_app_session();

$error = '';

if (is_admin_logged_in()) {
    redirect_admin('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                login_admin($user);

                $update = $db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
                $update->execute(['id' => $user['id']]);

                redirect_admin('dashboard.php');
            }

            $error = 'Invalid username or password.';
        } catch (Throwable $e) {
            $error = 'Database connection failed. Import database.sql and configure config.php.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="<?= url('assets/images/logo.svg') ?>" alt="" width="48" height="48" class="mb-3">
                            <h1 class="h4">Admin Login</h1>
                            <p class="text-muted small"><?= e(APP_NAME) ?></p>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3"><?= e($error) ?></div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">Sign In</button>
                        </form>

                        <p class="text-muted small text-center mt-4 mb-0">
                            <a href="<?= url('index.php') ?>">&larr; Back to site</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
