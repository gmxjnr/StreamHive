<?php

require_once __DIR__ . '/../models/Video.php';

$videos = Video::getAll();

require_once __DIR__ . '/../views/home.view.php';