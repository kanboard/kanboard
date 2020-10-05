<div class="page-header">
    <h2><?= t('Add a comment') ?></h2>
    <ul>
        <li>
            <?= $this->modal->medium('paper-plane', t('Send by email'), 'CommentMailController', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
    </ul>
</div>
<form method="post" action="<?= $this->url->href('CommentController', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->textEditor('comment', $values, $errors, array('autofocus' => true, 'required' => true, 'aria-label' => t('New comment'))) ?>

    <?= $this->modal->submitButtons() ?>
</form>
