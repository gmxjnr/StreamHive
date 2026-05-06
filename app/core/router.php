<?php

session_start();

$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'video':
        require_once __DIR__ . '/../controllers/VideoController.php';
        break;

    case 'login':
    case 'register':
    case 'logout':
        require_once __DIR__ . '/../controllers/AuthController.php';
        break;

    case 'upload':
        require_once __DIR__ . '/../controllers/UploadController.php';
        break;

    default:
        require_once __DIR__ . '/../controllers/HomeController.php';
        break;
}