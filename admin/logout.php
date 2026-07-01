<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_admin();
logout_admin();
redirect_admin('index.php');
