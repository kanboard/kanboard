<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <p class="comment-title">
        <span class="comment-username"><?= $this->e($comment['name'] ?: $comment['username']) ?></span> @ <span class="comment-date"><?= dt('%B %e, %Y at %k:%M %p', $comment['date']) ?></span>
    </p>

    <div class="comment-inner">

        <?php if (! isset($preview)): ?>
        <ul class="comment-actions">
            <li><a href="#comment-<?= $comment['id'] ?>"><?= t('link') ?></a></li>
            <?php if ((! isset($not_editable) || ! $not_editable) && ($this->userSession->isAdmin() || $this->userSession->isCurrentUser($comment['user_id']))): ?>
                <li>
                    <?= $this->a(t('remove'), 'comment', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id'])) ?>
                </li>
                <li>
                    <?= $this->a(t('edit'), 'comment', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id'])) ?>
                </li>
            <?php endif ?>
        </ul>
        <?php endif ?>

        <div class="markdown">
            <?php if (isset($is_public) && $is_public): ?>
                <?= $this->markdown(
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
                <?= $this->markdown(
                    $comment['comment'],
                    array(
                        'controller' => 'task',
                        'action' => 'show',
                        'params' => array(
                            'project_id' => $task['project_id']
                        )
                    )
                ) ?>
            <?php endif ?>
        </div>

    </div>
</div>