<?php

require_once __DIR__ . '/../core/db.php';

$sql = "
    SELECT 
        videos.*,
        users.username
    FROM videos
    JOIN users ON videos.user_id = users.id
    ORDER BY videos.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);