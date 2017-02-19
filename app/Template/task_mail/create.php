<div class="page-header">
    <h2><?= $this->text->e($task['title']) ?> &gt; <?= t('Send by email') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('TaskMailController', 'send', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Email'), 'email') ?>
    <?= $this->form->email('email', $values, $errors, array('autofocus', 'required', 'tabindex="1"')) ?>

    <?= $this->form->label(t('Subject'), 'subject') ?>
    <?= $this->form->text('subject', $values, $errors, array('required', 'tabindex="2"')) ?>

    <?= $this->modal->submitButtons(array(
        'submitLabel' => t('Send by email'),
        'tabindex'    => 3,
    )) ?>
</form>
