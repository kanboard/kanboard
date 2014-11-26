<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <p class="comment-title">
        <span class="comment-username"><?= Helper\escape($comment['name'] ?: $comment['username']) ?></span> @ <span class="comment-date"><?= dt('%B %e, %Y at %k:%M %p', $comment['date']) ?></span>
    </p>

    <div class="comment-inner">

        <?php if (! isset($preview)): ?>
        <ul class="comment-actions">
            <li><a href="#comment-<?= $comment['id'] ?>"><?= t('link') ?></a></li>
            <?php if ((! isset($not_editable) || ! $not_editable) && (Helper\is_admin() || Helper\is_current_user($comment['user_id']))): ?>
                <li>
                    <?= Helper\a(t('remove'), 'comment', 'confirm', array('task_id' => $task['id'], 'comment_id' => $comment['id'])) ?>
                </li>
                <li>
                    <?= Helper\a(t('edit'), 'comment', 'edit', array('task_id' => $task['id'], 'comment_id' => $comment['id'])) ?>
                </li>
            <?php endif ?>
        </ul>
        <?php endif ?>

        <div class="markdown">
            <?php if (isset($is_public) && $is_public): ?>
                <?= Helper\markdown(
                    $comment['comment'],
                    array(
                        'controller' => 'task',
                        'action' => 'readonly',
                        'params' => array(
                            'token' => $project['token']
                        )
                    )
                ) ?>
            <?php else: ?>
                <?= Helper\markdown($comment['comment']) ?>
            <?php endif ?>
        </div>

    </div>
</div>