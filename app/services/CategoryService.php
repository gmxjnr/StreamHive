<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/CategoryModel.php';

/**
 * CategoryService
 *
 * Business logic for categories: listing them, looking one up, fetching the
 * categories of a video, and assigning categories to a video. Assignment only
 * links category ids that really exist, so a tampered form cannot create
 * invalid rows.
 */
class CategoryService
{
    /**
     * Data access for the categories tables.
     */
    private CategoryModel $categoryModel;

    public function __construct(?CategoryModel $categoryModel = null)
    {
        $this->categoryModel = $categoryModel ?? new CategoryModel();
    }

    /**
     * All categories, for the upload form and the overview filter.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllCategories(): array
    {
        return $this->categoryModel->findAll();
    }

    /**
     * A single category by id, or null.
     *
     * @return array<string, mixed>|null
     */
    public function getCategory(int $id): ?array
    {
        return $this->categoryModel->findById($id);
    }

    /**
     * The categories linked to a video.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getForVideo(int $videoId): array
    {
        return $this->categoryModel->findByVideoId($videoId);
    }

    /**
     * Link a video to each of the given category ids. Ids that do not exist or
     * appear twice are ignored.
     *
     * @param array<int, mixed> $categoryIds
     */
    public function assignCategories(int $videoId, array $categoryIds): void
    {
        $validIds = array_map(static fn ($row): int => (int) $row['id'], $this->categoryModel->findAll());

        $linked = [];

        foreach ($categoryIds as $categoryId)
        {
            $categoryId = (int) $categoryId;

            if ($categoryId > 0 && in_array($categoryId, $validIds, true) && !in_array($categoryId, $linked, true))
            {
                $this->categoryModel->linkVideo($videoId, $categoryId);
                $linked[] = $categoryId;
            }
        }
    }
}
