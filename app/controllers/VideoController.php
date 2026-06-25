<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';
require_once dirname(__DIR__, 2) . '/app/services/VideoService.php';
require_once dirname(__DIR__, 2) . '/app/services/AuthService.php';

/**
 * VideoController
 *
 * Handles the video overview, the detail page, and uploading and deleting
 * videos. It reads the request, asks AuthService whether the visitor is allowed
 * to perform the action, and delegates the work to VideoService. No SQL here.
 */
class VideoController extends Controller
{
    private VideoService $videoService;
    private AuthService $authService;

    public function __construct(?VideoService $videoService = null, ?AuthService $authService = null)
    {
        $this->videoService = $videoService ?? new VideoService();
        $this->authService = $authService ?? new AuthService();
    }

    /**
     * Show the video overview / home page (GET /). Uses the JOIN data so each
     * video shows its uploader.
     */
    public function index(): void
    {
        $this->render('videos/index', [
            'title'  => 'StreamHive — Videos',
            'videos' => $this->videoService->getAllWithUploader(),
        ]);
    }

    /**
     * Show a single video (GET /videos/show?id=...).
     */
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $video = $this->videoService->getWithUploader($id);

        if ($video === null)
        {
            http_response_code(404);
            $this->render('videos/not-found', ['title' => 'StreamHive — Video not found']);
            return;
        }

        $this->render('videos/show', [
            'title' => $video['title'] . ' — StreamHive',
            'video' => $video,
        ]);
    }

    /**
     * Show the upload form (GET /videos/upload). Login required.
     */
    public function create(): void
    {
        $this->requireLogin();

        $this->render('videos/upload', [
            'title'       => 'StreamHive — Upload',
            'errors'      => [],
            'titleValue'  => '',
            'description' => '',
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

        $result = $this->videoService->upload((int) $currentUser['id'], $title, $description, $file);

        if ($result['errors'] === [])
        {
            $this->redirect('/videos/show?id=' . $result['videoId']);
        }

        $this->render('videos/upload', [
            'title'       => 'StreamHive — Upload',
            'errors'      => $result['errors'],
            'titleValue'  => $title,
            'description' => $description,
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

    /**
     * Redirect guests to the login page before a protected action.
     */
    private function requireLogin(): void
    {
        if (!$this->authService->isLoggedIn())
        {
            $this->redirect('/login');
        }
    }
}
