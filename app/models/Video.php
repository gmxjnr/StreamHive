<?php

require_once __DIR__ . '/../core/database.php';

class Video
{
    public static function getAll()
    {
        global $conn;

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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        global $conn;

        $sql = "
            SELECT 
                videos.*,
                users.username
            FROM videos
            JOIN users ON videos.user_id = users.id
            WHERE videos.id = :id
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}