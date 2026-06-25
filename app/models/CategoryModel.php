<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * CategoryModel
 *
 * Data access for the `categories` table and the `video_category` join table
 * that links videos and categories (a many-to-many relationship).
 */
class CategoryModel
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
     * All categories, alphabetically.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, name FROM categories ORDER BY name';

        return $this->database->query($sql)->fetchAll();
    }

    /**
     * Find a single category by id, or null when it does not exist.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, name FROM categories WHERE id = :id';

        $category = $this->database->query($sql, ['id' => $id])->fetch();

        return $category === false ? null : $category;
    }

    /**
     * The categories linked to a given video.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByVideoId(int $videoId): array
    {
        $sql = 'SELECT categories.id, categories.name
                FROM categories
                INNER JOIN video_category ON video_category.category_id = categories.id
                WHERE video_category.video_id = :video_id
                ORDER BY categories.name';

        return $this->database->query($sql, ['video_id' => $videoId])->fetchAll();
    }

    /**
     * Link a video to a category (one row in the join table).
     */
    public function linkVideo(int $videoId, int $categoryId): void
    {
        $sql = 'INSERT INTO video_category (video_id, category_id)
                VALUES (:video_id, :category_id)';

        $this->database->query($sql, [
            'video_id'    => $videoId,
            'category_id' => $categoryId,
        ]);
    }
}
