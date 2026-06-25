<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/UserModel.php';
require_once dirname(__DIR__, 2) . '/app/models/PasswordResetModel.php';

/**
 * AuthService
 *
 * All authentication business logic lives here: validating and registering new
 * accounts (hashing the password), logging in (verifying the password), keeping
 * track of the logged-in user in the session, and the password-recovery flow
 * (issuing and redeeming reset tokens). It also exposes the authorisation
 * checks (isLoggedIn / isAdmin) used by other parts of the app.
 */
class AuthService
{
    /**
     * How long a password reset token stays valid, in seconds.
     */
    private const RESET_TOKEN_LIFETIME = 3600;

    /**
     * Data access for the users table.
     */
    private UserModel $userModel;

    /**
     * Data access for the password_reset table.
     */
    private PasswordResetModel $passwordResetModel;

    public function __construct(?UserModel $userModel = null, ?PasswordResetModel $passwordResetModel = null)
    {
        $this->userModel = $userModel ?? new UserModel();
        $this->passwordResetModel = $passwordResetModel ?? new PasswordResetModel();
    }

    /**
     * Validate and register a new account. On success the user is logged in
     * straight away. Returns a list of validation errors; an empty list means
     * the registration succeeded.
     *
     * @return array<int, string>
     */
    public function register(string $username, string $email, string $password, string $passwordConfirm): array
    {
        $username = trim($username);
        $email = trim($email);

        $errors = $this->validateRegistration($username, $email, $password, $passwordConfirm);

        if ($errors !== [])
        {
            return $errors;
        }

        if ($this->userModel->findByEmail($email) !== null)
        {
            return ['An account with this email address already exists.'];
        }

        $userId = $this->userModel->create([
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'username' => $username,
            'role'     => 'user',
        ]);

        $this->establishSession($userId, $username, 'user');

        return [];
    }

    /**
     * Attempt to log in with an email and password. Returns true on success.
     */
    public function login(string $email, string $password): bool
    {
        $user = $this->userModel->findByEmail(trim($email));

        if ($user === null || !password_verify($password, $user['password']))
        {
            return false;
        }

        $this->establishSession(
            (int) $user['id'],
            (string) $user['username'],
            (string) ($user['role'] ?? 'user')
        );

        return true;
    }

    /**
     * Log the current user out and clear the session.
     */
    public function logout(): void
    {
        $this->startSession();
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Whether a user is currently logged in.
     */
    public function isLoggedIn(): bool
    {
        $this->startSession();

        return isset($_SESSION['user']['id']);
    }

    /**
     * Whether the logged-in user has the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
    }

    /**
     * Return the logged-in user (id, username, role), or null when logged out.
     *
     * @return array<string, mixed>|null
     */
    public function getCurrentUser(): ?array
    {
        $this->startSession();

        return $_SESSION['user'] ?? null;
    }

    /**
     * Start a password reset for an email address. Returns a fresh token when
     * the email belongs to an account, or null otherwise. Any older tokens for
     * that user are removed first, so only the latest token works.
     */
    public function requestPasswordReset(string $email): ?string
    {
        $user = $this->userModel->findByEmail(trim($email));

        if ($user === null)
        {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::RESET_TOKEN_LIFETIME);

        $this->passwordResetModel->deleteForUser((int) $user['id']);
        $this->passwordResetModel->create((int) $user['id'], $token, $expiresAt);

        return $token;
    }

    /**
     * Whether a reset token exists and has not expired.
     */
    public function isValidResetToken(string $token): bool
    {
        return $token !== '' && $this->passwordResetModel->findValidByToken($token) !== null;
    }

    /**
     * Set a new password using a reset token. Returns a list of errors; an empty
     * list means the password was changed and the token consumed.
     *
     * @return array<int, string>
     */
    public function resetPassword(string $token, string $password, string $passwordConfirm): array
    {
        $errors = [];

        if (mb_strlen($password) < 8)
        {
            $errors[] = 'Password must be at least 8 characters long.';
        }

        if ($password !== $passwordConfirm)
        {
            $errors[] = 'The passwords do not match.';
        }

        if ($errors !== [])
        {
            return $errors;
        }

        $reset = $this->passwordResetModel->findValidByToken($token);

        if ($reset === null)
        {
            return ['This reset link is invalid or has expired.'];
        }

        $this->userModel->updatePassword((int) $reset['user_id'], password_hash($password, PASSWORD_DEFAULT));
        $this->passwordResetModel->deleteByToken($token);

        return [];
    }

    /**
     * Apply the registration validation rules.
     *
     * @return array<int, string>
     */
    private function validateRegistration(string $username, string $email, string $password, string $passwordConfirm): array
    {
        $errors = [];

        if ($username === '')
        {
            $errors[] = 'Username is required.';
        }
        elseif (mb_strlen($username) > 50)
        {
            $errors[] = 'Username may be at most 50 characters.';
        }

        if ($email === '')
        {
            $errors[] = 'Email address is required.';
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $errors[] = 'Email address is not valid.';
        }
        elseif (mb_strlen($email) > 50)
        {
            $errors[] = 'Email address may be at most 50 characters.';
        }

        if (mb_strlen($password) < 8)
        {
            $errors[] = 'Password must be at least 8 characters long.';
        }

        if ($password !== $passwordConfirm)
        {
            $errors[] = 'The passwords do not match.';
        }

        return $errors;
    }

    /**
     * Store the logged-in user in the session, regenerating the session id to
     * prevent session fixation.
     */
    private function establishSession(int $id, string $username, string $role): void
    {
        $this->startSession();
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'       => $id,
            'username' => $username,
            'role'     => $role,
        ];
    }

    /**
     * Start the session once, if it is not already running.
     */
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
    }
}
