<?php

declare(strict_types=1);

/**
 * Password reset form. Only shown when the token is still valid.
 *
 * @var string             $token   The reset token from the link.
 * @var bool               $valid   Whether the token is valid / not expired.
 * @var array<int, string> $errors  Validation errors to show.
 * @var bool               $success Whether the password was just changed.
 */
?>

    <section>
        <h2>Reset password</h2>

        <?php if ($success): ?>
            <div class="notice">
                <p>Your password has been updated. You can now
                   <a href="/login">log in</a> with your new password.</p>
            </div>
        <?php elseif (!$valid): ?>
            <ul class="errors">
                <li>This reset link is invalid or has expired.</li>
            </ul>
            <p><a href="/forgot">Request a new reset link</a>.</p>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <ul class="errors">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" action="/reset">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <p>
                    <label for="password">New password</label><br>
                    <input type="password" id="password" name="password" minlength="8" required>
                </p>
                <p>
                    <label for="password_confirm">Confirm new password</label><br>
                    <input type="password" id="password_confirm" name="password_confirm" minlength="8" required>
                </p>
                <p>
                    <button type="submit">Update password</button>
                </p>
            </form>
        <?php endif; ?>
    </section>
