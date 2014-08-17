<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <p class="comment-title">
        <span class="comment-username"><?= Helper\escape($comment['name'] ?: $comment['username']) ?></span> @ <span class="comment-date"><?= dt('%B %e, %Y at %k:%M %p', $comment['date']) ?></span>
    </p>

    <div class="comment-inner">

        <?php if (! isset($preview)): ?>
        <ul class="comment-actions">
            <li><a href="#comment-<?= $comment['id'] ?>"><?= t('link') ?></a></li>
            <?php if (Helper\is_admin() || Helper\is_current_user($comment['user_id'])): ?>
                <li>
                    <a href="?controller=comment&amp;action=confirm&amp;task_id=<?= $task['id'] ?>&amp;comment_id=<?= $comment['id'] ?>"><?= t('remove') ?></a>
                </li>
                <li>
                    <a href="?controller=comment&amp;action=edit&amp;task_id=<?= $task['id'] ?>&amp;comment_id=<?= $comment['id'] ?>"><?= t('edit') ?></a>
                </li>
            <?php endif ?>
        </ul>
        <?php endif ?>

        <div class="markdown">
            <?= Helper\parse($comment['comment']) ?>
        </div>

    </div>
</div>