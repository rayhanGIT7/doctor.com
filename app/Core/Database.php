<?php

namespace App\Core;

use PDO;

class Database
{
    private static ?PDO $connection = null;

    // Returns one shared PDO connection for the whole request
    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $db  = config('db');
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";

            self::$connection = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$connection;
    }
}
