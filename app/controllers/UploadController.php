<?php

require_once __DIR__ . '/../core/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $video = $_FILES['video'];
    $thumbnail = $_FILES['thumbnail'];

    $videoName = time() . '_' . basename($video['name']);
    $thumbnailName = time() . '_' . basename($thumbnail['name']);

    $videoPath = __DIR__ . '/../../assets/uploads/' . $videoName;
    $thumbnailPath = __DIR__ . '/../../assets/uploads/thumbnails/' . $thumbnailName;

    move_uploaded_file($video['tmp_name'], $videoPath);
    move_uploaded_file($thumbnail['tmp_name'], $thumbnailPath);

    $sql = "
        INSERT INTO videos
        (
            user_id,
            title,
            description,
            filename,
            thumbnail,
            views,
            created_at
        )
        VALUES
        (
            :user_id,
            :title,
            :description,
            :filename,
            :thumbnail,
            0,
            NOW()
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':user_id' => $_SESSION['user']['id'],
        ':title' => $title,
        ':description' => $description,
        ':filename' => $videoName,
        ':thumbnail' => $thumbnailName
    ]);

    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../views/upload/upload.view.php';