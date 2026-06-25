<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Database.php';

/**
 * UserModel
 *
 * Data access for the `users` table. This is the only layer that runs SQL
 * against that table, and every statement is prepared. Read methods never
 * select the password hash; only findByEmail() returns it, because login
 * (Week 3) needs it for password_verify().
 */
class UserModel
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
     * Find a single user by id, or null when it does not exist.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, email, role, username, created_at
                FROM users
                WHERE id = :id';

        $user = $this->database->query($sql, ['id' => $id])->fetch();

        return $user === false ? null : $user;
    }

    /**
     * Find a single user by email (used for login), or null when not found.
     *
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT id, email, password, role, username, created_at
                FROM users
                WHERE email = :email';

        $user = $this->database->query($sql, ['email' => $email])->fetch();

        return $user === false ? null : $user;
    }

    /**
     * Return all users, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, email, role, username, created_at
                FROM users
                ORDER BY created_at DESC, id DESC';

        return $this->database->query($sql)->fetchAll();
    }

    /**
     * Insert a new user and return the generated id.
     *
     * Expected keys: email, password (already hashed), username, role (optional).
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (email, password, username, role)
                VALUES (:email, :password, :username, :role)';

        $this->database->query($sql, [
            'email'    => $data['email'],
            'password' => $data['password'],
            'username' => $data['username'],
            'role'     => $data['role'] ?? 'user',
        ]);

        return $this->database->lastInsertId();
    }
}
