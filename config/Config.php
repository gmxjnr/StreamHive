<?php

declare(strict_types=1);

/**
 * Config
 *
 * Simple environment loader. It reads the .env file in the project root once
 * and exposes the values through Config::get(). Because the credentials live
 * in .env (which is git-ignored) no secrets are hard-coded in the PHP source.
 */
class Config
{
    /**
     * Cached key/value pairs read from the .env file.
     *
     * @var array<string, string>|null
     */
    private static ?array $values = null;

    /**
     * Return a single configuration value, or a default when it is missing.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        if (self::$values === null)
        {
            self::load();
        }

        return self::$values[$key] ?? $default;
    }

    /**
     * Read and parse the .env file located in the project root.
     */
    private static function load(): void
    {
        self::$values = [];

        $path = dirname(__DIR__) . '/.env';

        if (!is_readable($path))
        {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line)
        {
            $line = trim($line);

            // Skip blank lines, comments and anything without a key/value pair.
            if ($line === '' || $line[0] === '#' || !str_contains($line, '='))
            {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            // Strip optional surrounding quotes around the value.
            $value = trim($value, "\"'");

            self::$values[$key] = $value;
        }
    }
}
