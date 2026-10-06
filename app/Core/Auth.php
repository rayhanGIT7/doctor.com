<?php

namespace App\Core;

use App\Models\User;

/**
 * Login state. Only the user id is stored in the session;
 * the user row is loaded fresh from the database on each request.
 */
class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];

        self::$user   = $user;
        self::$loaded = true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);

        self::$user = null;
    }

    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            $id = $_SESSION['user_id'] ?? null;

            if ($id) {
                $user = (new User())->find((int) $id);

                // A deactivated user is logged out automatically
                if ($user && $user['status'] === 'active') {
                    self::$user = $user;
                } else {
                    unset($_SESSION['user_id']);
                }
            }
        }

        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) self::user()['id'] : null;
    }

    public static function role(): ?string
    {
        return self::check() ? self::user()['role'] : null;
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }

        // Remember the page so we can come back after login
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            Session::set('intended', $_SERVER['REQUEST_URI']);
        }

        Session::flash('warning', 'Please login to continue.');
        header('Location: ' . url('login'));
        exit;
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();

        if (self::role() !== $role) {
            abort(403);
        }
    }

    // Where each role lands after login
    public static function homePath(): string
    {
        switch (self::role()) {
            case 'admin':
                return 'admin';
            case 'doctor':
                return 'doctor';
            default:
                return 'my/dashboard';
        }
    }
}
