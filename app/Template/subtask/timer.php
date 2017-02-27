<span class="subtask-time-tracking">
    <?php if (! empty($subtask['time_spent'])): ?>
        <strong><?= $this->text->e($subtask['time_spent']).'h' ?></strong> <?= t('spent') ?>
    <?php endif ?>

    <?php if (! empty($subtask['time_estimated'])): ?>
        <strong><?= $this->text->e($subtask['time_estimated']).'h' ?></strong> <?= t('estimated') ?>
    <?php endif ?>

    <?php if ($this->user->hasProjectAccess('SubtaskController', 'edit', $task['project_id']) && $subtask['user_id'] == $this->user->getId()): ?>
        <?= $this->subtask->renderTimer($task, $subtask) ?>
    <?php endif ?>
</span>
