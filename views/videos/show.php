<?php

declare(strict_types=1);

/**
 * Video detail page.
 *
 * @var array<string, mixed>      $video       Video with uploader name.
 * @var array<string, mixed>|null $currentUser Logged-in user, or null.
 */

$isOwner = !empty($currentUser) && (int) $currentUser['id'] === (int) $video['user_id'];
$isAdmin = !empty($currentUser) && ($currentUser['role'] ?? '') === 'admin';
?>

    <section class="video-detail">
        <h2><?= htmlspecialchars($video['title']) ?></h2>

        <video class="player" controls
               src="/uploads/<?= htmlspecialchars($video['filename']) ?>">
            Your browser does not support the video tag.
        </video>

        <p class="meta">
            Uploaded by <strong><?= htmlspecialchars($video['uploader']) ?></strong>
            &middot; <?= (int) $video['views'] ?> views
            &middot; <?= htmlspecialchars($video['created_at']) ?>
        </p>

        <?php if (!empty($video['description'])): ?>
            <p class="description"><?= nl2br(htmlspecialchars($video['description'])) ?></p>
        <?php endif; ?>

        <?php if ($isOwner || $isAdmin): ?>
            <form method="post" action="/videos/delete"
                  onsubmit="return confirm('Delete this video? This cannot be undone.');">
                <input type="hidden" name="id" value="<?= (int) $video['id'] ?>">
                <button type="submit" class="button danger">Delete video</button>
            </form>
        <?php endif; ?>

        <p><a href="/">&larr; Back to videos</a></p>
    </section>
