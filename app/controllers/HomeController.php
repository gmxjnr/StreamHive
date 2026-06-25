<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';

/**
 * HomeController
 *
 * Renders the landing page. From Week 4 the video overview becomes the main
 * page; for now this is a simple welcome that adapts to the login state.
 */
class HomeController extends Controller
{
    /**
     * Show the home page (GET /).
     */
    public function index(): void
    {
        $this->render('home', [
            'title' => 'StreamHive — Home',
        ]);
    }
}
