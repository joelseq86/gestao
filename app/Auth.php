<?php

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function isLoggedIn(): bool
    {
        self::start();
        return !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            redirect('/login');
        }
    }

    public static function attempt(string $username, string $password): bool
    {
        self::start();
        if ($username === APP_USERNAME && password_verify($password, APP_PASSWORD_HASH)) {
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function username(): string
    {
        return $_SESSION['username'] ?? '';
    }
}
