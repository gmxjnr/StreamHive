<?php

require_once __DIR__ . '/../core/database.php';

class Comment
{
    public static function getByVideoId($videoId)
    {
        global $conn;

        $sql = "
            SELECT
                comments.*,
                users.username
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.video_id = :video_id
            ORDER BY comments.created_at DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}