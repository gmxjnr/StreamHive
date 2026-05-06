<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container">

    <div class="auth-card">

        <h1>Login</h1>

        <?php if ($error): ?>

            <p class="error">
                <?= $error; ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>