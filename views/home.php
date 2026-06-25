<?php

declare(strict_types=1);

/**
 * Home / landing page.
 *
 * @var array<string, mixed>|null $currentUser Logged-in user, or null.
 */
?>

    <section>
        <h2>Welcome to StreamHive</h2>

        <?php if (!empty($currentUser)): ?>
            <p>You are logged in as
                <strong><?= htmlspecialchars($currentUser['username']) ?></strong>.</p>
            <p>Uploading and browsing videos arrives in the next phase.</p>
        <?php else: ?>
            <p>StreamHive is a small video platform.
                <a href="/login">Log in</a> or
                <a href="/register">create an account</a> to get started.</p>
        <?php endif; ?>
    </section>
