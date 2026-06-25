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
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="/">Stream<span>Hive</span></a>
            <nav class="nav">
                <a href="/">Home</a>
                <?php if (!empty($currentUser)): ?>
                    <a href="/videos/upload">Upload</a>
                    <span class="nav-user">Hi, <?= htmlspecialchars($currentUser['username']) ?></span>
                    <a class="button small" href="/logout">Log out</a>
                <?php else: ?>
                    <a href="/login">Log in</a>
                    <a class="button small" href="/register">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">
