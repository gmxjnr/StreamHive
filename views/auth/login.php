<?php

declare(strict_types=1);

/**
 * Login form.
 *
 * @var array<int, string> $errors Validation / login errors to show.
 * @var string             $email  Previously entered email (kept on error).
 */
?>

    <section>
        <h2>Log in</h2>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="/login">
            <p>
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($email) ?>" required>
            </p>
            <p>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" required>
            </p>
            <p>
                <button type="submit">Log in</button>
            </p>
        </form>

        <p>Forgot your password? <a href="/forgot">Reset it here</a>.</p>
        <p>No account yet? <a href="/register">Register here</a>.</p>
    </section>
