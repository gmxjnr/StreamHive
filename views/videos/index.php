<?php

declare(strict_types=1);

/**
 * Video overview / home page.
 *
 * @var array<int, array<string, mixed>> $videos      Videos with uploader name.
 * @var array<string, mixed>|null        $currentUser Logged-in user, or null.
 */
?>

    <section>
        <div class="page-head">
            <h2>Videos</h2>
            <?php if (!empty($currentUser)): ?>
                <a class="button" href="/videos/upload">Upload video</a>
            <?php endif; ?>
        </div>

        <?php if (empty($videos)): ?>
            <p class="empty">No videos yet.
                <?php if (!empty($currentUser)): ?>
                    Be the first to <a href="/videos/upload">upload one</a>.
                <?php else: ?>
                    <a href="/login">Log in</a> to upload one.
                <?php endif; ?>
            </p>
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
