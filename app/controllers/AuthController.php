<?php

class AuthController
{
    public function showLogin(array $params): void
    {
        if (Auth::isLoggedIn()) {
            redirect('/dashboard');
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(array $params): void
    {
        if (Auth::isLoggedIn()) {
            redirect('/dashboard');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($username, $password)) {
            redirect('/dashboard');
        }

        $error = 'Utilizador ou palavra-passe incorretos.';
        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout(array $params): void
    {
        Auth::logout();
        redirect('/login');
    }
}
