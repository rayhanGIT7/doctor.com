<?php

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function verify(): void
    {
        $token = $_POST['_csrf'] ?? '';

        if (!is_string($token) || !hash_equals(self::token(), $token)) {
            abort(419, 'Your session has expired. Please go back, refresh the page and try again.');
        }
    }
}
