<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Controller.php';
require_once dirname(__DIR__, 2) . '/app/services/AuthService.php';

/**
 * AuthController
 *
 * Handles login, registration, logout and the password-recovery flow. It reads
 * the form input, delegates the actual work to AuthService and then either
 * redirects on success or re-renders the form with the validation errors. No
 * SQL lives here.
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

    /**
     * Show the "forgot password" form (GET /forgot).
     */
    public function showForgotPassword(): void
    {
        if ($this->authService->isLoggedIn())
        {
            $this->redirect('/');
        }

        $this->render('auth/forgot', [
            'title'     => 'StreamHive — Forgot password',
            'errors'    => [],
            'email'     => '',
            'resetLink' => null,
        ]);
    }

    /**
     * Process the "forgot password" form (POST /forgot). For this school project
     * the reset link is shown on screen instead of being emailed.
     */
    public function forgotPassword(): void
    {
        if ($this->authService->isLoggedIn())
        {
            $this->redirect('/');
        }

        $email = $_POST['email'] ?? '';
        $token = $this->authService->requestPasswordReset($email);

        $this->render('auth/forgot', [
            'title'     => 'StreamHive — Forgot password',
            'errors'    => $token === null ? ['No account was found with that email address.'] : [],
            'email'     => $email,
            'resetLink' => $token !== null ? $this->resetLinkFor($token) : null,
        ]);
    }

    /**
     * Show the reset form for a token (GET /reset?token=...).
     */
    public function showReset(): void
    {
        $token = (string) ($_GET['token'] ?? '');

        $this->render('auth/reset', [
            'title'   => 'StreamHive — Reset password',
            'token'   => $token,
            'valid'   => $this->authService->isValidResetToken($token),
            'errors'  => [],
            'success' => false,
        ]);
    }

    /**
     * Process the reset form (POST /reset).
     */
    public function resetPassword(): void
    {
        $token = (string) ($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errors = $this->authService->resetPassword($token, $password, $passwordConfirm);

        if ($errors === [])
        {
            $this->render('auth/reset', [
                'title'   => 'StreamHive — Reset password',
                'token'   => $token,
                'valid'   => false,
                'errors'  => [],
                'success' => true,
            ]);
            return;
        }

        $this->render('auth/reset', [
            'title'   => 'StreamHive — Reset password',
            'token'   => $token,
            'valid'   => $this->authService->isValidResetToken($token),
            'errors'  => $errors,
            'success' => false,
        ]);
    }

    /**
     * Build an absolute reset URL for a token, based on the current host.
     */
    private function resetLinkFor(string $token): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . '/reset?token=' . $token;
    }
}
