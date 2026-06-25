<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';
require_once dirname(__DIR__, 2) . '/app/services/AuthService.php';

/**
 * AuthController
 *
 * Handles the login, registration and logout requests. It reads the form input,
 * delegates the actual work to AuthService and then either redirects on success
 * or re-renders the form with the validation errors. No SQL lives here.
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        $this->authService = $authService ?? new AuthService();
    }

    /**
     * Show the login form (GET /login).
     */
    public function showLogin(): void
    {
        if ($this->authService->isLoggedIn())
        {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'title'  => 'StreamHive — Log in',
            'errors' => [],
            'email'  => '',
        ]);
    }

    /**
     * Process the login form (POST /login).
     */
    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->authService->login($email, $password))
        {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'title'  => 'StreamHive — Log in',
            'errors' => ['Invalid email address or password.'],
            'email'  => $email,
        ]);
    }

    /**
     * Show the registration form (GET /register).
     */
    public function showRegister(): void
    {
        if ($this->authService->isLoggedIn())
        {
            $this->redirect('/');
        }

        $this->render('auth/register', [
            'title'    => 'StreamHive — Register',
            'errors'   => [],
            'username' => '',
            'email'    => '',
        ]);
    }

    /**
     * Process the registration form (POST /register).
     */
    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errors = $this->authService->register($username, $email, $password, $passwordConfirm);

        if ($errors === [])
        {
            $this->redirect('/');
        }

        $this->render('auth/register', [
            'title'    => 'StreamHive — Register',
            'errors'   => $errors,
            'username' => $username,
            'email'    => $email,
        ]);
    }

    /**
     * Log out and return to the home page (GET /logout).
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/');
    }
}
