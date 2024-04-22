<?php use Kanboard\Core\Security\Role; ?>
<form method="post" action="<?= $this->url->href('CommentController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->textEditor('comment', $values, $errors, array('required' => true, 'aria-label' => t('New comment'))) ?>

    <?php if ($this->user->getRole() !== Role::APP_USER) {
        $formName = 'visibility';
        $visibilityOptions['app-user'] = t('Standard users');
        $visibilityOptions['app-manager'] = t('Application managers or more');
    }
    ?>

    <?php if ($this->user->getRole() === Role::APP_ADMIN) {
        $visibilityOptions['app-admin'] = t('Administrators');
    }
    ?>

    <?php if (isset($visibilityOptions)): ?>
        <?= $this->form->label(t('Visibility:'), $formName) ?>
        <?= $this->form->select($formName, $visibilityOptions) ?>
    <?php endif ?>

    <?= $this->modal->submitButtons() ?>
</form>
