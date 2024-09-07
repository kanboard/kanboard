<div class="page-header">
    <h2><?= t('You already have one subtask in progress') ?></h2>
</div>
<form action="<?= $this->url->href('SubtaskRestrictionController', 'save', array('task_id' => $task['id'], 'subtask_id' => $subtask['id'])) ?>" method="post">
    <?= $this->form->csrf() ?>

    <?php if (empty($subtask_inprogress)): ?>
        <p><?= t('Unable to find another subtask in progress, you can close this window.') ?></p>
    <?php else: ?>
        <p><?= t('Select the new status of the subtask: "%s"', $subtask_inprogress['title']) ?></p>
        <?= $this->form->radios('status', $status_list) ?>
        <?= $this->form->hidden('id', $subtask_inprogress) ?>
    <?php endif ?>

    <?= $this->modal->submitButtons() ?>
</form>
