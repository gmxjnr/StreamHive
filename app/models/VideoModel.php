<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * VideoModel
 *
 * Data access for the `videos` table. This is the only layer that runs SQL
 * against that table, and every statement is prepared. The *WithUser methods
 * JOIN the `users` table so the overview and detail pages can show the name of
 * the uploader without a second query.
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
     * Find a single video together with its uploader's username, or null.
     *
     * @return array<string, mixed>|null
     */
    public function findByIdWithUser(int $id): ?array
    {
        $sql = 'SELECT videos.id, videos.user_id, videos.title, videos.description,
                       videos.filename, videos.views, videos.created_at,
                       users.username AS uploader
                FROM videos
                INNER JOIN users ON users.id = videos.user_id
                WHERE videos.id = :id';

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
     * Return all videos together with their uploader's username, newest first.
     * Uses a JOIN so the overview shows who uploaded each video.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAllWithUser(): array
    {
        $sql = 'SELECT videos.id, videos.title, videos.description, videos.filename,
                       videos.views, videos.created_at,
                       users.username AS uploader
                FROM videos
                INNER JOIN users ON users.id = videos.user_id
                ORDER BY videos.created_at DESC, videos.id DESC';

        return $this->database->query($sql)->fetchAll();
    }

    /**
     * Search videos by title or description (case-insensitive LIKE), with the
     * uploader's username. Two separate placeholders are used because native
     * prepared statements do not allow reusing one placeholder.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $term): array
    {
        $sql = 'SELECT videos.id, videos.title, videos.description, videos.filename,
                       videos.views, videos.created_at,
                       users.username AS uploader
                FROM videos
                INNER JOIN users ON users.id = videos.user_id
                WHERE videos.title LIKE :title_term
                   OR videos.description LIKE :description_term
                ORDER BY videos.created_at DESC, videos.id DESC';

        $like = '%' . $term . '%';

        return $this->database->query($sql, [
            'title_term'       => $like,
            'description_term' => $like,
        ])->fetchAll();
    }

    /**
     * Return all videos in a category, with the uploader's username.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByCategory(int $categoryId): array
    {
        $sql = 'SELECT videos.id, videos.title, videos.description, videos.filename,
                       videos.views, videos.created_at,
                       users.username AS uploader
                FROM videos
                INNER JOIN users ON users.id = videos.user_id
                INNER JOIN video_category ON video_category.video_id = videos.id
                WHERE video_category.category_id = :category_id
                ORDER BY videos.created_at DESC, videos.id DESC';

        return $this->database->query($sql, ['category_id' => $categoryId])->fetchAll();
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

    /**
     * Update the title and description of a video.
     *
     * @param array<string, mixed> $data Expected keys: title, description.
     */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE videos
                SET title = :title, description = :description
                WHERE id = :id';

        $statement = $this->database->query($sql, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'id'          => $id,
        ]);

        return $statement->rowCount() > 0;
    }

    /**
     * Increase the view count of a video by one.
     */
    public function incrementViews(int $id): void
    {
        $sql = 'UPDATE videos SET views = views + 1 WHERE id = :id';

        $this->database->query($sql, ['id' => $id]);
    }

    /**
     * Delete a video by id. Comments, likes and category links are removed
     * automatically through the ON DELETE CASCADE foreign keys.
     */
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM videos WHERE id = :id';

        $statement = $this->database->query($sql, ['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
