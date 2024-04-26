<?php use Kanboard\Core\Security\Role; ?>
<form method="post" action="<?= $this->url->href('CommentController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->textEditor('comment', $values, $errors, array('required' => true, 'aria-label' => t('New comment'))) ?>

    <?php
        $formName = 'visibility';
        $visibilityOptions['app-user'] = t('Standard users');
        $attribute[] = ('hidden');
    ?>

    <?php if ($this->user->getRole() !== Role::APP_USER) {
        echo $this->form->label(t('Visibility:'), $formName);
        $attribute = [];
        $visibilityOptions['app-user'] = t('Standard users');
        $visibilityOptions['app-manager'] = t('Application managers or more');
    }
    ?>

    <?php if ($this->user->getRole() === Role::APP_ADMIN) {
        $visibilityOptions['app-admin'] = t('Administrators');
    }
    ?>

    <?= $this->form->select($formName, $visibilityOptions, array(), array(), $attribute) ?>
    <?= $this->modal->submitButtons() ?>
</form>
