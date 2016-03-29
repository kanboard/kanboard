<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <?= $this->avatar->render($comment['user_id'], $comment['username'], $comment['name'], $comment['email'], $comment['avatar_path']) ?>

    <div class="comment-title">
        <?php if (! empty($comment['username'])): ?>
            <span class="comment-username"><?= $this->text->e($comment['name'] ?: $comment['username']) ?></span>
        <?php endif ?>

        <span class="comment-date"><?= $this->dt->datetime($comment['date_creation']) ?></span>
    </div>

    <div class="comment-content">
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

    <?php if (! isset($hide_actions)): ?>
        <div class="comment-actions">
            <ul>
                <li>
                    <i class="fa fa-link fa-fw"></i>
                    <a href="#comment-<?= $comment['id'] ?>"><?= t('link') ?></a>
                </li>
                <?php if ($editable && ($this->user->isAdmin() || $this->user->isCurrentUser($comment['user_id']))): ?>
                    <li>
                        <i class="fa fa-remove fa-fw"></i>
                        <?= $this->url->link(t('remove'), 'comment', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id']), false, 'popover') ?>
                    </li>
                    <li>
                        <i class="fa fa-edit fa-fw"></i>
                        <?= $this->url->link(t('edit'), 'comment', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id']), false, 'popover') ?>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    <?php endif ?>
</div>
