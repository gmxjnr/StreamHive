<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * LikeModel
 *
 * Data access for the `likes` table. A like belongs to a user and points at
 * either a video or a comment. The find* methods let the service check whether
 * a like already exists, which is how duplicate likes are prevented.
 */
class LikeModel
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
     * Find this user's like on a video, or null when there is none.
     *
     * @return array<string, mixed>|null
     */
    public function findForVideo(int $userId, int $videoId): ?array
    {
        $sql = 'SELECT id FROM likes
                WHERE user_id = :user_id AND video_id = :video_id';

        $like = $this->database->query($sql, [
            'user_id'  => $userId,
            'video_id' => $videoId,
        ])->fetch();

        return $like === false ? null : $like;
    }

    /**
     * Find this user's like on a comment, or null when there is none.
     *
     * @return array<string, mixed>|null
     */
    public function findForComment(int $userId, int $commentId): ?array
    {
        $sql = 'SELECT id FROM likes
                WHERE user_id = :user_id AND comment_id = :comment_id';

        $like = $this->database->query($sql, [
            'user_id'    => $userId,
            'comment_id' => $commentId,
        ])->fetch();

        return $like === false ? null : $like;
    }

    /**
     * Insert a like on a video and return the generated id.
     */
    public function createForVideo(int $userId, int $videoId): int
    {
        $sql = 'INSERT INTO likes (user_id, video_id) VALUES (:user_id, :video_id)';

        $this->database->query($sql, [
            'user_id'  => $userId,
            'video_id' => $videoId,
        ]);

        return $this->database->lastInsertId();
    }

    /**
     * Insert a like on a comment and return the generated id.
     */
    public function createForComment(int $userId, int $commentId): int
    {
        $sql = 'INSERT INTO likes (user_id, comment_id) VALUES (:user_id, :comment_id)';

        $this->database->query($sql, [
            'user_id'    => $userId,
            'comment_id' => $commentId,
        ]);

        return $this->database->lastInsertId();
    }

    /**
     * Delete a like by id.
     */
    public function deleteById(int $id): bool
    {
        $sql = 'DELETE FROM likes WHERE id = :id';

        $statement = $this->database->query($sql, ['id' => $id]);

        return $statement->rowCount() > 0;
    }

    /**
     * Count how many likes a video has.
     */
    public function countForVideo(int $videoId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM likes WHERE video_id = :video_id';

        $row = $this->database->query($sql, ['video_id' => $videoId])->fetch();

        return (int) $row['total'];
    }

    /**
     * Count how many likes a comment has.
     */
    public function countForComment(int $commentId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM likes WHERE comment_id = :comment_id';

        $row = $this->database->query($sql, ['comment_id' => $commentId])->fetch();

        return (int) $row['total'];
    }
}
