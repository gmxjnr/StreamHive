<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="container">
    <a class="back-link" href="index.php">← Terug naar home</a>

    <video class="video-player" controls>
        <source src="<?= ASSET_URL; ?>uploads/<?= htmlspecialchars($video['filename']); ?>" type="video/mp4">
        Je browser ondersteunt deze video niet.
    </video>

    <section class="video-detail">
        <h1><?= htmlspecialchars($video['title']); ?></h1>

        <p class="description">
            <?= htmlspecialchars($video['description']); ?>
        </p>

        <div class="meta">
            <p>Geüpload door: @<?= htmlspecialchars($video['username']); ?></p>
            <p><?= htmlspecialchars($video['views']); ?> views</p>
            <p>Bestand: <?= htmlspecialchars($video['filename']); ?></p>
        </div>
    </section>

    <section class="comments">
        <h2>Comments</h2>

        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <strong>@<?= htmlspecialchars($comment['username']); ?></strong>
                    <p><?= htmlspecialchars($comment['content']); ?></p>
                    <small><?= htmlspecialchars($comment['created_at']); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Geen comments gevonden.</p>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>