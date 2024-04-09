<?php use Kanboard\Core\Security\Role; ?>
<form method="post" action="<?= $this->url->href('CommentController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->textEditor('comment', $values, $errors, array('required' => true, 'aria-label' => t('New comment'))) ?>

    <?php if ($this->user->getRole() !== Role::APP_USER): ?>
        <?= $this->form->radio('privacy', t('Public'), 'app-user', true) ?>
        <?= $this->form->radio('privacy', t('Managers or more'), 'app-manager', false) ?>
    <?php endif ?>

    <?php if ($this->user->getRole() === Role::APP_ADMIN): ?>
        <?= $this->form->radio('privacy', t('Administrators'), 'app-admin', false) ?>
    <?php endif ?>

    <?= $this->modal->submitButtons() ?>
</form>
