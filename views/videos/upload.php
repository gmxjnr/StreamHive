<?php

declare(strict_types=1);

/**
 * Upload form.
 *
 * @var array<int, string> $errors      Validation errors to show.
 * @var string             $titleValue  Previously entered title (kept on error).
 * @var string             $description Previously entered description.
 */
?>

    <section>
        <h2>Upload a video</h2>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="/videos/upload" enctype="multipart/form-data">
            <p>
                <label for="title">Title</label><br>
                <input type="text" id="title" name="title" maxlength="255"
                       value="<?= htmlspecialchars($titleValue) ?>" required>
            </p>
            <p>
                <label for="description">Description</label><br>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>
            </p>
            <p>
                <label for="video">Video file (mp4, webm, ogg)</label><br>
                <input type="file" id="video" name="video" accept="video/*" required>
            </p>
            <p>
                <button type="submit">Upload</button>
            </p>
        </form>
    </section>
