<?php
session_start();
define('USUARIOS_LOGIN', __DIR__ . '/usuarios.json');

// Login
function login(string $email, string $password): bool {
    $usuarios = json_decode(file_get_contents(USUARIOS_LOGIN), true);
    foreach ($usuarios as $u) {
        if ($u['email'] === $email && $u['password'] === $password) {
            $_SESSION['email'] = $email;
            $_SESSION['role']  = $u['role'];
            return true;
        }
    }
    return false;
}

// Logout
function logout(): void {
    session_destroy();
}

// Comprueba si está logueado
function require_login(): void {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit;
    }
}

// Devuelve info del usuario
function me(): ?array {
    return $_SESSION['email'] ?? null ? [
        'email' => $_SESSION['email'],
        'role'  => $_SESSION['role']
    ] : null;
}
