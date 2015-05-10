<div class="page-header">
    <h2><?= t('Edit recurrence') ?></h2>
</div>
<section id="task-section">



<form method="post" action="<?= $this->u('task', 'recurrence', array('task_id' => $task['id'], 'project_id' => $task['project_id'], 'ajax' => $ajax)) ?>" autocomplete="off">

    <?= $this->formCsrf() ?>


    <div class="form-column">
        <?php if ($task['recurrence_status'] == \Model\Task::RECURE_STATUS_PROCESSED): ?>
        <ul>
            <li><?= t('Recurrent task has been generated') ?>
                <ul>
                    <li>
                        <?= t('Trigger to generate recurrent task: %s', $recurrence_trigger_list[$task['recurrence_trigger']]) ?></stong>
                    </li>
                    <li>
                        <?= t('Factor to calculate new due date: %s', $task['recurrence_factor']) ?></stong>
                    </li>
                    <li>
                        <?= t('Timeframe to calculate new due date: %s', $recurrence_timeframe_list[$task['recurrence_timeframe']]) ?></stong>
                    </li>
                    <li>
                        <?= t('Base date to calculate new due date: %s', $recurrence_basedate_list[$task['recurrence_basedate']]) ?></stong>
                    </li>
                </ul>
            </li>
        </ul>
        <?php endif ?>
        <?php if ($task['recurrence_parent'] || $task['recurrence_child']): ?>
        <ul>
            <?php if ($task['recurrence_parent']): ?>
            <li>
                <?= t('Recurrent task created by: %s', $task['recurrence_parent']) ?>
            </li>
            <?php endif ?>
            <?php if ($task['recurrence_child']): ?>
            <li>
                <?= t('Created recurrent task: %s', $task['recurrence_child']) ?>
            </li>
            <?php endif ?>
        </ul>
        <?php endif ?>

        <?php if ($task['recurrence_status'] != \Model\Task::RECURE_STATUS_PROCESSED): ?>

        <?= $this->formHidden('id', $values) ?>
        <?= $this->formHidden('project_id', $values) ?>

        <?= $this->formLabel(t('Generate recurrent task'), 'recurrence_status') ?>
        <?= $this->formSelect('recurrence_status', $recurrence_status_list, $values, $errors) ?> </br>

        <?= $this->formLabel(t('Trigger to generate recurrent task'), 'recurrence_trigger') ?>
        <?= $this->formSelect('recurrence_trigger', $recurrence_trigger_list, $values, $errors) ?> </br>

        <?= $this->formLabel(t('Factor to calculate new due date'), 'recurrence_factor') ?>
        <?= $this->formNumber('recurrence_factor', $values, $errors) ?> </br>

        <?= $this->formLabel(t('Timeframe to calculate new due date'), 'recurrence_timeframe') ?>
        <?= $this->formSelect('recurrence_timeframe', $recurrence_timeframe_list, $values, $errors) ?> </br>

        <?= $this->formLabel(t('Base date to calculate new due date'), 'recurrence_basedate') ?>
        <?= $this->formSelect('recurrence_basedate', $recurrence_basedate_list, $values, $errors) ?> </br>

        <?php endif ?>

    </div>

    <div class="form-actions">
      <?php if ($task['recurrence_status'] != \Model\Task::RECURE_STATUS_PROCESSED): ?>
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
        <?= t('or') ?>
      <?php endif ?>
      <?php if ($ajax): ?>
          <?= $this->a(t('cancel'), 'board', 'show', array('project_id' => $task['project_id'])) ?>
      <?php else: ?>
          <?= $this->a(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
      <?php endif ?>
    </div>
</form>
</section>
