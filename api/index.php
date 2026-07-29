<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';

if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], 'test-diag')) {
    echo "<h1>PHP Version: " . PHP_VERSION . "</h1>";
    echo "<h2>Loaded Extensions:</h2><pre>";
    print_r(get_loaded_extensions());
    echo "</pre>";
    exit;
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

try {
    define('LARAVEL_START', microtime(true));

    // Create required writable directories in /tmp for Vercel Serverless environment
    $storageDirs = [
        '/tmp/storage/app/public',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';

    putenv('APP_URL=https://eventamikom-web.vercel.app');
    $_ENV['APP_URL'] = 'https://eventamikom-web.vercel.app';
    putenv('SESSION_SECURE_COOKIE=true');
    $_ENV['SESSION_SECURE_COOKIE'] = 'true';
    if (!getenv('SESSION_DRIVER') && !isset($_ENV['SESSION_DRIVER'])) {
        putenv('SESSION_DRIVER=cookie');
        $_ENV['SESSION_DRIVER'] = 'cookie';
    }

    // Fallback env variables if missing on Vercel Dashboard
    if (!getenv('APP_KEY') && !isset($_ENV['APP_KEY'])) {
        putenv('APP_KEY=base64:C0PUHAv+7Hdc1GRUL8gkB357PfI1xMof8uqzG0PLIXM=');
        $_ENV['APP_KEY'] = 'base64:C0PUHAv+7Hdc1GRUL8gkB357PfI1xMof8uqzG0PLIXM=';
    }

    if (!getenv('GOOGLE_CLIENT_ID') && !isset($_ENV['GOOGLE_CLIENT_ID'])) {
        $gid = '825236407169-e40b75h96kiit3e58lupmrgh7ig26qsg.apps.googleusercontent.com';
        putenv("GOOGLE_CLIENT_ID={$gid}");
        $_ENV['GOOGLE_CLIENT_ID'] = $gid;
    }

    if (!getenv('GOOGLE_REDIRECT_URI') && !isset($_ENV['GOOGLE_REDIRECT_URI'])) {
        putenv('GOOGLE_REDIRECT_URI=https://eventamikom-web.vercel.app/auth/google/callback');
        $_ENV['GOOGLE_REDIRECT_URI'] = 'https://eventamikom-web.vercel.app/auth/google/callback';
    }

    if (!getenv('DB_CONNECTION') && !isset($_ENV['DB_CONNECTION'])) {
        putenv('DB_CONNECTION=pgsql');
        putenv('DB_HOST=ep-green-credit-az9nn8gq.c-3.ap-southeast-1.aws.neon.tech');
        putenv('DB_PORT=5432');
        putenv('DB_DATABASE=neondb');
        putenv('DB_USERNAME=neondb_owner');
        putenv('DB_PASSWORD=npg_2bMLhl3KfkDQ');
        putenv('DB_SSLMODE=require');
        $_ENV['DB_CONNECTION'] = 'pgsql';
        $_ENV['DB_HOST'] = 'ep-green-credit-az9nn8gq.c-3.ap-southeast-1.aws.neon.tech';
        $_ENV['DB_PORT'] = '5432';
        $_ENV['DB_DATABASE'] = 'neondb';
        $_ENV['DB_USERNAME'] = 'neondb_owner';
        $_ENV['DB_PASSWORD'] = 'npg_2bMLhl3KfkDQ';
        $_ENV['DB_SSLMODE'] = 'require';
    }

    // Register the Composer autoloader...
    require __DIR__ . '/../vendor/autoload.php';

    /** @var Application $app */
    $app = require __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath('/tmp/storage');
    $app->instance('path.storage', '/tmp/storage');

    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

    // Auto-fix PostgreSQL check constraint for roles on NeonDB
    try {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
        }
    } catch (\Throwable $ignored) {
        // Ignored if constraint already dropped or connection fails momentarily
    }

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Vercel Server Error Diagnostics</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
