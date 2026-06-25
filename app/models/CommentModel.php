<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * CommentModel
 *
 * Data access for the `comments` table. findByVideoId() JOINs the `users` table
 * so each comment carries its author's username, and uses two small correlated
 * subqueries to attach the like count and whether the current user liked it,
 * avoiding a separate query per comment.
 */
class CommentModel
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
     * All comments on a video, newest first, with author name and like info.
     *
     * @param int $currentUserId Logged-in user id, or 0 for a guest.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByVideoId(int $videoId, int $currentUserId = 0): array
    {
        $sql = 'SELECT comments.id, comments.user_id, comments.content, comments.created_at,
                       users.username AS author,
                       (SELECT COUNT(*) FROM likes
                        WHERE likes.comment_id = comments.id) AS like_count,
                       (SELECT COUNT(*) FROM likes
                        WHERE likes.comment_id = comments.id
                          AND likes.user_id = :current_user_id) AS liked_by_me
                FROM comments
                INNER JOIN users ON users.id = comments.user_id
                WHERE comments.video_id = :video_id
                ORDER BY comments.created_at DESC, comments.id DESC';

        return $this->database->query($sql, [
            'video_id'        => $videoId,
            'current_user_id' => $currentUserId,
        ])->fetchAll();
    }

    /**
     * Find a single comment by id (used to check ownership before deleting).
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, user_id, video_id, content, created_at
                FROM comments
                WHERE id = :id';

        $comment = $this->database->query($sql, ['id' => $id])->fetch();

        return $comment === false ? null : $comment;
    }

    /**
     * Insert a new comment and return the generated id.
     *
     * Expected keys: user_id, video_id, content.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO comments (user_id, video_id, content)
                VALUES (:user_id, :video_id, :content)';

        $this->database->query($sql, [
            'user_id'  => $data['user_id'],
            'video_id' => $data['video_id'],
            'content'  => $data['content'],
        ]);

        return $this->database->lastInsertId();
    }

    /**
     * Delete a comment by id. Its likes are removed through ON DELETE CASCADE.
     */
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM comments WHERE id = :id';

        $statement = $this->database->query($sql, ['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
