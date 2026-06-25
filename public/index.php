<?php

declare(strict_types=1);

/**
 * StreamHive front controller (entry point).
 *
 * Week 1: this is a placeholder page that only proves the project structure
 * and the header/footer includes work. Real request routing through the
 * Router is added in a later week.
 */

$title = 'StreamHive — Home';

require __DIR__ . '/../views/partials/header.php';
?>

    <section>
        <h2>Hello StreamHive</h2>
        <p>The project skeleton is up and running.</p>
    </section>

<?php
require __DIR__ . '/../views/partials/footer.php';
