<?php

require_once __DIR__ . '/../../core/config.php';

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamHive</title>

    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="header">

    <a class="logo" href="index.php">
        Stream<span>Hive</span>
    </a>

    <form class="search-form" method="GET" action="index.php">
        <input
            type="text"
            name="search"
            placeholder="Zoeken..."
            value="<?= htmlspecialchars($_GET['search'] ?? ''); ?>"
        >

        <button type="submit">
            Search
        </button>
    </form>

    <nav class="nav">

        <?php if (isset($_SESSION['user'])): ?>

            <a href="index.php?page=upload">Upload</a>
            <a href="index.php?page=logout">Logout</a>

        <?php else: ?>

            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register">Register</a>

        <?php endif; ?>

    </nav>

</header>