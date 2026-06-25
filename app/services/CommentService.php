<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/CommentModel.php';

/**
 * CommentService
 *
 * Business logic for comments: validating new comments, fetching the comments
 * for a video (with author and like info), and enforcing who may delete a
 * comment. The controller checks that the visitor is logged in; this service
 * checks the content and the ownership rules.
 */
class CommentService
{
    /**
     * Largest comment we accept.
     */
    private const MAX_LENGTH = 2000;

    /**
     * Data access for the comments table.
     */
    private CommentModel $commentModel;

    public function __construct(?CommentModel $commentModel = null)
    {
        $this->commentModel = $commentModel ?? new CommentModel();
    }

    /**
     * All comments on a video, with author name and like info.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getComments(int $videoId, ?int $currentUserId = null): array
    {
        return $this->commentModel->findByVideoId($videoId, $currentUserId ?? 0);
    }

    /**
     * Validate and store a new comment. Returns a list of validation errors;
     * an empty list means the comment was saved.
     *
     * @return array<int, string>
     */
    public function addComment(int $userId, int $videoId, string $content): array
    {
        $content = trim($content);

        if ($content === '')
        {
            return ['Comment cannot be empty.'];
        }

        if (mb_strlen($content) > self::MAX_LENGTH)
        {
            return ['Comment is too long (max ' . self::MAX_LENGTH . ' characters).'];
        }

        $this->commentModel->create([
            'user_id'  => $userId,
            'video_id' => $videoId,
            'content'  => $content,
        ]);

        return [];
    }

    /**
     * Delete a comment, but only when the requester wrote it or is an admin.
     */
    public function deleteComment(int $commentId, int $userId, bool $isAdmin): bool
    {
        $comment = $this->commentModel->findById($commentId);

        if ($comment === null)
        {
            return false;
        }

        if (!$isAdmin && (int) $comment['user_id'] !== $userId)
        {
            return false;
        }

        return $this->commentModel->delete($commentId);
    }
}
