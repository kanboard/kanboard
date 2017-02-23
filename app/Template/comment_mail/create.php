<div class="page-header">
    <h2><?= t('Create and send a comment by email') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('CommentMailController', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>

    <?= $this->form->label(t('Email'), 'email') ?>
    <?= $this->form->email('email', $values, $errors, array('autofocus', 'required', 'tabindex="1"')) ?>

    <?= $this->form->label(t('Subject'), 'subject') ?>
    <?= $this->form->text('subject', $values, $errors, array('required', 'tabindex="2"')) ?>

    <?= $this->form->textEditor('comment', $values, $errors, array('required' => true, 'tabindex' => 3)) ?>

    <?= $this->modal->submitButtons(array(
        'submitLabel' => t('Send by email'),
        'tabindex'    => 4,
    )) ?>
</form>
