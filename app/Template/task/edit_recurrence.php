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

    <form method="post" action="<?= $this->url->href('task', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off">

        <?= $this->form->csrf() ?>

        <?= $this->form->hidden('id', $values) ?>
        <?= $this->form->hidden('project_id', $values) ?>

        <?= $this->form->label(t('Generate recurrent task'), 'recurrence_status') ?>
        <?= $this->form->select('recurrence_status', $recurrence_status_list, $values, $errors) ?>

        <?= $this->form->label(t('Trigger to generate recurrent task'), 'recurrence_trigger') ?>
        <?= $this->form->select('recurrence_trigger', $recurrence_trigger_list, $values, $errors) ?>

        <?= $this->form->label(t('Factor to calculate new due date'), 'recurrence_factor') ?>
        <?= $this->form->number('recurrence_factor', $values, $errors) ?>

        <?= $this->form->label(t('Timeframe to calculate new due date'), 'recurrence_timeframe') ?>
        <?= $this->form->select('recurrence_timeframe', $recurrence_timeframe_list, $values, $errors) ?>

        <?= $this->form->label(t('Base date to calculate new due date'), 'recurrence_basedate') ?>
        <?= $this->form->select('recurrence_basedate', $recurrence_basedate_list, $values, $errors) ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </div>
    </form>

<?php endif ?>