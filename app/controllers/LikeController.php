<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';
require_once dirname(__DIR__, 2) . '/app/services/LikeService.php';
require_once dirname(__DIR__, 2) . '/app/services/AuthService.php';

/**
 * LikeController
 *
 * Handles liking/unliking a video or a comment. Each action toggles the like
 * through LikeService and redirects back to the video detail page. Liking
 * requires a logged-in user.
 */
class LikeController extends Controller
{
    private LikeService $likeService;
    private AuthService $authService;

    public function __construct(?LikeService $likeService = null, ?AuthService $authService = null)
    {
        $this->likeService = $likeService ?? new LikeService();
        $this->authService = $authService ?? new AuthService();
    }

    /**
     * Toggle a like on a video (POST /likes/video).
     */
    public function toggleVideo(): void
    {
        $this->requireLogin();

        $videoId = (int) ($_POST['video_id'] ?? 0);
        $currentUser = $this->authService->getCurrentUser();

        $this->likeService->toggleVideoLike((int) $currentUser['id'], $videoId);

        $this->redirect('/videos/show?id=' . $videoId);
    }

    /**
     * Toggle a like on a comment (POST /likes/comment).
     */
    public function toggleComment(): void
    {
        $this->requireLogin();

        $commentId = (int) ($_POST['comment_id'] ?? 0);
        $videoId = (int) ($_POST['video_id'] ?? 0);
        $currentUser = $this->authService->getCurrentUser();

        $this->likeService->toggleCommentLike((int) $currentUser['id'], $commentId);

        $this->redirect('/videos/show?id=' . $videoId);
    }
}
