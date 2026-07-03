<?php
/**
 * Local configuration overrides for Docker environment.
 */

declare(strict_types=1);

define('DB_HOST', 'host.docker.internal');
define('DB_NAME', 'daily_gospel');
define('DB_USER', 'root');
define('DB_PASS', 'Bangladesh07'); // or '' if root has no password