<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <?= $this->avatar->render($comment['user_id'], $comment['username'], $comment['name'], $comment['email'], $comment['avatar_path']) ?>

    <div class="comment-title">
        <?php if (! empty($comment['username'])): ?>
            <strong class="comment-username"><?= $this->text->e($comment['name'] ?: $comment['username']) ?></strong>
        <?php endif ?>

        <small class="comment-date"><?= $this->dt->datetime($comment['date_creation']) ?></small>
    </div>

    <div class="comment-content">
        <div class="markdown">
            <?= $this->text->markdown($comment['comment'], isset($is_public) && $is_public) ?>
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
                        <?= $this->url->link(t('remove'), 'CommentController', 'confirm', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id']), false, 'popover') ?>
                    </li>
                    <li>
                        <i class="fa fa-edit fa-fw"></i>
                        <?= $this->url->link(t('edit'), 'CommentController', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'comment_id' => $comment['id']), false, 'popover') ?>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    <?php endif ?>
</div>
