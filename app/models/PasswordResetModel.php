<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * PasswordResetModel
 *
 * Data access for the `password_reset` table. A row stores a one-time token for
 * a user together with an expiry time; findValidByToken() only returns tokens
 * that have not expired yet.
 */
class PasswordResetModel
{
    /**
     * Shared database connection wrapper.
     */
    private Database $database;

    public function __construct(?Database $database = null)
    {
        $this->database = $database ?? Database::getInstance();
    }

    /**
     * Store a reset token for a user. $expiresAt is a 'Y-m-d H:i:s' datetime.
     */
    public function create(int $userId, string $token, string $expiresAt): int
    {
        $sql = 'INSERT INTO password_reset (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)';

        $this->database->query($sql, [
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
        ]);

        return $this->database->lastInsertId();
    }

    /**
     * Find a token that exists and has not expired, or null otherwise.
     *
     * @return array<string, mixed>|null
     */
    public function findValidByToken(string $token): ?array
    {
        $sql = 'SELECT id, user_id, token, expires_at
                FROM password_reset
                WHERE token = :token AND expires_at > NOW()';

        $row = $this->database->query($sql, ['token' => $token])->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Remove every reset token belonging to a user (used before issuing a new
     * one, so only the latest token works).
     */
    public function deleteForUser(int $userId): void
    {
        $sql = 'DELETE FROM password_reset WHERE user_id = :user_id';

        $this->database->query($sql, ['user_id' => $userId]);
    }

    /**
     * Remove a token once it has been used.
     */
    public function deleteByToken(string $token): void
    {
        $sql = 'DELETE FROM password_reset WHERE token = :token';

        $this->database->query($sql, ['token' => $token]);
    }
}
