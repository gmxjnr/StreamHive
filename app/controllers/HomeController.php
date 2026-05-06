<?php

require_once __DIR__ . '/../models/Video.php';

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $videos = Video::search($search);
} else {
    $videos = Video::getAll();
}

require_once __DIR__ . '/../views/home.view.php';