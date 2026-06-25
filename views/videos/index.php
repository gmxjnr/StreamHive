<?php

declare(strict_types=1);

/**
 * Video overview / home page, with search and category filtering.
 *
 * @var array<int, array<string, mixed>> $videos           Videos with uploader.
 * @var array<int, array<string, mixed>> $categories       All categories.
 * @var string                           $search           Current search term.
 * @var int|null                         $activeCategoryId Selected category id.
 * @var array<string, mixed>|null        $activeCategory   Selected category row.
 * @var array<string, mixed>|null        $currentUser      Logged-in user, or null.
 */
?>

    <section>
        <div class="page-head">
            <h2>
                <?php if (!empty($activeCategory)): ?>
                    Category: <?= htmlspecialchars($activeCategory['name']) ?>
                <?php elseif ($search !== ''): ?>
                    Search results for &ldquo;<?= htmlspecialchars($search) ?>&rdquo;
                <?php else: ?>
                    Videos
                <?php endif; ?>
            </h2>
            <?php if (!empty($currentUser)): ?>
                <a class="button" href="/videos/upload">Upload video</a>
            <?php endif; ?>
        </div>

        <form method="get" action="/" class="search-bar">
            <input type="search" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search videos by title or description&hellip;">
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($categories)): ?>
            <ul class="category-filter">
                <li>
                    <a href="/" class="<?= ($activeCategoryId === null && $search === '') ? 'active' : '' ?>">All</a>
                </li>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="/?category=<?= (int) $category['id'] ?>"
                           class="<?= $activeCategoryId === (int) $category['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (empty($videos)): ?>
            <p class="empty">No videos found.</p>
        <?php else: ?>
            <ul class="video-grid">
                <?php foreach ($videos as $video): ?>
                    <li class="video-card">
                        <a href="/videos/show?id=<?= (int) $video['id'] ?>">
                            <span class="thumb" aria-hidden="true">&#9654;</span>
                            <h3><?= htmlspecialchars($video['title']) ?></h3>
                        </a>
                        <p class="meta">
                            <?= htmlspecialchars($video['uploader']) ?>
                            &middot; <?= (int) $video['views'] ?> views
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
