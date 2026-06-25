<?php

declare(strict_types=1);

/**
 * "Forgot password" form. After submitting, the generated reset link is shown
 * on screen (for this school project it is not actually emailed).
 *
 * @var array<int, string> $errors    Errors to show.
 * @var string             $email     Previously entered email.
 * @var string|null        $resetLink Generated reset link, or null.
 */
?>

    <section>
        <h2>Forgot password</h2>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($resetLink !== null): ?>
            <div class="notice">
                <p>A reset link has been generated. In a real application this would
                   be emailed to you; for this school project it is shown here:</p>
                <p><a href="<?= htmlspecialchars($resetLink) ?>"><?= htmlspecialchars($resetLink) ?></a></p>
                <p>The link is valid for one hour.</p>
            </div>
        <?php else: ?>
            <p>Enter your email address and we will generate a password reset link.</p>
            <form method="post" action="/forgot">
                <p>
                    <label for="email">Email</label><br>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($email) ?>" required>
                </p>
                <p>
                    <button type="submit">Generate reset link</button>
                </p>
            </form>
        <?php endif; ?>

        <p><a href="/login">&larr; Back to login</a></p>
    </section>
