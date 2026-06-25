<?php

declare(strict_types=1);

/**
 * Shared page header, included at the top of every view.
 *
 * Variables provided by Controller::render():
 *
 * @var string                     $title       Page title.
 * @var array<string, mixed>|null  $currentUser Logged-in user, or null.
 */

$pageTitle = isset($title) ? $title : 'StreamHive';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <h1><a href="/">StreamHive</a></h1>
        <nav>
            <a href="/">Home</a>
            <?php if (!empty($currentUser)): ?>
                <span>Hi, <?= htmlspecialchars($currentUser['username']) ?></span>
                <a href="/logout">Log out</a>
            <?php else: ?>
                <a href="/login">Log in</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
