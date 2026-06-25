<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/VideoModel.php';
require_once dirname(__DIR__, 2) . '/app/services/CategoryService.php';

/**
 * VideoService
 *
 * Business logic for videos: building the (optionally filtered) overview,
 * validating uploads, storing the uploaded file safely on disk, recording a
 * view, assigning categories, and enforcing who may delete a video. The
 * controller calls this service; the service calls the models. No SQL here.
 */
class VideoService
{
    /**
     * Video file extensions the platform accepts.
     */
    private const ALLOWED_EXTENSIONS = ['mp4', 'webm', 'ogg'];

    /**
     * Data access for the videos table.
     */
    private VideoModel $videoModel;

    /**
     * Used to assign and read a video's categories.
     */
    private CategoryService $categoryService;

    public function __construct(?VideoModel $videoModel = null, ?CategoryService $categoryService = null)
    {
        $this->videoModel = $videoModel ?? new VideoModel();
        $this->categoryService = $categoryService ?? new CategoryService();
    }

    /**
     * Videos for the overview, optionally filtered by a search term or a
     * category. Search takes priority over the category filter.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOverview(?string $search, ?int $categoryId): array
    {
        if ($search !== null && trim($search) !== '')
        {
            return $this->videoModel->search(trim($search));
        }

        if ($categoryId !== null && $categoryId > 0)
        {
            return $this->videoModel->findByCategory($categoryId);
        }

        return $this->videoModel->findAllWithUser();
    }

    /**
     * A single video with its uploader, for the detail page.
     *
     * @return array<string, mixed>|null
     */
    public function getWithUploader(int $id): ?array
    {
        return $this->videoModel->findByIdWithUser($id);
    }

    /**
     * All categories, for the overview filter and the upload form.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCategories(): array
    {
        return $this->categoryService->getAllCategories();
    }

    /**
     * A single category by id (used to label a filtered overview).
     *
     * @return array<string, mixed>|null
     */
    public function getCategory(int $id): ?array
    {
        return $this->categoryService->getCategory($id);
    }

    /**
     * The categories of a single video, for the detail page.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getVideoCategories(int $videoId): array
    {
        return $this->categoryService->getForVideo($videoId);
    }

    /**
     * Count one view for a video.
     */
    public function recordView(int $id): void
    {
        $this->videoModel->incrementViews($id);
    }

    /**
     * Validate and store an uploaded video, then link the chosen categories.
     *
     * @param array<string, mixed> $file        One entry from $_FILES.
     * @param array<int, mixed>    $categoryIds Selected category ids.
     *
     * @return array{errors: array<int, string>, videoId: int|null}
     */
    public function upload(int $userId, string $title, string $description, array $file, array $categoryIds = []): array
    {
        $title = trim($title);
        $errors = $this->validateUpload($title, $file);

        if ($errors !== [])
        {
            return ['errors' => $errors, 'videoId' => null];
        }

        $filename = $this->storeFile($file);

        if ($filename === null)
        {
            return ['errors' => ['Saving the uploaded file failed. Please try again.'], 'videoId' => null];
        }

        $videoId = $this->videoModel->create([
            'user_id'     => $userId,
            'title'       => $title,
            'description' => $description,
            'filename'    => $filename,
        ]);

        $this->categoryService->assignCategories($videoId, $categoryIds);

        return ['errors' => [], 'videoId' => $videoId];
    }

    /**
     * Delete a video, but only when the requester owns it or is an admin.
     * Also removes the file from disk. Returns true when something was deleted.
     */
    public function delete(int $id, int $userId, bool $isAdmin): bool
    {
        $video = $this->videoModel->findById($id);

        if ($video === null)
        {
            return false;
        }

        if (!$isAdmin && (int) $video['user_id'] !== $userId)
        {
            return false;
        }

        $this->deleteFile((string) $video['filename']);

        return $this->videoModel->delete($id);
    }

    /**
     * Apply the upload validation rules (title + file).
     *
     * @param array<string, mixed> $file
     *
     * @return array<int, string>
     */
    private function validateUpload(string $title, array $file): array
    {
        $errors = [];

        if ($title === '')
        {
            $errors[] = 'Title is required.';
        }
        elseif (mb_strlen($title) > 255)
        {
            $errors[] = 'Title may be at most 255 characters.';
        }

        $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($errorCode === UPLOAD_ERR_NO_FILE)
        {
            $errors[] = 'Please choose a video file to upload.';
        }
        elseif ($errorCode !== UPLOAD_ERR_OK)
        {
            $errors[] = 'The file could not be uploaded. It may be too large.';
        }
        elseif (!in_array($this->extensionOf($file['name'] ?? ''), self::ALLOWED_EXTENSIONS, true))
        {
            $errors[] = 'Only video files are allowed (' . implode(', ', self::ALLOWED_EXTENSIONS) . ').';
        }

        return $errors;
    }

    /**
     * Move the uploaded file into public/uploads/ under a safe, unique name.
     * Returns the stored filename, or null on failure.
     *
     * @param array<string, mixed> $file
     */
    private function storeFile(array $file): ?string
    {
        $temporaryPath = (string) ($file['tmp_name'] ?? '');

        if (!is_uploaded_file($temporaryPath))
        {
            return null;
        }

        $filename = $this->buildSafeFilename((string) ($file['name'] ?? ''));
        $targetPath = $this->uploadDirectory() . '/' . $filename;

        if (!move_uploaded_file($temporaryPath, $targetPath))
        {
            return null;
        }

        return $filename;
    }

    /**
     * Build a safe, unique filename from the original name. The original name is
     * reduced to letters, numbers and dashes (so "../" and slashes disappear),
     * which removes any path-traversal risk, and a random suffix guarantees
     * uniqueness.
     */
    private function buildSafeFilename(string $originalName): string
    {
        $extension = $this->extensionOf($originalName);

        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $slug = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '-', $base));
        $slug = trim($slug, '-');

        if ($slug === '')
        {
            $slug = 'video';
        }

        $slug = substr($slug, 0, 40);

        return $slug . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
    }

    /**
     * Remove a stored file from public/uploads/, if it still exists.
     */
    private function deleteFile(string $filename): void
    {
        // basename() strips any directory part as an extra safety net.
        $path = $this->uploadDirectory() . '/' . basename($filename);

        if (is_file($path))
        {
            unlink($path);
        }
    }

    /**
     * The lower-case extension of a filename (without the dot).
     */
    private function extensionOf(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Absolute path to the uploads directory.
     */
    private function uploadDirectory(): string
    {
        return dirname(__DIR__, 2) . '/public/uploads';
    }
}
