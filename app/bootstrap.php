<?php

use App\Core\Session;
use App\Core\View;

define('BASE_PATH', dirname(__DIR__));

// Load App\... classes from the app/ folder
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/helpers.php';

date_default_timezone_set(config('timezone'));

// Error handling: log every error, show details only in debug mode
error_reporting(E_ALL);
ini_set('display_errors', config('debug') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/app.log');

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    // Throw away any half-rendered page
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);

    $message = config('debug')
        ? $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
        : 'Something went wrong. Please try again later.';

    View::render('errors/error', ['code' => 500, 'message' => $message], 'error');
});

Session::start();
