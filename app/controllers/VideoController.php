<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';
require_once dirname(__DIR__, 2) . '/app/services/VideoService.php';
require_once dirname(__DIR__, 2) . '/app/services/CommentService.php';
require_once dirname(__DIR__, 2) . '/app/services/LikeService.php';
require_once dirname(__DIR__, 2) . '/app/services/AuthService.php';

/**
 * VideoController
 *
 * Handles the video overview (with search and category filtering), the detail
 * page (with its view counter, comments and likes), and uploading and deleting
 * videos. It reads the request, asks AuthService whether the visitor is allowed
 * to perform the action, and delegates the work to the services. No SQL here.
 */
class VideoController extends Controller
{
    private VideoService $videoService;
    private CommentService $commentService;
    private LikeService $likeService;
    private AuthService $authService;

    public function __construct(
        ?VideoService $videoService = null,
        ?CommentService $commentService = null,
        ?LikeService $likeService = null,
        ?AuthService $authService = null
    ) {
        $this->videoService = $videoService ?? new VideoService();
        $this->commentService = $commentService ?? new CommentService();
        $this->likeService = $likeService ?? new LikeService();
        $this->authService = $authService ?? new AuthService();
    }

    /**
     * Show the video overview / home page (GET /). Supports an optional search
     * term (?search=) and category filter (?category=).
     */
    public function index(): void
    {
        $search = isset($_GET['search']) ? (string) $_GET['search'] : '';
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;

        $activeCategory = $categoryId !== null ? $this->videoService->getCategory($categoryId) : null;

        $this->render('videos/index', [
            'title'            => 'StreamHive — Videos',
            'videos'           => $this->videoService->getOverview($search, $categoryId),
            'categories'       => $this->videoService->getCategories(),
            'search'           => $search,
            'activeCategoryId' => $categoryId,
            'activeCategory'   => $activeCategory,
        ]);
    }

    /**
     * Show a single video with its comments and like state, and count one view
     * (GET /videos/show?id=...).
     */
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        // Count the visit before reading, so the displayed count includes it.
        $this->videoService->recordView($id);
        $video = $this->videoService->getWithUploader($id);

        if ($video === null)
        {
            http_response_code(404);
            $this->render('videos/not-found', ['title' => 'StreamHive — Video not found']);
            return;
        }

        $currentUser = $this->authService->getCurrentUser();
        $currentUserId = $currentUser !== null ? (int) $currentUser['id'] : null;

        $this->render('videos/show', [
            'title'      => $video['title'] . ' — StreamHive',
            'video'      => $video,
            'categories' => $this->videoService->getVideoCategories($id),
            'comments'   => $this->commentService->getComments($id, $currentUserId),
            'videoLike'  => $this->likeService->videoLikeInfo($id, $currentUserId),
        ]);
    }

    /**
     * Show the upload form (GET /videos/upload). Login required.
     */
    public function create(): void
    {
        $this->requireLogin();

        $this->render('videos/upload', [
            'title'              => 'StreamHive — Upload',
            'errors'             => [],
            'titleValue'         => '',
            'description'        => '',
            'categories'         => $this->videoService->getCategories(),
            'selectedCategories' => [],
        ]);
    }

    /**
     * Process the upload form (POST /videos/upload). Login required.
     */
    public function store(): void
    {
        $this->requireLogin();

        $currentUser = $this->authService->getCurrentUser();
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $file = $_FILES['video'] ?? [];
        $categoryIds = isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] : [];

        $result = $this->videoService->upload((int) $currentUser['id'], $title, $description, $file, $categoryIds);

        if ($result['errors'] === [])
        {
            $this->redirect('/videos/show?id=' . $result['videoId']);
        }

        $this->render('videos/upload', [
            'title'              => 'StreamHive — Upload',
            'errors'             => $result['errors'],
            'titleValue'         => $title,
            'description'        => $description,
            'categories'         => $this->videoService->getCategories(),
            'selectedCategories' => array_map('intval', $categoryIds),
        ]);
    }

    /**
     * Delete a video (POST /videos/delete). Only the owner or an admin may do
     * this; the service enforces that rule.
     */
    public function delete(): void
    {
        $this->requireLogin();

        $id = (int) ($_POST['id'] ?? 0);
        $currentUser = $this->authService->getCurrentUser();

        $this->videoService->delete($id, (int) $currentUser['id'], $this->authService->isAdmin());

        $this->redirect('/');
    }
}
