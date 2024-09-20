<div class="page-header">
    <h2><?= t('Move Subtask to another Task') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('SubtaskController', 'moveToOtherTask', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <div class="form-group">
        <label for="new_task_id"><?= t('New Task ID') ?></label>
        <input type="number" name="new_task_id" id="new_task_id" value="<?= $task['id'] ?>" class="form-control" required>
    </div>

    <?= $this->modal->submitButtons() ?>
</form>
