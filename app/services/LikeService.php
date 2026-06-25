<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/LikeModel.php';

/**
 * LikeService
 *
 * Business logic for likes. Toggling is the core rule: if the user already
 * liked the item the like is removed, otherwise it is added. Because we check
 * for an existing like first, a user can never have two likes on the same
 * video or comment.
 */
class LikeService
{
    /**
     * Data access for the likes table.
     */
    private LikeModel $likeModel;

    public function __construct(?LikeModel $likeModel = null)
    {
        $this->likeModel = $likeModel ?? new LikeModel();
    }

    /**
     * Toggle the current user's like on a video.
     *
     * @return array{liked: bool, count: int}
     */
    public function toggleVideoLike(int $userId, int $videoId): array
    {
        $existing = $this->likeModel->findForVideo($userId, $videoId);

        if ($existing !== null)
        {
            $this->likeModel->deleteById((int) $existing['id']);
            $liked = false;
        }
        else
        {
            $this->likeModel->createForVideo($userId, $videoId);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'count' => $this->likeModel->countForVideo($videoId),
        ];
    }

    /**
     * Toggle the current user's like on a comment.
     *
     * @return array{liked: bool, count: int}
     */
    public function toggleCommentLike(int $userId, int $commentId): array
    {
        $existing = $this->likeModel->findForComment($userId, $commentId);

        if ($existing !== null)
        {
            $this->likeModel->deleteById((int) $existing['id']);
            $liked = false;
        }
        else
        {
            $this->likeModel->createForComment($userId, $commentId);
            $liked = true;
        }

        return [
            'liked' => $liked,
            'count' => $this->likeModel->countForComment($commentId),
        ];
    }

    /**
     * Like count for a video and whether the given user liked it. Used to render
     * the like button on the detail page.
     *
     * @return array{count: int, liked: bool}
     */
    public function videoLikeInfo(int $videoId, ?int $userId): array
    {
        return [
            'count' => $this->likeModel->countForVideo($videoId),
            'liked' => $userId !== null && $this->likeModel->findForVideo($userId, $videoId) !== null,
        ];
    }
}
