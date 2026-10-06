<?php

namespace App\Core;

/**
 * Session wrapper. Flash messages, validation errors and old form input
 * live for exactly one request (the one after a redirect).
 */
class Session
{
    private static array $flash = [];
    private static array $errors = [];
    private static array $old = [];

    public static function start(): void
    {
        session_name('medibook_session');
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !empty($_SERVER['HTTPS']),
        ]);
        session_start();

        // Move one-time data out of the session so it is shown only once
        self::$flash  = $_SESSION['_flash'] ?? [];
        self::$errors = $_SESSION['_errors'] ?? [];
        self::$old    = $_SESSION['_old'] ?? [];
        unset($_SESSION['_flash'], $_SESSION['_errors'], $_SESSION['_old']);
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // $type is a Bootstrap alert type: success, danger, warning, info
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    public static function withErrors(array $errors, array $old = []): void
    {
        // Never keep passwords in the session
        unset($old['password'], $old['password_confirmation'], $old['current_password'], $old['_csrf']);

        $_SESSION['_errors'] = $errors;
        $_SESSION['_old']    = $old;
    }

    public static function flashes(): array
    {
        return self::$flash;
    }

    public static function errors(): array
    {
        return self::$errors;
    }

    public static function old(): array
    {
        return self::$old;
    }
}
