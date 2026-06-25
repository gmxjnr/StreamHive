<?php

declare(strict_types=1);

/**
 * Video detail page: player, categories, like button, and the comment section.
 *
 * @var array<string, mixed>             $video       Video with uploader name.
 * @var array<int, array<string, mixed>> $categories  This video's categories.
 * @var array<int, array<string, mixed>> $comments    Comments with author + likes.
 * @var array{count: int, liked: bool}   $videoLike   Like count + own like state.
 * @var array<string, mixed>|null        $currentUser Logged-in user, or null.
 */

$isOwner = !empty($currentUser) && (int) $currentUser['id'] === (int) $video['user_id'];
$isAdmin = !empty($currentUser) && ($currentUser['role'] ?? '') === 'admin';
?>

    <section class="video-detail">
        <h2><?= htmlspecialchars($video['title']) ?></h2>

        <video class="player" controls
               src="/uploads/<?= htmlspecialchars($video['filename']) ?>">
            Your browser does not support the video tag.
        </video>

        <div class="video-bar">
            <p class="meta">
                Uploaded by <strong><?= htmlspecialchars($video['uploader']) ?></strong>
                &middot; <?= (int) $video['views'] ?> views
                &middot; <?= htmlspecialchars($video['created_at']) ?>
            </p>

            <?php if (!empty($currentUser)): ?>
                <form method="post" action="/likes/video" class="like-form">
                    <input type="hidden" name="video_id" value="<?= (int) $video['id'] ?>">
                    <button type="submit" class="like-button<?= $videoLike['liked'] ? ' liked' : '' ?>">
                        <?= $videoLike['liked'] ? '&hearts;' : '&#9825;' ?>
                        Like &middot; <?= (int) $videoLike['count'] ?>
                    </button>
                </form>
            <?php else: ?>
                <span class="like-button static">&hearts; <?= (int) $videoLike['count'] ?></span>
            <?php endif; ?>
        </div>

        <?php if (!empty($categories)): ?>
            <p class="category-tags">
                <?php foreach ($categories as $category): ?>
                    <a class="tag" href="/?category=<?= (int) $category['id'] ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($video['description'])): ?>
            <p class="description"><?= nl2br(htmlspecialchars($video['description'])) ?></p>
        <?php endif; ?>

        <?php if ($isOwner || $isAdmin): ?>
            <form method="post" action="/videos/delete"
                  onsubmit="return confirm('Delete this video? This cannot be undone.');">
                <input type="hidden" name="id" value="<?= (int) $video['id'] ?>">
                <button type="submit" class="button danger">Delete video</button>
            </form>
        <?php endif; ?>

        <section class="comments">
            <h3><?= count($comments) ?> comment<?= count($comments) === 1 ? '' : 's' ?></h3>

            <?php if (!empty($currentUser)): ?>
                <form method="post" action="/comments/store" class="comment-form">
                    <input type="hidden" name="video_id" value="<?= (int) $video['id'] ?>">
                    <textarea name="content" rows="3" maxlength="2000"
                              placeholder="Add a comment&hellip;" required></textarea>
                    <button type="submit">Post comment</button>
                </form>
            <?php else: ?>
                <p><a href="/login">Log in</a> to leave a comment.</p>
            <?php endif; ?>

            <ul class="comment-list">
                <?php foreach ($comments as $comment): ?>
                    <?php
                    $commentLiked = (int) $comment['liked_by_me'] > 0;
                    $canDeleteComment = !empty($currentUser)
                        && ((int) $currentUser['id'] === (int) $comment['user_id'] || $isAdmin);
                    ?>
                    <li class="comment">
                        <div class="comment-head">
                            <strong><?= htmlspecialchars($comment['author']) ?></strong>
                            <span class="meta"><?= htmlspecialchars($comment['created_at']) ?></span>
                        </div>
                        <p class="comment-body"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                        <div class="comment-actions">
                            <?php if (!empty($currentUser)): ?>
                                <form method="post" action="/likes/comment" class="like-form">
                                    <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">
                                    <input type="hidden" name="video_id" value="<?= (int) $video['id'] ?>">
                                    <button type="submit" class="like-button small<?= $commentLiked ? ' liked' : '' ?>">
                                        <?= $commentLiked ? '&hearts;' : '&#9825;' ?>
                                        <?= (int) $comment['like_count'] ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="like-button small static">&hearts; <?= (int) $comment['like_count'] ?></span>
                            <?php endif; ?>

                            <?php if ($canDeleteComment): ?>
                                <form method="post" action="/comments/delete"
                                      onsubmit="return confirm('Delete this comment?');">
                                    <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">
                                    <input type="hidden" name="video_id" value="<?= (int) $video['id'] ?>">
                                    <button type="submit" class="link-button">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <p><a href="/">&larr; Back to videos</a></p>
    </section>
