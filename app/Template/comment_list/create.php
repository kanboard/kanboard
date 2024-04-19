<?php use Kanboard\Core\Security\Role;?>
<div class="page-header">
    <h2><?= t('Add a comment') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('CommentListController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->textEditor('comment', array('project_id' => $task['project_id']), array(), array('required' => true, 'aria-label' => t('New comment'))) ?>

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
