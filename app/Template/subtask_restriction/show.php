<div class="page-header">
    <h2><?= t('You already have one subtask in progress') ?></h2>
</div>
<form action="<?= $this->url->href('SubtaskRestrictionController', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'subtask_id' => $subtask['id'])) ?>" method="post">

    <?= $this->form->csrf() ?>

    <p><?= t('Select the new status of the subtask: "%s"', $subtask_inprogress['title']) ?></p>
    <?= $this->form->radios('status', $status_list) ?>
    <?= $this->form->hidden('id', $subtask_inprogress) ?>

    <?= $this->modal->submitButtons() ?>
</form>
