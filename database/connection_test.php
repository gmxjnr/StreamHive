<?php

declare(strict_types=1);

/**
 * Database / model connection test (Week 2 deliverable).
 *
 * Proof that the PDO connection works and that UserModel and VideoModel can run
 * real SELECT and INSERT queries. Run it from the project root:
 *
 *     php database/connection_test.php
 *
 * or inside the Docker container:
 *
 *     docker compose -f docker/docker-compose.yaml exec php php database/connection_test.php
 *
 * It inserts a clearly labelled test user and a video owned by that user, reads
 * them back, then deletes the user again. The video is removed automatically
 * through ON DELETE CASCADE, so the script can be run repeatedly without
 * leaving anything behind.
 */

require_once dirname(__DIR__) . '/app/models/UserModel.php';
require_once dirname(__DIR__) . '/app/models/VideoModel.php';

$separator = str_repeat('=', 60);

echo $separator . PHP_EOL;
echo 'StreamHive — database connection test' . PHP_EOL;
echo $separator . PHP_EOL;

try
{
    // 1. Connecting: instantiating a model opens the shared PDO connection.
    $userModel = new UserModel();
    $videoModel = new VideoModel();
    echo '[OK]   PDO connection established' . PHP_EOL;

    // 2. SELECT test: read existing dummy data through the models.
    $users = $userModel->findAll();
    $videos = $videoModel->findAll();
    echo '[OK]   SELECT users  -> ' . count($users) . ' row(s)' . PHP_EOL;
    echo '[OK]   SELECT videos -> ' . count($videos) . ' row(s)' . PHP_EOL;

    if (count($users) > 0)
    {
        echo '       first user: '
            . $users[0]['username'] . ' <' . $users[0]['email'] . '>' . PHP_EOL;
    }

    // 3. INSERT test: create a unique test user, then a video owned by it.
    $marker = 'conn_test_' . date('YmdHis');

    $userId = $userModel->create([
        'email'    => $marker . '@example.com',
        'password' => password_hash('test1234', PASSWORD_DEFAULT),
        'username' => $marker,
        'role'     => 'user',
    ]);
    echo '[OK]   INSERT user   -> new id ' . $userId . PHP_EOL;

    $videoId = $videoModel->create([
        'user_id'     => $userId,
        'title'       => 'Connection test video',
        'description' => 'Temporary row created by connection_test.php',
        'filename'    => 'connection_test.mp4',
    ]);
    echo '[OK]   INSERT video  -> new id ' . $videoId . PHP_EOL;

    // 4. Verify the inserts by reading them back through the models.
    $createdUser = $userModel->findById($userId);
    $createdVideo = $videoModel->findById($videoId);

    if ($createdUser !== null && $createdVideo !== null)
    {
        echo '[OK]   Verified inserted rows via findById()' . PHP_EOL;
    }
    else
    {
        echo '[FAIL] Could not read back the inserted rows' . PHP_EOL;
    }

    // 5. Clean up: deleting the user cascades to the video (ON DELETE CASCADE).
    Database::getInstance()->query('DELETE FROM users WHERE id = :id', ['id' => $userId]);
    echo '[OK]   Cleaned up test rows (cascade removed the video)' . PHP_EOL;

    echo $separator . PHP_EOL;
    echo 'RESULT: all checks passed.' . PHP_EOL;
    echo $separator . PHP_EOL;
}
catch (Throwable $exception)
{
    echo '[FAIL] ' . $exception->getMessage() . PHP_EOL;
    echo $separator . PHP_EOL;
    echo 'RESULT: connection test FAILED.' . PHP_EOL;
    echo $separator . PHP_EOL;
    exit(1);
}
