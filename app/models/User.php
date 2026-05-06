<?php

require_once __DIR__ . '/../core/database.php';

class User
{
    public static function findByEmail($email)
    {
        global $conn;

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($username, $email, $password)
    {
        global $conn;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO users
            (username, email, password, role, created_at)
            VALUES
            (:username, :email, :password, 'user', NOW())
        ";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
    }
}