<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';
require_once dirname(__DIR__, 2) . '/app/services/CommentService.php';
require_once dirname(__DIR__, 2) . '/app/services/AuthService.php';

/**
 * CommentController
 *
 * Handles posting and deleting comments. Both actions require a logged-in user
 * and redirect back to the video detail page afterwards (Post/Redirect/Get).
 */
class CommentController extends Controller
{
    private CommentService $commentService;
    private AuthService $authService;

    public function __construct(?CommentService $commentService = null, ?AuthService $authService = null)
    {
        $this->commentService = $commentService ?? new CommentService();
        $this->authService = $authService ?? new AuthService();
    }

    /**
     * Store a new comment (POST /comments/store).
     */
    public function store(): void
    {
        $this->requireLogin();

        $videoId = (int) ($_POST['video_id'] ?? 0);
        $content = $_POST['content'] ?? '';
        $currentUser = $this->authService->getCurrentUser();

        $this->commentService->addComment((int) $currentUser['id'], $videoId, $content);

        $this->redirect('/videos/show?id=' . $videoId);
    }

    /**
     * Delete a comment (POST /comments/delete). Only the author or an admin may
     * do this; the service enforces that rule.
     */
    public function delete(): void
    {
        $this->requireLogin();

        $commentId = (int) ($_POST['comment_id'] ?? 0);
        $videoId = (int) ($_POST['video_id'] ?? 0);
        $currentUser = $this->authService->getCurrentUser();

        $this->commentService->deleteComment($commentId, (int) $currentUser['id'], $this->authService->isAdmin());

        $this->redirect('/videos/show?id=' . $videoId);
    }
}
