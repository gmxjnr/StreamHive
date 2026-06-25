<?php

declare(strict_types=1);

/**
 * Database
 *
 * PDO wrapper for StreamHive. A single shared connection (singleton) is reused
 * for the whole request, so individual models never open their own connection.
 * Every query runs through prepared statements.
 *
 * NOTE: This is the Week 1 skeleton. The actual PDO connection and the generic
 * prepared-statement query() method are implemented in Week 2.
 */
class Database
{
    /**
     * The single shared instance.
     */
    private static ?Database $instance = null;

    /**
     * The underlying PDO connection.
     */
    private ?PDO $connection = null;

    /**
     * Private constructor: callers use Database::getInstance() instead.
     */
    private function __construct()
    {
        // Week 2: build the DSN from Config and open the PDO connection here.
    }

    /**
     * Return the shared Database instance, creating it on first use.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Run a prepared statement and return the resulting PDOStatement.
     *
     * @param string              $sql    SQL containing named or positional placeholders.
     * @param array<string, mixed> $params Values bound to those placeholders.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        // Week 2: prepare $sql, bind $params, execute and return the statement.
        throw new RuntimeException('Database::query() is implemented in Week 2.');
    }
}
