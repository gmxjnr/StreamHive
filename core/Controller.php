<?php

declare(strict_types=1);

/**
 * Controller
 *
 * Small base class that every controller extends. It provides two things every
 * controller needs: rendering a view (wrapped in the shared header/footer) and
 * redirecting. Controllers stay focused on handling the request and never touch
 * the database directly.
 */
abstract class Controller
{
    /**
     * Render a view inside the shared header and footer.
     *
     * Keys in $data become variables inside the view (for example
     * $errors, $email). The logged-in user and page title are filled in
     * automatically when the caller does not provide them.
     *
     * @param string               $view Path under /views without the .php extension.
     * @param array<string, mixed> $data Variables made available to the view.
     */
    protected function render(string $view, array $data = []): void
    {
        if (!isset($data['currentUser']))
        {
            $data['currentUser'] = $_SESSION['user'] ?? null;
        }

        if (!isset($data['title']))
        {
            $data['title'] = 'StreamHive';
        }

        extract($data, EXTR_SKIP);

        require dirname(__DIR__) . '/views/partials/header.php';
        require dirname(__DIR__) . '/views/' . $view . '.php';
        require dirname(__DIR__) . '/views/partials/footer.php';
    }

    /**
     * Send a redirect response and stop further processing.
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
