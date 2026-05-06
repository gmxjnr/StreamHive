<?php

require_once __DIR__ . '/../methods/getVideos.php';

include __DIR__ . '/../templates/header.php';

?>

<main class="container">

    <h1>Video's</h1>

    <div class="video-grid">

        <?php foreach($videos as $video): ?>

            <a 
                class="video-card"
                href="/app/pages/video.php?id=<?= $video['id']; ?>"
            >

                <div class="thumbnail">
                    ▶
                </div>

                <div class="video-info">

                    <h3>
                        <?= htmlspecialchars($video['title']); ?>
                    </h3>

                    <p class="description">
                        <?= htmlspecialchars($video['description']); ?>
                    </p>

                    <div class="meta">
                        <p>@<?= htmlspecialchars($video['username']); ?></p>
                        <p><?= $video['views']; ?> views</p>
                    </div>

                </div>

            </a>

        <?php endforeach; ?>

    </div>

</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>