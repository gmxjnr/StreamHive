<?php

declare(strict_types=1);

/**
 * Registration form.
 *
 * @var array<int, string> $errors   Validation errors to show.
 * @var string             $username Previously entered username (kept on error).
 * @var string             $email    Previously entered email (kept on error).
 */
?>

    <section>
        <h2>Create an account</h2>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="/register">
            <p>
                <label for="username">Username</label><br>
                <input type="text" id="username" name="username" maxlength="50"
                       value="<?= htmlspecialchars($username) ?>" required>
            </p>
            <p>
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email" maxlength="50"
                       value="<?= htmlspecialchars($email) ?>" required>
            </p>
            <p>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password"
                       minlength="8" required>
            </p>
            <p>
                <label for="password_confirm">Confirm password</label><br>
                <input type="password" id="password_confirm" name="password_confirm"
                       minlength="8" required>
            </p>
            <p>
                <button type="submit">Register</button>
            </p>
        </form>

        <p>Already have an account? <a href="/login">Log in here</a>.</p>
    </section>
