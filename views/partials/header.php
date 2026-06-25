<?php

declare(strict_types=1);

/**
 * Shared page header, included at the top of every view.
 *
 * The including script may set $title before the include to change the page
 * title; otherwise a sensible default is used.
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
        </nav>
    </header>
    <main>
