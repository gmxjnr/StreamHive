<?php

require_once __DIR__ . '/../models/User.php';

$page = $_GET['page'] ?? 'login';
$error = '';

if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($page === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $existingUser = User::findByEmail($email);

        if ($existingUser) {
            $error = 'Dit e-mailadres is al in gebruik.';
        } else {
            User::create($username, $email, $password);

            header('Location: index.php?page=login');
            exit;
        }
    }

    require_once __DIR__ . '/../views/auth/register.view.php';
    exit;
}

if ($page === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            header('Location: index.php');
            exit;
        }

        $error = 'Ongeldige login gegevens.';
    }

    require_once __DIR__ . '/../views/auth/login.view.php';
    exit;
}