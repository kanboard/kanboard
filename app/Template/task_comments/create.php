<form method="post" action="<?= $this->url->href('CommentController', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>


    <?= $this->form->textEditor('comment', $values, $errors, array('required' => true, 'aria-label' => t('New comment'))) ?>



    <?= $this->form->radio('privacy', t('Public'), 'public', true) ?>

    <?php if ($this->user->hasAccess('ProjectCreationController', 'create')): ?>
        <?= $this->form->radio('privacy', t('Managers or more'), 'manager', false) ?>
    <?php endif ?>

    <?php if ($this->user->hasAccess('UserCreationController', 'create')): ?>
        <?= $this->form->radio('privacy', t('Administrators'), 'admin', false) ?>
    <?php endif ?>



    <?= $this->modal->submitButtons() ?>
</form>
