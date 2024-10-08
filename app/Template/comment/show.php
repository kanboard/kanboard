<?php

use Kanboard\Core\Security\Role;

if ($this->user->getRole() === Role::APP_MANAGER && $comment['visibility'] === Role::APP_ADMIN) {
    return;
}

if ($this->user->getRole() === Role::APP_USER && $comment['visibility'] !== Role::APP_USER) {
    return;
}

?>

<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <?= $this->avatar->render($comment['user_id'], $comment['username'], $comment['name'], $comment['email'], $comment['avatar_path']) ?>

    <div class="comment-title">
        <?php if (! empty($comment['username'])): ?>
            <strong class="comment-username"><?= $this->text->e($comment['name'] ?: $comment['username']) ?></strong>
        <?php endif ?>

        <small class="comment-date"><?= t('Created at:') ?> <?= $this->dt->datetime($comment['date_creation']) ?></small>
        <small class="comment-date"><?= t('Updated at:') ?> <?= $this->dt->datetime($comment['date_modification']) ?></small>
        <small class="comment-visibility"><?= t('Visibility:') ?>
            <?php if ($comment['visibility'] === Role::APP_USER): ?>
                <?= t('Standard users') ?>
            <?php elseif ($comment['visibility'] === Role::APP_MANAGER): ?>
                <?= t('Application managers or more') ?>
            <?php else: ?>
                <?= t('Administrators') ?>
            <?php endif ?>
        </small>
    </div>

    <?php if (! isset($hide_actions)): ?>
    <div class="comment-actions">
        <div class="dropdown">
            <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-cog"></i><i class="fa fa-caret-down"></i></a>
            <ul>
                <li>
                    <?= $this->url->icon('link', t('Link'), 'TaskViewController', 'show', array('task_id' => $task['id']), false, '', '', $this->app->isAjax(), 'comment-'.$comment['id']) ?>
                </li>
                <li data-comment-id="<?= $comment['id'] ?>">
                    <?= $this->url->icon('reply', t('Reply'), 'TaskViewController', 'show', array('task_id' => $task['id']), false, 'js-reply-to-comment', '', $this->app->isAjax(), 'form-task_id') ?>
                </li>
                <?php if ($editable && ($this->user->isAdmin() || $this->user->isCurrentUser($comment['user_id']))): ?>
                    <li>
                        <?= $this->modal->medium('edit', t('Edit'), 'CommentController', 'edit', array('task_id' => $task['id'], 'comment_id' => $comment['id'])) ?>
                    </li>
                    <li>
                        <?= $this->modal->confirm('trash-o', t('Remove'), 'CommentController', 'confirm', array('task_id' => $task['id'], 'comment_id' => $comment['id'])) ?>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
    <?php endif ?>

    <div class="comment-content">
        <div class="markdown">
            <?= $this->text->markdown($comment['comment'], isset($is_public) && $is_public) ?>
        </div>
        <template id="comment-reply-content-<?= $comment['id'] ?>">
            <textarea><?= $this->text->reply($comment['name'] ?: $comment['username'], $comment['comment']) ?></textarea>
        </template>
    </div>
</div>