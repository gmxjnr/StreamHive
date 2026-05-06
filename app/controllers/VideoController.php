<?php

require_once __DIR__ . '/../models/Video.php';
require_once __DIR__ . '/../models/Comment.php';

if (!isset($_GET['id'])) {
    die('Geen video geselecteerd.');
}

$video = Video::getById($_GET['id']);

if (!$video) {
    die('Video niet gevonden.');
}

$comments = Comment::getByVideoId($_GET['id']);

require_once __DIR__ . '/../views/video.view.php';