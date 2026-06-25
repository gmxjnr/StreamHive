<?php

declare(strict_types=1);

/**
 * Upload form.
 *
 * @var array<int, string>                $errors             Validation errors.
 * @var string                            $titleValue         Previous title.
 * @var string                            $description        Previous description.
 * @var array<int, array<string, mixed>>  $categories         All categories.
 * @var array<int, int>                   $selectedCategories Previously chosen ids.
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
            <?php if (!empty($categories)): ?>
                <p>
                    <label for="categories">Categories</label><br>
                    <select id="categories" name="categories[]" multiple size="5">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>"
                                <?= in_array((int) $category['id'], $selectedCategories, true) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>
                    <small>Hold Ctrl (Cmd on Mac) to select more than one.</small>
                </p>
            <?php endif; ?>
            <p>
                <label for="video">Video file (mp4, webm, ogg)</label><br>
                <input type="file" id="video" name="video" accept="video/*" required>
            </p>
            <p>
                <button type="submit">Upload</button>
            </p>
        </form>
    </section>
