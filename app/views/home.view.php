<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="container">
    <h1>Video's</h1>

    <?php if (count($videos) > 0): ?>
        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
                <a class="video-card" href="index.php?page=video&id=<?= $video['id']; ?>">

                    <?php if (!empty($video['thumbnail'])): ?>
                        <img 
                            class="thumbnail-image"
                            src="/assets/uploads/thumbnails/<?= htmlspecialchars($video['thumbnail']); ?>"
                            alt="<?= htmlspecialchars($video['title']); ?>"
                        >
                    <?php else: ?>
                        <div class="thumbnail">▶</div>
                    <?php endif; ?>

                    <div class="video-info">
                        <h3><?= htmlspecialchars($video['title']); ?></h3>

                        <p class="description">
                            <?= htmlspecialchars($video['description']); ?>
                        </p>

                        <div class="meta">
                            <p>@<?= htmlspecialchars($video['username']); ?></p>
                            <p><?= htmlspecialchars($video['views']); ?> views</p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Geen video's gevonden.</p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>