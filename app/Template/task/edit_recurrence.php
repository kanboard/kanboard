<div class="page-header">
    <h2><?= t('Edit recurrence') ?></h2>
</div>

<?php if ($task['recurrence_status'] != \Model\Task::RECURRING_STATUS_NONE): ?>
<div class="listing">
    <?= $this->render('task/recurring_info', array(
        'task' => $task,
        'recurrence_trigger_list' => $recurrence_trigger_list,
        'recurrence_timeframe_list' => $recurrence_timeframe_list,
        'recurrence_basedate_list' => $recurrence_basedate_list,
    )) ?>
</div>
<?php endif ?>

<?php if ($task['recurrence_status'] != \Model\Task::RECURRING_STATUS_PROCESSED): ?>

    <form method="post" action="<?= $this->u('task', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'ajax' => $ajax)) ?>" autocomplete="off">

        <?= $this->formCsrf() ?>

        <?= $this->formHidden('id', $values) ?>
        <?= $this->formHidden('project_id', $values) ?>

        <?= $this->formLabel(t('Generate recurrent task'), 'recurrence_status') ?>
        <?= $this->formSelect('recurrence_status', $recurrence_status_list, $values, $errors) ?>

        <?= $this->formLabel(t('Trigger to generate recurrent task'), 'recurrence_trigger') ?>
        <?= $this->formSelect('recurrence_trigger', $recurrence_trigger_list, $values, $errors) ?>

        <?= $this->formLabel(t('Factor to calculate new due date'), 'recurrence_factor') ?>
        <?= $this->formNumber('recurrence_factor', $values, $errors) ?>

        <?= $this->formLabel(t('Timeframe to calculate new due date'), 'recurrence_timeframe') ?>
        <?= $this->formSelect('recurrence_timeframe', $recurrence_timeframe_list, $values, $errors) ?>

        <?= $this->formLabel(t('Base date to calculate new due date'), 'recurrence_basedate') ?>
        <?= $this->formSelect('recurrence_basedate', $recurrence_basedate_list, $values, $errors) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>

            <?php if ($ajax): ?>
                <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
            <?php else: ?>
                <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
            <?php endif ?>
        </div>
    </form>

<?php endif ?>