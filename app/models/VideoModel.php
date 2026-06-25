<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * VideoModel
 *
 * Data access for the `videos` table. Week 2 covers the basic CRUD that the
 * other layers need first: reading and creating. The JOIN with users, search
 * and the view counter are added in later weeks, as planned in the README.
 */
class VideoModel
{
    /**
     * Shared database connection wrapper.
     */
    private Database $database;

    public function __construct(?Database $database = null)
    {
        $this->database = $database ?? Database::getInstance();
    }

    /**
     * Find a single video by id, or null when it does not exist.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, user_id, title, description, filename, views, created_at
                FROM videos
                WHERE id = :id';

        $video = $this->database->query($sql, ['id' => $id])->fetch();

        return $video === false ? null : $video;
    }

    /**
     * Return all videos, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, user_id, title, description, filename, views, created_at
                FROM videos
                ORDER BY created_at DESC, id DESC';

        return $this->database->query($sql)->fetchAll();
    }

    /**
     * Insert a new video and return the generated id.
     *
     * Expected keys: user_id, title, filename, description (optional).
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO videos (user_id, title, description, filename)
                VALUES (:user_id, :title, :description, :filename)';

        $this->database->query($sql, [
            'user_id'     => $data['user_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'filename'    => $data['filename'],
        ]);

        return $this->database->lastInsertId();
    }
}
