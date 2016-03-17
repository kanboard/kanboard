<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">
    <?php if (! empty($comment['email'])): ?>
        <?= $this->user->avatar($comment['email'], $comment['name'] ?: $comment['username'], 32) ?>
    <?php endif ?>

    <div class="comment-title">
        <?php if (! isset($preview)): ?>
        <ul class="comment-actions">
            <li><a href="#comment-<?= $comment['id'] ?>" title="<?= t('link') ?>"><i class="fa fa-link fa-fw"></i></a></li>
            <?php if ($editable && ($this->user->isAdmin() || $this->user->isCurrentUser($comment['user_id']))): ?>
                <li>
                    <?= $this->url->link('<i class="fa fa-trash fa-fw"></i>', 'comment', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id']), t('remove'), 'popover', t('remove')) ?>
                </li>
                <li>
                    <?= $this->url->link('<i class="fa fa-pencil fa-fw"></i>', 'comment', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id']), false, 'popover', t('edit')) ?>
                </li>
            <?php endif ?>
        </ul>
        <?php endif ?>
        <?php if (! empty($comment['username'])): ?>
            <span class="comment-username"><?= $this->text->e($comment['name'] ?: $comment['username']) ?></span> @
        <?php endif ?>

        <span class="comment-date"><?= $this->dt->datetime($comment['date_creation']) ?></span>
    </div>
    <div class="comment-inner">
        <div class="markdown">
            <?php if (isset($is_public) && $is_public): ?>
                <?= $this->text->markdown(
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
                <?= $this->text->markdown(
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
